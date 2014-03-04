<?php

$link = $this->getObject('link', 'htmlelements');
$objThumbnail = & $this->getObject('thumbnails', 'filemanager');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$h = $this->getObject('htmlheading', 'htmlelements');
$form = $this->getObject('form', 'htmlelements');
$strComment = '';

$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$showcomments = $objSysConfig->getValue('SHOW_PHOTO_COMMENTS_FORM', 'photogallery');
$scripts = '<script type="text/javascript" src="' . $this->_objConfig->getModuleURI() . 'photogallery/resources/lightbox/js/prototype.js"></script>
<script type="text/javascript" src="' . $this->_objConfig->getModuleURI() . 'photogallery/resources/lightbox/js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="' . $this->_objConfig->getModuleURI() . 'photogallery/resources/lightbox/js/lightbox.js"></script>
<link rel="stylesheet" href="' . $this->_objConfig->getModuleURI() . 'photogallery/resources/lightbox/css/lightbox.css" type="text/css" media="screen" />';
$this->appendArrayVar('headerParams', $scripts);
$str = '<div id="image">';
//$link->href = $this->uri(array('action' => 'viewimage', 'imageid' => $image['id']));

$loggedIn = $this->_objUser->isLoggedIn();
if (!$loggedIn) {
?>
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

}
// Get image display size - no need to resize it its small anyway
$info = getimagesize($this->_objFileMan->getFullFilePath($image['file_id']));
if (isset($info[0])) {
    $width = $info[0];
} else {
    $width = 500;
}
if ($width > 500) {
    $width = 500;
}

$filename = $this->_objFileMan->getFileName($image['file_id']);
$path = $objThumbnail->getThumbnail($image['file_id'], $filename);
$bigPath = $this->_objFileMan->getFilePath($image['file_id']);

$link->href = $bigPath;
$link->link = '<img title="' . $image['title'] . '" src="' . $bigPath . '" alt="' . $image['title'] . '" width="' . $width . '" />';
$link->extra = ' rel="lightbox" ';
$str.=$link->show() . '</div>';



// Add Comment Form
$form->action = $this->uri(array('action' => 'addcomment', 'imageid' => $this->getParam('imageid'), 'albumid' => $this->getParam('albumid')));

$name = new textinput('name');
if ($loggedIn) {
    $name->value = $this->_objUser->fullname();
} else {
    $name->value = $this->getParam('name');
}
$form->addRule('name', $this->objLanguage->languagetext('mod_photogallery_needname', 'photogallery'), 'required');

$table = new htmltable();
$table->width = '200';
$table->startRow();
$table->addCell('<label for="name">' . $this->objLanguage->languagetext('mod_photogallery_givename', 'photogallery') . ':</label>');
$table->addCell($name->show());
$table->endRow();

$email = new textinput('email');
if ($loggedIn) {
    $email->value = $this->_objUser->email();
} else {
    $email->value = $this->getParam('email');
}

$table->startRow();
$table->addCell('<label for="email">' . $this->objLanguage->languagetext('mod_photogallery_email', 'photogallery') . ':</label>');
$table->addCell($email->show());
$table->endRow();

$website = new textinput('website');
$website->value = $this->getParam('website');


$table->startRow();
$table->addCell('<label for="website">' . $this->objLanguage->languagetext('mod_photogallery_site', 'photogallery') . ':</label>');
$table->addCell($website->show());
$table->endRow();

$commentField = new textarea('comment');
$commentField->value = $this->getParam('comment');
$button = new button();
$button->value = $this->objLanguage->languagetext('mod_photogallery_postcomment', 'photogallery');
$button->setToSubmit();


$this->setVar('pageTitle', 'Photo Gallery - ' . $this->_objDBAlbum->getAlbumTitle($this->getParam('albumid')) . ' - ' . $image['title']);

$form->addToForm('<h3>' . $this->objLanguage->languagetext('mod_photogallery_postcomment', 'photogallery') . '</h3>' . $table->show());
$form->addToForm($commentField->show());


if (!$loggedIn) {
    $objCaptcha = $this->getObject('captcha', 'utilities');
    $captcha = new textinput('request_captcha');
    //$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'input_request_captcha');
    $fieldset = $this->newObject('fieldset', 'htmlelements');
    $fieldset->legend = 'Verify Image';
    $fieldset->contents = stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')) . '<br /><div id="captchaDiv">' . $objCaptcha->show() . '</div>' . $captcha->show() . '  <a href="javascript:redraw();">' . $this->objLanguage->languageText('word_redraw', 'security', 'Redraw') . '</a>';
    $form->addToForm($fieldset->show());
}

$form->addToForm('<br/>' . $button->show());

if (count($comments) > 0) {
    $strComment = '<h3>Comments (' . count($comments) . ')</h3>';
    foreach ($comments as $comment) {
        $strComment .= '<div class="comment"><div class="commentmeta"><span class="commentauthor">' . $comment['name'] . '</span> says:';
        $strComment .= '</div>	<div class="commentbody">' . $comment['comment'] . '</div><div class="commentdate">';
        $strComment .= $comment['commentdate'] . '</div>	</div>';
    }
}
$link->extra = '';
$link->href = $this->uri(null, 'photogallery');
$link->link = 'Photo Gallery';
$galLink = $link->show();

$link->href = $this->uri(array('action' => 'viewalbum', 'albumid' => $this->getParam('albumid')), 'photogallery');
$link->link = $this->_objDBAlbum->getAlbumTitle($this->getParam('albumid'));
$albumLink = $link->show();

$nav = $this->_objUtils->getImageNav($image['id']);

$head = '<div id="main2">' . $nav . '<div id="gallerytitle">
		<h2><span>' . $galLink . ' | </span> <span>' . $albumLink . '
		| </span>' . $image['title'] . '
		</h2></div>

	';


$desc = ($image['description'] == '') ? '[add a description]' : $image['description'];

$ajax = "<span class=\"subdued\" id=\"description\">[add a description]</span>
						<script>
						
						        new Ajax.InPlaceEditor('description', 'index.php', { callback: function(form, value) { return 'module=photogallery&action=saveimage&imageid=" . $image['id'] . "&field=description&myparam=' + escape(value) }})
						</script>";

$desc = '<div id="narrow"><div id="imageDesc" style="display: block;">' . /* $ajax */$image['description'] . '</div>';

print $head;
echo $desc;
echo $str;
if ($showcomments) {
//echo $desc;
    if ($showcomments == 'true' || $showcomments == 'TRUE') {
        echo $strComment;
        echo $form->show();
    }
} else {
    echo $strComment;
    echo $form->show();
}
echo '</div></div>';
?>
