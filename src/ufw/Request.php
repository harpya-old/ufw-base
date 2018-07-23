<?php
namespace harpya\ufw;


class Request {
    
    const HTTP_GET = 'get';
    const HTTP_POST = 'post';
    
    
    protected $headers = [];
    protected $parameters = [];
    protected $method = 'get';
    protected $contentType;
    
    public static function of() {
        $obj = new Request();
        
        $headers = \getallheaders();        
        $obj->contentType = Utils::get('Content-Type', $headers, 'application/json');
        $obj->headers = $headers;
        $obj->parameters = $_REQUEST;
        $obj->method = Utils::get('REQUEST_METHOD', $_SERVER, self::HTTP_GET);
        $obj->loadDataRequest();
        return $obj;
    }
    
    
    protected function loadDataRequest() {
        
        $input = file_get_contents('php://input');
        
        $json = json_decode($input,true);
        $text = json_encode($json);
        
        
        
        if (is_array($json) && !empty($json) &&             
            ($json == json_decode($text))) {
            $this->parameters = array_replace($this->parameters, $json);
        }
        
    }
    
    
    public function get($key, $default=false) {
        return Utils::get($key, $this->parameters, $default);
    }
    
    public function getAll() {
        return $this->parameters;
    }
    
    public function getHeader($key, $default=false) {
        return Utils::get($key, $this->headers, $default);
    }
    
    public function update($arr) {
        $this->parameters = array_replace($this->parameters, $arr);
    }
    
    
}

