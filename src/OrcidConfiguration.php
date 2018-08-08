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
 * 
 * @property string $urlToken Description
 * @property string $urlRecord Description
 * @property string $clientId Description
 * @property string $clientSecret
 */
class OrcidConfiguration
{   
    public $urlToken;
    public $urlRecord;
    public $urlSearch;
    public $urlSearchToken;
    public $clientId;
    public $clientSecret ;
    public $namespaceWork = "http://www.orcid.org/ns/work";
    public $namespaceCommon = "http://www.orcid.org/ns/common";
    
    public function __construct($config = [])
    {
        foreach($config as $key => $value){           
            $this->{$key} = $value ;
        }
    }
    
}
