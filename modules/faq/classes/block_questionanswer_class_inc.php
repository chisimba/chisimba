<?php
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Model class for the dynamic block listing questions and answers in a category
* @author Brent van Rensburg
* @copyright 2008 University of the Western Cape
*/
class block_latestcatquestions extends object
{
    public $title;
	
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
    public function showFAQs($categoryId)
    {	
    	$contextId = $this->contextCode;
    	$lbCategory = $this->objLanguage->languageText('mod_faq_category');
        
        //$latestCat = $this->objDbFaqCategories->getLastestCategory($this->contextCode);
        $categoryRow = $this->objDbFaqCategories->getRow('id', $categoryId);
        $questions = $this->objDbFaqEntries->getAll("WHERE contextid='" . $contextId . "' AND categoryid='" .$categoryId. "' ORDER BY _index");

        $count = 1;
        $str = "";
        if(count($questions) > '0'){
                foreach($questions as $question){
                        $catQuestion = $question['question'];
                        $catAnswer = $question['answer'];

                        $str .= "<div class=\"wrapperDarkBkg\">".$count.'. '.$catQuestion."<br />"."<div class=\"wrapperLightBkg\">"."<p>".$catAnswer."</p>"."</div>"."</div>"."<br />";
                        $count++;
                }
        }
        return $str;
    }

}
?>