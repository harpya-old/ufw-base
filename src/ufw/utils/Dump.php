<?php
namespace harpya\ufw\utils;


class Dump {
    
    const TYPE_FILE = 'file';
    
    protected $type;
    
    
    public function __construct($props=[]) {
        $this->bind($props);
    }
    
    
    protected function bind($props=[]) {
        $this->type = \harpya\ufw\Utils::get('type', $props, 'file');
        foreach ($props as $k => $v) {
            $this->k = $v;
        }
    }
    
    
    public function dump($data) {
        
        if (!is_scalar($data)) {
            $data = json_encode($data);
        }
        
        if ($this->type == 'file') {
            file_put_contents($this->filename??'output.out', $data);
        }
        
    }
    
    
    
    
    
    
}