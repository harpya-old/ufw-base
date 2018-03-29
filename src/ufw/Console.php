<?php

namespace xbrain\ufw;

class Console extends \Clio\Console {
    
    protected static $lsArgs = false;
    
    
    public static function getArg($argName) {
        
        $ls = self::getArgs();
        
        if (is_scalar($argName)) {
            if (array_key_exists($argName, $ls)) {
                return $ls[$argName];
            } else {
                return false;
            }
        } elseif (is_array($argName)) {
            
            foreach ($argName as $arg) {
                if (array_key_exists($arg, $ls)) {
                    return $ls[$arg];
                }
            }
            
        }
        return false;
    }
    
    
    public static function getArgs() {
        
       if (self::$lsArgs === false) {        
            $ls = $argv??$_SERVER['argv']??[];

            $resp = [];

            for($i=1;$i<count($ls);$i++) {
                if (substr($ls[$i],0,2) == '--') {
                    if (strpos($ls[$i], '=') !== false) {
                        list($cmd,$opt) = explode('=',$ls[$i]);
                        $resp[$cmd] = $opt;
                    } else {
                        if (substr($ls[$i+1]??false,0,1) == '-') {
                            $resp[$ls[$i]] = true;
                        } else {
                            $resp[$ls[$i]] = $ls[$i+1]??true;
                            $i++;
                        }
                     }
                } else {
                    $resp[$i] = $ls[$i];
                }
            }
            
            self::$lsArgs = $resp;
            
       } else {
           $resp = self::$lsArgs;
       }
       
       return $resp;
    }
    
    
}