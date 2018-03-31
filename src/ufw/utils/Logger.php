<?php
namespace harpya\ufw\utils;


trait Logger {
    
    protected $statusOutputOn=true;
    
    
    public function turnDebugOutputOff() {
        $this->statusOutputOn = false;
    }
    public function turnDebugOutputOn() {
        $this->statusOutputOn = true;
    }
    
    
    public function initLogger($props=[]) {
        
    }
    
    
    public function debug($msg, $additionalInfo=false) {
        $this->dump('debug', $msg, $additionalInfo);
    }
    
    public function info($msg, $additionalInfo=false) {
        $this->dump('info', $msg, $additionalInfo);
    }
    
    public function notice($msg, $additionalInfo=false) {
        $this->dump('notice', $msg, $additionalInfo);
    }
    
    public function warn($msg, $additionalInfo=false) {
        $this->dump('warning', $msg, $additionalInfo);
    }
    
    public function error($msg, $additionalInfo=false) {
        $this->dump('error', $msg, $additionalInfo);
    }
    
    public function notify($msg, $additionalInfo=false) {
        $this->dump('notify', $msg, $additionalInfo);
    }
    
    protected function dump($type, $msg, $additionalInfo=false) {
        
        if ($this->statusOutputOn) {
            $out = sprintf("\n%s : %-8s : %s ",date('Y-m-d H:i:s'),$type, $msg);        
            echo $out;
    //        var_dump($additionalInfo);
            if ($additionalInfo !== false) {
                echo "\n";
                echo print_r($additionalInfo,true);
                echo "\n";
            }
        }
    }
    
    
}