<?php

namespace harpya\ufw;


class Router extends \AltoRouter {
    
    protected $applicationName;
    protected $defaults = [];
    
    
    public function loadRoutes($path=false, $application='') {
        
        if (!file_exists($path)) {
            return;
        } elseif (is_dir($path)) {
            $d = dir($path);
            while (false !== ($entry = $d->read())) {

                if (strtolower(substr($entry,-4)) == 'json') {
                    $this->loadRouteFile($path.'/'.$entry, $application);
                }

            }
            $d->close();
        } else {
            $this->loadRouteFile($path);
        }
    }
    
    
    
    
    public function loadRouteFile($path, $application='') {
        $txt = file_get_contents($path);
        
        $json = json_decode($txt, true);
        
        if (!$json) {
            Application::getInstance()->warn("Invalid route JSON file", ['contents'=>$txt, 'path'=>$path, 'application' => $application]);
            return;
        }
        
        if (empty($application)) {
            $prefix = '';
        } else {
            $prefix = '/app/'.$application;
        }

        if (array_key_exists('prefix', $json)) {
            $prefix .= '/'. $json['prefix'];
        }

        if (array_key_exists('routes', $json)) {
            $this->processMap($json['routes'],$prefix);
        }
        
        if (array_key_exists('default', $json)) {
            $this->defaults[$application] = $json['default'];
        }        
    }
    
    
    /**
     * For each array item, pre process and add to global mapping
     * 
     * @param array $arr
     * @param string $prefix
     */
    protected function processMap($arr, $prefix='') {
        
        foreach ($arr as $uri => $options) {
            
            $uri = $prefix . $this->preProcessURI($uri);
            
            foreach ($options as $method => $target) {
                if (array_key_exists('name', $target)) {
                    $this->map($method, $uri, $target, $target['name']);
                } else {
                    $this->map($method, $uri, $target);
                }
            }
        }
        
    }
    
    /**
     * Replace the macros by regular expressions to extract arguments from URL
     * @param string $uri
     * @return string
     */
    protected function preProcessURI($uri) {
        $s = preg_replace("/\{([\w]+)\}/", "[*:$1]", $uri);
            
        if (!empty($s)) {
            $uri = $s;
        }
        
        return $uri;
    }
    
    
    
    
    
