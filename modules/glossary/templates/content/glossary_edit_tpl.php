<script language="JavaScript" type="text/JavaScript">

function confirmDelete(url, msg) {

	if (confirm(msg)) {
		location.href = url;
	}
}

</script>
<?php
/* A PHP template for the Home Page of the Glossary Module */

// Classes being used
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');

// Edit term

// Create Header Tag ' Edit Term
$this->titleAddTerm =& $this->getObject('htmlheading', 'htmlelements');
$this->titleAddTerm->type=1;
$this->titleAddTerm->str=$this->objLanguage->languageText('mod_glossary_name', 'glossary').' - '.$this->objLanguage->languageText('mod_glossary_edit', 'glossary').' "'.$record['term'].'"';
echo $this->titleAddTerm->show();


if ($message != '') {

    $timeoutObject = $this->getObject('timeoutmessage', 'htmlelements');
    $timeoutObject->setMessage($message);

    $editMessage = '<div class="" align="center">';
    $editMessage .= $timeoutObject->show();
    $editMessage .= '</div>'; //<br />

    echo $editMessage;
}

// Edit term and definition

// Start of Form
$editTermForm = new form(
    'editWord',
    $this->uri(
        array(
    		'action' => 'editconfirm',
    		'id'     => $record['item_id']
    	),
    'glossary')
);
$editTermForm->displayType = 3;

$hiddenIdInput = new textinput('id');
$hiddenIdInput->fldType = 'hidden';
$hiddenIdInput->value = $record['item_id'];
$editTermForm->addToForm($hiddenIdInput->show());



$addTable = $this->newObject('htmltable', 'htmlelements');
$addTable->width='500';
$addTable->cellpadding = 10;

$addTable->startRow();
$termLabel = new label($this->objLanguage->languageText('mod_glossary_term', 'glossary'), 'input_term');
$addTable->addCell($termLabel->show(), 100);

$termInput = new textinput('term', htmlentities(stripslashes($record['term'])));
$termInput->size = 50;
$addTable->addCell($termInput->show(), 400);

$addTable->endRow();
$addTable->startRow();

$definitionLabel = new label($this->objLanguage->languageText('mod_glossary_definition', 'glossary'), 'input_definition');
$addTable->addCell($definitionLabel->show().':', null);

$definition = new textarea('definition', stripslashes($record['definition']));
$addTable->addCell($definition->show(), null);

$addTable->endRow();

$addTable->startRow();

$submitButton = new button('submit', $this->objLanguage->languageText('mod_glossary_updateTerm', 'glossary'));
$submitButton->setToSubmit();

$addTable->addCell(' ', null);
$addTable->addCell($submitButton->show(), null);
$addTable->endRow();


$editTermForm->addRule('term',$this->objLanguage->languageText('mod_glossary_termRequired', 'glossary'),'required');
$editTermForm->addRule('definition',$this->objLanguage->languageText('mod_glossary_defnRequired', 'glossary'),'required');

$editTermForm->addToForm($addTable);

echo $editTermForm->show();

// --------
?>
<table cellspacing="5"><tr><td width="50%" height="50%" valign="top" style="border: 1px solid ActiveBorder; padding: 5px;"><!-- border="1" cellpadding="5"  -->
<!--<div style="border: 1px solid ActiveBorder; padding: 5px; height: 100%;">-->
<?php

// See Also's

//--$seeAlsoFieldset = &$this->newObject('fieldset','htmlelements');

// Create Header Tag ' Edit Term
$this->seeAlsoTerm =& $this->getObject('htmlheading', 'htmlelements');
$this->seeAlsoTerm->type=3;
$this->seeAlsoTerm->str=$this->objLanguage->languageText('mod_glossary_seeAlso', 'glossary').': <em>'.$record['term'].'</em> '.$this->objLanguage->languageText('mod_glossary_linkToOthers', 'glossary');

//--$seeAlsoFieldset->addContent ( );
echo $this->seeAlsoTerm->show();

// -------

