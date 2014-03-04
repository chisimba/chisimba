<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$objBlocks = $this->getObject('blockfilter', 'dynamiccanvas');
$pageContent = $this->getVar('pageContent');
$pageContent = $objBlocks->parse($pageContent);
echo $pageContent;
?>
