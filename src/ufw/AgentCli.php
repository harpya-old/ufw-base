<?php
namespace harpya\ufw;

class AgentCli {
    use utils\Logger;
    
    const HELP = [
        "options" => [
            "--help" => [
                "summary" => "Shows this help."
            ],
            "--config" => [
                "summary" => "Define the configuration for this instance",
            ], 
            "--config-file" => [
                "summary" => "Informs to agent the path of configuration file"
                
            ], 
            "--tasks" => [
                "summary" => "Define the tasks list for this instance",
            ], 
            "--tasks-file" => [
                "summary" => "Informs to agent the path of tasks file"
                
            ], 
            "--run" => [
                "summary" => "Starts the agent instance"                
            ],
            "--status" => [
                "summary" => "Gets the instance's status"
            ],
            "--force-clean" => [
                "summary" => "Used with --status option, implies that all 'dead' instances will be cleaned from lock file"
            ],
            "--force-restart" => [                
                "summary" => "Used with --status option, implies that all 'dead' instances will be restarted, with the same configuration"
            ]
        ]
    ];
    
    
    const DEFAULT_CONFIG = [
            'delay' => 5,
            'host' => 'http://localhost:8008',
            'config_ttl' => 90,       
            'lock_dir' => './tmp',
            'tasks_ttl' => 60
        ];
    
    
    protected $run;
    protected $config;
    protected $configFilename;
    protected $tasks = [];
    protected $lsAgentStatus = [];
    
    protected $executionPlan = [];
    protected $nextExecution = 0;
    
    protected $lastConfigUpdate;
    protected $lastTasksUpdate;
    
    
    public function help() {
        echo "ufw - Async agent to uFw framework\nOptions:\n";
        foreach (self::HELP['options'] as $helpOption => $helpDescription) {
            echo "\n   $helpOption \t " . ($helpDescription['summary']??"");
            if (array_key_exists('examples', $helpDescription)) {
                echo "\n            \t Ex:";
                foreach ($helpDescription['examples'] as $example) {
                    echo "\n           \t   $example";
                }
                echo "\n";
            }
        }
        echo "\n";
    }
    
    
    public function run() {
        
        if (Console::getArg('--config')) {
            $this->applyConfig('cli',Console::getArg('--config'));
            $this->config['config_ttl'] = false;
            //return;
        } elseif (Console::getArg('--config-file')) {
            $this->configFilename = Console::getArg('--config-file');
            $this->applyConfig('file',$this->configFilename);
        }
        
        if (Console::getArg('--tasks')) {
            $this->applyTasks('cli',Console::getArg('--tasks'));
            //return;
        } elseif (Console::getArg('--tasks-file')) {
            $this->applyTasks('file',Console::getArg('--tasks-file'));
        }
        
        
        
        
        if (Console::getArg('--run')) {
            $this->runAgent();
            return;
        } elseif (Console::getArg('--status')) {
            $this->checkStatus();
            return;
        }
        
        
//        if (Console::getArg('--create')) {
//            $this->createProject(Console::getArg('--create'));
//            return;
//        } elseif (Console::getArg('--init')) {
//            $this->initWorkspace();
//            return;
//        } elseif (Console::getArg('--run')) {
//            $this->runAction(Console::getArg('--run'),json_decode(Console::getArg('--data','[]'),true)  );
//        }
        
        
        
        $this->help();
    }
    
    
    public function checkStatus() {
        
        try {
            $this->lsAgentStatus = $this->readLockFile();
            $ph = new utils\ProcessHandler(true);
            
            echo "\n";
            foreach ($this->lsAgentStatus  as $pid => $data) {
                
                if ($ph->getProcess($pid)) {
                    $stat = ' running ';
                } else {
                    $stat = ' dead    ';
                    
                    if (Console::getArg('--force-restart')) {
                        $stat = $this->restartAgent($data);
                    } elseif (Console::getArg('--force-clean')) {
                        $this->cleanAgent($pid);
                    }                    
                    
                }
                
               
                
                
                echo "\n pid:            $pid       ($stat)";
                echo "\n started:       " . date('Y-m-d H:i:s',$data['started']);
                echo "\n configuration: " . json_encode($data['config'])."\n---\n";
            }
            
            
            if (Console::getArg('--force-clean')) {
                $this->writeLockFile($this->lsAgentStatus);
            }
            
            
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }
        
    }
    
    
    protected function restartAgent($data) {
        
        // TODO: create the command line with $data
        
        $command = "";
        $bp = new utils\BackgroundProcess($command);
    }
    
    
    
    protected function cleanAgent($pid) {
        if (array_key_exists($pid, $this->lsAgentStatus )) {
            unset($this->lsAgentStatus[$pid]);
        }
    }
    
    
    
    public function __construct() {
        $this->turnDebugOutputOff();
        $this->applyConfig('cli', '[]');
        $this->turnDebugOutputOn();
    }
    
    
    protected function runAgent() {
        
        $this->lastTasksUpdate = time();
        $this->lastConfigUpdate = time();
        
        $this->run = true;
        $this->info("Starting execution");
        //$this->readConfig();
        
        
        if (empty($this->tasks)) {
            $this->warn("No tasks available");
            echo "\n\n##################################\n## Error: no tasks assigned \n##################################\n\n";
            exit;
        }
        
        $this->addJobToLockFile();
        
        while($this->run) {            
            $task = $this->getNextTask();
            if ($task) {
                $this->debug('Executing', $task->getAll());
                $response = $task->execute();
                $this->debug("Output", $response);
//                $this->runTask($task);
            }
            $this->sleep();
            $this->refreshConfiguration();   
            $this->refreshTasks();
        }
        
        
        
    }
    
    
    protected function getLockDir() {
        $lockDir = Utils::get('lock_dir', $this->config,'./');
        return $lockDir;
    }
    
