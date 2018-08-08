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
 * 
 * @property string $access_token Description
 * @property string $refresh_token Description
 * @property int $expires_in Description
 * @property string $scope Description
 * @property string $orcid Description
 * @property string $name Description
 */
class OrcidSession
{
    public $access_token;
    public $refresh_token;
    public $expires_in;
    public $scope;
    public $orcid;
    public $name;
    protected $configuration;
    
    public function __construct(OrcidConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }
            
    public function start(string $code, string $redirectUri): OrcidSession
    {
        $response = (new \GuzzleHttp\Client() )->post($this->configuration->urlToken, [
                                                        "query" => ["client_id" => $this->configuration->clientId,
                                                                    "client_secret" => $this->configuration->clientSecret,
                                                                    "code" => $code,
                                                                    "redirect_uri" => $redirectUri ],
                                                          "headers" => ["Accept" => "application/json"]
                                                      ]);
        if($response->getStatusCode() !== 200){
            throw new OrcidClientException( (string) $response->getBody()->getContents() );
        }        
        $json = \json_decode( $response->getBody()->getContents() );        
        if(is_null($json)){
            throw new OrcidClientException("json problem");
        }
        $this->access_token = $json->access_token;
        $this->refresh_token = $json->refresh_token ;
        $this->expires_in = $json->expires_in;
        if(isset($json->orcid)){
            $this->orcid = $json->orcid;
        }
        if(isset($json->name)){
            $this->name = $json->name ;
        }
        if(isset($json->scope)){
            $this->scope = $json->scope ;
        }
        return $this ;
    }
    
}
