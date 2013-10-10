<?
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* The class provides a hello world block to demonstrate
* how to use blockalicious
*
* @author Derek Keats
*
*/
class block_stumble extends object
{
    var $title;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->title="";
        $this->blockType = "none";
    }

    /**
    * Method to output a Tweet block
    */
    public function show()
	{
        return $this->getWidget();
    }

    private function getWidget()
    {
        return "<div id=\"wpdc_embed_12096418361\" "
         . "style=\"display: none\">"
         . " Submit to Stumbleupon flash button</div>"
         . "<head></head><script src=\""
         . "http://www.widgipedia.com/embed/Ernesto-Quezada/Submit-to-Stumbleupon-flash-button_"
         . "297w-12096418361t-1209641836659i-0p.js\">"
          . "</script>";
    }
}