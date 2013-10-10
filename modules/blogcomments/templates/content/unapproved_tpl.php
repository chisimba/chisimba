<?php

$heading = $this->objLanguage->languageText('mod_blogcomments_moderate_unapproved_comments', 'blogcomments');
$post = $this->objLanguage->languageText('mod_blogcomments_post', 'blogcomments');
$author = $this->objLanguage->languageText('mod_blogcomments_author', 'blogcomments');
$email = $this->objLanguage->languageText('mod_blogcomments_email', 'blogcomments');
$url = $this->objLanguage->languageText('mod_blogcomments_url', 'blogcomments');
$date = $this->objLanguage->languageText('mod_blogcomments_date', 'blogcomments');
$ip = $this->objLanguage->languageText('mod_blogcomments_ip', 'blogcomments');
$userAgent = $this->objLanguage->languageText('mod_blogcomments_user_agent', 'blogcomments');
$approve = $this->objLanguage->languageText('mod_blogcomments_approve', 'blogcomments');
$delete = $this->objLanguage->languageText('mod_blogcomments_delete', 'blogcomments');
$noAction = $this->objLanguage->languageText('mod_blogcomments_no_action', 'blogcomments');
$moderate = $this->objLanguage->languageText('mod_blogcomments_moderate', 'blogcomments');
$noModComments = $this->objLanguage->languageText('mod_blogcomments_nomodcomments', 'blogcomments');

$html = '<h1>'.$heading.'</h1>';

if (count($comments) > 0) {
    $html .= '<form method="post">';

    foreach ($comments as $comment) {
        $html .= '<div style="margin-top:20px;border-top:2px solid black">';
        $html .= '<ul>';
        $html .= '<li>'.$post.': <a href="'.$comment['link'].'">'.htmlspecialchars($comment['post']['post_title']).'</a></li>';
        $html .= '<li>'.$author.': '.htmlspecialchars($comment['comment_author']).'</li>';
        $html .= '<li>'.$email.': '.htmlspecialchars($comment['comment_author_email']).'</li>';
        $html .= '<li>'.$url.': '.htmlspecialchars($comment['comment_author_url']).'</li>';
        $html .= '<li>'.$date.': '.date('Y-m-d H:i:s', $comment['comment_date']).'</li>';
        $html .= '<li>'.$ip.': '.htmlspecialchars($comment['comment_author_ip']).'</li>';
        $html .= '<li>'.$userAgent.': '.htmlspecialchars($comment['comment_agent']).'</li>';
        $html .= '</ul>';
        $html .= $comment['comment_content'];
        $html .= '<p><input name="comment['.$comment['id'].']" type="radio" value="approve" /> '.$approve;
        $html .= '<input name="comment['.$comment['id'].']" type="radio" value="delete" /> '.$delete;
        $html .= '<input name="comment['.$comment['id'].']" type="radio" value="" checked /> '.$noAction.'</p>';
        $html .= '</div>';
    }

    $html .= '<p style="margin-top:20px;padding-top:20px;border-top:2px solid black"><input type="submit" value="'.$moderate.'" /></p></form>';
} else {
    $html .= '<p style="margin-top:20px;">'.$noModComments.'</p>';
}

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);
$objUi = $this->getObject('blogui', 'blog');
$left = $objUi->leftBlocks($this->objUser->userid());
$cssLayout->setMiddleColumnContent($html);
$cssLayout->setLeftColumnContent($left);
echo $cssLayout->show();
