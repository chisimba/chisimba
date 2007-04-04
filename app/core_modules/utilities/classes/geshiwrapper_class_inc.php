<?php
/**
* 
* This is a KINKY wrapper for the geshi syntax hilighting class. 
* It does not implement all of the geshi methods or properties.
*
* In KINKY / KEWL.NextGen developers are encouraged to think in design patterns.
* This is an example of the ADAPTER pattern, which produces a wrapper. The underlying
* class being wrapped can be updated without the need to change the KINKY/KEWL.Nextgen
* adapter class. 
*
* For usage see the examples provided in the wrapgeshi module controller and templates
* 
* @author Derek Keats
*
* @version $Id$
* @copyright 2005 GNU GPL
*
**/
require_once($this->getResourcePath('geshi/geshi.php', 'utilities'));

class geshiwrapper extends object
{
    /**
    * @var string $source Source to Parse. Note, this is not the path to the file
    */
    public $source;
    
    /**
    * @var string $language Language to perform the syntax highlighting in
    */
    public $language;
    
    /**
    * @var string $path Path to Geshi files
    */
    private $path;
    
    /**
    * @var string $gError Geshi Error
    */
    public $gError;
    
   
    /**
    * @var object $objG Geshi Object
    */
    public $objG;

    /**
    * 
    * Standard constructor which provides the default language and path
    * information.
    * 
    */
    public function init()
    {
        //Set the source to null
        $this->source = NULL;
        // set the language
        $this->language = "php";
        //Set the path for the geshi files
        $this->path = $this->getResourcePath('geshi/');;
    }
    
    
  
    /**
    * Method to start geshi. Geshi cannot be started in the init 
    * because parameters have to be set before instantiating Geshi.
    */
    public function startGeshi()
    {
        //Check if the source is set
        if ( !$this->source ) {
            $this->gError = "mod_geshi_error_nosource";
            return FALSE;
        } else {
            $this->objG = new GeSHi($this->source, $this->language, $this->path);
            return TRUE;
        }
        
    }
    
    /**
    *
    * Method to wrap the geshi enable_line_numbers method. Note that I have
    * not used the underscores to keep to KINKY camel case. Valid values for $flag
    * are as follows
    * GESHI_NORMAL_LINE_NUMBERS - Use normal line numbering
    * GESHI_FANCY_LINE_NUMBERS - Use fancy line numbering
    * GESHI_NO_LINE_NUMBERS - Disable line numbers (default for geshi, but
    * changed here)
    *
    */
    public function enableLineNumbers($flag=GESHI_NORMAL_LINE_NUMBERS)
    {
        $this->objG->enable_line_numbers($flag);
    }
    
    
    /**
    * Method to start the line number at any number
    *
    * As of GeSHi 1.0.2, you can now make the line numbers start at any number, 
    * rather than just 1. This feature is useful if you're highlighting code 
    * from a file from around a certain line number in that file, as an additional 
    * guide to those who will view the code. You set the line numbers by calling the 
    * start_line_numbers_at() method:
    * $geshi->start_line_numbers_at($number);
    * This method provides a wrapper for the geshi method.
    *
    * @param integer $number Must be a positive integer (or zero). 
    *
    */
    public function startLineNumbersAt($number)
    {
        $this->objG->start_line_numbers_at($number);
    }
    

    /**
    * 
    * Method to change the header from/to <pre> HTML to/from <div> HTML
    * To change/set the header to use, you call the set_header_type() method:
    * $geshi->set_header_type(GESHI_HEADER_DIV);
    *    or...
    * $geshi->set_header_type(GESHI_HEADER_PRE);
    *
    * This has been abstracted to accept a text input either div or pre
    * where anything other than pre defaults to pre
    *
    */
    public function setHeaderType($type)
    {
        if ($type=="div") {
            $this->objG->set_header_type(GESHI_HEADER_DIV);
        } else {
            $this->objG->set_header_type(GESHI_HEADER_PRE);
        }
    }
    
    /**
    *
    * Method to wrap the geshi set_overall_style method. Note that I have
    * Not used the underscores to keep to KINKY camel case
    *
    */
    public function setOverallStyle($style, $overwrite=FALSE)
    {
        $this->objG->set_overall_style($style, $overwrite);    
    }
    
    
    /**
    *
    * Wrapper for the geshi user_classes method.
    *
    * CAUTION: This should be the very first method you call after creating a 
    * new GeSHi object (i.e. after geshiStart()! That way, various other 
    * methods can act upon your choice to use classes correctly. In theory, 
    * you could call this method just before parsing the code, but this may 
    * result in unexpected behaviour.
    *
    */
    public function enableClasses($enable=TRUE)
    {
        if ($enable == TRUE ) {
            $this->objG->enable_classes();
        } else {
            $this->objG->enable_classes(FALSE);
        }
    }
    
 
    
    /**
    * 
    * Show method to return the geshi code
    *
    */
    public function show()
    {
        return $this->objG->parse_code();
    }

} #class

?>