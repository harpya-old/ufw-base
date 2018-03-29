<?php
namespace harpya\ufw;


class Setup {
    
    const FILES_BASE = [
        'bootstrap/init.php' => 'PD9waHANCnJlcXVpcmUgX19ESVJfXy4iLy4uL3ZlbmRvci9hdXRvbG9hZC5waHAiOw0KcmVxdWlyZSBfX0RJUl9fLiIvLi4vLi4vdWZ3L3ZlbmRvci9hdXRvbG9hZC5waHAiOw0KDQp1c2UgaGFycHlhXHVmd1xBcHBsaWNhdGlvbjsNCg0KJHJlcXVlc3QgPSBcaGFycHlhXHVmd1xSZXF1ZXN0OjpvZigpOw0KDQoNCiRhcHAgPSBBcHBsaWNhdGlvbjo6Z2V0SW5zdGFuY2UoWw0KICAgIEFwcGxpY2F0aW9uOjpERUZfQVBQU19QQVRIID0+ICcuLi9hcHBzLycsDQogICAgQXBwbGljYXRpb246OkNNUF9ST1VURVIgPT4gbmV3IGhhcnB5YVx1ZndcUm91dGVyKCksDQogICAgQXBwbGljYXRpb246OkNNUF9SRVFVRVNUID0+ICRyZXF1ZXN0LA0KICAgIEFwcGxpY2F0aW9uOjpDTVBfQ09ORklHID0+IFxoYXJweWFcdWZ3XENvbmZpZzo6b2YoX19ESVJfXykNCl0pOw0KDQokYXBwLT5pbml0KCk7DQoNCiRhcHAtPnJ1bigpOw0K',
        'config/routes.json' => 'ew0KICAgICJyb3V0ZXMiIDogew0KICAgICAgICAiL2luZm8iIDogew0KICAgICAgICAgICAgIkdFVCIgOiB7DQogICAgICAgICAgICAgICAgImV2YWwiIDogInBocGluZm8oKTsiDQogICAgICAgICAgICB9DQogICAgICAgIH0NCiAgICB9DQp9',
        'public/.htaccess' => 'DQo8SWZNb2R1bGUgbW9kX25lZ290aWF0aW9uLmM+ICAgICAgICANCiAgIE9wdGlvbnMgLU11bHRpVmlld3MgIA0KPC9JZk1vZHVsZT4gDQoNCjxJZk1vZHVsZSBtb2RfcmV3cml0ZS5jPiANCiAgICBSZXdyaXRlRW5naW5lIE9uIA0KICAgIFJld3JpdGVDb25kICV7UkVRVUVTVF9GSUxFTkFNRX0gIS1kIA0KICAgIFJld3JpdGVDb25kICV7UkVRVUVTVF9GSUxFTkFNRX0gIS1mIA0KICAgIFJld3JpdGVSdWxlIF4gaW5kZXgucGhwIFtMXSANCiAgPC9JZk1vZHVsZT4=',
        'public/index.php' => 'PD9waHANCg0KdHJ5eyANCiAgICAgcmVxdWlyZV9vbmNlICcuLi9ib290c3RyYXAvaW5pdC5waHAnOyANCiB9IGNhdGNoIChcRXhjZXB0aW9uICRleCkgew0KICAgICRkYXRhID0gWydleGNlcHRpb24nID0+IHRydWUsICdzdWNjZXNzJyA9PiBmYWxzZSwgJ21zZycgPT4gJGV4LT5nZXRNZXNzYWdlKCksJ2NvZGUnPT4kZXgtPmdldENvZGUoKV07ICANCiAgICANCiAgICANCiAgICBoZWFkZXIoJ0NvbnRlbnQtdHlwZTogdGV4dC9qc29uJyk7DQogICAgZWNobyBqc29uX2VuY29kZSgkZGF0YSwgSlNPTl9PQkpFQ1RfQVNfQVJSQVkpIC4gIlxuIjsNCiAgICBleGl0OyANCiB9IA=='
    ];
    
    const FILES_APP = [
        
    ];
    
    
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
    
    public function help() {
        echo "ufw - Helper to uFw framework\nOptions:\n";
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
        
        
        if (Console::getArg('--create')) {
            $this->createProject(Console::getArg('--create'));
            return;
        } elseif (Console::getArg('--init')) {
            $this->initProject();
            return;
        } elseif (Console::getArg('--run')) {
            $this->runAction(Console::getArg('--run'),json_decode(Console::getArg('--data','[]'),true)  );
        }
        
        
        
        $this->help();
    }
    
    
    protected function initProject() {        
        $this->createDirectory(['apps', 'bootstrap', 'config', 'public']);
        foreach (self::FILES_BASE as $pathname => $contents) {
            $this->createFile($pathname, $contents);
        }
    }
    
    
    protected function createProject($projectName='myProj') {        
        Console::stdout("Creating $projectName \n");        
        
        $this->createDirectory("./apps/".$projectName, ['src', 'routes', 'bootstrap', 'config', 'public', 'doc','templates']);
        echo "\n";
    }
    
    
    protected function createDirectory($path, $children=[]) {
        $success =  true;
        if (is_array($path)) {
            foreach ($path as $item) {
                $this->createDirectory($item);
            }
        } elseif (is_scalar($path)) {
            echo "\n Creating $path ";
            if (strpos($path, '/') !== false) {
                $pathParts = explode("/", $path);
                $path = '';
                foreach ($pathParts as $item) {
                    if (!empty($item)) {
                        $path .= $item;
                       if (!file_exists($path)) {
                            mkdir($path);
                       }
                       $path .= '/';
                    }
                }
            } else {
                if (file_exists($path)) {
                    $success = true;
                } else {
                    $success = mkdir($path);
                }
            }
            
            if (!$success) {
                throw new \Exception("Error creating $path");
            }
            
            if ($children) {
                foreach ($children as $item) {
                    $this->createDirectory($path.'/'.$item);
                }
            }
            
        }
        
        
    }
    
    
    protected function createFile($pathname, $contents) {
        
        if (strpos($pathname, '/') !== false) {
            $dirName = dirname($pathname);
            $this->createDirectory($dirName);            
        }
        file_put_contents($pathname, base64_decode($contents));
        
    }
    
    
    
    protected function runAction($url,$data=[]) {
        
        if (strpos($url, ":")) {
            $parts = explode(":",$url);
            $method = strtoupper($parts[0]);
            $uri = $parts[1];
        } else {
            $method = 'GET';
            $uri = $url;
        }
        
        
//        print_r($data);
//        echo "\n $url \n " . __DIR__ . "\n\n";
        $_REQUEST = $data;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
                

require __DIR__."/../../../../../vendor/autoload.php";
// require __DIR__."/../../ufw/vendor/autoload.php";


$request = \harpya\ufw\Request::of();

$appFolder = __DIR__.'/../../../../../apps/';
//$appFolder = '../../../../../apps/';
$appFolder ='../apps/';

//echo "\n\n\n $appFolder \n\n ";

$app = \harpya\ufw\ApplicationCli::getInstance([
    Application::DEF_APPS_PATH => $appFolder ,
    Application::CMP_ROUTER => new \harpya\ufw\Router(),
    Application::CMP_REQUEST => $request,
    Application::CMP_CONFIG => \harpya\ufw\Config::of(__DIR__.'/../../../..')
]);



$app->init();


///print_r($app->getRouter()->getRoutes());


$app->run();

        
        
        
                
//        require_once __DIR__ .'/../../../../../bootstrap/init.php'; 
        
        exit;
    }
    
    
    
    
}


