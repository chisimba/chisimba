<?php
  header("Content-Type: text/html;charset=utf-8");
  
  $objImView = $this->getObject('viewer');
  echo $objImView->renderOutputForBrowser($records);
  exit();
