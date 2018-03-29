<?php
      
    namespace OpenEdition\OrcidClient ;
    
    require 'vendor/autoload.php';
    
   
    $params = array();
        
    foreach($argv as $arg){
        
        $argv = array_slice($argv, 1);
        
        $exploded = explode("=",$arg);
            
        if(count($exploded) == 2){
            
            $name = $exploded[0];
            $value = $exploded[1];
            $params[$name] = $value;
            
        }
            
    }
         
    $query = "orcid" ;
       
    
    $client = new OrcidClient( new OrcidConfiguration( $config ) );
          
    $sessions = new OrcidSessions() ;
    
    if(isset($params["token"])){
        
        $searchSession = new OrcidSession( array("access_token" => $params["token"] ) ) ;
    
        $sessions->save( $searchSession, "search" ) ;
    }
    
    if(isset($params["query"])){
        
        $query = $params["query"] ;
    }
       
    try{
        
        $token = $sessions->getSession("search")->getToken() ;
        
        echo("Requesting Orcid API Search q: " . $query . " token: " . $token);
    
        $Response = $client->search($token, $query);
        
        echo $Response->getBody()->getContents() ;
        
        
    } catch (OrcidClientException $ex) {
        
              
        try{
            
            echo("Requesting Orcid API Get Search Token ...");
            
            $Response = $client->getSearchToken() ;
                        
            $searchSession = (new OrcidInputJsonAdapter( $Response->getBody()->getContents() ) )->getSession() ;
                          
            $sessions->save($searchSession, "search" ) ;
            
            $token = $searchSession->getToken() ;
            
            echo("Requesting Orcid API Search q: " . $query . " token: " . $token);
    
            $Response = $client->search($token, $query);
            
            echo $Response->getBody()->getContents() ;
            
        } catch (OrcidClientException $ex) {
            
            echo( $ex->getMessage() ) ;
        }
       
        
    }
    catch (OrcidClientException $ex) {
            
        echo( $ex->getMessage() ) ;
    }
    

       
