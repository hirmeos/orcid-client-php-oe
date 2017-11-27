<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OpenEdition\OrcidClient;

/**
 * Description of OrcidOutputXmlAdapter
 *
 * @author vinogradov
 */
class OrcidOutputXmlAdapter
{
    const NAMESPACE_WORK = "http://www.orcid.org/ns/work";
    const NAMESPACE_COMMON = "http://www.orcid.org/ns/common";
    const XSD_WORK = "/work-2.0.xsd";
    private $dom;
    
    public function __construct()
    {
        $this->dom = new \DOMDocument("1.0", "UTF-8");
        
        $this->dom->preserveWhiteSpace = false;
            
    }
    
    public function process(OrcidRecordEntity $document, $putCode = false )
    {
        
        $root = $this->appendRootWork() ;
                            
        if( $putCode !== false ){
            
            $root->setAttribute("put-code", $putCode  ); // on update
            
        }
                      
        $this->appendTitle($root, $document) ;
        
        $this->appendJournalTitle($root, $document) ;
               
        $this->appendShortDescription($root, $document) ;
                 
        $root->appendChild( $this->dom->createElementNS(self::NAMESPACE_WORK, "type", $document->getWorkType() ) );   
             
        $this->appendPublicationDate($root, $document);
        
        $this->appendExternalIds($root, $document) ;
        
        $root->appendChild(  $this->dom->createElementNS(self::NAMESPACE_WORK,"url", $document->getUrl() ) );      
        
        $this->appendContributors($root, $document );    
        
        return $this ;
    }
    
    
    public function output()
    {
        return $this->dom->saveXML();
    }
    
    private function appendRootWork()
    {
        $new = $this->dom->appendChild( $this->dom->createElementNS(self::NAMESPACE_WORK,"work:work") );
        
        $nsCommon = $this->dom->createAttributeNS(self::NAMESPACE_COMMON, "common:common");  
        
        $new->setAttributeNS("http://www.w3.org/2001/XMLSchema-instance", 
                                    "xsi:schemaLocation",
                                    "http://www.orcid.org/ns/work /work-2.0.xsd "); 
        
        return $new ;
    }
       
    
    private function appendTitle(\DOMElement $node, OrcidRecordEntity $document)
    {
        $workTitle = $node->appendChild( $this->dom->createElementNS(self::NAMESPACE_WORK,"title") );
        
        
        /*************************   COMMON:TITLE   ***************************/
        $cdata = $this->dom->createCDATASection($document->getTitle() ) ;
        
        $commonTitle = $workTitle->appendChild( $this->dom->createElementNS(self::NAMESPACE_COMMON, "title"
                                                             
                                                        )
                                            );      
        $commonTitle->appendChild($cdata) ;
        
        /*********************   COMMON:SUBTITLE   ***************************/
        try{
            $cdata = $this->dom->createCDATASection($document->getSubtitle() ) ;
            
            $commonSubtitle = $workTitle->appendChild($this->dom->createElementNS(self::NAMESPACE_COMMON,
                                                            "subtitle"                                                          
                                                        )
                                                );
            $commonSubtitle->appendChild($cdata) ;
        } 
        catch (EmptyFieldException $ex) {
        }   
    }
    
    private function appendShortDescription(\DOMElement $node, OrcidRecordEntity $document)
    {
        try{
            $cdata = $this->dom->createCDATASection($document->getShortDescription() ) ;
            
            $shortDescription = $node->appendChild( $this->dom->createElementNS(self::NAMESPACE_WORK,
                                                        "short-description"                                            
                                                        ) 
                                                    );
            
            $shortDescription->appendChild($cdata) ;
        } 
        catch (EmptyFieldException $ex) {
        }        
    }
    
