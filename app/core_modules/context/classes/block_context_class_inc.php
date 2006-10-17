<?
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* A block class to produce a context chooser
*
* @author Nic Appleby
* 
* $Id$
*
*/
class block_context extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
    	try {
    		$this->objLanguage = & $this->getObject('language', 'language');
    		$this->title = ucWords($this->objLanguage->code2Txt("mod_context_contexts",'context'));
    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
    
    /**
    * Standard block show method. It uses the renderform
    * class to render the login box
    */
    public function show()
    {
    	try {
        $objContext =& $this->getObject('dbcontext', 'context');
        $courses = $objContext->getListOfPublicContext();
        if (count($courses)==0) {
            $msg = $this->objLanguage->code2Txt('mod_context_nocontexts','context');
            return "<span class='noRecordsMessage'>$msg</span>";
            
        } else {
            $form = new form('joincontext', $this->uri(array('action'=>'joincontext'), 'context'));
            $dropdown = new dropdown ('contextCode');
            foreach ($courses AS $course)
            {
                $dropdown->addOption($course['contextcode'], $course['menutext']);
            }
            $dropdown->setSelected($objContext->getContextCode());
            $button = new button ('submitform', ucwords($this->objLanguage->code2Txt('mod_context_joincontext', 'context')));
            $button->setToSubmit();
            
            $form->addToForm($dropdown->show().'<br />'.$button->show());
            
            return $form->show();
        }

    	} catch (customException $e) {
    		customException::cleanUp();
    	}
    }
}
?>