    protected function getLockFile() {
        $lockFile = $this->getLockDir()."/ufw.lock";
        return $lockFile;
    }
    
    
    protected function addJobToLockFile() {
        
        $lockDir = $this->getLockDir();
        
        $this->debug("Checking the lock dir $lockDir");
        
        if (!is_writable($lockDir)) {
            $msg = "Folder not writable ($lockDir)";
            $this->error($msg);
            throw new \Exception($msg,7);
        }
        
        $lockFile = $this->getLockFile();
        
        $this->debug("Using lock file $lockFile");
        if (file_exists($lockFile)) {
            $this->debug("Reading the lock file");
            $lock = json_decode(file_get_contents($lockFile),true);
        }
        
        $pid = getmypid();
        
        $lock[$pid] = [
            'started' => time(),
            'pid' => $pid,
            'config' => $this->config
        ];
        
        $this->debug("Adding new entry to lock file", $lock[$pid]);
        
        $this->writeLockFile($lock);
        
    }
    
    
    protected function writeLockFile($lock=false) {
        
        $lockFile = $this->getLockFile();
        file_put_contents($lockFile, json_encode($lock));
    }
    
    
    protected function readLockFile() {
        
        $lockDir = $this->getLockDir();
        if (!is_writable($lockDir)) {
            throw new \Exception("Folder not writable ($lockDir)",7);
        }
        
        $lockFile = $this->getLockFile();
        
        if (file_exists($lockFile)) {
            $lock = json_decode(file_get_contents($lockFile),true);
        } else {
            throw new \Exception("Lock file does not exists ($lockFile)",8);
        }
        return $lock;
        
    }
    
    
    protected function applyConfig($type, $data) {
        if ($type == 'cli') {
            $json = json_decode($data,true);
            $this->config = array_replace_recursive(self::DEFAULT_CONFIG, $json);
            $this->debug("Applying cli configuration",  $this->config);
        } elseif ($type == 'file') {
            if (file_exists($data)) {
                $text = file_get_contents($data);
                $json = json_decode($text,true);
                $this->config = array_replace_recursive(self::DEFAULT_CONFIG, $json);
                $this->debug("Applying file configuration",  $this->config);
            }
        }
        
    }
    
    
    protected function applyTasks($type, $data) {
        if ($type == 'cli') {
            
            $json = json_decode($data,true);
            $tasks = Task::fromArray($json);
            $this->tasks = $tasks;
            $this->debug("Applying cli task list",  $this->tasks);
        } elseif ($type == 'file') {
            $this->debug("\n\n\n\n\n ####### \n Applying the tasks from $data");
            if (file_exists($data)) {
                $text = file_get_contents($data);
                $json = json_decode($text,true);
                $tasks = Task::fromArray($json);
                $this->tasks = array_replace_recursive($this->tasks, $tasks);
                $this->debug("#######\n######### Applying file tasks ",  $this->tasks);
            }
        }
        
        foreach ($this->tasks as $k => $task) {
            $this->executionPlan[] = $k;
        }
        $this->nextExecution = 0;
        
    }
    
    
    public function sleep() {
        $n = Utils::get('delay', $this->config, 10);
        sleep($n);
    }
    
    public function getNextTask() {
        $i =  $this->executionPlan[$this->nextExecution++];        
        $this->nextExecution = $this->nextExecution % count($this->executionPlan);
        return $this->tasks[$i];
    }
    
    public function runTask($task) {
        
        $this->debug("Running task", $task);
         
        $baseURI = Utils::get('base_uri', $task, Utils::get('host', $this->config, 'http://localhost/'));
        
        
        $client = new \GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => $baseURI
        ]);
        
        // base_uri, method, uri, options
        
        $httpMethod = Utils::get('method', $task, 'GET');
        $uri = Utils::get('uri', $task, '/');
        $options = Utils::get('options', $task, []);
        
        
        $response = $client->request($httpMethod, $uri, $options);
        
        $body = $response->getBody();
        
        
        $this->debug("Output", $body);
        
        if (Utils::get('output', $task)) {
            file_put_contents(Utils::get('output', $task), $body);
        }
        
    }
    
    
    protected function refreshConfiguration() {
        
        $ttl = Utils::get('config_ttl', $this->config,60);
        
        $elapsedTime = (time() - $this->lastConfigUpdate);
        $diffTTL = $ttl - $elapsedTime;
        
//        $this->debug("### diffTTL = $diffTTL" );
        
        
        if ($diffTTL < 0) {
            $this->debug("### Refreshing configuration");           
            $pid = getmypid();
            $config = $this->readLockFile();
            
//            $this->debug("### Configuration", ['pid'=>$pid, 'lock' => $config]);
            if (array_key_exists($pid, $config)) {
//                $this->debug("### Found!!! ", [$config[$pid] ]);
                $this->config = $config[$pid]['config'];
            }
//            $this->debug("### Final!!! ", [$this->config]);
            $this->lastConfigUpdate = time();
        }
    }
    
    protected function refreshTasks() {
        
        if (!Console::getArg('--tasks-file')) {
            return false;
        }
        
        $ttl = Utils::get('tasks_ttl', $this->config,60);
                
        $elapsedTime = (time() - $this->lastTasksUpdate);
        $diffTTL = $ttl - $elapsedTime;
        
        $this->debug("### Tasks refreshing countdown $diffTTL ");  
        if ($diffTTL < 0) {
            $this->debug("### Refreshing configuration");  
            $this->applyTasks('file',Console::getArg('--tasks-file'));
             $this->lastTasksUpdate = time();
        }
        
        
    }
    
    
    
    
    
    
    public function checkAndRestart() {
        
    }
    
    
    
    
}
