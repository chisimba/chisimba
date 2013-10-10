<?php


//set the layout of the choosequestiontype template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');


//Load the classes for the template

$this->loadclass('htmltable','htmlelements');
$this->loadclass('htmlheading','htmlelements');
$this->loadclass('geticon','htmlelements');
$this->loadclass('link','htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('fieldsetex', 'htmlelements');

$this->dbQuestions = $this->newObject('dbquestions');


//Set the language items
$choosetype=$this->objLanguage->languageText('mod_mcqtests_choosetype','mcqtests');
$typeLabel=$this->objLanguage->languageText('mod_mcqtests_typelabel','mcqtests');
$mcqtestLabel=$this->objLanguage->languageText('mod_mcqtests_mcqtestlabel','mcqtests');
$clozetestLabel=$this->objLanguage->languageText('mod_mcqtests_clozetestlabel','mcqtests');
$freeformLabel=$this->objLanguage->languageText('mod_mcqtests_freeformlabel','mcqtests');
$selectLabel=$this->objLanguage->languageText('mod_mcqtests_selectlabel','mcqtests');
//get the addicon
$objIcon=$this->newObject('geticon', 'htmlelements');
$count = count($questions);
if (empty($questions)) {
    $count = 0;
}

$batchOptions = new dropdown('qnoption');
$batchOptions->setId("qnoption");
$batchOptions->addOption('-', '[-Select question type-]');
$batchOptions->addOption('mcq', 'MCQ questions');
$batchOptions->addOption('freeform', 'Free form test entry questions');
$batchLabel = new label ('Select question type ', 'input_qnoptionlabel');

echo '<strong><h1>'.$test['name'].'</h1></strong>';
$fd=$this->getObject('fieldsetex','htmlelements');

$fd->addLabel('<strong>'.$batchLabel->show().'</strong>'.$batchOptions->show());
$fd->setLegend('Select question type');
$formmanager=$this->getObject('formmanager');

$questionContentStr='<div id="addquestion">'.$formmanager->createAddQuestionForm($test).'</div>';
$questionContentStr.='<div id="freeform">'.$formmanager->createAddFreeForm($test).'</div>';

$fd->addLabel($questionContentStr);
echo $fd->show();



?>
<script type="text/javascript" language="javascript">
    //<![CDATA[

    jQuery(document).ready(function() {

        jQuery('#freeform').hide();
        jQuery('#addquestion').hide();
        jQuery("#qnoption").change(function(){
            var val=this.value;

            if(val == 'freeform'){
                jQuery('#freeform').show();
                jQuery('#addquestion').hide();
            }else if(val == 'mcq'){
                jQuery('#addquestion').show();
                jQuery('#freeform').hide();
            }else{
                jQuery('#freeform').hide();
                jQuery('#addquestion').hide();
            }

        });

        // check the type of question selected.
        jQuery("#input_typemcq").bind('click',function(){
            myVal = jQuery("#input_options").val();
            jQuery("select[name=options] option[value=4]").attr("selected", true);
            jQuery("select[name=options]").attr("disabled", "");
        });

        jQuery("#input_typetf").bind('click',function(){
            myVal = jQuery("#input_options").val();
            jQuery("select[name=options] option[value=2]").attr("selected", true);
            jQuery("select[name=options]").attr("disabled", "disabled");
        });

        // when submitting the form, remove the hidden attribute if input type is true/false
        jQuery('form').bind('submit', function() {
            if(jQuery("#input_typetf:checked").val() == "tf") {
                jQuery(this).find(':input').removeAttr('disabled');
            }
        });
    });

    function processQuestionType()
    {
        if (document.getElementById('input_qnoption').value == '-')
        {
            alert('Please select an action');
            document.getElementById('input_qnoption').focus();
        }  else {
            //document.getElementById('form_qnform').submit();
            document.getElementById('input_qnoptionlabel').textContent='Updated!';
        }
    }
    //]]>
</script>