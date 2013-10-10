<?php

$this->setLayoutTemplate('faq2_layout_tpl.php');

    $header = $this->getHTMLElement('htmlheading');
    $header->type = 4;
    $header->str =$objLanguage->languageText("faq2_sayitedit",'faq2');
    $header->str .="&nbsp;".$objLanguage->languageText("faq2_into",'faq2');;
    $header->str .="&nbsp;".$objLanguage->languageText("faq2_category",'faq2');
    $header->str .="&nbsp;&raquo;&nbsp;".$category[0]['categoryname'];
    echo $header->show();
    echo $display;
    // Load classes.
	$this->loadHTMLElement("form");
	$this->loadHTMLElement('textinput');
	$this->loadHTMLElement("textarea");
	$this->loadHTMLElement("button");
	$this->loadHTMLElement("checkbox");
	$this->loadHTMLElement("dropdown");
    $this->loadHTMLElement("label");
    // Display form.
	$form = new form("edit",
		$this->uri(array(
	    	'module'=>'faq2',
	   		'action'=>'translateconfirm',
			'id'=>$list[0]['entryid'],
                        'catid'=>$category[0]['categoryid']
	)));
	$form->setDisplayType(1);

        
        // Create table original faq.
        $objTable =& $this->newObject('htmltable','htmlelements');
        $objTable->width='75%';
        $objTable->border='';
        $objTable->cellpadding='5';
        $objTable->cellspacing='3';
        
        $objTable->startRow();
        $objTable->addCell("<hr>");
        $objTable->endRow();
        
        $objTable->startRow();
        $objTable->addCell($list[0]['question']);
        $objTable->endRow();
        
        $objTable->startRow();
        $objTable->addCell("<hr>");
        $objTable->endRow();
        
        $objTable->startRow();
        $objTable->addCell($list[0]['answer']);
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($list[0]['language']);
        $objTable->endRow();
        
        echo "<div class='wrapperLightBkg'>";
       //$str="<fieldset border='style:1px solid #cccccc'>";
       echo $objTable->show();
       echo "";
        //end table
       //$str.="</fieldset>";
        //$form->addToForm($str);

	$label = new label ($objLanguage->languageText("word_question"), 'input_question');
	$form->addToForm("<b>" . $label->show() . ":</b>");
	$form->addToForm(new textarea("question", null, 5, 80));

	$label = new label ($objLanguage->languageText("word_answer"), 'input_answer');
	$form->addToForm("<b>" . $label->show() . ":</b>");
	$form->addToForm(new textarea("answer", null, 5, 80));

	
	
	$labellang=new label($objLanguage->languageText("mod_faq2_selectlanguage","faq2"),'');
	$labelselectlicense=new label($objLanguage->languageText("mod_creativecommons_selectalicense"),'');
	
	//$language =& $this->newObject('language','language');
         $languageCodes = & $this->newObject('languagecode','language');
        $languageDropdown = new dropdown('language');
        // Sort Associative Array by Language, not ISO Code
        $languageList = $languageCodes->iso_639_2_tags->codes;
    
        asort($languageList);

        foreach ($languageCodes->iso_639_2_tags->codes as $key => $value) {
        $languageDropdown->addOption($key, $value);
        }
        $languageDropdown->setSelected($list[0]['language']);
    

        $form->addToForm("<b>".$labellang->show().":<b><br>" . $languageDropdown->show());
	
	$form->addToForm("<b>" . $labelselectlicense->show() . ":</b> &nbsp;<br>".$this->objCreativecommons->show());

  

        $form->addToForm('&nbsp;');
        $form->addToForm('&nbsp;');

	$button = new button("submit", $objLanguage->languageText("word_update"));
	$button->setToSubmit();

        $cancelButton =new button("submit", $objLanguage->languageText("word_cancel"));
        $cancelButton->setOnClick("window.location='".$this->uri(array('action'=>'translate','id'=>$list[entryid], 'catid'=>$categoryid))."';");

	$form->addToForm($button->show().' / '.$cancelButton->show());

	echo $form->show();
	
?>
