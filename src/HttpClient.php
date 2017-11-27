<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace OpenEdition\OrcidClient ;
 
use Psr\Http\Message\RequestInterface ;
use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Message\ServerRequestInterface ;
use GuzzleHttp\Psr7\BufferStream ;
/**
 * Description of HttpClient
 *
 * @author vinogradov
 */
class HttpClient
{
    
    private static $defaultOptions = array(CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_TIMEOUT => 120,
                                            CURLOPT_HEADER => true,
                                            CURLOPT_FOLLOWLOCATION => false);
    
    private $curlOptions = array();
    
    public function __construct( $options = array() )
    {
        $this->curlOptions = $options + self::$defaultOptions;
    }
    
    public function request(ServerRequestInterface $Request, ResponseInterface $Response )
    {
             
        $headers = array();
        
        foreach($Request->getHeaders() as $headerName => $headerArray){
            
            foreach($headerArray as $headerLine ){
                
                $headers[] = $headerName. ": ". $headerLine;
                
            }
            
        }
        
        $uri = $Request->getUri();
        
        if(count($Request->getQueryParams() ) > 0){
            
            parse_str($uri->getQuery(), $params);  
            $params = array_merge($params, $Request->getQueryParams() );
            $uri = $uri->withQuery( http_build_query( $params) ) ;
            
        }
               
        $body = $Request->getBody();
        
        $options = array(
                        CURLOPT_URL => (string) $uri,
                        CURLOPT_HTTPHEADER => $headers
                        );
        
        $options = $options + $this->curlOptions; 
        
        if(strtolower($Request->getMethod()) == "post"){
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = (string) $body;
        }
        if(strtolower($Request->getMethod()) == "put"){
            $options[CURLOPT_CUSTOMREQUEST] = "PUT";
            $options[CURLOPT_POSTFIELDS] = (string) $body;
        }
        if(strtolower($Request->getMethod()) == "delete"){
           $options[CURLOPT_CUSTOMREQUEST] = "DELETE";
        }
            
        $ch = curl_init();
        curl_setopt_array($ch, $options); 
             
        $result = curl_exec($ch);
        
        if($result === false){
            $error = "URL " . (string) $uri. " unreachable ";
            if( curl_error($ch) ){
                $error .= curl_error($ch);
            }
            curl_close($ch);
            throw new \Exception( $error );
        }
        
        $statusCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerContent = substr($result, 0, $headerSize);
        
        $responseBody = new BufferStream();     
        $responseBody->write( substr($result, $headerSize) );
        
        curl_close($ch);
        
        $Response = $Response->withStatus(  $statusCode  ) ;
               
        foreach($this->parseHeaders($headerContent) as $headerName => $headerValue ){
            
            $Response = $Response->withHeader($headerName, $headerValue );
            
        }
             
        return $Response->withBody( $responseBody );
                       
    }
    
      
    private function parseHeaders($headerContent)
    {
        $headers = array();
        
        $lineBreak = "\r\n";
        $arrRequests = explode($lineBreak.$lineBreak, $headerContent);
        
        for ($index = 0; $index < count($arrRequests); $index++) {
            foreach (explode($lineBreak, $arrRequests[$index]) as $i => $line){
                if ($i === 0){
                   
                }
            else{
                    list ($key, $value) = explode(': ', $line);
                    
                    $headers[trim($key)][] = $value;
                    
                }
            }
        }
        
        return $headers;
    }
}
