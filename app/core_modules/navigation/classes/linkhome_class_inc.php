<?

/***
* A simple classs that puts up links back to the module main page, and to the site main page.
*
*/


class linkhome extends object
 {
     var $objLanguage;

     function init()
     {

     }

     function putlinks()
     {
         print "<a href='".$this->uri(array(),$this->getParam('module'))."' class='pseudobutton'>Back to ".$this->getParam('module')." Menu</a><br>\n";
         print "<a href='".$this->uri(array(),'_default')."' class='pseudobutton'>Back to Main Page</a><br>\n";
     }

 } //end of class linkhome



?>
