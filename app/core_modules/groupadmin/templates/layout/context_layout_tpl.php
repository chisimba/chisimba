<?php

$headerParams=$this->getJavascriptFile('x.js','postlogin');
$headerParams.="
<script type=\"text/javascript\">        

function adjustLayout()
{
     var leftnavHeight = 0;
     var rightnavHeight = 0;
     var contentHeight = 0;
     
     if (document.getElementById('leftnav')) {
         leftnavHeight = document.getElementById('leftnav').offsetHeight;
     }
     
     
     if (document.getElementById('contentHasLeftMenu')) {
         contentHeight = document.getElementById('contentHasLeftMenu').offsetHeight;
     }
     
     biggestHeight = Math.max(leftnavHeight, contentHeight);
     
     
     if (biggestHeight > contentHeight) {
         document.getElementById('contentHasLeftMenu').style.height = biggestHeight+\"px\";
    } 
}

window.onload = function()
{
  xAddEventListener(window, \"resize\",
    adjustLayout, false);
  adjustLayout();
}

</script>";

$this->appendArrayVar('headerParams',$headerParams);

    echo '<div id="leftnav">';
    $this->userMenuBar=& $this->getObject('contextmenu','toolbar');
    echo $this->userMenuBar->show();
    echo '</div><!-- End div leftnav -->';

    echo ('<div id="contentHasLeftMenu">');

    // get content
	echo $this->getContent(); 
    
    echo ('</div><!-- End div contentHasLeftMenu -->');
?>
