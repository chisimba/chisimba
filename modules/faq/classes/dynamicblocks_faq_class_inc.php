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
* The faq dynamic blocks class to render dynamic blocks
* @author Tohir Solomons
* 
*/
class dynamicblocks_faq extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objCategory = $this->getObject('dbfaqcategories');
        $this->objEntries = $this->getObject('dbfaqentries');
        
        $this->loadClass('link', 'htmlelements');
    }
    
    /**
     * Method to render a category
     * @param string $id Record Id of the Category
     * @return string Results
     */
    public function renderCategory($id)
    {
        $category = $this->objCategory->listSingleId($id);
        
        if ($category == FALSE) {
            return '';
        }
        
        $entries = $this->objEntries->getAll(" WHERE categoryid='{$id}' ORDER BY _index");
        
        if (count($entries) == 0) {
            $str =  "<div class=\"noRecordsMessage\">" . $this->objLanguage->languageText("faq_noentries","faq") . "</div>";
        } else {
            $str = '<ul>';
            
            foreach ($entries as $entry)
            {
                $link = new link ($this->uri(array('action'=>'view', 'category'=>$id)));
                $link->href .= "#".$entry['id'];
                $link->link = $entry['question'];
                
                $str .= '<li>'.$link->show().'</li>';
            }
            
            $str .= '</ul>';
        }
        
        $viewLink = new link ($this->uri(array('action'=>'view', 'category'=>$id)));
        $viewLink->link = $this->objLanguage->languageText('mod_faq_viewcategory', 'faq', 'View Category').': '.$category['categoryname'];
        
        $faqHomeLink = new link ($this->uri(NULL, 'faq'));
        $faqHomeLink->link = $this->objLanguage->languageText('mod_faq_faqhome', 'faq', 'FAQ Home');
        
        $str .= $faqHomeLink->show().' / '.$viewLink->show();
        
        return $str;
    }
}
?>