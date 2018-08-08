<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OpenEdition\OrcidClient;

/**
 * Description of OrcidRecordWork
 *
 * @author vinogradov
 * 
 * @property string $title Description
 * @property string $subTitle Description
 * @property string $journalTitle
 * @property string $shortDescription Description
 * @property string $url Description
 * @property string $type
 * @property string $doi Description
 * @property string $isbn Description
 * @property string $issn Description
 * @property string $putCode Description
 * @property array $authors Description
 * @property array $principalInvestigators Description
 * @property OrcidConfiguration $configuration
 */
class OrcidRecordWork
{
    public $putCode;
    public $title;
    public $subTitle;
    public $journalTitle;
    public $shortDescription ;
    public $url;
    public $type;
    public $doi;
    public $isbn;
    public $issn;
    public $year;
    public $authors ;
    public $principalInvestigators;
    protected $configuration ;
    
    public function __construct(OrcidConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }
    
    public function add($orcid,$token): OrcidRecordWork
    {
        $xml = $this->domDocument()->saveXml() ;
        $request = new \GuzzleHttp\Psr7\Request("POST", $this->configuration->urlRecord.$orcid."/work", 
                                                ["Accept" => "application/json", "Content-length" => strlen( $xml ),
                                                 "Content-type" => "application/vnd.orcid+xml;charset=UTF-8",
                                                 "Authorization" => " Bearer " . $token], $xml );
        $response = (new \GuzzleHttp\Client())->send($request);
        if($response->getStatusCode() != 201){            
            throw new OrcidClientException( $response->getStatusCode() . ": " . $response->getBody()->getContents()  );                
        }
        if( !$response->hasHeader("Location") ){            
            throw new OrcidClientException("No put code in response headers");             
        }                               
        $this->putCode = end( explode("/", $response->getHeaderLine("Location") ) ); //  ex.: http://api.sandbox.orcid.org/v2.0/0000-0001-8591-8766/work/786950 
        return $this ;
    }
    
    public function update($orcid,$token): OrcidRecordWork
    {
        if(empty($this->putCode)){
            throw new OrcidClientException("work can't be updated it has no put-code") ;
        }
        $xml = $this->domDocument()->saveXml() ;
        $request = new \GuzzleHttp\Psr7\Request("POST", $this->configuration->urlRecord. $orcid. "/work/" . $this->putCode, 
                                                ["Accept" => "application/json", "Content-length" => strlen( $xml ),
                                                 "Content-type" => "application/vnd.orcid+xml;charset=UTF-8",
                                                 "Authorization" => " Bearer " . $token], $xml );
        $response = (new \GuzzleHttp\Client())->send($request);
        if($response->getStatusCode() != 200){            
            throw new OrcidClientException( $response->getStatusCode() . ": " . $response->getBody()->getContents()  );                
        }
        return $this ;
    }
    
    public function delete($orcid,$token): OrcidRecordWork
    {
        if(empty($this->putCode)){
            throw new OrcidClientException("work can't be deleted it has no put-code") ;
        }
        $request = new \GuzzleHttp\Psr7\Request("DELETE", $this->configuration->urlRecord. $orcid. "/work/" . $this->putCode, 
                                                ["Accept" => "application/json", 
                                                 "Content-type" => "application/vnd.orcid+xml;charset=UTF-8",
                                                 "Authorization" => " Bearer " . $token]);
        $response = (new \GuzzleHttp\Client())->send($request);
        if($response->getStatusCode() != 204){            
            throw new OrcidClientException( $response->getStatusCode() . ": " . $response->getBody()->getContents()  );                
        }
        return $this ;
    }
    
