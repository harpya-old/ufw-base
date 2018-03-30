<?php

namespace harpya\ufw\view;

class Smarty extends \Smarty {
    
    
    protected static $instance;
    protected $initialized;
    protected $tempTplDir;
    
    /**
     * 
     * @param string $tplDir
     * @return Smarty
     */
    public static function getInstance($tplDir="templates") {
        if (!self::$instance) {
            self::$instance = new Smarty($tplDir);            
        }
        
        return self::$instance;
    }
    
    public function init() {
        if (!$this->initialized) {
            $tplPath = \harpya\ufw\Application::getInstance()->getApplicationsPath2().
            \harpya\ufw\Utils::getInstance()->getApplicationName().'/'.$this->tempTplDir;

            $this->setTemplateDir($tplPath);
            $this->initialized = true;
        }
    }
    
    
    
    public function __construct($tplDir="templates") {
        parent::__construct();
        $this->tempTplDir = $tplDir;
//        $this->setCaching(false);
//        $this->setCompileCheck(true);
//        $this->force_compile = true;
        $this->setCompileDir("../tmp/tpl_compile");            
    }
    
}