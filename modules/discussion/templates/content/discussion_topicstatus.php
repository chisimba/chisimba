<?PHP
//Sending display to 1 column layout
ob_start();
$js='
<script language="JavaScript" type="text/javascript">

if(!document.getElementById && document.all)
document.getElementById = function(id){ return document.all[id]}


    function showReasonForm()
    {
        if (document.topicStatusForm.topic_status[1].checked)
            {
                    showhide(\'closeReason\', \'block\');
            } else{
                    showhide(\'closeReason\', \'none\');
            }

    }

    function showhide (id, visible)
    {
        var itemstyle = document.getElementById(id).style
        itemstyle.display = visible;
        /*
        if (visible = \'hidden\')
        {
            itemstyle.style.display = \'none\';
        } else {
            itemstyle.style.display = \'block\';
        }*/
    }
</script>';
echo $js;


$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');


$icon=$this->newObject('geticon','htmlelements');
$icon->setIcon($topic['type_icon'], NULL, 'icons/discussion/');
$icon->alt= $topic['type_name'];
$icon->title=$topic['type_name'];

$header = new htmlheading();
$header->type=1;
$header->str=$icon->show().' '.$this->objLanguage->languageText('mod_discussion_topicstatus').': '.stripslashes($topic['post_title']);
echo $header->show();

echo ('<blockquote>');

/*
if ($topic['status'] == 'OPEN') {
    echo ('<p><strong>'.$topic['post_title'].'</strong> is currently open to replies.</p>');
} else {
    echo ('currently closed');
}*/

$topicStatusForm = new form('topicStatusForm', $this->uri( array('module'=>'discussion', 'action'=>'changetopicstatus')));

$objElement = new radio('topic_status');
$objElement->addOption('OPEN','<strong>'.$this->objLanguage->languageText('word_open').'</strong> - '.$this->objLanguage->languageText('mod_discussion_allowusersreply'));
$objElement->addOption('CLOSE','<strong>'.$this->objLanguage->languageText('word_close').'</strong> - '.$this->objLanguage->languageText('mod_discussion_preventusersreply'));

if ($topic['topicstatus'] == 'OPEN') {
    $objElement->setSelected('OPEN');
} else {
    $objElement->setSelected('CLOSE');
}
$objElement->extra = ' onClick="showReasonForm()"';
$objElement->setBreakSpace('<br />');
$topicStatusForm->addToForm('<p>'.$objElement->show().'</p>');

$topicStatusForm->addToForm('<div id="closeReason">');

$header = new htmlheading();
$header->type=3;
$header->str=$this->objLanguage->languageText('mod_discussion_providereason');
$topicStatusForm->addToForm($header->show());

$editor=&$this->newObject('htmlarea','htmlelements');
$editor->setName('reason');
$editor->setContent('');
$editor->setRows(10);
$editor->setColumns('100%');
$editor->setContent($topic['lockReason']);
$editor->context = TRUE;

$topicStatusForm->addToForm( $editor);

$topicStatusForm->addToForm('</div>');

$submitButton = new button('submitform', $this->objLanguage->languageText('mod_discussion_savetopicstatus'));
$submitButton->cssClass = 'save';
$submitButton->setToSubmit();

$topicStatusForm->addToForm('<p>'.$submitButton->show().'</p>');

$topicHiddenInput = new textinput('topic');
$topicHiddenInput->fldType = 'hidden';
$topicHiddenInput->value = $topic['topic_id'];
$topicStatusForm->addToForm($topicHiddenInput->show());

echo ($topicStatusForm->show());

echo ('</blockquote>');

$discussionLink = new link($this->uri(array( 'module'=> 'discussion', 'action' => 'discussion', 'id' => $topic['discussion_id'])));
$discussionLink->link = $this->objLanguage->languageText('mod_discussion_backtodiscussion');
$discussionLink->title = $this->objLanguage->languageText('mod_discussion_backtodiscussion');

echo $discussionLink->show();

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);
?>