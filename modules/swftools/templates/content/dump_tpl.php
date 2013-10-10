<?php

if ($mode == 'showdoc') {
    echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="580" height="700" id="myviewport" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="'.$flashFile.'" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="'.$flashFile.'" quality="high" bgcolor="#ffffff" width="580" height="700" name="myviewport" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>';
} else {
    $this->loadClass('form', 'htmlelements');
    $this->loadClass('button', 'htmlelements');
    $form = new form ('document', $this->uri(array('action'=>'doconversion')));
    
    $objSelectFile = $this->getObject('selectfile', 'filemanager');
    $objSelectFile->restrictFileList = array('pdf');
    
    $button = new button ('doit', $this->objLanguage->languageText('mod_swftools_converttoflash', 'context', 'Convert to Flash'));
    $button->setToSubmit();
    
    $form->addToForm($objSelectFile->show().'<br />'.$button->show());
    
    echo $form->show();
}
?>