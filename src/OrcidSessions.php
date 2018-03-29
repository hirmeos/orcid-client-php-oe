<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OpenEdition\OrcidClient;

/**
 * Description of OrcidSessionRepository
 *
 * @author vinogradov
 */
class OrcidSessions
{
    
    protected $sessions = array();
         
    public function save(OrcidSession $session, $id = "")
    {
        $this->sessions[$id] = $session ;
    }
    
    public function getSession( $id )
    {
        if(isset($this->sessions[$id])){
            
            return $this->sessions[$id] ;
        }
        
        throw new OrcidClientException("no session with id : " . $id) ;
    }
}