if ($seeAlsoNum == 0 && $numRecords > 1)
{
	//--$seeAlsoFieldset->addContent ();
    echo $this->objLanguage->languageText('mod_glossary_noTermsLinked', 'glossary').'. ';

} else {

//--$seeAlsoFieldset->addContent ();
    echo '<ul>';

//    for ($z=0;$z<10;++$z) {
//        echo "<li>$z</li>";
//    }

	foreach ($seeAlsoList as $element) {
		if (
		    (($element['item_id'] != $id) && !empty($element['term1']))
		    || (($element['item_id'] == $id) && !empty($element['term2']))
		) {
    		//--$seeAlsoFieldset->addContent ();
    		echo '<li>';

		  if ($element['item_id'] != $id) {

              //--$seeAlsoFieldset->addContent ();
    		  echo $element['term1'];

		  } else {

              //--$seeAlsoFieldset->addContent ();
		      echo $element['term2'];

		  }


		  // Delete Link
		  //--$seeAlsoFieldset->addContent();
          echo '&nbsp;';

		  // URL Delete Link
		  $deleteLinkIcon = $this->getObject('geticon', 'htmlelements');
		  $deleteLinkIcon->setIcon('delete');
		  $deleteLinkIcon->alt=$objLanguage->languageText('mod_glossary_delete', 'glossary');
		  $deleteLinkIcon->title=$objLanguage->languageText('mod_glossary_delete', 'glossary');

		  $link = $this->uri(
            array(
				'action'=>'deleteseealso',
				'id'=>$record['item_id'] ,
				'seealso'=>$element['id']
    		),
    		'glossary'
		  );

		  $deleteLink = new link("javascript:confirmDelete('$link', '".$objLanguage->languageText('mod_glossary_pop_deleteseealso', 'glossary')."');");
		  $deleteLink->link = $deleteLinkIcon->show();


		  //--$seeAlsoFieldset->addContent ();
		  echo $deleteLink->show();

            //--$seeAlsoFieldset->addContent ();
		    echo '</li>';
        }
	}

//--$seeAlsoFieldset->addContent();
    echo '</ul>';

}

// -------

if ($numRecords == 1)
// Prevents logical error
// If only one record exists,
{
	//--$seeAlsoFieldset->addContent ();
    echo '<p>'.$objLanguage->languageText('mod_glossary_onlyword', 'glossary').'</p>';

} else if ($notLinkedToNum == 0) {


		//--$seeAlsoFieldset->addContent ();
    echo '<p>'.$record['term'].' '.$this->objLanguage->languageText('mod_glossary_isLinkedtoAll', 'glossary').'</p>';


} else {

	// Form to Add See Also Link
	// Start of Form
	$addSeeAlsoForm = new form(
    	'addseealso',
    	$this->uri(
            array(
    			'action'=>'addseealsoconfirm'
    		),
        	'glossary'
    	)
    );

    $seeAlsoHiddenIdInput = new textinput('id');
    $seeAlsoHiddenIdInput->fldType = 'hidden';
    $seeAlsoHiddenIdInput->value = $record['item_id'];
    $addSeeAlsoForm->addToForm($seeAlsoHiddenIdInput->show());

	$seeAlso = new dropdown('seealso');

	foreach ($others as $element) {

		$seeAlso->addOption($element['id'], $element['term']);

	}

	// Instructions
    $seeAlsoLabel = new label($this->objLanguage->languageText('mod_glossary_selectTermLink', 'glossary'), 'input_seealso');
    $addSeeAlsoForm->addToForm($seeAlsoLabel->show().': ', null);

	//$addSeeAlsoForm->addToForm($this->objLanguage->languageText('mod_glossary_selectTermLink', 'glossary').':');

	// Add Drop Down
	$addSeeAlsoForm->addToForm($seeAlso);


	$submitButton = new button('submit', $this->objLanguage->languageText('mod_glossary_add', 'glossary'));
	$submitButton->setToSubmit();


	$addSeeAlsoForm->addToForm($submitButton);
	$addSeeAlsoForm->displayType =3;

	//--$seeAlsoFieldset->addContent ();
    echo $addSeeAlsoForm->show();


}

//--echo $seeAlsoFieldset->show();

?>
<!--</div>-->
</td>
<td valign="top" height="100%" rowspan="2" style="border: 1px solid ActiveBorder; padding: 5px;">
<!--<div style="border: 1px solid ActiveBorder; padding: 5px;">-->
<?php
// Images
?>
<!--<fieldset>-->
<h3><?php echo $this->objLanguage->languageText('mod_glossary_imagesfor', 'glossary').' <em>'.$record['term'].'</em>'; ?></h3>
<!--<iframe src="<?php echo $this->uri(array(
		'module' => 'glossary',
		'action' => 'listimages',
		'id'     => $record['item_id']
	)); ?>" width="99%" height="170" frameborder="0" scrolling="auto" style="overflow-x: hidden;" marginwidth="0" marginheight="0" hspace="0" vspace="0"></iframe>-->
<?php
//$this->loadClass('form', 'htmlelements');
//$this->loadClass('textinput', 'htmlelements');
//$this->loadClass('button', 'htmlelements');
//$this->loadClass('label', 'htmlelements');

// Filemanage test of image input

