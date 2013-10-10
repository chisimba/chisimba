<?php
/*
* Template for the update bar in pbl_tpl.php
*/

$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$path=$this->uri(array('action'=>'showboard'));
$path2=$this->uri(array('action'=>'showtasks'));

?>
 <script language='JavaScript'>
        <!--
    <? echo $this->script2; ?>
        // -->

    <!-- Hide this from older browsers
    // This script was supplied by Hypergurl
    // http://www.hypergurl.com
    <!-- Begin
        var message='Update Case and Tasks!! ';
        var speed=400;
        var visible=0;

        function flash() {
        if (visible == 0) {
            document.updateboard.style='color:red';
            visible=1;
        } else {
            document.updateboard.style='color:green';
            visible=0;
        }
        setTimeout('flash()', speed);
        }
    // End -->
    // end hide -->

        function alert(){
            for(i=1; i<100; i++){   
                document.write("Update Case");
            }
        }

    </script>
<body onload='flash()'>
<?php
    $colour = $this->getSession('color');
    $colour2 = $this->getSession('color2');
    
    if(!isset($colour)){
        $color = 'green';
    }else{
        $color = $colour;
    }
    if(!isset($colour2)){
        $color2 = 'green';
    }else{
        $color2 = $colour2;
    }
    
    $update = $this->objLanguage->languagetext('mod_pbl_updateboard','pbl');
    $content = $this->objLanguage->languagetext('mod_pbl_updatecontent','pbl');
    
    $objLink = new link($this->uri(array('action'=>'refresh')));
    $objLink->name = 'updateboard';
    $objLink->cssId = 'updateboard';
    $objLink->link = $update;
    $objLink->target = 'board';
    $objLink->style = "color:$color ";
    $link1 = "&nbsp;".$objLink->show();
    
    $objLink = new link($this->uri(array('action'=>'showcontent')));
    $objLink->name = 'updatecontent';
    $objLink->cssId = 'updatecontent';
    $objLink->link = $content;
    $objLink->target = 'content';
    $objLink->style = "color:$color2 ";
    $link2 = $objLink->show().'&nbsp;';

    $table = new htmltable();
    $table->startRow();
    $table->addCell($link1,'40%','center','left');
//    $table->addCell($link2,'60%','center','right');
    $table->endRow();

    // display links
    echo $table->show();
?>