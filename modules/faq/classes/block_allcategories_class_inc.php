<?php
/**
* @package faq
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* The faq block class displays a block with a list of categories.
* @author Brent van Rensburg
*/

class block_faq extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_faq_name');

        $this->objDbFaqCategories =& $this->getObject('dbfaqcategories');
        $this->objDbContext = &$this->getObject('dbcontext', 'context');

        $this->contextCode = $this->objDbContext->getContextCode();
        // If we are not in a context...
//
        if ($this->contextCode == null) {
            $this->contextCode = 'root';
        }

        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
    }

    /**
    * Method to show the form for selecting a category
    */
    public function showForm()
    {
        $contextId = $this->contextCode;
       // $lbAllCats = $this->objLanguage->languageText('mod_faq_allcategories');

        $categories = $this->objDbFaqCategories->getAll("WHERE contextid='" . $contextId . "' ORDER BY categoryname");
		
		$objAllCatsTable = new htmlTable('categories');
		$objAllCatsTable->cellspacing = 2;
		$objAllCatsTable->width = '40%';
		
		/*$objAllCatsTable->startRow();
		$objAllCatsTable->addCell("<i>".$lbAllCats."</i>");
		$objAllCatsTable->endRow();
		
		$objAllCatsTable->startRow();
		$objAllCatsTable->addCell("<br />");
		$objAllCatsTable->endRow();*/
		
		$count = 1;
		
		if(count($categories) > '0'){
  			foreach($categories as $category){
  				$categoryName = $category['categoryname'];
			
	  			$objAllCatsTable->startRow();
				$objAllCatsTable->addCell($count.'. '.$categoryName);
				$objAllCatsTable->endRow();
				
				$count++;
  			}
		}

        return $objAllCatsTable->show();
    }
}
?>