<?php
namespace harpya\ufw;

/**
 * 
 */
class Setup {

    const FILES_BASE = [
        'bootstrap/init.php' => 'PD9waHANCnJlcXVpcmUgX19ESVJfXy4iLy4uL3ZlbmRvci9hdXRvbG9hZC5waHAiOw0KDQp1c2UgaGFycHlhXHVmd1xBcHBsaWNhdGlvbjsNCg0KJHJlcXVlc3QgPSBcaGFycHlhXHVmd1xSZXF1ZXN0OjpvZigpOw0KDQokYXBwID0gQXBwbGljYXRpb246OmdldEluc3RhbmNlKFsNCiAgICBBcHBsaWNhdGlvbjo6REVGX0FQUFNfUEFUSCA9PiAnLi4vYXBwcy8nLA0KICAgIEFwcGxpY2F0aW9uOjpDTVBfUk9VVEVSID0+IG5ldyBoYXJweWFcdWZ3XFJvdXRlcigpLA0KICAgIEFwcGxpY2F0aW9uOjpDTVBfUkVRVUVTVCA9PiAkcmVxdWVzdCwNCiAgICBBcHBsaWNhdGlvbjo6Q01QX0NPTkZJRyA9PiBcaGFycHlhXHVmd1xDb25maWc6Om9mKF9fRElSX18pLA0KICAgIEFwcGxpY2F0aW9uOjpDTVBfVklFVyA9PiBcaGFycHlhXHVmd1x2aWV3XFNtYXJ0eTo6Z2V0SW5zdGFuY2UoKQ0KXSk7DQoNCiRhcHAtPmluaXQoKTsNCg0KJGFwcC0+cnVuKCk7DQo=',
        'config/routes.json' => 'ew0KICAgICJyb3V0ZXMiIDogew0KICAgICAgICAiL2luZm8iIDogew0KICAgICAgICAgICAgIkdFVCIgOiB7DQogICAgICAgICAgICAgICAgImV2YWwiIDogInBocGluZm8oKTsiDQogICAgICAgICAgICB9DQogICAgICAgIH0NCiAgICB9DQp9',
        'public/.htaccess' => 'DQo8SWZNb2R1bGUgbW9kX25lZ290aWF0aW9uLmM+ICAgICAgICANCiAgIE9wdGlvbnMgLU11bHRpVmlld3MgIA0KPC9JZk1vZHVsZT4gDQoNCjxJZk1vZHVsZSBtb2RfcmV3cml0ZS5jPiANCiAgICBSZXdyaXRlRW5naW5lIE9uIA0KICAgIFJld3JpdGVDb25kICV7UkVRVUVTVF9GSUxFTkFNRX0gIS1kIA0KICAgIFJld3JpdGVDb25kICV7UkVRVUVTVF9GSUxFTkFNRX0gIS1mIA0KICAgIFJld3JpdGVSdWxlIF4gaW5kZXgucGhwIFtMXSANCiAgPC9JZk1vZHVsZT4=',
        'public/index.php' => 'PD9waHANCg0KdHJ5eyANCiAgICAgcmVxdWlyZV9vbmNlICcuLi9ib290c3RyYXAvaW5pdC5waHAnOyANCiB9IGNhdGNoIChcRXhjZXB0aW9uICRleCkgew0KICAgICRkYXRhID0gWydleGNlcHRpb24nID0+IHRydWUsICdzdWNjZXNzJyA9PiBmYWxzZSwgJ21zZycgPT4gJGV4LT5nZXRNZXNzYWdlKCksJ2NvZGUnPT4kZXgtPmdldENvZGUoKV07ICANCiAgICANCiAgICANCiAgICBoZWFkZXIoJ0NvbnRlbnQtdHlwZTogdGV4dC9qc29uJyk7DQogICAgZWNobyBqc29uX2VuY29kZSgkZGF0YSwgSlNPTl9PQkpFQ1RfQVNfQVJSQVkpIC4gIlxuIjsNCiAgICBleGl0OyANCiB9IA=='
    ];
    const FILES_APP = [
        'routes/main.json' => 'ew0KICAgICJkZWZhdWx0Ijogew0KCSJjb250cm9sbGVyIiA6ICJcXG15YXBwXFxNeUNvbnRyb2xsZXIiLA0KICAgICAgICAibWV0aG9kIiA6ICJ3ZWxjb21lIg0KICAgIH0sDQogICAgInJvdXRlcyI6IHsNCiAgICAgICAgIi9pbmZvIiA6IHsNCiAgICAgICAgICAgICJHRVQiIDogew0KICAgICAgICAgICAgICAgICJldmFsIiA6ICJwaHBpbmZvKCk7Ig0KICAgICAgICAgICAgfQ0KICAgICAgICB9DQogICB9DQp9DQo=',
        'src/MyController.php' => 'PD9waHANCg0KbmFtZXNwYWNlIG15YXBwOw0KDQpjbGFzcyBNeUNvbnRyb2xsZXIgZXh0ZW5kcyBcaGFycHlhXHVmd1xDb250cm9sbGVyICB7DQoNCglwdWJsaWMgZnVuY3Rpb24gd2VsY29tZSgpIHsNCiAgICAgICAgICAgIFxoYXJweWFcdWZ3XEFwcGxpY2F0aW9uOjpnZXRJbnN0YW5jZSgpDQogICAgICAgICAgICAgICAgICAgIC0+Z2V0VmlldygpDQogICAgICAgICAgICAgICAgICAgIC0+ZGlzcGxheSgnd2VsY29tZS50cGwnKTsNCgl9DQoNCn0NCg==',
        'bootstrap.php' => 'PD9waHANCiR0b0luY2x1ZGUgPSBfX0RJUl9fIC4iL3ZlbmRvci9hdXRvbG9hZC5waHAiOw0KaW5jbHVkZSAkdG9JbmNsdWRlOw0K',
        'composer.json' => 'ew0KICAgICJhdXRvbG9hZCI6IHsNCiAgICAgICAgInBzci00Ijogew0KICAgICAgICAgICAgIm15YXBwXFwiOiAic3JjLyINCiAgICAgICAgfQ0KICAgIH0NCn0=',
        'templates/welcome.tpl' => 'PCFET0NUWVBFIGh0bWw+DQo8aHRtbCBsYW5nPSJlbiI+DQo8aGVhZD4NCiAgPHRpdGxlPkhhcnB5YTwvdGl0bGU+DQogIDxtZXRhIGNoYXJzZXQ9InV0Zi04Ij4NCiAgPG1ldGEgbmFtZT0idmlld3BvcnQiIGNvbnRlbnQ9IndpZHRoPWRldmljZS13aWR0aCwgaW5pdGlhbC1zY2FsZT0xIj4NCiAgPGxpbmsgcmVsPSJzdHlsZXNoZWV0IiBocmVmPSJodHRwczovL21heGNkbi5ib290c3RyYXBjZG4uY29tL2Jvb3RzdHJhcC8zLjMuNy9jc3MvYm9vdHN0cmFwLm1pbi5jc3MiPg0KICA8c2NyaXB0IHNyYz0iaHR0cHM6Ly9hamF4Lmdvb2dsZWFwaXMuY29tL2FqYXgvbGlicy9qcXVlcnkvMy4zLjEvanF1ZXJ5Lm1pbi5qcyI+PC9zY3JpcHQ+DQogIDxzY3JpcHQgc3JjPSJodHRwczovL21heGNkbi5ib290c3RyYXBjZG4uY29tL2Jvb3RzdHJhcC8zLjMuNy9qcy9ib290c3RyYXAubWluLmpzIj48L3NjcmlwdD4NCjwvaGVhZD4NCjxib2R5Pg0KDQo8ZGl2IGNsYXNzPSJjb250YWluZXIiPg0KPGRpdiBjbGFzcz0icm93IGp1bWJvdHJvbiI+DQo8ZGl2IGNsYXNzPSJjb2wtbWQtMTIiPg0KDQo8ZGl2IGNsYXNzPSJjb2wtbWQtMiI+DQo8aW1nIHNyYz0iaHR0cDovL3d3dy5oYXJweWEubmV0L2ltZy9sb2dvLnBuZyIgc3R5bGU9IndpZHRoOjEwMCUiPg0KPC9kaXY+DQo8ZGl2IGNsYXNzPSJjb2wtbWQtMTAiPg0KICA8aDE+SGFycHlhPC9oMT4gDQogIDxwPllldCBhbm90aGVyIHNpbXBsZSBQSFAgZnJhbWV3b3JrLCBpbnRlbmRlZCB0byBkZXZlbG9wIEFQSSBiYXNlZCB3ZWIgYXBwbGljYXRpb25zLjwvcD4gDQoNCjxwPg0KPGg0PkxpbmtzPC9oND4NCjx1bD4NCgk8bGk+IDxhIGhyZWY9Imh0dHA6Ly93d3cuaGFycHlhLm5ldCI+d3d3LmhhcnB5YS5uZXQ8L2E+IDwvbGk+DQoJPGxpPiA8YSBocmVmPSJodHRwczovL2dpdGh1Yi5jb20vaGFycHlhL3Vmdy1iYXNlIj5HaXRIdWI8L2E+IDwvbGk+DQoJPGxpPiA8YSBocmVmPSJodHRwczovL3BhY2thZ2lzdC5vcmcvcGFja2FnZXMvaGFycHlhL3Vmdy1iYXNlIj5QYWNrYWdpc3Q8L2E+IDwvbGk+DQo8L3VsPg0KPC9wPg0KPC9kaXY+DQo8L2Rpdj4NCjwvZGl2Pg0KDQoNCjxwPg0KDQo8L3A+IA0KDQo8L2Rpdj4NCg0KPC9ib2R5Pg0KPC9odG1sPg=='
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

    
    /**
     * Shows the help options
     */
    public function help() {
        
        echo "ufw - Helper to uFw framework\n\nOptions:\n";
        foreach (self::HELP['options'] as $helpOption => $helpDescription) {
            echo "\n   $helpOption \t " . ($helpDescription['summary'] ?? "");
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

    
    /**
     * Inspect the command line arguments and execute it.
     *  
     * @return void
     */
    public function run() {

        echo "
 _   _                              
| | | |                             
| |_| | __ _ _ __ _ __  _   _  __ _ 
|  _  |/ _` | '__| '_ \| | | |/ _` |
| | | | (_| | |  | |_) | |_| | (_| |
\_| |_/\__,_|_|  | .__/ \__, |\__,_|
                 | |     __/ |      
                 |_|    |___/        

";
        
        // Create a new project
        if (Console::getArg('--create')) {
            $this->createProject(Console::getArg('--create'));
            return;
        // Initialize the current workspace    
        } elseif (Console::getArg('--init')) {
            $this->initProject();
            return;
        // Run a command from an application    
        } elseif (Console::getArg('--run')) {
            $this->runAction(Console::getArg('--run'), json_decode(Console::getArg('--data', '[]'), true));
            return;
        } else {        
            $this->help();
        }
    }

    
    /**
     * Initialize the current workspace
     */
    protected function initProject() {
        Utils::getInstance()->createDirectory(['apps', 'bootstrap', 'config', 'public', 'tmp/tpl_compile', 'plugins']);
        foreach (self::FILES_BASE as $pathname => $contents) {
            Utils::getInstance()->createFile($pathname, $contents);
        }
        chmod("tmp", 775);
        chmod("tmp/tpl_compile", 775);
    }

    
    
    
    /**
     * Create a new project workspace, and initialize it.
     * 
     * @param string $projectName
     */
    protected function createProject($projectName = 'myProj') {
        Console::stdout("Creating $projectName \n");

        Utils::getInstance()->createDirectory("./apps/" . $projectName, ['src', 'routes', 'config', 'public', 'doc', 'templates']);
        echo "\n";
        foreach (self::FILES_APP as $pathname => $contents) {
            Utils::getInstance()->createFile("./apps/" . $projectName . "/" . $pathname, $contents);
        }
        echo "\n Don't forget to run 'composer update' in your root project folder (apps/" . $projectName . ") before start to use! \n";
    }

    
    /**
     * Runs the action requested on command line
     * 
     * @param string $url
     * @param array $data
     */
    protected function runAction($url, $data = []) {        
        $this->prepareRunAction($url, $data);
        $this->performRunAction();
    }
    
    
    /**
     * 
     * @param string $url
     * @param array $data
     */
    protected function prepareRunAction($url, $data = []) {
        
        if (strpos($url, ":")) {
            $parts = explode(":", $url);
            $method = strtoupper($parts[0]);
            $uri = $parts[1];
        } else {
            $method = 'GET';
            $uri = $url;
        }

        // Set the global variables 
        $_REQUEST = $data;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
    }
    
    /**
     * Emulate the same as what the web server do, to fulfill a request.
     */
    protected function performRunAction() {
        
        require __DIR__ . "/../../../../../vendor/autoload.php";


        $request = \harpya\ufw\Request::of();

        $appFolder = __DIR__ . '/../../../../../apps/';
        $appFolder = '../apps/';

        $app = \harpya\ufw\ApplicationCli::getInstance([
                    Application::DEF_APPS_PATH => $appFolder,
                    Application::CMP_ROUTER => new \harpya\ufw\Router(),
                    Application::CMP_REQUEST => $request,
                    Application::CMP_CONFIG => \harpya\ufw\Config::of(__DIR__ . '/../../../..')
        ]);

        $app->init();

        $out = $app->run();
        print_r($out);
        exit;
    }

}
