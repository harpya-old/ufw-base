<?php

namespace harpya\ufw;

/**
 * @author Eduardo Luz <eduluz@harpya.net>
 * @package ufw
 */
class Controller {
    
    protected $view;
    protected static $testMode = false;
    
    protected static $instance;    
    
    protected $parms=[];
    
    public static function setTestMode($flag=false) {
        self::$testMode = $flag;
    }
    
    
    public function getParm($key,$default=false) {
        if (array_key_exists($key, $this->parms)) {
            return $this->parms[$key];
        } else {
            return $default;
        }
    }
    
    public function getParms() {
        return $this->parms;
    }
    
    public function __construct($parms=[]) {
        $this->parms = $parms;
    }


    
    /**
     * 
     * @param Controller $instance
     * @return Controller
     */
    public static function getInstance($instance=false) {
        
        if ($instance) {
            self::$instance = $instance;
        }
        
        return self::$instance;
    }
    
    

    
    
    /**
     * 
     * @param type $view
     * @return View
     */
    public function getView($view=false) {
        
        if ($view) {
            $this->view = $view;
        } elseif (!$this->view) {
            $this->view = Application::getInstance()->getView();            
        }
        
        if (!$this->view) {
            throw new \Exception("Undefined View");
        }
        
        return $this->view;
    }
    
    
     public function sendJSON($parms=[]) {
         if (Controller::$testMode) {
             return $parms;
         }
        @header("Content-type: text/json");
        echo json_encode($parms,true);
        exit;
    }
    
    
    public function setHTTPCode($code) {
        http_response_code($code);
    }
    
    
}