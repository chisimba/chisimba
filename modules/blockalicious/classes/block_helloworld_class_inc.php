<?php
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
class block_helloworld extends object
{
    public $title;
    
    /**
    * Constructor for the class
    */
    public function init()
    {
        //Set the title - 
        $this->title='Hello world';
    }
    
    /**
    * Method to output a block with information on how help works
    */
    public function show()
    {
       return "Hello world";
    }
}
?>