    public function domDocument(): \DOMDocument
    {
        $dom = new \DOMDocument("1.0", "UTF-8");       
        $dom->preserveWhiteSpace = false;
        $work = $dom->appendChild( $dom->createElementNS($this->configuration->namespaceWork,"work:work") );       
        $nsCommon = $dom->createAttributeNS($this->configuration->namespaceCommon, "common:common");         
        $work->setAttributeNS("http://www.w3.org/2001/XMLSchema-instance", "xsi:schemaLocation", $this->configuration->namespaceWork . " /work-2.0.xsd "); 
        if( !empty($this->putCode)){            
            $work->setAttribute("put-code", $this->putCode  );             
        }
        $workTitle = $work->appendChild( $dom->createElementNS($this->configuration->namespaceWork, "title") );        
        $title = $workTitle->appendChild( $dom->createElementNS($this->configuration->namespaceCommon, "title") );      
        $title->appendChild( $dom->createCDATASection( $this->title ) ) ;
        if(!empty($this->subTitle)){
            $subtitle = $workTitle->appendChild($dom->createElementNS($this->configuration->namespaceCommon,"subtitle") );
            $subtitle->appendChild( $dom->createCDATASection( $this->subTitle ) ) ;
        }
        if(!empty($this->journalTitle)){
            $journalTitle = $work->appendChild( $dom->createElementNS($this->configuration->namespaceWork,"journal-title") );
            $journalTitle->appendChild( $dom->createCDATASection( $this->journalTitle ) );
        }
        if(!empty($this->shortDescription)){
            $shortDescription = $work->appendChild( $dom->createElementNS($this->configuration->namespaceWork,"short-description") );            
            $shortDescription->appendChild( $dom->createCDATASection($this->shortDescription ) ) ;
        }
         $work->appendChild( $dom->createElementNS($this->configuration->namespaceWork, "type", $this->type ) ); 
         if(!empty($this->year)){
            $publicationDate = $work->appendChild( $dom->createElementNS($this->configuration->namespaceCommon, "publication-date") );           
            $publicationDate->appendChild( $dom->createElementNS($this->configuration->namespaceCommon, "year", $this->year ) );
         }
         $externalIds = $work->appendChild( $dom->createElementNS($this->configuration->namespaceCommon, "external-ids" ) );
         $externalIds->appendChild( $this->externalIdNode($dom, "uri", $this->url, "self") );
         if(!empty($this->doi)){
             $doi = $externalIds->appendChild( $this->externalIdNode($dom, "doi", $this->doi, "self") ) ;
             $doi->appendChild( $dom->createElementNS($this->configuration->namespaceCommon, "external-id-url", "https://doi.org/" .   $this->doi) );
         }
         if(!empty($this->isbn)){
            $relationship = "self";            
            if ($this->type === "book-chapter"){               
                $relationship = "part-of" ;               
            }
            $isbn = $externalIds->appendChild( $this->externalIdNode($dom, "isbn", $this->isbn, $relationship) ) ;
         }
         if(!empty($this->issn)){
             $issn = $externalIds->appendChild( $this->externalIdNode($dom, "issn", $this->issn, "part-of") ) ;
         }
         $work->appendChild(  $dom->createElementNS($this->configuration->namespaceWork,"url", $this->url ) );   
         $contributors = $work->appendChild( $dom->createElementNS($this->configuration->namespaceWork,"contributors") );
         if(is_array($this->authors)){
             foreach($this->authors as $name){
                 $contributors->appendChild( $this->nodeContributor($dom, $name, "author") );
             }
         }
         if(is_array($this->principalInvestigators)){
             foreach($this->principalInvestigators as $name){
                 $contributors->appendChild( $this->nodeContributor($dom, $name, "principal-investigator") );
             }
         }        
         return $dom ;
    }
    
    protected function externalIdNode(\DOMDocument $dom, $type, $value, $relationship): \DOMNode
    {
        $externalId = $dom->createElementNS($this->configuration->namespaceCommon, "external-id");                    
        $externalId->appendChild( $dom->createElementNS($this->configuration->namespaceCommon,"external-id-type", $type) );        
        $externalId->appendChild( $dom->createElementNS($this->configuration->namespaceCommon, "external-id-value", $value ) );                   
        $externalId->appendChild( $dom->createElementNS($this->configuration->namespaceCommon,"external-id-relationship",$relationship) );                   
        return $externalId ;       
    }
    
    protected function nodeContributor(\DOMDocument $dom, $name, $role): \DOMNode
    {
        $contributor = $dom->createElementNS($this->configuration->namespaceWork, "contributor");                         
        $creditName = $contributor->appendChild( $dom->createElementNS($this->configuration->namespaceWork,"credit-name"));  
        $creditName->appendChild( $dom->createCDATASection( $name ) ) ;            
        $attributes = $contributor->appendChild( $dom->createElementNS( $this->configuration->namespaceWork,"contributor-attributes" ));                
        $attributes->appendChild( $dom->createElementNS( $this->configuration->namespaceWork , "contributor-role", $role) );
        return $contributor ;
    }
}
