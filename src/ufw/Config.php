<?php

namespace harpya\ufw;

class Config {
    
    protected $path;
    
    
    public static function of($path) {
        $obj = new Config();
        $obj->path = $path;
        return $obj;
    }
    
    public function getPath() {
        return $this->path;
    }
    
}
