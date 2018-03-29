<?php

error_reporting(E_ALL & !E_NOTICE);
//error_reporting(E_ALL);

$autoloader = require __DIR__ . '/../src/composer_autoloader.php';


if (!$autoloader()) {
    die(
      'You need to set up the project dependencies using the following commands:' . PHP_EOL .
      'curl -sS https://getcomposer.org/installer | php' . PHP_EOL .
      'php composer.phar install' . PHP_EOL
    );
}


//require getcwd()."/vendor/xbrain/ufw-base/vendor/autoload.php";

if (!function_exists('\getallheaders'))
    {
    
        function getallheaders()
        {
                $headers = [];
                foreach ($_SERVER as $name => $value)
                {
                        if (substr($name, 0, 5) == 'HTTP_')
                        {
                                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                        }
                }
                return $headers;
        }
    }

    

return new $className();
