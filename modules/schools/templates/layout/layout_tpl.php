<?php
/**
*
*  A layout template for schools
*  Author: Kevin Cyster kcyster@gmail.com
*  Date: March 10, 2012, 3:20 pm
*
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