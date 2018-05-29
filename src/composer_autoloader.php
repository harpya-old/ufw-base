<?php

/**
 * @return bool 
 */
return function () : bool {
    $files = array(
      __DIR__ . '/../../../autoload.php',  // composer dependency
      __DIR__ . '/../vendor/autoload.php', // stand-alone package
    );
    $response = false;
    foreach ($files as $file) {
        if (is_file($file)) {            
            require_once $file;
            $response = true;
            //return true;
//        } else {
//            $response = false;
        }
    }
    return $response;
};