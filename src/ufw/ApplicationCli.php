<?php
namespace xbrain\ufw;

class ApplicationCli extends Application {

    /**
     * 
     * @param array $props
     * @return Application
     */
    public static function getInstance($props=[]) {
        if (!self::$instance) {
            self::$instance = new ApplicationCli($props);
        }
        return self::$instance;
    }
    
    
    public function getApplicationsPath2() {
        return __DIR__."/../../../../".$this->appsPath;
    }
    
    
}