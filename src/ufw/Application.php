<?php
namespace xbrain\ufw;

class Application {
    
    const CMP_VIEW = 'view';
    const CMP_REQUEST = 'request';
    const CMP_DB = 'db';
    const CMP_DEBUG = 'debug';
    const CMP_ROUTER = 'route';
    const CMP_HTTP = 'http';
    const CMP_CONFIG = 'config';
    
    const DEF_APPS_PATH = 'app_path';
    
    protected $lsComponents = [];
    protected $config = [];

    protected $appsPath = '../apps/';
    
    
    protected static $instance;
    

    /**
     * 
     * @param array $props
     * @return Application
     */
    public static function getInstance($props=[]) {
        if (!self::$instance) {
            self::$instance = new Application($props);
        }
        return self::$instance;
    }
    
    
    public function __construct($props=[]) {
        if (is_array($props)) {
            $this->loadProps($props);
        }
    }
    
    
    protected function loadProps($props=[]) {
        foreach ($props as $cmpID => $value) {
            $this->addProp($cmpID, $value);
        }
    }
    
    
    public function addProp($cmpID, $value) {
        switch ($cmpID) {
            case self::DEF_APPS_PATH:
                $this->appsPath = $value;
                break;
            case self::CMP_DB:
                if (!array_key_exists($cmpID, $this->lsComponents)) {
                    $this->lsComponents[$cmpID] = [];
                }
                $this->lsComponents[$cmpID][] = $value;
                break;
            case self::CMP_VIEW:
            case self::CMP_DEBUG:
            case self::CMP_ROUTER:
            case self::CMP_HTTP: 
            case self::CMP_REQUEST: 
            case self::CMP_CONFIG: 
                $this->lsComponents[$cmpID] = $value;
                break;
            default:
                // invalid component
        }        
    }
    
    
    
    public function run() {
        $result = $this->getRouter()->resolve();
        
        try {
            $response = $this->getRouter()->evaluate($result);
        } catch (\Exception $ex) {
            http_response_code($ex->getCode());
            $response = ['msg'=>$ex->getMessage(), 'code'=>$ex->getCode()];
        }

        
        if ($response) {
            $this->sendJSON($response);
        }
    }
    
    
    /**
     * 
     * @param type $key
     * @param type $index
     * @return type
     * @throws \Exception
     */
    protected function getComponent($key, $index=false) {
        if (!Utils::get($key, $this->lsComponents)) {
            throw new \Exception("Component " . $key." is not defined",1);
        }
        
        $cmp = Utils::get($key, $this->lsComponents);
        
        if ($index!==false && is_array($cmp)) {
            if (array_key_exists($index, $cmp)) {
                $cmp = $cmp[$index];
            } else {
                throw new \Exception("Component $key ($index) is not defined", 2);
            }
        }
        
        return $cmp;        
    }
    
    
    /**
     * 
     * @return Request
     * @throws \Exception
     */
    public function getRequest() {
        return $this->getComponent(self::CMP_REQUEST);
    }
    
    
  
    /**
     * 
     * @return Router
     * @throws \Exception
     */
    public function getRouter() {
        return $this->getComponent(self::CMP_ROUTER);
    }
    
    /**
     * 
     * @return Config
     */
    public function getConfig() {
        return $this->getComponent(self::CMP_CONFIG);
    }
    
    
    /**
     * 
     * @return DAO
     */
    public function getDB($index=0) {
        return $this->getComponent(self::CMP_DB, $index);
    }
    
    
    public function getApplicationsPath() {
        return $this->appsPath;
    }
    
    
    public function getApplicationsPath2() {
        return $this->appsPath;
    }
    
    
    
    
    
    
    public function init() {        
        $this->loadConfig();        
        
        $path = $this->getConfig()->getPath().'/' . $this->getApplicationsPath()  .$this->getRouter()->getApplicationName().'/routes/';
        $this->getRouter()->loadRoutes($path,$this->getRouter()->getApplicationName());
        
    }
    
    
    protected function loadConfig() {
        $path = $this->getConfig()->getPath().'/../config/routes.json';
        $this->getRouter()->loadRoutes($path);
        
        
        
        
    }
    
    
    
     public function sendJSON($parms=[]) {
        header("Content-type: text/json");
        echo json_encode($parms,true);
        exit;
    }
    
    
    
}