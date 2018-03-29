<?php

namespace harpya\ufw;


class Router extends \AltoRouter {
    
    
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
    }
    
    
    protected function processMap($arr, $prefix='') {
        
        foreach ($arr as $uri => $options) {
            
            $s = preg_replace("/\{([\w]+)\}/", "[*:$1]", $uri);
            
            if (!empty($s)) {
                $uri = $s;
            }
            
            $uri = $prefix . $uri;
            
            foreach ($options as $method => $target) {
                if (array_key_exists('name', $target)) {
                    $this->map($method, $uri, $target, $target['name']);
                } else {
                    $this->map($method, $uri, $target);
                }
            }
        }
        
    }
    
    
    public function evaluate2() {
        $return = false;
        $match = $this->match();
//        echo $_SERVER['REQUEST_URI'];
        if (is_array($match) && array_key_exists('target', $match)) {
            
            $target = $match['target'];
 
            if (array_key_exists('controller', $target)) {
                $return = $this->processController($target, $match);               
            } elseif (array_key_exists('view', $target)) {          
                $return = $this->processView($target,$match);
            } elseif (array_key_exists('eval', $target)) {
                $return = eval($target['eval']);
            } else {
                $return = ['success'=>false, 'msg' =>  "Target undefined",'code'=>404, 'info'=> $match];           
            }
        } else {
            $return = ['success'=>false, 'msg' =>  "Target not found",'code'=>404, 'info'=> $match];
        }
        
//        if (json_decode(json_encode($return),true)===$return) {
//            $this->sendJSON($return);
//        }
        
        
        return $return;
    }
    
    
    
    public function evaluate($target) {
        
        switch ($target['type']) {
            case 'controller' :
                $return = $this->processController($target['target'], $target['match']);
                break;
            case 'view':
                $return = $this->processView($target['target'], $target['match']);
                break;
            case 'eval':
                $return = eval($target['target']['eval']);
                break;
            default:
                throw new \Exception("Undefined target",404);
        }
        return $return;
    }
    
    
    
    
    protected $applicationName;
    
    public function getApplicationName() {
        if (!$this->applicationName) {
            $matches = [];
            $uri = $_SERVER['REQUEST_URI'];
            preg_match("/^\/app\/([\w]+)\//", $uri, $matches);
            if (is_array($matches) && (count($matches)==2)) {
                $this->applicationName = $matches[1];
            } else {
                $this->applicationName = '';
//                throw new \Exception("Invalid URI",9002);
            }
            //echo "\n ".$this->applicationName . "\n";
        }
        return $this->applicationName;
    }
    
    
    
    public function resolve() {
        $return = false;
        $match = $this->match();
//        echo $_SERVER['REQUEST_URI'];
//        echo "<br>";
//        print_r($match);
//        echo "<br>";
//        print_r($this->routes);
//        echo "<br>";
        
        if (is_array($match) && array_key_exists('target', $match)) {
            
            $target = $match['target'];
            $return = ['target'=>$target, 'match'=>$match, 'application' => $this->getApplicationName()];
            
            if (array_key_exists('controller', $target)) {
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
        
//        if (json_decode(json_encode($return),true)===$return) {
//            $this->sendJSON($return);
//        }
        
        
        return $return;        
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
    
    
    
    protected function processController($target, $match) {
        $return = false;        
        $controller = $target['controller'];
        $method = $target['method'];
        
        $pathToInclude = Application::getInstance()->getApplicationsPath2().$this->getApplicationName()."/bootstrap.php";        
        
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
            $return = ['success'=>false, 'msg' =>  "Target not found ($method)",'code'=>404, 'info'=> $target];
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
    
    
    public function execute() {
        
    }
    
    
    
}
