<?php

// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">';

$this->setVar('canvas', '_default');
$viewer = $this->getObject("viewer");
$cssLayout = & $this->newObject('csslayout', 'htmlelements'); // Set columns to 2
$cssLayout->setNumColumns(3);
$competitions = $viewer->getNews('competitionsblock');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$index = 0;
$leftSideContent = "";
foreach ($competitions as $competition) {
    $title = $competition['storytitle'];
    $link = new link($this->uri(array("action" => "viewstory", "storyid" => $competition['id'])));
    $link->link = $competition['storytext'];
    $content = $link->show();
    $block = "competitions" . $index++;
    $hidden = 'default';
    $showToggle = false;
    $showTitle = true;
    $cssClass = "featurebox";
    $leftSideContent.=$objFeatureBox->show(
                    $title,
                    $content,
                    $block,
                    $hidden,
                    $showToggle,
                    $showTitle,
                    $cssClass, '');
}
$centerContent = "";
$featurednewsstories = $viewer->getNews('featurednews');
$objTrim = $this->getObject('trimstr', 'strings');
$index = 0;
foreach ($featurednewsstories as $featurednewsstory) {
    if ($index > 2) {
        break;
    }
    $title = $featurednewsstory['storytitle'];
    $link = new link($this->uri(array("action" => "viewstory", "storyid" => $featurednewsstory['id'])));
    $link->link = "<div id='readmore'>Read More</div>";
    $content = $objTrim->strTrim($featurednewsstory['storytext'].'...', 2400) . $link->show();
    $block = "featurednews" . $index++;
    $hidden = 'default';
    $showToggle = FALSE;
    $showTitle = TRUE;
    $cssClass = "featurebox";
    $centerContent.=$objFeatureBox->show(
                    $title,
                    $content,
                    $block,
                    $hidden,
                    $showToggle,
                    $showTitle,
                    $cssClass, '');
    $index++;
}

$centerContent.=$viewer->getBottomBlocks();
$rightSideColumn = "";
$rightadverts = $viewer->getNews('rightadverts');
foreach ($rightadverts as $rightadvert) {
    $title = $rightadvert['storytitle'];
    $link = new link($this->uri(array("action" => "viewstory", "storyid" => $featurednewsstory['id'])));
    $link->link = $rightadvert['storytext'];
    $content = $link->show();
    $block = "rightadverts" . $index++;
    $hidden = 'default';
    $showToggle = FALSE;
    $showTitle = TRUE;
    $cssClass = "featurebox";
    $rightSideColumn.=$objFeatureBox->show(
                    $title,
                    $content,
                    $block,
                    $hidden,
                    $showToggle,
                    $showTitle,
                    $cssClass, '');
}
$cssLayout->setLeftColumnContent($leftSideContent);
$cssLayout->setMiddleColumnContent($centerContent);
$cssLayout->setRightColumnContent($rightSideColumn);

echo $cssLayout->show();
?>
