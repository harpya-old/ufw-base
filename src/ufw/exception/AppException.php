<?php

namespace harpya\ufw\exception;


class AppException extends \Exception {
    
    protected $httpCode=false;
    
    /**
     * 
     * @param string $message
     * @param int $code
     * @param int $httpCode
     * @param \Throwable $previous
     */
    public function __construct(string $message = "", int $code = 0, $httpCode=false, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        if ($httpCode) {
            $this->httpCode = $httpCode;
        }
    }
    
    /**
     * 
     * @return int
     */
    public function getHttpCode() {
        return $this->httpCode;
    }
    
    
}