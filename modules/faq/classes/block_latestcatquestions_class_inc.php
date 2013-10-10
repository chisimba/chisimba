<?php
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Model class for the dynamic block listing questions in a category
* @author Brent van Rensburg
* @copyright 2008 University of the Western Cape
*/
class block_latestcatquestions extends object
{
	var $title;
	
    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('phrase_faq');

        $this->objDbFaqCategories =& $this->getObject('dbfaqcategories');
        $this->objDbFaqEntries =& $this->getObject('dbFaqEntries');
        $this->objDbContext = &$this->getObject('dbcontext', 'context');

        $this->contextCode = $this->objDbContext->getContextCode();
        // If we are not in a context...

        if ($this->contextCode == null) {
            $this->contextCode = 'root';
        }
        
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
    }

    /**
    * Method to show the table of the list of questions of a specified category
    */
    public function showQuestions($categoryId)
    {	
    	$contextId = $this->contextCode;
    	$lbCategory = $this->objLanguage->languageText('mod_faq_category');
        
        //$latestCat = $this->objDbFaqCategories->getLastestCategory($this->contextCode);
        $categoryRow = $this->objDbFaqCategories->getRow('id', $categoryId);
        $questions = $this->objDbFaqEntries->getAll("WHERE contextid='" . $contextId . "' AND categoryid='" .$categoryId. "' ORDER BY _index");
		
        $form = new form('category questions', $this->uri(''));
        $form->setDisplayType(3);
        
        $objlatestCatTable = new htmlTable('latest category');
		$objlatestCatTable->cellspacing = 2;
		$objlatestCatTable->width = '40%';
		
		$objlatestCatTable->startRow();
		$objlatestCatTable->addCell($lbCategory.': ');
		$objlatestCatTable->addCell($categoryRow['categoryname']);
		$objlatestCatTable->endRow();
		
		$objlatestCatTable->startRow();
		$objlatestCatTable->addCell("<br />");
		$objlatestCatTable->addCell("<br />");
		$objlatestCatTable->endRow();
		
		$objlatestQuesTable = new htmlTable('latest questions');
		$objlatestQuesTable->cellspacing = 2;
		$objlatestQuesTable->width = '40%';
		
		$count = 1;
		
		if(count($questions) > '0'){
  			foreach($questions as $question){
  				$catQuestion = $question['question'];
			
	  			$objlatestQuesTable->startRow();
				$objlatestQuesTable->addCell($count.'. '.$catQuestion);
				$objlatestQuesTable->endRow();
				
				$count++;
  			}
		} else {
			$objlatestQuesTable->startRow();
			$objlatestQuesTable->addCell("<i>".$this->objLanguage->languageText('faq_noentries')."</i>");
			$objlatestQuesTable->endRow();
		}
		
		$form->addToForm($objlatestCatTable->show());
		$form->addToForm($objlatestQuesTable->show());
		
		return $form->show();
    }

}
?>