    /**
     * Determine which type of target is, and execute it.
     *  
     * @param array $target
     * @return mixed
     * @throws \Exception
     */
    public function evaluate($target) {
        
        
        switch (Utils::get('type', $target)) {             
            case 'class' :
                $return = $this->processClass(Utils::get('target',$target), Utils::get('match', $target));
                break;
            case 'controller' :
                $return = $this->processController(Utils::get('target', $target), Utils::get('match', $target));
                break;
            case 'view':
                $return = $this->processView(Utils::get('target', $target), Utils::get('match', $target));
                break;
            case 'eval':
                $return = eval($target['target']['eval']);
                break;
            default:
                throw new \Exception("Undefined target",404);
        }
        return $return;
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
     * Perform the match among the URI and the routes available
     * @return mixed
     */
    public function resolve() {
        $return = false;
        $match = $this->match();

        if (!$match) {            
            $target = $this->getDefaultRoute();
            if ($target) {
                $match = ['target' => $target];
            }                            
        }
        
        if (substr($match['name'],0,7) =='plugin:') {
            Application::getInstance()->preparePlugin($match['name']);
        }
        
        
        if (is_array($match) && array_key_exists('target', $match)) {
            
            $target = $match['target'];
            
            $return = ['target'=>$target, 'match'=>$match, 'application' => $this->getApplicationName()];
            
            if (array_key_exists('class', $target)) {
                $return['type'] = 'class';
            } elseif (array_key_exists('controller', $target)) {
                $return['type'] = 'controller';
            } elseif (array_key_exists('view', $target)) {
                $return['type'] = 'view';
            } elseif (array_key_exists('eval', $target)) {
                $return['type'] = 'eval';
            } else { 
                $return = ['success'=>false, 'msg' =>  "Target undefined",'code'=>404, 'info'=> $match];           
            }
        } else {
            $return = ['success'=>false, 'msg' =>  "Target not found",'code'=>404, 'info'=> $match];
        }
        
        return $return;        
    }
    
    
    protected function getDefaultRoute() {
        if (utils::get($this->getApplicationName(), $this->defaults)) {
            return utils::get($this->getApplicationName(), $this->defaults);
        }
    }
    
    
    protected function processRequest($params=[]) {
        if (!is_array($params)) {
            $params = [];
        }
        $request = array_merge($_REQUEST??[], ($params??[]));
        
        $headers = getallheaders();
        if (is_array($headers) 
                && array_key_exists('Content-Type', $headers) 
                && (in_array($headers['Content-Type'], ['application/json','text/json'])
                        || strpos('/json',$headers['Content-Type'])>0)
                        ) {
            $input = file_get_contents('php://input');
            $json = json_decode($input, true);
            if (is_array($json)) {
                $request = array_replace($request, $json);
            }
        }        
        return $request;
    }
    
    
    
    protected function processClass($target, $match) {
        $return = false;        
        $class = $target['class'];
        $method = $target['method'];
        $app = $this->determineAppName($target);
        
        $pathToInclude = Application::getInstance()->getApplicationsPath2()."$app/bootstrap.php";        

        if (file_exists($pathToInclude)) {
            include $pathToInclude;
        } else {
            throw new \Exception("Bootstrap file not found", 405);
        }
        
        if (($class != null) && (is_callable(array($class, $method)))) {
            $params = $this->processRequest($match['params']);
            $object = new $class($params);
            Controller::getInstance($object);                
            try{
                $return = call_user_func_array(array($object, $method), [$params]);
            } catch (\Exception $ex) {
                $return = ['success'=>false,  'msg' => $ex->getMessage(), 'code' => $ex->getCode()];
            }

        } else {            
            $return = ['success'=>false, 'msg' =>  "Target not found ($method)",'code'=>4041, 'info'=> $target];
        }
        return $return;
    }

    protected function determineAppName($target) {
        $app = Utils::get('app', $target);
        
        if ($app) {
            \harpya\ufw\Utils::getInstance()->setApplicationName($app);
        } else {
            $app = $this->getApplicationName();
        }
        return $app;
    }

    
    protected function processController($target, $match) {
        $return = false;        
        $controller = $target['controller'];
        $method = $target['method'];
        $app = $this->determineAppName($target);
        
        $pathToInclude = Application::getInstance()->getApplicationsPath2()."$app/bootstrap.php";        

        if (file_exists($pathToInclude)) {
            include $pathToInclude;
        } else {
            throw new \Exception("Bootstrap file not found", 405);
        }
        
        
        if (($controller != null) && (is_callable(array($controller, $method)))) {
            $params = $this->processRequest($match['params']);
            $object = new $controller($params);
            Controller::getInstance($object);                
            try{
                $return = call_user_func_array(array($object, $method), [$params]);
            } catch (\Exception $ex) {
                $return = ['success'=>false,  'msg' => $ex->getMessage(), 'code' => $ex->getCode()];
            }

        } else {            
            $return = ['success'=>false, 'msg' =>  "Target not found ($method)",'code'=>4042, 'info'=> $target];
        }
        return $return;
    }
    
    
    protected function processView($target,$match=false, $display=true) {
        $controller = $target['view']??false;
        $object = new $controller();
        if ($display) {
            $object->getTpl()->display($target['template']);
            $return = ['success' => 'true', 'template' => $target['template']];
        } else {
            $return = $object->getTpl()->fetch($target['template']);
        }
        return $return;
    }
    
    
    public function getAllRoutes() {
        return $this->routes;
    }
    
}
