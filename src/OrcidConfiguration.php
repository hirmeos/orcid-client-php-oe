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
    protected $config ;
      
    public function __construct($clientId = "",$clientSecret = "")
    {
        
        $this->config = array(
                            "ORCID_URL_TOKEN" => "https://pub.sandbox.orcid.org/oauth/token",
                            "ORCID_URL_RECORD" =>   "https://api.sandbox.orcid.org/v2.0/",
                            "ORCID_URL_SEARCH" => "https://pub.sandbox.orcid.org/v2.0/search",
                            "ORCID_URL_SEARCH_TOKEN" =>  "https://sandbox.orcid.org/oauth/token"
                        );
        
        if($clientId !== "" && $clientSecret !== ""){
            
            $this->setCredentials($clientId, $clientSecret) ;
            
        }
              
    }
    
    public function setCredentials($clientId, $clientSecret)
    {
        $this->config["client_id"] = $clientId ;
        $this->config["client_secret"] = $clientSecret ;
    }
    
    public function getUrlToken()
    {
        return $this->get('ORCID_URL_TOKEN') ;
    }
    
    public function getUrlRecord()
    {
        return $this->get('ORCID_URL_RECORD') ;
    }
    
    public function getClientId()
    {
        return $this->get('CLIENT_ID') ;
    }
    
    public function getClientSecret()
    {
        return $this->get('CLIENT_SECRET') ;
    }
    
    public function getUrlSearch()
    {
        return $this->get('ORCID_URL_SEARCH') ;
    }
    
    private function get( $name ){
        
        if(isset($this->config[$name]) ){
            
            return $this->config[$name] ;
            
        }
        
        if(isset($this->config[strtolower($name)]) ){
            
            return $this->config[strtolower($name)] ;
            
        }
        
        throw new \RuntimeException("Undefined orcid configuration: " . $name );
        
    }
}
