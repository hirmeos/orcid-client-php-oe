<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OpenEdition\OrcidClient;

/**
 * Description of OrcidSession
 *
 * @author vinogradov
 */
class OrcidSession
{
    public function __construct($data = array()) 
    {
        foreach($data as $key => $value){
            
            $this->__set($key,$value) ;
        }
    }
    
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
    
    public function __get( $name )
    {
        if( isset($this->{$name}) ){
            
            return $this->{$name} ;
        }   
        
        throw new EmptyFieldException ("unset field " . $name ) ;
        
    }
    
    public function getToken()
    {
        return $this->__get("access_token") ;
    }
}
