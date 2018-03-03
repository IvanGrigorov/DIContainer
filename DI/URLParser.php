<?php


class URLParser  {

    private $url;

    public function __construct($url) {
        $this->url = $url;
    }
    
    
    function parseUrl($url) {
        $urlQuery = explode("/", $url);
        return $urlQuery;
        
    }
    
} 

?>