<?php

namespace harpya\ufw;

class Utils {
    
    
    public static function get($key, $array=[], $default=false) {        
        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return $default;
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

    