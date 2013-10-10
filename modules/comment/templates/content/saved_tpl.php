<?php
$objIcon = $this->getObject('geticon', 'htmlelements');
$objIcon->setIcon('close');
$closeLink = "<a href=\"javascript: opener.location.reload();self.close ()\">"
  . $objIcon->show() . "</a>";
if (isset($comment)) {
   echo "<table align=\"center\"><tr>"
     . "<td width=\"100%\" valign=\"top\" style=\"border-top: 1px #DFDFDF solid; border-bottom: 2px #CCCCCC solid;\">"
     . "<b>" . $this->objLanguage->languageText("mod_comment_savedtit",'comment')
     . "</b></td><tr><td valign=\"top\" height=\"155px\" >" 
     . $comment . "</td></tr><tr><td align=\"right\">" 
     . $closeLink . "</td></tr></tr></table>"; 
}

?>
