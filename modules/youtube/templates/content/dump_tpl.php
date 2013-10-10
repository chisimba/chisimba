<?php
if (isset($str)) {
   if(isset($debug)) {
       echo "<pre>$str</pre>";
   } else {
       echo $str;
   }
}
?>