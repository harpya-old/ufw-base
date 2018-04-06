<?php
namespace harpya\ufw;

trait PluginManager {
    
    protected static $pluginList = [];
    protected static $pluginMap = [];
    
    public function loadPluginList() {        
        
        if (!empty(self::$pluginList)) {
            return;
        }
        
        try {
            self::$pluginList = [];
            $path = $this->getConfigPath()."/plugins.json";
            $json = Utils::loadJSON($path);
            
            foreach ($json as $pluginName => $pluginData) {
                
                if (!Utils::get('enabled', $pluginData, true)) {
                    continue;
                }
                
                $this->addPlugin($pluginName, $pluginData);
            }
        } catch (\Exception $ex) {
            //throw $ex;
        }
        
        
    }
    
    
    
    public function addPlugin($name, $props=[]) {
        
        
        
        if (Utils::get('routes', $props)) {            
            $this->addRoutes($name, $props['routes']);
            unset($props['routes']);
        }
        
        $pluginData = $props;
        $pluginData['path'] = Utils::get('path', $props, $name);
        
        $this->loadPluginRoutes($name, $pluginData['path']);
        
        
        self::$pluginList[$name] = $pluginData;


    }
    
    public function loadPluginRoutes($name, $path) {
        $pathRoutes = getcwd() .'/../plugins/'.$path.'/routes';
        
        if (file_exists($pathRoutes) && is_readable($pathRoutes)) {
            
            $d = dir($pathRoutes);
            while (false !== ($entry = $d->read())) {

                if (strtolower(substr($entry,-4)) !== 'json') {
                    continue;
                }    

                $json = Utils::loadJSON("$pathRoutes/$entry");
                if (Utils::get('routes', $json)) {
                    $this->addRoutes($name, $json['routes']);
                }

            }
            $d->close();
            
        }
    }
    
    
    
    /**
     * 
     * @param string $pluginName
     * @param array $routes
     */
    protected function addRoutes($pluginName, $routes) {
        
        foreach ($routes as $uri => $targets) {
            foreach ($targets as $method => $target) {
                $newURI = str_replace("//", "/", "/plugin/$pluginName/$uri");
                $uid = 'plugin:'.md5(microtime(true) . rand(0, 9999999));
                self::$pluginMap[$uid] = $pluginName;
                $this->getRouter()->map($method,$newURI, $target,$uid);
            }
        }

    }
    
    public function getPluginByName($name) {
        return Utils::get($name, self::$pluginList);
    }
    
    
    public function getPluginByUID($uid) {
        $name = Utils::get($uid, self::$pluginMap); 
        return $this->getPluginByName($name);
    }
    
    public function preparePlugin($uid) {
        $pluginData = $this->getPluginByUID($uid);
        $path = getcwd() .'/../plugins/'.$pluginData['path'].'/bootstrap.php';
        
        if (file_exists($path)) {
            include_once($path);
        } else {
            $this->warn("Plugin bootstrap file not found ($path)");
        }
        
    }
    
    
    
}