<?php

//$middleColumn = $this->getVar('middleColumn');
//$middleColumn = "<div class='forum_main'>$middleColumn</div>";
//$cssLayout = $this->newObject('csslayout', 'htmlelements');
//$cssLayout->setNumColumns(1);
//$cssLayout->setMiddleColumnContent($middleColumn);
//// Display the Layout
//$objModule = $this->getObject('modules', 'modulecatalogue');
//$isRegistered = $objModule->checkIfRegistered('oer');
//if ($isRegistered) {
//    echo '<div id="threecolumn">' . $cssLayout->show() . '</div>';
//} else {
//    echo $cssLayout->show();
//}
?>
<?php

/**
 *  A layout template for use with dynamic canvas content. This layout
 *  template is required in any module that will be parsing JSON templates.
 *  See demo_tpl.php for an example of a JSON template. Modules using JSON
 *  templates render all their content as blocks. No direct rendering to
 *  templates is done.
 *
 */
$objBlocks = $this->getObject('blockfilter', 'dynamiccanvas');
$pageContent = $this->getVar('pageContent');
$pageContent = $objBlocks->parse($pageContent);
echo $pageContent;
?>