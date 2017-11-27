<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OpenEdition\OrcidClient;

/**
 * Description of OrcidRecordEntity
 *
 * @author vinogradov
 */
interface OrcidRecordEntity
{
    public function getWorkType() ;
    
    public function getUrl() ;
    
    public function getTitle() ;
    
    public function getSubtitle() ;
    
    public function getShortDescription() ;
    
    public function getParentTitle() ;
    
    public function getAuthors() ;
    
    public function getPrincipalInvestigators() ;
    
    public function getDoi() ;
    
    public function getIsbn() ;
    
    public function getIssn() ;
    
    public function getPublicationDate() ;
}
