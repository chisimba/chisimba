<?php
 /*
 * @category  Chisimba
 * @package   apo (Academic Planning Office)
 * @author    Jacqueline Gil
 */
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$form = new form('finishdocument');

$label = new label();
$label->labelValue = "You have finished filling in the form. <br>To edit select OK and click on the Title of the course name.</br> <br>To submit the form select the form in the documents panel and press submit.</br>";

$legend = "<b>Finished</b>";
$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($label->show());
$form->addToForm($fs->show());

$button = new button('ok', $this->objLanguage->languageText('word_ok'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>