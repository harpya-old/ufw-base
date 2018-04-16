<?php

/**
 * @return bool 
 */
return function () : bool {
    $files = array(
      __DIR__ . '/../../../autoload.php',  // composer dependency
      __DIR__ . '/../vendor/autoload.php', // stand-alone package
    );
    $response = true;
    foreach ($files as $file) {
        if (is_file($file)) {            
            require_once $file;
            //return true;
        } else {
            $response = false;
        }
    }
    return $response;
};