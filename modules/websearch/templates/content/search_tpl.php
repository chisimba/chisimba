<?php


if (isset($str)) {
   
   $output = "<table align=\"center\"><tr><td>" 
   . $str . "</td><td>" 
   . $str2 . "</td></tr></table>";
   
   $table = $this->newObject('htmltable','htmlelements');
   $table->width='100%';
   $table->border='0';
   $table->cellspacing = '10';
   $table->cellpadding ='10';
   
   $table->startRow();
   $table->addCell("<div align=\"center\">"  .'<b />'. $output . "</div>");
   $table->endRow();
   
   echo "<div align=\"center\">" .'<br />'  . $table->show() . "</div>";
    
}


?>