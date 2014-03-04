<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
//Load Classes
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

//Append javascript to check all fields
$this->appendArrayVar('headerParams', "
<script type=\"text/javascript\">
        // Action to be taken once page has loaded
        jQuery(document).ready(function(){
            jQuery(\"#input_selectall\").bind('click', function() {
                //if checked, check the other checkboxes, otherwise uncheck all
                var act = jQuery('#input_selectall').attr('checked');
                //Get no of checkboxes from hidden input that stores the count
                var count = jQuery('#input_doc_count').attr('value');
            if(act) {
                var todo = 'checked';
            } else {
                var todo = '';
            }
            for (var i = 0; i < count; i++) {
                jQuery('#set4batch_'+i).attr('checked', todo);
            }
            });
        });
</script>
");


$header = new htmlheading();
$header->type = 2;
$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'podcaster');

if ($selected == '') {
    $folders = $this->getDefaultFolder($this->baseDir);
    $selected = $folders[0];
}
if ($selected != "unknown0") {
    $cfile = substr($selected, strlen($this->baseDir));
    $header->str = $cfile;

    echo $header->show();
}


//Add navigation to fieldset
$fs = new fieldset();
$fs->setLegend('Navigation');
$fs->addContent($links);

echo $fs->show();

//Add Form
$form = new form('registerdocumentform', $this->uri(array('action' => 'batchexecute', 'mode' => $mode, 'active' => 'Y', 'rowcount' => $files['count'])));

$table = &$this->newObject("htmltable", "htmlelements");
//Store file count
$filecount = $files['count'];
if ($filecount > 0) {
    $count = 0;
    //Create a check all checkbox
    $selectall = &new checkBox('selectall', Null, Null);
    $selectall->setValue('clicked');
    //Store count
    $textinput = new textinput('doc_count');
    $textinput->size = 1;
    $textinput->value = $filecount;
    $textinput->setType('hidden');

    $table->startRow();
    $table->addCell($selectall->show() . $textinput->show() . "<b>" . $this->objLanguage->languageText('mod_podcaster_select', 'podcaster', "Select") . "</b>");
    $table->addCell("<b>" . $this->objLanguage->languageText('mod_podcaster_type', 'podcaster', "Type") . "</b>");
    $table->addCell("<b>" . $this->objLanguage->languageText('mod_podcaster_title', 'podcaster', "Title") . "</b>");
    $table->addCell("<b>" . $this->objLanguage->languageText('mod_podcaster_refno', 'podcaster', "Ref No") . "</b>");
    $table->addCell("<b>" . $this->objLanguage->languageText('mod_podcaster_owner', 'podcaster', "Owner") . "</b>");
    $table->endRow();
    foreach ($files as $file) {
        if (count($file) > 1) {
            $dlink1 = new link($this->uri(array("action" => "downloadfile", "filepath" => $file['id'], "filename" => $file['actualfilename'])));
            $dlink1->link = $file['thumbnailpath'];

            $dlink2 = new link($this->uri(array("action" => "downloadfile", "filepath" => $file['id'], "filename" => $file['actualfilename'])));
            $dlink2->link = $file['actualfilename'];
            //Get the document Id
            $docId = $this->documents->getIdWithRefNo($file['refno']);

            //Create checkbox to help select record for batch execution
            $approve = &new checkBox($docId . '_app', Null, Null);
            $approve->setValue('execute');
            $approve->setId('set4batch_' . $count);

            $table->startRow();
            $table->addCell($approve->show());
            $table->addCell($dlink1->show());
            $table->addCell($dlink2->show());
            $table->addCell($file['refno']);
            $table->addCell($file['owner'] . '(' . $file['telephone'] . ')');
            $table->endRow();
            $count++;
        }
    }
} else {
    $table->startRow();
    $table->addCell('<strong class="confirm">'.$this->objLanguage->languageText('mod_podcaster_norecords', 'podcaster', 'There are no records found')).'</strong>';
    $table->endRow();
}

//add table to form
$form->addToForm($table->show());
if ($filecount > 0) {
    $button = new button('submit', $this->objLanguage->languageText('mod_podcaster_deleteselected', 'podcaster', 'Delete Selected'));
    $button->setToSubmit();

    $form->addToForm(" </br> " . $button->show());
}

//Add Navigations
if ($filecount > 0) {
    //Compute new start val
    $newstart = $start + $rows;
    $newprev = $start - $rows;
    //Navigation Flag
    $str = "";
    //Create table to hold buttons(forms)
    $table = &$this->newObject("htmltable", "htmlelements");
    $table->width = '100%';
    $table->startRow();
    $nextflag = "nonext";
    //Add prev button
    if ($newprev >= 0) {
        $str .= "prev";
        $button = new button('submit', $this->objLanguage->languageText('mod_podcaster_wordprevious', 'podcaster', 'Previous'));
        $button->setToSubmit();
        //Add Form
        $prevform = new form('prevform', $this->uri(array('action' => 'viewfolder', 'mode' => $mode, 'active' => 'Y', 'start' => $newprev, 'rowcount' => $files['count'], 'folder' => $dir)));

        $prevform->addToForm("</ br> " . $button->show() . " </ br>");

        $table->addCell($prevform->show(), "50%", 'top', 'right');
    }
    //Add Next button
    if ($newstart < $files['count'] && $start != $files['count'] && $files['count'] > $rows) {

        $button = new button('submit', $this->objLanguage->languageText('mod_podcaster_wordnext', 'podcaster', 'Next'));
        $button->setToSubmit();
        //Add Form
        $nextform = new form('nextform', $this->uri(array('action' => 'viewfolder', 'mode' => $mode, 'active' => 'Y', 'start' => $newstart, 'rowcount' => $files['count'], 'folder' => $dir)));

        $nextform->addToForm("</ br> " . $button->show() . " </ br>");
        if (!empty($str)) {
            $table->addCell($nextform->show(), "50%", 'top', 'left');
        } else {
            $table->addCell(" ", "50%", 'top', 'left');
            $table->addCell($nextform->show(), "50%", 'top', 'left');
        }
        $str .= "next";
        $nextflag = "next";
    }
    if ($nextflag == "nonext") {
        $table->addCell(" ", "50%", 'top', 'left');
    }
    $table->endRow();
    $navtable = $table->show();
}
//Add documents table to fieldset
$fs = new fieldset();
$fs->setLegend($this->objLanguage->languageText('mod_podcaster_folders', 'podcaster', 'Folders'));
//Check if str is empty
if (!empty($str)) {
    $fs->addContent($form->show() . "<br/>" . $navtable);
} else {
    $fs->addContent($form->show());
}
echo $fs->show();
?>