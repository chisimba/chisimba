<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('word_keyword', 'word', 'Keyword').': '.$keyword;
echo $header->show();

$objTrimString = $this->getObject('trimstr', 'strings');
$objThumbnails = $this->getObject('thumbnails', 'filemanager');
$objDateTime = $this->getObject('dateandtime', 'utilities');

$output = '';

$int = 'WEEK';
$fdate = $this->objKeywords->getLastNewsStoryDate($keyword);
$timeline = $this->uri(array('action'=>'generatekeywordtimeline', 'id'=>$keyword));
$timeline = str_replace('&amp;', '&', $timeline);
$objIframe = $this->getObject('iframe', 'htmlelements');
$objIframe->width = "100%";
$objIframe->height="310";
$ret = $this->uri(array("mode" => "plain",
      "action" => "viewtimeline", 
      "timeLine" => ($timeline),
      //"timeLine" => urlencode($timeline),
      "intervalUnit" => $int,
      "focusDate" => $fdate,
      "tlHeight" => '300'), "timeline");
$objIframe->src=$ret;
$objIframe->frameborder=0;
$objIframe->id='timelineiframe';
$objIframe->scrolling='no';
echo $objIframe->show();

foreach ($stories as $story)
{
    
    $output .= '<div class="newsstory">';
    
    $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
    $storyLink->link = $story['storytitle'];
    
    if ($story['storyimage'] != '') {
        $storyLink->link = '<img class="storyimage" src="'.$objThumbnails->getThumbnail($story['storyimage'], $story['filename']).'" alt="'.$story['storytitle'].'" title="'.$story['storytitle'].'" />';
        
        $output .= '<div class="storyimagewrapper">'.$storyLink->show().'</div>';
    }
    
    $storyLink->link = $story['storytitle'];
    
    $output .= '<h3>'.$objDateTime->formatDateOnly($story['storydate']).' - '.$storyLink->show().'</h3>';
    
    if ($story['location'] != '') {
        $locationLink = new link ($this->uri(array('action'=>'viewbylocation', 'id'=>$story['storylocation'])));
        $locationLink->link = $story['location'];
        //$output .= '[ '.$locationLink->show().' ] ';
        $output .= '[ '.$story['location'].' ] ';
    }
    
    $output .= $objTrimString->strTrim(strip_tags($story['storytext']), 150, TRUE);
    
    $storyLink->link = $this->objLanguage->languageText('mod_news_readstory', 'news', 'Read Story');
    $output .= ' ('.$storyLink->show().')';

}
echo $output;
?>