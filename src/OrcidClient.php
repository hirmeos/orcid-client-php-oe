<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OpenEdition\OrcidClient;

use GuzzleHttp\Psr7\Response ;
use GuzzleHttp\Psr7\Uri ;
use Psr\Http\Message\ServerRequestInterface ;
use GuzzleHttp\Psr7\BufferStream as Stream ;


/**
 * Description of OrcidClient
 *
 * @author vinogradov
 */
class OrcidClient
{

    protected $configuration ;
  
    public function __construct(OrcidConfiguration $configuration)
    {
      
        $this->configuration = $configuration ;
    }
    
    public function request(ServerRequestInterface $Request)
    {
       
        $Response = (new HttpClient() )->request( $Request,
                                                 new Response()
                                                );
                     
        return $Response ;
        
    }
    
    
    public function updateWork($orcid, $token, $putCode, $xml)
    {
        $stream = new Stream();
        $stream->write( $xml );
        
        $Response = $this->request( (New OrcidRequest() )->withMethod("put")
                                                ->withHeader( "Accept", "application/json" )
                                                ->withHeader("Content-length", strlen( $xml ) )
                                                ->withHeader( "Content-type", "application/vnd.orcid+xml;charset=UTF-8" )
                                                ->withUri(new Uri( $this->configuration->getUrlRecord() . $orcid ."/work/" . $putCode  ) )                                                    
                                                ->withBody( $stream )
                                                ->withHeader( "Authorization", " Bearer ".$token )
                                    );
        
        if($Response->getStatusCode() != StatusCode::OK){
            
            throw new OrcidClientException( $Response->getBody()->getContents(), $Response->getStatusCode() );
                
        }
        
        return $Response ;
    }
    
    public function addWork($orcid, $token, $xml)
    {
        $stream = new Stream();
        $stream->write( $xml );
        
        $Response = $this->request( (New OrcidRequest() )->withMethod("post")
                                                ->withHeader( "Accept", "application/json" )
                                                ->withHeader("Content-length", strlen( $xml ) )
                                                ->withHeader( "Content-type", "application/vnd.orcid+xml;charset=UTF-8" )
                                                ->withUri(new Uri( $this->configuration->getUrlRecord() . $orcid . "/work") )
                                                ->withBody( $stream )
                                                ->withHeader( "Authorization", " Bearer ".$token )
                                    );
        if($Response->getStatusCode() != StatusCode::CREATED){
            
            throw new OrcidClientException( $Response->getBody()->getContents(), $Response->getStatusCode() );
                
        }
        
        return $Response ;
    }
    
    public function exchangeCode($code, $redirectUri)
    {
        $Response = $this->request( (New OrcidRequest() )->withMethod("post")
                                                ->withUri( new Uri( $this->configuration->getUrlToken()  ) )
                                                ->withHeader( "Accept", "application/json" )
                                                ->withQueryParams(array("grant_type" => "authorization_code",
                                                                        "code" => $code,
                                                                        "redirect_uri" => $redirectUri,
                                                                        "client_id" => $this->configuration->getClientId(),
                                                                        "client_secret" => $this->configuration->getClientSecret()
                                                                        )
                                                                    )
                                 );
        if($Response->getStatusCode() != StatusCode::OK){
            
            throw new OrcidClientException( $Response->getBody()->getContents(), $Response->getStatusCode() );
                
        }
        
        return $Response ;
    }
    
    
    public function deleteWork($orcid, $token, $putCode )
    {
        $Response = $this->request( (New OrcidRequest() )->withUri( new Uri( $this->configuration->getUrlRecord()  . $orcid ."/work/" . $putCode  ) ) 
                                                ->withMethod("delete")
                                                ->withHeader( "Accept", "application/json" )
                                                ->withHeader( "Content-type", "application/vnd.orcid+xml;charset=UTF-8" )
                                                ->withHeader( "Authorization", " Bearer ".$token )
                             );
        
        if($Response->getStatusCode() != StatusCode::NO_CONTENT && $Response->getStatusCode() != StatusCode::NOT_FOUND ){
            
            throw new OrcidClientException( $Response->getBody()->getContents(), $Response->getStatusCode() );
                
        }
        
        return $Response ;
    }
    
    public function search($token, $query)
    {
        $Response = $this->request( (New OrcidRequest() )->withUri( new Uri( $this->configuration->getUrlSearch() ) )
                                                    ->withQueryParams(array("q" => $query) )
                                                    ->withHeader( "Authorization", " Bearer ".$token )
                                                    ->withHeader( "Content-type", "application/vnd.orcid+xml;charset=UTF-8" )
                                                    ->withMethod("get") 
                                    );
        
        if($Response->getStatusCode() != StatusCode::OK){
            
            throw new OrcidClientException( $Response->getBody()->getContents(), $Response->getStatusCode() );
                
        }
        
        return $Response ;
    }
    
    public function getSearchToken()
    {
        $Response = $this->request( (New OrcidRequest() )->withMethod("post")
                                                ->withUri( new Uri( $this->configuration->getUrlToken()  ) )
                                                ->withHeader( "Accept", "application/json" )
                                                ->withQueryParams(array("grant_type" => "client_credentials",
                                                                        "scope" => "/read-public" ,
                                                                        "client_id" => $this->configuration->getClientId(),
                                                                        "client_secret" => $this->configuration->getClientSecret()
                                                                        )
                                                                    )
                                 );
        if($Response->getStatusCode() != StatusCode::OK){
            
            throw new OrcidClientException( $Response->getBody()->getContents(), $Response->getStatusCode() );
                
        }
        
        return $Response ;
    }
}
