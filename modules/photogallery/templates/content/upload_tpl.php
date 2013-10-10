<?php

$list = NULL;
$h1 = $this->getObject('htmlheading', 'htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('textinput','htmlelements');

$form = new form('uploadfrm');
$form->action = $this->uri(array('action' => 'upload'));
$form->extra = ' enctype="multipart/form-data" ';

$dropdown = new dropdown('albumselect');
$dropdown->addOption('','a New Album +');
$dropdown->setSelected('');
$dropdown->setId('');
$dropdown->addFromDB($albumbsArr, 'title','id');
$dropdown->extra = ' onChange="albumSwitch(this)" ';

$albumtitle = new textinput('albumtitle');
$albumtitle->setId('albumtitle');
$albumtitle->size = '22';
$albumtitle->extra = ' onkeyup="updateFolder(this, \'folderdisplay\', \'autogen\');" ';

$scriptFile = $this->_objConfig->getModuleUri().'photogallery/resources/admin.js';
$this->appendArrayVar('headerParams','<script type="text/javascript" src="'.$scriptFile.'"></script>');

$h1->str = $this->objLanguage->languageText('mod_photogallery_upload', 'photogallery');
$str = $this->objLanguage->languageText('mod_photogallery_upload_help', 'photogallery');
echo $h1->show();

if(isset($errmsg))
{
	print '<div class="warning">'.$errmsg.'</div>';
}
echo $str;

$cnt = count($albumbsArr);
$i = 0;

foreach ($albumbsArr as $album)
{
 	$i++;
	$list .="'".$album['title']."'";
	$list = ($i < $cnt && $cnt!=1) ? $list.', ': $list;
}

if(!isset($list))
{
	$list = NULL;
}

$str = "<script type=\"text/javascript\">
        window.totalinputs = 5;
        // Array of album names for javascript functions.
        var albumArray = new Array ( ".$list." );
      
      </script>";
      
echo  $str;

$form->addToForm('<div id="albumselect">Upload to: '.$dropdown->show());
$form->addToForm('<div id="albumtext" style="margin-top: 5px;">called:  '.$albumtitle->show().'in the folder named: ');


$bigString = '<div style="position: relative; display: inline;">
              <div id="foldererror" style="display: none; color: #D66; position: absolute; z-index: 100; top: -2em; left: 0px;">That name is already used.</div>
              <input id="folderdisplay" size="18" type="text" name="folderdisplay" disabled="true" onkeyup="validateFolder(this);"/> 
            </div>
            <label><input type="checkbox" name="autogenfolder" id="autogen" checked="true" onClick="toggleAutogen(\'folderdisplay\', \'albumtitle\', this);" /> Auto-generate folder names</label>
            <input type="hidden" name="folder" value="" />

          </div>
          
        </div>
        
        <hr />
        
    
        
        <!-- This first one is the template that others are copied from -->
        <div class="fileuploadbox" id="filetemplate">
          <input type="file" size="40" name="files[]" />
        </div>
        <div class="fileuploadbox">
          <input type="file" size="40" name="files[]" />

        </div>
        <div class="fileuploadbox">
          <input type="file" size="40" name="files[]" />
        </div>
        <div class="fileuploadbox">
          <input type="file" size="40" name="files[]" />
        </div>
        <div class="fileuploadbox">
          <input type="file" size="40" name="files[]" />

        </div>

        <div id="place" style="display: none;"></div><!-- New boxes get inserted before this -->
        
        <p><a href="javascript:addUploadBoxes(\'place\',\'filetemplate\',5)" title="Doesn`t reload!">+ Add more upload boxes</a> <small>(won`t reload the page, but remember your upload limits!)</small></p>
        
        
        <p><input type="submit" value="Upload" onclick="this.form.folder.value = this.form.folderdisplay.value;" class="button" /></p><hr/>
';

$form->addToForm($bigString);

echo '<div id="main">'.$form->show().'</div>';
?>     
