<?php
echo $this->getJavascriptFile('TreeMenu.js','tree');
$tree =  $this->newObject('sharednodes', 'contextcontent');
$objHeading =  $this->newObject('htmlheading','htmlelements');
$objForm =  $this->newObject('form','htmlelements');
$objInput =  $this->newObject('textinput','htmlelements');
$button= $this->newObject ('button','htmlelements');

$this->objH = $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1;
$this->objH->str=$this->objLanguage->languageText('mod_contextadmin_importcontent');
print $this->objH->show();


 $this->objH->type=3;
 $this->objH->str=$this->objLanguage->languageText('mod_contextadmin_importselectnode');
print $this->objH->show();

//the form
$objForm->name='frmnodes';
$objForm->setAction($this->uri(array('action'=>'showimportnode','contextId' => $this->getParam('contextId')),'contextadmin'));
$objForm->setDisplayType(3);

//the submit button
$button->name = 'nextbutton';
$button->setToSubmit();
$button->setValue($this->objLanguage->languageText('mod_context_next'));

$objInput->name='inpnodeId';
$objInput->fldType = 'hidden';
$objInput->value = null;
$objForm->addRule('inpnodeId', $this->objLanguage->languageText('mod_context_err_selectnode'), 'required');

$objForm->addToForm($objInput);
$objForm->addToForm('<div name="div1" id="div1">'.$button->show().'</div>');
print $objForm->show();
print $tree->biuld($this->objDBContext->getContextId());
//print 'show nodes';
?>
<script language="javascript" type="text/javascript">
    document.getElementById("div1").style.visibility = 'hidden';
    function nodeInput(nodeId)
    {
        document.frmnodes.inpnodeId.value = nodeId;
        document.getElementById("div1").style.visibility = 'visible';
    }
</script>