<?php
$objIcon = $this->getObject('geticon', 'htmlelements');
$objIcon->setIcon('close');
$closeLink = "<a href=\"javascript: opener.location.reload();self.close ()\">"
  . $objIcon->show() . "</a>";
  
   echo "<table align=\"center\"><tr>"
     . "<td width=\"100%\" valign=\"top\" style=\"border-top: 1px #DFDFDF solid; border-bottom: 2px #CCCCCC solid;\">"
     . "<b>" . $this->objLanguage->languageText('mod_comment_deletedit','comment')
     . "</b></td></tr><tr><td valign=\"top\" height=\"155px\" >" 
      . $closeLink . "</td></tr></table>";
   /*  
      // exit form - javascript
	$javascript = "<script language=\"javascript\" type=\"text/javascript\">
    function submitExitForm(){
        document.exit.submit();
    }
	</script>";

	echo $javascript;
	$exitBtn = new button('cancel', 'cancel');
	$exitBtn->setOnClick('javascript:submitExitForm()');
	$btnCancel = $exitBtn->show();
	echo $btnCancel;
*/
?>



