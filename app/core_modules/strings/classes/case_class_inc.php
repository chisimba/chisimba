<?php
/**
* This class provides some methods for 
* working wiht cases in text
* 
* @author Derek Keats
* 
*/

class case extends object {
    /**
    * Standard constructor for KEWL.NextGen, instantiates
    * the user object
    */
    function init()
    {
        $this->objUser = &$this->getObject("user", "security");
    } 

    /**
    * 
    * Method to turn camelCase into words 
    * for examlle camelCase becomes camel Case
    * 
    * @param string $str The string to parse
    * 
    */
    function camel2words($str)
    {
        $ar = preg_split("{
          (?<=[a-z])  # A look-behind assertion
                      # for a lowercase letter
          (?=[A-Z])   # A look-ahead assertion
                      # for an uppercase letter               
          }x", $str);
        
        $ret = "";
        $i = 0;
        while ($i <= count($ar)) {
            $ret .= $ar[$i] . " ";
        }
        return $ret;

    
    
} // end class
?>
