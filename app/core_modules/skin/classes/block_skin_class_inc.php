<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class that shows the skin chooser block
*
* @author Tohir Solomons
*
*/
class block_skin extends object
{
    /**
    * string $title Title of the Block
    */
    public $title;
    
    /**
    * Constructor for the class
    */
    function init()
    {
 		//Create an instance of the language object
        $this->objLanguage =& $this->getObject('language','language');
        //Set the title
        $this->title=$this->objLanguage->languageText('mod_skin_name', 'skin');
    }
    
    /**
    * Method to output a block with information on how help works
    */
    function show()
	{
        $objSkin = $this->getObject('skin', 'skin');
        return $objSkin->putSkinChooser();
    }
}
?>