    private function appendJournalTitle(\DOMElement $node, OrcidRecordEntity $document)
    {
         try{
             $cdata = $this->dom->createCDATASection($document->getParentTitle() ) ;
             
            $journalTitle = $node->appendChild( $this->dom->createElementNS(self::NAMESPACE_WORK,
                                                           "journal-title"
                                                        ) 
                                                );
            $journalTitle->appendChild( $cdata );
        } 
        catch (EmptyFieldException $ex) {
        } 
    }
    
    
    private function appendExternalIds(\DOMElement $node, OrcidRecordEntity $document)
    {
        /*********************   COMMON:EXTERNAL-IDS   ***************************/
                
        $new = $node->appendChild( $this->dom->createElementNS(self::NAMESPACE_COMMON, "external-ids" ) );
        
        $this->appendExternalId($new, "uri", $document->getUrl(), "self") ;
        
        try{
            
            $doi = $this->appendExternalId($new, "doi", $document->getDoi(), "self") ;
            
            $doi->appendChild( $this->dom->createElementNS(self::NAMESPACE_COMMON,
                                                                 "external-id-url",
                                                                  "https://doi.org/" .   $document->getDoi()
                                                                ));
            
        } catch (EmptyFieldException $ex) {

        }
        
        try{
            $relationship = "self";
            
            if ($document->getWorkType() == "book-chapter"){
                
                $relationship = "part-of";
                
            }
            $this->appendExternalId($new, "isbn",  $document->getIsbn(), $relationship) ;
            
        } catch (EmptyFieldException $ex) {

        }
        
        try{
            
            $this->appendExternalId($new, "issn",  $document->getIssn(), "part-of" ) ;
            
        } catch (EmptyFieldException $ex) {

        }
                 
    }
    
    
    private function appendPublicationDate(\DOMElement $node, OrcidRecordEntity $document)
    {
        try{
            
            $new = $this->dom->createElementNS(self::NAMESPACE_COMMON,
                                                "publication-date");
           
            $new->appendChild( $this->dom->createElementNS(self::NAMESPACE_COMMON,
                                                            "year", 
                                                             $document->getPublicationDate()) );
                       
            $node->appendChild($new);
            
        } 
        catch (EmptyFieldException $ex) {
            
        }
    }
    
    private function appendExternalId(\DOMElement $node, $type, $value, $relationship)
    {
        $new = $this->dom->createElementNS(self::NAMESPACE_COMMON,"external-id");
                    
        $new->appendChild( $this->dom->createElementNS(self::NAMESPACE_COMMON,"external-id-type", $type) );
        
        $new->appendChild( $this->dom->createElementNS(self::NAMESPACE_COMMON,
                                                       "external-id-value",
                                                       $value
                                                       ) );
                    
        $new->appendChild( $this->dom->createElementNS(self::NAMESPACE_COMMON,"external-id-relationship",$relationship) );
                    
        return $node->appendChild($new);
        
    }
    
     
     private function appendContributors(\DOMElement $node, OrcidRecordEntity $document)
     {
        
        $new = $node->appendChild( $this->dom->createElementNS(self::NAMESPACE_WORK,"contributors") );
        
         try{
            foreach($document->getAuthors() as $author){
                
                $this->appendContributor($new, $author, "author");
                
            }
        } catch (EmptyFieldException $ex) {
        } 
        
        try{
             
           foreach($document->getPrincipalInvestigators() as $author){
               
                $this->appendContributor($new, $author, "principal-investigator");
               
            } 
            
        } 
        catch (EmptyFieldException $ex) {
        }
            
     }
     
     private function appendContributor(\DOMElement $node, $name, $role)
     {
         $new = $this->dom->createElementNS(self::NAMESPACE_WORK, "contributor");
         
         $cdata = $this->dom->createCDATASection( $name ) ;
                
         $creditName = $new->appendChild( $this->dom->createElementNS(self::NAMESPACE_WORK,
                                                        "credit-name"
                                                         )
                                            );
         
         $creditName->appendChild( $cdata ) ;   
         
        $nodeAttributes = $new->appendChild( $this->dom->createElementNS( self::NAMESPACE_WORK,"contributor-attributes" ));
                
        $nodeAttributes->appendChild( $this->dom->createElementNS( self::NAMESPACE_WORK , "contributor-role", $role) );
                
        $node->appendChild( $new );
     }
}
