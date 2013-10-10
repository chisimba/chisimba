<script type="text/javascript">
//<![CDATA[
function init () {
    $('input_redraw').onclick = function () {
        redraw();
    }
}
function redraw () {
    var url = 'index.php';
    var pars = 'module=security&action=generatenewcaptcha';
    var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showResponse} );
}
function showLoad () {
    $('load').style.display = 'block';
}
function showResponse (originalRequest) {
    var newData = originalRequest.responseText;
    $('captchaDiv').innerHTML = newData;
}
//]]>
</script>
<?php
// check if the site signup user string is set, if so, use it to populate the fields
if (isset($userstring)) {
    $userstring = base64_decode($userstring);
    $userstring = explode(',', $userstring);
} else {
    $userstring = NULL;
}
$this->loadClass('form', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("phrase_registeron", 'liftclub', "Register On") . ' ' . $this->objConfig->getSitename() . '&nbsp;( ' . $this->objLanguage->languageText("phrase_step", 'liftclub', "Step") . " 1 )";
echo '<div style="padding:10px;">' . $header->show();
$form = new form('startregister', $this->uri(array(
    'action' => 'modifydetails'
)));
$messages = array();
$table = $this->newObject('htmltable', 'htmlelements');
$table->width = '100%';
$userneedRadio = new radio('userneed');
$userneedRadio->addOption('find', "" . $this->objLanguage->languageText('phrase_find', 'liftclub', 'Find'));
$userneedRadio->addOption('offer', $this->objLanguage->languageText('phrase_offer', 'liftclub', 'Offer'));
$userneedRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;');
if ($mode == 'addfixup') {
    $userneedRadio->setSelected($this->getSession('userneed'));
} else {
    $userneedRadio->setSelected('find');
}
$userneedLabel = $this->objLanguage->languageText('phrase_iwanto', 'liftclub', 'I want tossss') . '&nbsp;';
$needtypeDropdown = new dropdown('needtype');
$needtypeLabel = new label($this->objLanguage->languageText('phrase_thefollowing', 'liftclub', 'the following') . '&nbsp;', 'input_register_title');
$titles = array(
    "phrase_carpool",
    "phrase_trip",
    "phrase_schoolpool"
);
foreach($titles as $title) {
    $_title = trim($this->objLanguage->languageText($title, 'liftclub'));
    $needtypeDropdown->addOption($_title, $_title);
}
if ($mode == 'addfixup') {
    $needtypeDropdown->setSelected($this->getSession('needtype'));
}
$table->startRow();
$table->addCell($userneedLabel . $userneedRadio->show() . "&nbsp;&nbsp;--&nbsp;&nbsp;" . $needtypeLabel->show() . "&nbsp;" . $needtypeDropdown->show() , 50, NULL, 'left');
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_membership', 'liftclub', 'Membership Information');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
$button = new button('submitform', 'Step 2');
$button->setToSubmit();
$form->addToForm('<p align="left">' . $button->show() . '</p><br/ ><br/ >');
echo $form->show();
echo '</div>';
?>
