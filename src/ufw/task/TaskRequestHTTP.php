<?php
namespace harpya\ufw\task;

use \harpya\ufw\Utils;

class TaskRequestHTTP extends \harpya\ufw\Task {
    
    protected $baseURI;
    protected $method;
    protected $uri;
    protected $options;
    
    /**
     *
     * @var \GuzzleHttp\Promise\FulfilledPromise 
     */
    protected $response;
    
    
    /**
     *
     * @var type 
     */
    protected $result;
    
    
    
    
    /**
     * 
     * @return \GuzzleHttp\Promise\FulfilledPromise
     */
    public function getResponse() {
        return $this->response;
    }
    
    
    public function bind($parms=[]) {
        parent::bind($parms);
        $this->baseURI = Utils::get('base_uri', $parms);
        $this->method = Utils::get('method', $parms,'GET');
        $this->uri = Utils::get('uri', $parms,'/');
        $this->options = Utils::get('options', $parms,[]);
        
    }
    
    public function execute($parms=[]) {

        $client = new \GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => $this->baseURI
        ]);        
        
        $options = array_replace($this->options, $parms);                
        $result = $client->request($this->method, $this->uri, $options);
        $response = $result->getBody();        
        
        return $response;
    }
    
}