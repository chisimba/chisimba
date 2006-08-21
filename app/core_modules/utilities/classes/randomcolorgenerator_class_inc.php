<?php
/**
* Class to Generate a Random Color
* @author Tohir Solomons
*
* Found at: http://www.zend.com/tips/tips.php?id=243&single=1
* It's setup to create colors on the lighter side of the spectrum so you
* can use them with darker colors for the text.  
*/ 
class randomcolorgenerator extends object
{

    /**
    * Constructor
    */
    public function init()
    { }
    
    /**
    * Method to generate a random color
    * @return string Color in Hexadecimal format
    */
    public function generateColor()
    {
        $r = rand(128,255);
        $g = rand(128,255);
        $b = rand(128,255);
        
        return dechex($r).dechex($g).dechex($b);
    }

}

?>