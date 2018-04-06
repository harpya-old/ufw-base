<?php

namespace harpya\ufw;

class Utils {
    
    /**
     *
     * @var Utils
     */
    protected static $instance;
    
    /**
     *
     * @var string 
     */
    protected $applicationName;
    
    /**
     * 
     * @return Utils
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Utils();
        }
        return self::$instance;
    }
    
    public static function get($key, $array=[], $default=false) {        
        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];            
        }
        
        if (is_array($array) && is_array($key)) {
            switch (count($key)) {
                case 0: 
                    return $array;
                case 1:
                    return self::get($key[0], $array, $default);
                case 2:
                    $part0 = self::get($key[0], $array);
                    return self::get($key[1], $part0, $default);
                case 3:
                    $part0 = self::get($key[0], $array);
                    $part1 = self::get($key[1], $part0);
                    return self::get($key[2], $part1, $default);
                case 4:
                    $part0 = self::get($key[0], $array);
                    $part1 = self::get($key[1], $part0);
                    $part2 = self::get($key[2], $part1);
                    return self::get($key[3], $part2, $default);
            }
        }
        
        
        return $default;
    }
    

    /**
     * Create a set of folders into the $path
     * 
     * @param string $path
     * @param array $children
     * @throws \Exception
     */
    public function createDirectory($path, $children = []) {
        $success = true;
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
                    $this->createDirectory($path . '/' . $item);
                }
            }
        }        
    }

    /**
     * Create a file with the name $pathname, and put into it the $contents
     * @param string $pathname filename
     * @param string $contents base64 encoded string
     */
    public function createFile($pathname, $contents) {

        if (strpos($pathname, '/') !== false) {
            $dirName = dirname($pathname);
            $this->createDirectory($dirName);
        }
        file_put_contents($pathname, base64_decode($contents));
    }
    
    
    
    public function getApplicationName() {
        if (!$this->applicationName) {
            $matches = [];
            $uri = $_SERVER['REQUEST_URI'];
            preg_match("/^\/app\/([\w]+)\//", $uri, $matches);
            if (is_array($matches) && (count($matches)==2)) {
                $this->applicationName = $matches[1];
            } else {
                $this->applicationName = '';
            }
        }
        return $this->applicationName;        
    }
    
    
    /**
     * 
     * @param string $filename
     * @return array
     * @throws \Exception
     */
    public function loadJSON($filename) {
        if (file_exists($filename) && is_readable($filename)) {
            $contents = file_get_contents($filename);
            
            $json = json_decode($contents, true);
            if (!$json) {
                throw new \Exception("Invalid JSON contents in file $filename",20);
            }
            return $json;
        } else {
             throw new \Exception("Unreachable file $filename",21);
        }
    }
    
}





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

    