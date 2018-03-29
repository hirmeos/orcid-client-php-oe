<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OpenEdition\OrcidClient;

/**
 * Description of OrcidInputJsonAdapter
 *
 * @author vinogradov
 */
class OrcidInputJsonAdapter
{
    protected $parsedBody ;
    protected $body ;
    
    
    public function __construct( $string )
    {
               
        $this->body = $string ;
        
        $this->parsedBody = json_decode( $this->body );
        
        if($this->parsedBody == null){
            
            throw new OrcidClientException(" failed to decode json: " .$this->body );
            
        }
    }
    
    public function getSession()
    {
        $session = new OrcidSession();
                       
        $session->__set( "access_token", $this->read("access_token") );
        
        $notMandatory = array("name","orcid","refresh_token","scope","expires_in") ;
        
        foreach($notMandatory as $parameter){
            
            try{
                
             $session->__set( $parameter, $this->read( $parameter ) );
                      
            } catch (OrcidClientException $ex) {

            }     
            
        }
             
        return $session ;
    }
    
    private function read( $element )
    {
        if(!isset($this->parsedBody->{$element}) ){
            
            throw new OrcidClientException(" no element in response: " . $element );
        }
        
        return $this->parsedBody->{$element} ;
    }   
}
