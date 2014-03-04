<?php

$this->setVar('canvas', '_default');
$cssLayout = & $this->newObject('csslayout', 'htmlelements'); // Set columns to 2
$cssLayout->setNumColumns(2);
$story = $this->objDbNews->getStory($storyid);
$viewer = $this->getObject("viewer");
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
$cssLayout->setLeftColumnContent($leftSideContent);
$cssLayout->setMiddleColumnContent($story['storytext']);

echo $cssLayout->show();
?>
