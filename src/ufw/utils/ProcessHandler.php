<?php
namespace harpya\ufw\utils;



class ProcessHandler implements 
    \ArrayAccess, 
    \Countable, 
    \IteratorAggregate {

    // Process list (array of Process objects)
    protected $processesList;

    protected $filters = [];
    
    public function __construct($load = true) {

        if ($load)
            $this->refresh();
    }

    /**
     * 
     * @param type $filters
     * @return $this
     */
    public function setFilter($filters=[]) {
        $this->filters = $filters;
        return $this;
    }
    
    
    public function getProcess($id) {
        
        if (array_key_exists($id, $this->processesList)) {
            return $this->processesList[$id];
        }

        return false;
    }

    public function getProcessByName($name) {
        $result = [];


        foreach ($this->processesList as $process) {
            if ($process->command == $name)
                $result [] = $process;
        }

        return ( $result );
    }



    public function getChildren($id) {
        $result = [];


        foreach ($this->processesList as $process) {
            if ($process->parentProcessID == $id)
                $result [] = $process;
        }

        return ( $result );
    }

    
    
    
    public function refresh() {
        $this->processesList = [];

        exec("ps -aefwwww", $output, $status);
        $count = count($output);

        for ($i = 1; $i < $count; $i ++) {
            $line = trim($output [$i]);
            $columns = preg_split("/[\s,]+/", $line);
            
            $commandLine = substr($line,48);
            
            $process = new Process($commandLine, null);

            if (preg_match('/\d+:\d+/', $columns [4]))
                $start_time = date('Y-m-d H:i:s', strtotime($columns [4]));
            else
                $start_time = date('Y-m-d', strtotime($columns [4])) . ' ??:??:??';

            $process->user = $columns [0];
            $process->processID = $columns [1];
            $process->parentProcessID = $columns [2];
            $process->startTime = $start_time;
            $process->cpuTime = $columns [5];
            $process->tty = $columns [6];

            $this->addProcess($process);

        }
    }
    
    
    protected function addProcess($process) {
        
        if (!empty($this->filters)) {
            
            $skipInclude = false;
            $processValues = get_object_vars($process);
           // print_r($processValues);
            foreach ($this->filters as $field => $value) {
                
                if (!array_key_exists($field, $processValues)) {
                    continue;
                }
                
                $v = $processValues[$field];
                
//                echo "\n $field = $v ";
                
                if ($v != $value && ( strpos($v, $value)===false) ) {
                    $skipInclude = true;
                }
            }
            
            
            if ($skipInclude) {
                return;
            }
            
        }
//        print_r($process);
        $this->processesList [$process->processID ] = $process;        
    }
    

    // Countable interface
    public function count() {
        return ( count($this->processesList) );
    }

    // IteratorAggregate interface 
    public function getIterator() {
        return ( new \ArrayIterator($this->processesList) );
    }

    // ArrayAccess interface
    public function offsetExists($offset) {
        return ( $offset >= 0 && $offset < count($this->processesList) );
    }

    public function offsetGet($offset) {
        return ( $this->processesList [$offset] );
    }

    public function offsetSet($offset, $member) {
        throw ( new \Exception("Unsupported operation.") );
    }

    public function offsetUnset($offset) {
        throw ( new \Exception("Unsupported operation.") );
    }

}


