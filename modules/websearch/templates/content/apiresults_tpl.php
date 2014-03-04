<?php
if (isset($str)) {
   echo $str; 
}
$objRender = $this->getObject('rendersearchresults');
echo $objRender->render($ar, $pages, $count);
?>