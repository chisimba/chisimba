<?php

$imageFile = $this->uri(array('action' => 'file', 'id' => $image, 'filename' => $fname), 'filemanager');
            
//echo '<a href="#" onClick="javascript:self.close();" alt="'.$objLanguage->languageText('mod_glossary_clicktoclose', 'glossary').'" title="'.$objLanguage->languageText('mod_glossary_clicktoclose', 'glossary').'"><img id="picfile" src="'.$imageFile.'" border="0" onLoad="resizeWindow(this.width, this.height);" /></a>';
echo '<center><img id="picfile" src="'.$imageFile.'" border="0" onLoad="resizeWindow(this.width, this.height);" /></center>';

//echo ('<center>'.$image['caption'].'</center>');
//echo ('<p><a href="javascript" onClick="window.close();">'.$objLanguage->languageText('mod_glossary_clicktoclose', 'glossary').'</a></p>');

?>