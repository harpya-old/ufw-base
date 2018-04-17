<?php

namespace harpya\ufw;

/**
 * Description of Session
 *
 * @author eduardoluz
 */
class Session {
    
    protected static $instance;
    
    /**
     * 
     * @return Session
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Session();
        }
        return self::$instance;
    }
    
    /**
     * 
     * @return $this
     */
    public function init() {
        session_start();
        return $this;
    }
    
    
    public function get($name, $default=false) {
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
    }
    
    
    /**
     * 
     * @param type $name
     * @param type $value
     * @return $this
     */
    public function set($name,$value) {
        $_SESSION[$name] = $value;
        return $this;
    }
    
    
    
    
}