//$objSelectFile->name = 'nameofforminput';
//$editTermForm->addToForm($objSelectFile->show());

if (count($images) == 0) {
    echo '<p>'.$this->objLanguage->languageText('mod_glossary_noimageslisted', 'glossary').'</p>';
} else {
    // Popup
    $objPopup = $this->newObject('windowpop', 'htmlelements');
    // Delete icon
    $objIconDelete = $this->getObject('geticon', 'htmlelements');
    $objIconDelete->alt = $this->objLanguage->languageText('mod_glossary_delete', 'glossary');
    $objIconDelete->title = $this->objLanguage->languageText('mod_glossary_delete', 'glossary');
    $objIconDelete->setIcon('delete');
    // Images table
    $objTableImages = $this->newObject('htmltable','htmlelements');
    $objTableImages->width = '80%';
    $objTableImages->attributes = ' border="0"';
    $objTableImages->cellspacing = '0';
    $objTableImages->cellpadding = '5';

//    for ($z=0;$z<10;++$z) {
//        $objTableImages->startRow();
//        $objTableImages->addCell("$z");
//        $objTableImages->endRow();
//    }

    foreach ($images AS $image)
    {
        $objTableImages->startRow();
        $uriPreview = $this->uri(
            array(
                'action' => 'previewimage',
                'id' => $image['image'],
                'fname' => $image['filename']
            )
        );
        $objPopup->set('location', $uriPreview);
        $objPopup->set('window_name', 'previewImage');
        $objPopup->set('linktext', $image['caption']);
        $objPopup->set('width', '10');
        $objPopup->set('height', '10');
        $objPopup->set('left', '100');
        $objPopup->set('top', '100');
        $objConfirm = $this->newObject('confirm', 'utilities');
        $uriDelete = $this->uri(
            array(
                'action' => 'deleteimage',
                'id' => $image['imageid'],
                'returnid' => $id
            )
        ); //$image['image']
        $objConfirm->setConfirm(
            $objIconDelete->show(),
            $uriDelete,
            $this->objLanguage->languageText('mod_glossary_areyousuredeleteimage', 'glossary')
        );
        //echo ('<li>'.$image['filename'].'</li>');
        $objTableImages->addCell($objPopup->show());
        $objTableImages->addCell($objConfirm->show());
        //echo ('<li>'.$objPop->show().' '.$objConfirm->show().'</li>');
        $objTableImages->endRow();
    }
    echo '<p>'.$objTableImages->show().'</p>';
}
//echo ('</ul>');
// Image add form
$objFormImageAdd = new form(
    'uploadimage',
    $this->uri(
        array(
    		'action' => 'uploadimage'
    	),
        'glossary'
    )
);
$objFormImageAdd->extra = 'enctype="multipart/form-data"';

// ID
$objTextInputId = new textinput('id');
$objTextInputId->fldType = 'hidden';
$objTextInputId->value = $id;
$objFormImageAdd->addToForm($objTextInputId->show());

// Image add table
$objTableImageAdd = $this->newObject('htmltable', 'htmlelements');
$objTableImageAdd->width = '99%';
$objTableImageAdd->cellpadding = 5;

$objTableImageAdd->startRow();
$objLabelImage = new label($this->objLanguage->languageText('mod_glossary_image', 'glossary').':', 'input_userFile');
$objTableImageAdd->addCell($objLabelImage->show());
$objSelectImage = $this->newObject('selectimage', 'filemanager');
$objSelectImage->name = 'userFile';
$objTableImageAdd->addCell($objSelectImage->show());
$objTableImageAdd->endRow();
//--$file = $this->getParam('userFile');

$objTableImageAdd->startRow();
$objTableImageAdd->addCell('&nbsp;');
$objTableImageAdd->endRow();

$objTableImageAdd->startRow();
$objLabelCaption = new label($this->objLanguage->languageText('mod_glossary_imagecaption', 'glossary').':', 'input_caption');
$objTableImageAdd->addCell($objLabelCaption->show());
$objTextInputCaption = new textinput('caption');
$objTextInputCaption->size = 30;
$objTableImageAdd->addCell($objTextInputCaption->show());
$objTableImageAdd->endRow();

$objTableImageAdd->startRow();
$objButtonSubmit = new button('submit', $this->objLanguage->languageText('mod_glossary_addimage', 'glossary'));
$objButtonSubmit->setToSubmit();
$objTableImageAdd->addCell('&nbsp;');
$objTableImageAdd->addCell($objButtonSubmit->show()); //, NULL, NULL, NULL, NULL, 'colspan="2"'
$objTableImageAdd->endRow();

