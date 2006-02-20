<?php
class strings extends controller 
{
    /**
    * $var string $action The action to performin dispatch
    */
    var $action;

    /**
    * Standard init method, create an instance of the language object
    */
    function init()
    { 
        // Create instance of the strings framework extensions
        $this->objRandom = $this->getObject('random');
        $this->objH = $this->getObject('highlight');
        $this->objU = $this->getObject('url');
    } 

    function dispatch ()
    {
        $str = "<h1>Unit test for string class</h1>\n";
        //Random
        $str .= "<h2>Testing random_class_inc.php</h2>\n";
        $str .= "Random guid: ". $this->objRandom->guid()."<br />\n";
        $str .= "MD5 Random guid: ". $this->objRandom->md5Guid()."<br />\n";
        $str .= "UserId from Random guid: "
         . $this->objRandom->getUserIdFromGuid($this->objRandom->guid())
         ."<br />\n";
        $str .= "User full name from Random guid: "
         . $this->objRandom->getFullNameFromGuid($this->objRandom->guid())
         ."<br />\n";
        $str .= "UserId from Random guid (bad guid): "
         . $this->objRandom->getUserIdFromGuid("II_99_UIIUUIOUO")
         ."<br />\n";
        $str .= "UserId from Random guid (bad guid): "
         . $this->objRandom->getFullNameFromGuid("I_99_IUIIUUIOUO")
         ."<br />\n";
         
        //Highlight
        $str .= "<h2>Testing highlight_class_inc.php</h2>\n";
        $testStr="Now is the time for all good hackers to come to the aid of the party to aid the party.";
        $this->objH->keyword="to the";
        $str .= $this->objH->show($testStr);
        
        //URL
        $str .= "<h2>Testing url_class_inc.php</h2>\n";
        $testStr="Now is the time for all good hackers to come to the aid of the party at http://www.party.com .";
        $str .= $this->objU->makeClickableLinks($testStr);
        
        $this->setVarByRef('str', $str);
        return 'main_tpl.php';
        
    }

}//end class