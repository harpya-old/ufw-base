<?php
namespace harpya\ufw\task;

use \harpya\ufw\Utils;

class TaskLocal extends \harpya\ufw\Task {
    
    protected $className;
    protected $constructorParms;
    protected $method;
    protected $methodParms;
    
    public function bind($parms=[]) {
        parent::bind($parms);
        $this->className = Utils::get('class', $parms);
        $this->constructorParms = Utils::get('constructor_parms', $parms,null);
        $this->method = Utils::get('method', $parms);
        $this->methodParms = Utils::get('method_parms', $parms);
    }

    
    public function execute($parms=[]) {
        $className = $this->className; 
        $method = $this->method;
        
        $args = array_replace($this->methodParms, $parms);
        
        $object = new $className($this->constructorParms);
        try{
            $return = call_user_func_array(array($object, $method), $args);
        } catch (\Exception $ex) {
            $return = ['success'=>false,  'msg' => $ex->getMessage(), 'code' => $ex->getCode()];
        }
        
//        $response = 
        
    }
    
}