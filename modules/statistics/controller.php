<?php
/* -------------------- stories class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
/**
* Module class to test stats
* 
* @author Derek Keats 
* @porter Dean van Niekerk
* @email dvanniekerk@uwc.ac.za
*/

class statistics extends controller {
    public $strout;
    public $text;

    /**
    * Intialiser for the adminGroups object
    * 
    * @param byref $ string $engine the engine object
    */
    public function init()
    {
        $this->objFlesch = &$this->getObject('readingease');
    } 
    /**
    * *The standard dispatch method for the module. The dispatch() method must 
    * return the name of a page body template which will render the module 
    * output (for more details see Modules and templating)
    */
    
    public function dispatch($action)
    {
        
        $str1="\"There's Toad Hall,\" said the Rat; \"and that creek on the left, 
        where the notice-board says, 'Private. No landing allowed,' leads to his 
        boat-house, where we'll leave the boat. The stables are over there to the 
        right. That's the banqueting-hall you're looking at now - very old, that is. 
        Toad is rather rich, you know, and this is really one of the nicest houses 
        in these parts, though we never admit as much to Toad.\"";
        $str1 .= "<br />Flesch: " 
          . $this->objFlesch->calculateFlesch($str1)
          . "<br />Reading grade: " 
          . $this->objFlesch->calculateReadingGrade($str1)
          . "<br />Reading age: " 
          . $this->objFlesch->calculateReadingAge($str1)
          ."<br /><br />";
          
        $str2="The foregoing warranties by each party are in lieu of all other 
        warranties, express or implied, with respect to this agreement, including 
        but not limited to implied warranties of merchantability and fitness for
        a particular purpose. Neither party shall have any liability whatsoever 
        for any cover or setoff nor for any indirect, consequential, exemplary, incidental 
        or punitive damages, including lost profits, even if such party has been 
        advised of the possibility of such damages.";
        $str2 .= "<br />Flesch: " 
          . $this->objFlesch->calculateFlesch($str2)
          . "<br />Reading grade: " 
          . $this->objFlesch->calculateReadingGrade($str2)
          . "<br />Reading age: " 
          . $this->objFlesch->calculateReadingAge($str2)
          ."<br /><br />";
        $this->setVar('strout', $str1.$str2);
        return 'main_tpl.php';
    } 
} 

?>