<?php
$url=$this->uri(array('action'=>'stayonline'));
print "<a href=\"javascript:;\" onclick=\"window.open('".$url."','stayon','scrollbars=yes,width=340,height=130');\">".
$this->objLanguage->languageText('mod_keepsessionalive_stayonline','keepsessionalive')."</a>";

?>
