<?php
namespace harpya\ufw;

/**
 * 
 */
class Setup {

    const FILES_BASE = [
        'bootstrap/init.php' => 'PD9waHANCnJlcXVpcmUgX19ESVJfXy4iLy4uL3ZlbmRvci9hdXRvbG9hZC5waHAiOw0KLy9yZXF1aXJlIF9fRElSX18uIi8uLi8uLi91ZncvdmVuZG9yL2F1dG9sb2FkLnBocCI7DQoNCnVzZSBoYXJweWFcdWZ3XEFwcGxpY2F0aW9uOw0KDQokcmVxdWVzdCA9IFxoYXJweWFcdWZ3XFJlcXVlc3Q6Om9mKCk7DQoNCg0KJGFwcCA9IEFwcGxpY2F0aW9uOjpnZXRJbnN0YW5jZShbDQogICAgQXBwbGljYXRpb246OkRFRl9BUFBTX1BBVEggPT4gJy4uL2FwcHMvJywNCiAgICBBcHBsaWNhdGlvbjo6Q01QX1JPVVRFUiA9PiBuZXcgaGFycHlhXHVmd1xSb3V0ZXIoKSwNCiAgICBBcHBsaWNhdGlvbjo6Q01QX1JFUVVFU1QgPT4gJHJlcXVlc3QsDQogICAgQXBwbGljYXRpb246OkNNUF9DT05GSUcgPT4gXGhhcnB5YVx1ZndcQ29uZmlnOjpvZihfX0RJUl9fKQ0KXSk7DQoNCiRhcHAtPmluaXQoKTsNCg0KJGFwcC0+cnVuKCk7DQo=',
        'config/routes.json' => 'ew0KICAgICJyb3V0ZXMiIDogew0KICAgICAgICAiL2luZm8iIDogew0KICAgICAgICAgICAgIkdFVCIgOiB7DQogICAgICAgICAgICAgICAgImV2YWwiIDogInBocGluZm8oKTsiDQogICAgICAgICAgICB9DQogICAgICAgIH0NCiAgICB9DQp9',
        'public/.htaccess' => 'DQo8SWZNb2R1bGUgbW9kX25lZ290aWF0aW9uLmM+ICAgICAgICANCiAgIE9wdGlvbnMgLU11bHRpVmlld3MgIA0KPC9JZk1vZHVsZT4gDQoNCjxJZk1vZHVsZSBtb2RfcmV3cml0ZS5jPiANCiAgICBSZXdyaXRlRW5naW5lIE9uIA0KICAgIFJld3JpdGVDb25kICV7UkVRVUVTVF9GSUxFTkFNRX0gIS1kIA0KICAgIFJld3JpdGVDb25kICV7UkVRVUVTVF9GSUxFTkFNRX0gIS1mIA0KICAgIFJld3JpdGVSdWxlIF4gaW5kZXgucGhwIFtMXSANCiAgPC9JZk1vZHVsZT4=',
        'public/index.php' => 'PD9waHANCg0KdHJ5eyANCiAgICAgcmVxdWlyZV9vbmNlICcuLi9ib290c3RyYXAvaW5pdC5waHAnOyANCiB9IGNhdGNoIChcRXhjZXB0aW9uICRleCkgew0KICAgICRkYXRhID0gWydleGNlcHRpb24nID0+IHRydWUsICdzdWNjZXNzJyA9PiBmYWxzZSwgJ21zZycgPT4gJGV4LT5nZXRNZXNzYWdlKCksJ2NvZGUnPT4kZXgtPmdldENvZGUoKV07ICANCiAgICANCiAgICANCiAgICBoZWFkZXIoJ0NvbnRlbnQtdHlwZTogdGV4dC9qc29uJyk7DQogICAgZWNobyBqc29uX2VuY29kZSgkZGF0YSwgSlNPTl9PQkpFQ1RfQVNfQVJSQVkpIC4gIlxuIjsNCiAgICBleGl0OyANCiB9IA=='
    ];
    const FILES_APP = [
        'routes/main.json' => 'ew0KICAgICJkZWZhdWx0Ijogew0KCSJjb250cm9sbGVyIiA6ICJcXG15YXBwXFxNeUNvbnRyb2xsZXIiLA0KICAgICAgICAibWV0aG9kIiA6ICJ3ZWxjb21lIg0KICAgIH0sDQogICAgInJvdXRlcyI6IHsNCiAgICAgICAgIi9pbmZvIiA6IHsNCiAgICAgICAgICAgICJHRVQiIDogew0KICAgICAgICAgICAgICAgICJldmFsIiA6ICJwaHBpbmZvKCk7Ig0KICAgICAgICAgICAgfQ0KICAgICAgICB9DQogICB9DQp9DQo=',
        'src/MyController.php' => 'PD9waHANCg0KbmFtZXNwYWNlIG15YXBwOw0KDQpjbGFzcyBNeUNvbnRyb2xsZXIgZXh0ZW5kcyBcaGFycHlhXHVmd1xDb250cm9sbGVyICB7DQoNCglwdWJsaWMgZnVuY3Rpb24gd2VsY29tZSgpIHsNCiAgICAgICAgZWNobyAiXG5XZWxjb21lIHRvIEhhcnB5YSFcbiI7DQoJfQ0KDQp9DQo=',
        'bootstrap.php' => 'PD9waHANCiR0b0luY2x1ZGUgPSBfX0RJUl9fIC4iL3ZlbmRvci9hdXRvbG9hZC5waHAiOw0KaW5jbHVkZSAkdG9JbmNsdWRlOw0K',
        'composer.json' => 'ew0KICAgICJhdXRvbG9hZCI6IHsNCiAgICAgICAgInBzci00Ijogew0KICAgICAgICAgICAgIm15YXBwXFwiOiAic3JjLyINCiAgICAgICAgfQ0KICAgIH0NCn0='
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
        echo "ufw - Helper to uFw framework\nOptions:\n";
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
        Utils::getInstance()->createDirectory(['apps', 'bootstrap', 'config', 'public']);
        foreach (self::FILES_BASE as $pathname => $contents) {
            Utils::getInstance()->createFile($pathname, $contents);            
        }
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
