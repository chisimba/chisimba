<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class to display a block for the wiki
*
* @author Kevin Cyster
*/
class block_wiki extends object
{
    /*
    * @var object $objLanguage: The language class in the language module
    * @access private
    */
    private $objLanguage;

    /*
    * @var string $title: The title of the block
    * @access public
    */
    public $title;

    /**
    * Constructor for the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // load htmlelements
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        
        $this->objDbwiki = $this->newObject('dbwiki', 'wiki');
        
        // system classes
        $this->objLanguage = $this->getObject('language', 'language');
        
         // language items
        $this->title = $this->objLanguage->languageText('mod_wiki_name', 'wiki');
    }

    /**
    * Method to output a block with smiley icons
    *
    * @access public
    * @return string $str: The output string
    */
    public function show()
	{
        // get data
        $data = $this->objDbwiki->getPublicWikis();

        // text elements
        $selectLabel = $this->objLanguage->languageText('mod_wiki_select', 'wiki');
        $objDrop = new dropdown('wiki');
        $objDrop->addOption(NULL, $selectLabel);
        foreach($data as $line){
            $objDrop->addOption($line['id'], $line['wiki_name']);
        }
        $objDrop->setSelected($this->getSession('wiki_id'));
        $objDrop->extra = 'onchange="javascript:if(this.value != \'\'){$(\'form_select\').submit();}else{return false}"';
        $selectDrop = $objDrop->show();
        
        $objForm = new form('select', $this->uri(array(
            'action' => 'select_wiki',
        ), 'wiki'));
        $objForm->addToForm($selectDrop);        
        $str = $objForm->show();
        
        return $str;
    }
}
?>