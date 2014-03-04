<?php
    $objLang= $this->getObject('language', 'language');
	$this->table= $this->getObject('htmltable','htmlelements');
	$this->textbox=new textinput();
    $this->form= $this->getObject('form','htmlelements');
    $this->button= $this->getObject('button', 'htmlelements');
    $this->button1= $this->getObject('button', 'htmlelements');
	

$this->form->name="confirm";
$formAction = 'index.php?module=contextadmin';
$this->form->setAction($formAction);



$this->table->border="0";
$this->table->align="center";
$this->table->width="30%";

$arr = array($objLanguage->languageText("phrase_confirmdeletion",'contextadmin'));
$this->table->addRow($arr, "even", "colspan=\"2\"");

$arr = array($objLanguage->languageText('label_coursecode','contextadmin'));
$attr="\"<input type=\"text\" name=\"courseCode\" value=".$_REQUEST['courseCode'].""; 
$this->table->addRow($arr, "even",$attr);

//echo $this->table->show();

$this->button->name ="action";
$this->button->setValue('yes');
$this->button->setCSS('button');
$this->button->setToSubmit();


$this->button1->name ="action";
$this->button1->setValue('no');
$this->button1->setCSS('button');
$this->button1->setToSubmit();

echo "<br>";
echo $outSt;
$this->form->addToForm($this->table->show()."<br>".$this->button->show()."&nbsp;&nbsp;".$this->button1->show());
          
echo $this->form->show();
?>