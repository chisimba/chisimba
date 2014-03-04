<?php
echo "<h1>" . $this->objLanguage->languageText('mod_websearch_bookmark') . "</h1>";
if (isset($str)) {
   echo $str; 
}
echo "<br /> <b>" . $this->objLanguage->languageText('mod_websearch_wwclose') . "</b>";
?>