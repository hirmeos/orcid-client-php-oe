<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OpenEdition\OrcidClient;

/**
 * Description of OrcidConfiguration
 *
 * @author vinogradov
 */
class OrcidConfiguration
{
    protected $config = [] ;
    
    const URL_TOKEN = "urlToken" ;
    const URL_RECORD = "urlRecord" ;
    const URL_SEARCH = "urlSearch" ;
    const URL_SEARCH_TOKEN = "urlSearchToken" ;
    const CLIENT_ID = "clientId" ;
    const CLIENT_SECRET = "clientSecret" ;
      
    public function __construct(array $config)
    {
        
        $this->config = $config ;
              
    }
    
    public function setCredentials($clientId, $clientSecret)
    {
        $this->config[self::CLIENT_ID] = $clientId ;
        $this->config[self::CLIENT_SECRET] = $clientSecret ;
    }
    
    public function getUrlToken()
    {
        return $this->get(self::URL_TOKEN) ;
    }
    
    public function getUrlRecord()
    {
        return $this->get(self::URL_RECORD) ;
    }
    
    public function getClientId()
    {
        return $this->get(self::CLIENT_ID) ;
    }
    
    public function getClientSecret()
    {
        return $this->get(self::CLIENT_SECRET) ;
    }
    
    public function getUrlSearch()
    {
        return $this->get(self::URL_SEARCH) ;
    }
    
    private function get( $name ){
        
        if(isset($this->config[$name]) ){
            
            return $this->config[$name] ;
            
        }
        
        if(isset($this->config[strtolower($name)]) ){
            
            return $this->config[strtolower($name)] ;
            
        }
        
        throw new OrcidClientException("Undefined orcid configuration: " . $name );
        
    }
}
