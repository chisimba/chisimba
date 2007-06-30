<?php
/**
* Template
* 
*
* A simple template engine.  
*
* @author prince mbekwa
* @version $version$ - 1.0
* @package 5ive
* @subpackage installer
*/
class Template 
{
	/**
	* Hold all the variables that are going to be imported into the template file
	* @var array
	*/
    var $template_vars = Array(); 

	
    /**
	* Constructor
	* 
	* @param string $file the file name you want to load
	* @return void
	* @access public
	*/
    function Template($file = null) 
	{
        $this->file = $file;
    }//end Template()

	
	/**
	* Set a variable into the template
	*
	* If the variable is a template object, go and call its template::fetch() method
	* 
	* @param string $name The name for this value in the template file
	* @param string $value The value to show in the template file
	* @return void
	* @access public
	*/
    function set($name, $value) 
	{
		if(is_a($value, 'Template')) {
			$value = $value->fetch();	
		}
		$this->template_vars[$name] = $value;        
    }//end set()

    
	/**
	* Create the template and import its variables
	* 
	* @param string $file The file to use as the template
	* @return string The parsed template
	* @access public
	*/
    function fetch($file = null) 
	{
        if (is_null($file)) $file = $this->file;
		
		if (!file_exists($file)) {
			trigger_error('Template file '.$file.' does not exist ', E_USER_ERROR);	
		}

		// Extract the vars to local namespace
        extract($this->template_vars);          
		
        ob_start();                    
		include($file);                
        $contents = ob_get_contents(); 
        ob_end_clean();                
        return $contents;              
		
    }//end fetch()
	
}//end class
?>