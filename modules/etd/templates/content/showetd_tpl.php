<?php
/**
* @package etd
*/

/**
* Template for the front page of the etd module.
* @param array $etd The etd data to be displayed.
* @param array $files The list files attached to the etd.
*/

$this->setLayoutTemplate('etd_layout_tpl.php');

// set up html elements
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objHead = $this->newObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('tabbedbox', 'htmlelements');

// set up language items
$lbAbstract = $this->objLanguage->languageText('word_abstract');
$lbAudience = $this->objLanguage->languageText('word_audience');
$lbContributor = $this->objLanguage->languageText('word_contributor');
$lbCountry = $this->objLanguage->languageText('word_country');
$lbFaculty = $this->objLanguage->languageText('word_faculty');
$lbDegree = $this->objLanguage->languageText('phrase_degreeobtained');
$lbLevel = $this->objLanguage->languageText('phrase_degreelevel');
$lbGrantor = $this->objLanguage->languageText('word_grantor');
$lbDepartment = $this->objLanguage->languageText('word_department');
$lbInstitution = $this->objLanguage->languageText('word_institution');
$lbFormat = $this->objLanguage->languageText('word_format');
$lbGrantor = $this->objLanguage->languageText('word_grantor');
$lbKeywords = $this->objLanguage->languageText('word_keywords');
$lbLanguage = $this->objLanguage->languageText('word_language');
$lbPublisher = $this->objLanguage->languageText('word_publisher');
$lbRelationship = $this->objLanguage->languageText('word_relationship');
$lbRights = $this->objLanguage->languageText('word_rights');
$lbSource = $this->objLanguage->languageText('word_source');
$lbDocType = $this->objLanguage->languageText('phrase_documenttype');
$lbYear = $this->objLanguage->languageText('word_year');
$lbFulltext = $this->objLanguage->languageText('mod_etd_downloadfulltext', 'etd');
$lbCitation = $this->objLanguage->languageText('phrase_citationlist');
$lbEmbargo = $this->objLanguage->languageText('mod_etd_fulltextembargoeduntil', 'etd');

