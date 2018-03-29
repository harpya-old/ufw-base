<?php
namespace harpya\ufw;

class AgentCli {
    use utils\Logger;
    
    const HELP = [
        "options" => [
            "--help" => [
                "summary" => "Shows this help."
            ],
            "--create" => [
                "summary" => "Create a new application.",
                "examples" => [
                    "--create=demo  \t create the application 'demo'"
                ]
            ], 
            "--init" => [
                "summary" => "Initialize the uFw workspace, creating folders and files needed."
                
            ], 
            "--run" => [
                "summary" => "Runs the application service defined by URI passwd by parameter.",
                "examples" => [
                    "--run=get:/app/demo/application_route",
                    "--run=post:/app/demo/user",
                    "--run=put:/app/demo/user/123"
                ]
                
            ],
            "--data" => [
                "summary" => "Pass data to application (Presumes the --run option)",
                "format" => "String in JSON notation",
                "description" => "Used",
                "examples" => [
                    "--data='{\"email\":\"email@domain.com\"}'",
                    "--data='[{\"record\":625, \"code\":\"AB123\"},{\"record\":731, \"code\":\"23F4E\"}]'"
                ]
            ]
        ]
    ];
    
    
    const DEFAULT_CONFIG = [
            'delay' => 5,
            'host' => 'http://localhost:8008',
            'config_ttl' => 90,       
            'lock_dir' => './',
            'tasks_ttl' => 60
        ];
    
    
    protected $run;
    protected $config;
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
            //return;
        } elseif (Console::getArg('--config-file')) {
            $this->applyConfig('file',Console::getArg('--config-file'));
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
                    $stat = ' missing ';
                    
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
        $this->applyConfig('cli', '[]');
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
                $this->runTask($task);
            }
            $this->sleep();
            $this->refreshConfiguration();            
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
            throw new \Exception("Lock file does not exists",8);
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
            $this->tasks = $json;
            $this->debug("Applying cli task list",  $this->tasks);
        } elseif ($type == 'file') {
            if (file_exists($data)) {
                $text = file_get_contents($data);
                $json = json_decode($text,true);
                $this->tasks = array_replace_recursive($this->tasks, $json);
                $this->debug("Applying file tasks ",  $this->tasks);
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
        
        $ttl = Utils::get('config_ttl', $this->config,'./');
        
        $elapsedTime = (time() - $this->lastConfigUpdate);
        $diffTTL = $ttl - $elapsedTime;
        
        if ($diffTTL < 0) {
            $this->debug("Refreshing configuration");
            $pid = getmypid();
            $config = $this->readLockFile();
            if (array_key_exists($pid, $config)) {
                $this->config = $config[$pid];
            }
            
            $this->lastConfigUpdate = time();
        }
    }
    
    protected function refreshTasks() {
        
        $ttl = Utils::get('tasks_ttl', $this->config,'./');
                
        $elapsedTime = (time() - $this->lastTasksUpdate);
        $diffTTL = $ttl - $elapsedTime;
        
        if ($diffTTL < 0) {
            $this->applyTasks('file',Console::getArg('--tasks-file'));
             $this->lastTasksUpdate = time();
        }
        
        
    }
    
    
    
    
    
    
    public function checkAndRestart() {
        
    }
    
    
    
    
}
