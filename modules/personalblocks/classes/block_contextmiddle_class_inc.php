<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Display middle area personal blocks
*
* @author Derek Keats
*
*/
class block_contextmiddle extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
    *
    * @var string $objLanguage String object property for holding the language object
    * @access public
    *
    */
    public $objLanguage;
    /**
     * The type of block
     *
     * @var    object
     * @access public
     */
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->objLanguage = & $this->getObject('language', 'language');
        $this->title = $this->objLanguage->code2txt('mod_personalblocks_contextmiddle', 'personalblocks');
        $this->title = ucfirst($this->title);
        $this->blockType = "none";
    }

    /**
    * Method to output block
    */
    public function show()
    {
        return $this->getWidget();
    }

    /**
    *
    * Retrieve the block
    * @return string The rendered block
    * @access private
    *
    */
    private function getWidget()
    {
        // Instantiate the rendering class
        $objPbrender = $this->getObject("pbrender", "personalblocks");
        return $objPbrender->renderMiddle(TRUE, "context");
    }
}
?>