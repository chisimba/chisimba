<?php
/**
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for displaying a block for searching the etd repository using a simple search or link to the advanced search
* @author Megan Watson
* @copyright (c) 2006 UWC
* @version 0.2
*/

class block_etdlinks extends object
{
    /**
    * @var the block title
    */
    public $title;

    /**
    * @var bool $hideHelp Boolean value determining whether to hide the help block on the right side
    */
    private $access = 'user';

    /**
    * Constructor
    */
    public function init()
    {
        $this->dbSubmit = $this->getObject('dbsubmissions', 'etd');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->loadClass('link','htmlelements');

        $this->title = $this->objLanguage->languageText('word_links');
        $this->userId = $this->objUser->userId();
        
        $this->access = $this->getSession('accessLevel');
    }

    /**
    * Method to check if user has started submitting a document
    *
    * @access private
    * @return bool
    */
    private function checkSubmissions()
    {
        return $this->dbSubmit->getUserSubmission($this->userId);
    }

    /**
    * Method to display a search.
    *
    * @access public
    * @param string $break The break to use between radio buttons.
    */
    public function show()
    {   
        $stats = $this->objLanguage->languageText('word_statistics');
        $eshelf = $this->objLanguage->languageText('mod_etd_eshelf', 'etd');
        $faq = $this->objLanguage->languageText('word_faq');
        $submit = $this->objLanguage->languageText('phrase_newsubmission');
        $submit2 = $this->objLanguage->languageText('phrase_continuesubmission');
        $rss = $this->objLanguage->languageText('word_rss2');
        
        // Statistics page link
		$objLink = new link($this->uri(array('action' => 'viewstats')));
		$objLink->link = $stats;
		$list = '<p>'.$objLink->show().'</p>';
		
        // E-shelf link
		$objLink = new link($this->uri(array('action' => 'vieweshelf')));
		$objLink->link = $eshelf;
		$list .= '<p>'.$objLink->show().'</p>';
		
        // FAQ page link
		$objLink = new link($this->uri(array('action' => 'viewfaq')));
		$objLink->link = $faq;
		$list .= '<p>'.$objLink->show().'</p>';
		
		// Check for a current submission - only submit one document at a time.
		if(isset($this->access) && in_array('student', $this->access)){
    		$check = $this->checkSubmissions();
    		if(!($check === FALSE)){
    		    // Submission link
        		$objLink = new link($this->uri(array('action' => 'submit', 'mode' => 'showresource', 'submitId' => $check)));
        		$objLink->link = $submit2;
        		$list .= '<p>'.$objLink->show().'</p>';
    		}else{
        		// Submission link
        		$objLink = new link($this->uri(array('action' => 'submit', 'mode' => 'addsubmission')));
        		$objLink->link = $submit;
        		$list .= '<p>'.$objLink->show().'</p>';
    		}
		}
		
        // RSS link
		$this->objIcon->setIcon('rss', 'gif', 'icons/filetypes');
		$objLink = new link($this->uri(array('action' => 'showrss')));
		$objLink->link = $rss;
		$list .= '<p>'.$this->objIcon->show().' '.$objLink->show().'</p>';

        return $list;
    }
}
?>