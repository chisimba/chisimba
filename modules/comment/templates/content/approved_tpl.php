<?php
$objIcon = $this->getObject('geticon', 'htmlelements');
$objIcon->setIcon('close');
$closeLink = "<A href=\"javascript: opener.location.reload();self.close ()\">"
  . $objIcon->show() . "</A>";
if (isset($approved)) {
   echo "<table align=\"center\"><tr>"
     . "<td width=\"100%\" valign=\"top\" style=\"border-top: 1px #DFDFDF solid; border-bottom: 2px #CCCCCC solid;\">";
     if ($approved == 1){
         echo "<b>" . $this->objLanguage->languageText('mod_comment_approvedit','comment');
     }else{
         echo "<b>" . $this->objLanguage->languageText('mod_comment_disapprovedit','comment');
     }
     echo "</b></tr><tr><td align=\"right\">"
     . $closeLink . "</td></tr></tr><table>"; 
}
?>