$objFormImageAdd->addToForm($objTableImageAdd->show());

$objFormImageAdd->addRule('caption', $this->objLanguage->languageText('mod_glossary_caption_validation', 'glossary'), 'required');
/*
$form->addRule(array('name'=>'caption','length'=>15), 'Your surname is too long', 'maxlength');
$form->addRule('caption','Please enter your name','required');
$form->addRule('userFile','Please enter your name','required');
*/

echo $objFormImageAdd->show();

?>
<!--</fieldset>-->
<!--</div>-->
</td>
</tr>
<tr>
<td width="50%" height="50%" valign="top" style="border: 1px solid ActiveBorder; padding: 5px;">
<!--<div style="border: 1px solid ActiveBorder; padding: 5px; height: 50%;">-->
<?php

// URL's

//--echo ('<br />');
// -------

//--$urlFieldset = &$this->newObject('fieldset','htmlelements');

// Create Header Tag ' Website Links
$this->urlLinks =& $this->getObject('htmlheading', 'htmlelements');
$this->urlLinks->type=3;
$this->urlLinks->str=$this->objLanguage->languageText('mod_glossary_websiteLinksFor', 'glossary').' <em>'.$record['term'].'</em>';

//--$urlFieldset->addContent();
echo $this->urlLinks->show();


if ($urlNum == 0)
{
	//--$urlFieldset->addContent ();
    echo $this->objLanguage->languageText('mod_glossary_noUrlsFound', 'glossary').'.';

} else {

	//--$urlFieldset->addContent ();
    echo '<ul>';

//    for ($z=0;$z<10;++$z) {
//        echo "<li>$z</li>";
//    }

	foreach ($urlList as $element) {
        if (!empty($element['url'])) {
		  //--$urlFieldset->addContent ();
          echo '<li>';

		  $itemLink = new link($element['url']);
		  $itemLink->target = '_blank';
		  $itemLink->link = $element['url'];

		  //--$urlFieldset->addContent( );
          echo $itemLink->show();

		  //--$urlFieldset->addContent(  );
          echo '&nbsp;';

		  // URL Delete Link
		  $deleteLinkIcon =& $this->getObject('geticon', 'htmlelements');
		  $deleteLinkIcon->setIcon('delete');
		  $deleteLinkIcon->alt=$objLanguage->languageText('mod_glossary_delete', 'glossary');
		  $deleteLinkIcon->title=$objLanguage->languageText('mod_glossary_delete', 'glossary');

		  $link = $this->uri(
            array(
                'action'=>'deleteurl',
                'id'=>$record['item_id'],
                'link'=>$element['id']
            ),
            'glossary'
		  );

		  $deleteLink = new link("javascript:confirmDelete('$link', '".$objLanguage->languageText('mod_glossary_pop_deleteurl', 'glossary')."');");
		  $deleteLink->link = $deleteLinkIcon->show();


		  //--$urlFieldset->addContent ();
          echo $deleteLink->show();

		  //--$urlFieldset->addContent ();
          echo '</li>';
		}
	}

	//--$urlFieldset->addContent ();
    echo '</ul>';

}

// -------

// Start of Form
$addUrlForm = new form('addurl',
    $this->uri(
        array(
    		'action'=>'addurlconfirm'
    //		'id'=>$record['id']
    	),
        'glossary'
    )
);

$hiddenIdInput = new textinput('id');
$hiddenIdInput->fldType = 'hidden';
$hiddenIdInput->value = $record['item_id'];
$addUrlForm->addToForm($hiddenIdInput->show());


$urlInput = new textinput('url');
$urlInput->size = 30;
//$urlInput->extra = ' title ="asfas"';
$urlInput->value = 'http://';


$urlLabel = new label($this->objLanguage->languageText('mod_glossary_addUrl', 'glossary'), 'input_url');
$addUrlForm->addToForm($urlLabel->show().': ', null);

$addUrlForm->addToForm($urlInput->show());


$submitButton = new button('submit', $this->objLanguage->languageText('mod_glossary_add', 'glossary'));
$submitButton->setToSubmit();


$addUrlForm->addToForm($submitButton->show());
$addUrlForm->displayType =3;

//--$urlFieldset->addContent( );
echo $addUrlForm->show();

//--$urlFieldset->addContent ('<br />');

//--echo $urlFieldset->show();
?>
<!--</div>-->
</td>
</tr>
</table>
<p align="center"><a href="<?php echo $this->uri(array('action'=>'search', 'term'=>$record['term'])); ?>"><?php echo $this->objLanguage->languageText('mod_glossary_returntoglossary', 'glossary'); ?></a></p>