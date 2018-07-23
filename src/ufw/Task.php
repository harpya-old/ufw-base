<?php
namespace harpya\ufw;

use \harpya\ufw\Utils;

/**
 * @author Eduardo Luz <eduluz@harpya.net>
 * @package ufw
 */
abstract class Task {
    
    const TASK_REQUEST_HTTP = 'request_http';
    const TASK_LOCAL = 'local';
    
    
    protected $type;    
    protected $dumpOutput;
    
    public function getType() {
        return $this->type;
    }
    
    public function getDumpOutput() {
        return $this->dumpOutput;
    }
    
    /**
     * 
     * @param type $type
     * @param type $props
     * @return \harpya\ufw\task\TaskRequestHTTP
     * @throws \Exception
     */
    public static function of($type, $props=[]) {
        switch ($type) {
            case self::TASK_REQUEST_HTTP: 
                $obj = new task\TaskRequestHTTP($props);
                break;
            case self::TASK_LOCAL: 
                $obj = new task\TaskLocal($props);
                break;
            default:
                throw new \Exception("Invalid Task type ($type)", 9);
        }
        return $obj;
    }
    
    
    public static function resolve($props=[]) {
        
        if (Utils::get('type', $props)) {
            return self::of($props['type'], $props);
        } else {
            return self::of(self::TASK_LOCAL, $props);
        }
        
    } 
    
    
    
    public static function fromArray($tasks=[]) {
        if (!is_array($tasks) || empty($tasks)) {
            return [];
        }
        
        $response = [];
        foreach ($tasks as $task) {
            $response[] = Task::resolve($task);
        }
        return $response;
        
    }
    
    
    
    public function __construct($props=[]) {
        $this->bind($props);
    }
    
    public function bind($parms=[]) {
        
        if (Utils::get('output', $parms)) {
            $output = json_decode(Utils::get('output', $parms), true);
            if (is_array($output)) {
                
                $this->dumpOutput = new utils\Dump($output);
            }
        }
        
    }
    
    
    protected function dumpOutput() {
        if ($this->dumpOutput) {
            
           
            
            
        }
    }
    
    public function getAll() {        
        return get_object_vars($this);
    }


    public abstract function execute($parms=[]);
    
    
}