$resourceStr = ''; $listStr = ''; $tableStr = ''; $downloadStr = ''; $altDownloadStr = '';
if(!empty($resource)){
    
    // Add the meta tags to the <head>
    if(isset($metaTags) && !empty($metaTags)){
        $this->appendArrayVar('headerParams', $metaTags);
    }
    
    // Set the page title
    $this->setVar('pageTitle', $resource['dc_title']);
    
    // Create the heading using the title and author
    $objHead->str = $resource['dc_title'];
    $objHead->type = 2;
    $headStr = $objHead->show();
    
    $headStr .= '<p><u>'.$resource['dc_creator'].'</u></p>';
    
    // Display abstract
    $resourceStr .= '<p><b>'.$lbAbstract.':</b></p>';
    $resourceStr .= '<p style="padding-left: 10px;">'.$resource['dc_description'].'</p>';
    $resourceStr .= '<hr />';
    
    // Check the embargo date against the current date
    $isEmbargoed = FALSE;
    if(!empty($resource['embargo'])){
        $now = time(); $end = time();
        $emDate = $resource['embargo'];
        $params = explode('-', $emDate);
        if(!empty($params)){
            $end = mktime(0,0,0, $params[1], $params[2], $params[0]);
        }
        if($end > $now){
            $isEmbargoed = TRUE;
        }
    }
    
    // Download the full text - if not under embargo
    if(!empty($resource['embargo']) && $isEmbargoed){
        $objDate = $this->getObject('dateandtime', 'utilities');
        $embargo = $objDate->formatDate($resource['embargo']);
        
        $downloadStr = $lbEmbargo.'&nbsp;'.$embargo.'.<hr />';
    }else{
        $fileData = $this->etdFiles->getFile($resource['submitid']);
        if(!empty($fileData)){
            $url = $fileData[0]['filepath'].$fileData[0]['storedname'];
            $objIcon->setIcon('fulltext');
            $objIcon->title = $lbFulltext;
            $objLink = new link($url);
            $objLink->extra = 'onclick="javascript:var url = \'index.php\';var pars = \'module=etd&amp;action=registerdownload\';var regDownload = new Ajax.Request(url, {method: \'post\', parameters: pars});"';            
            $objLink->link = $objIcon->show();
            
            $downloadStr = '<p><b>'.$lbFulltext.':</b> &nbsp; '.$objLink->show().'</p>';
            $downloadStr .= '<hr />';
            
            $altDownloadStr = '<p><b>'.$lbFulltext.':</b> &nbsp; '.$this->objConfig->getsiteRoot().$url.'</p>';
            $altDownloadStr .= '<hr />';
        }
    }
        
    $objTable = new htmltable();
    $objTable->cellpadding = '5';

    // faculty
    if(!empty($resource['thesis_degree_faculty'])){
        $objTable->addRow(array('<b>'.$lbFaculty.':</b>', $resource['thesis_degree_faculty']));
    }
    
    // department
    if(!empty($resource['thesis_degree_discipline'])){
        $objTable->addRow(array('<b>'.$lbDepartment.':</b>', $resource['thesis_degree_discipline']));
    }

    // Degree name
    if(!empty($resource['thesis_degree_name'])){
        $objTable->addRow(array('<b>'.$lbDegree.':</b>', $resource['thesis_degree_name']));
    }
       
    // Institution / grantor
    if(!empty($resource['thesis_degree_grantor'])){
        $objTable->addRow(array('<b>'.$lbInstitution.':</b>', $resource['thesis_degree_grantor']));
    }
    
    // Year/date
    if(!empty($resource['dc_date'])){
        $objTable->startRow();
        $objTable->addCell('<b>'.$lbYear.':</b>', '20%');
        $objTable->addCell($resource['dc_date']);
        $objTable->endRow();
    }
    
    $objTable->addRow(array('<hr />', '<hr />'));
       
    // Type
    if(!empty($resource['dc_type'])){
        $objTable->addRow(array('<b>'.$lbDocType.':</b>', $resource['dc_type']));
    }

    // Format
    if(!empty($resource['dc_format'])){
        $objTable->addRow(array('<b>'.$lbFormat.':</b>', $resource['dc_format']));
    }

    // Country
    if(!empty($resource['dc_coverage'])){
        $objTable->addRow(array('<b>'.$lbCountry.':</b>', $this->objLangCode->getName($resource['dc_coverage'])));
    }
    
    // Keywords
    if(!empty($resource['dc_subject'])){
        $objTable->addRow(array('<b>'.$lbKeywords.':</b>', $resource['dc_subject']));
    }
    
    $objTable->addRow(array('<hr />', '<hr />'));
    
    // Contributor
    if(!empty($resource['dc_contributor'])){
        $objTable->addRow(array('<b>'.$lbContributor.':</b>', $resource['dc_contributor']));
    }
    
    // Relationship
    if(!empty($resource['dc_relationship'])){
        $objTable->addRow(array('<b>'.$lbRelationship.':</b>', $resource['dc_relationship']));
    }
    
    // Rights
    if(!empty($resource['dc_rights'])){
        $objTable->addRow(array('<b>'.$lbRights.':</b>', $resource['dc_rights']));
    }
    
    // Publisher
    if(!empty($resource['dc_publisher'])){
        $objTable->addRow(array('<b>'.$lbPublisher.':</b>', $resource['dc_publisher']));
    }
       
    // Source
    if(!empty($resource['dc_source'])){
        $objTable->addRow(array('<b>'.$lbSource.':</b>', $resource['dc_source']));
    }

    // Language
    if(!empty($resource['dc_language'])){
        $objTable->addRow(array('<b>'.$lbLanguage.':</b>', $resource['dc_language']));
    }
    
    // Audience
    if(!empty($resource['dc_audience'])){
        $objTable->addRow(array('<b>'.$lbAudience.':</b>', $resource['dc_audience']));
    }
    
    $objTable->addRow(array('<hr />', '<hr />'));

    $tableStr = $objTable->show();
    
    if(isset($citationList) && !empty($citationList)){
        $listStr = '<p><b>'.$lbCitation.'</b></p>';
        $listStr .= $citationList['citation_list'];
    }
}

// Set session for printing / emailing resource
$this->setSession('resource', $headStr.$resourceStr.$altDownloadStr.$tableStr);

$str = $objFeatureBox->showContent('<center>'.$headStr.'</center>', $resourceStr.$downloadStr.$tableStr.$listStr);

echo $str.'<br />';
?>