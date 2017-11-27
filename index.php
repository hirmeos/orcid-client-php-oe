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
    
    $logger = new Logger() ;
    
    $client = new OrcidClient( new OrcidConfiguration() );
          
    $sessionRepository = new OrcidSessionRepository() ;
    
    if(isset($params["token"])){
        
        $searchSession = new OrcidSession( array("access_token" => $params["token"] ) ) ;
    
        $sessionRepository->save( $searchSession, "search" ) ;
    }
    
    if(isset($params["query"])){
        
        $query = $params["query"] ;
    }
       
    try{
        
        $token = $sessionRepository->getSession("search")->getToken() ;
        
        $logger->debug("Requesting Orcid API Search q: " . $query . " token: " . $token);
    
        $Response = $client->search($token, $query);
        
        echo $Response->getBody()->getContents() ;
        
        
    } catch (ObjectNotFoundException $ex) {
        
              
        try{
            
            $logger->debug("Requesting Orcid API Get Search Token ...");
            
            $Response = $client->getSearchToken() ;
                        
            $searchSession = (new OrcidInputJsonAdapter( $Response->getBody()->getContents() ) )->getSession() ;
                          
            $sessionRepository->save($searchSession, "search" ) ;
            
            $token = $searchSession->getToken() ;
            
            $logger->debug("Requesting Orcid API Search q: " . $query . " token: " . $token);
    
            $Response = $client->search($token, $query);
            
            echo $Response->getBody()->getContents() ;
            
        } catch (UnexpectedResponseException $ex) {
            
            $logger->debug( $ex->getMessage() ) ;
        }
        catch (InvalidDataException $ex){
            
             $logger->debug( $ex->getMessage() ) ;
        }
        
    }
    catch (UnexpectedResponseException $ex) {
            
        $logger->debug( $ex->getMessage() ) ;
    }

       
