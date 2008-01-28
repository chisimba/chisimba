<style type="text/css">
.blockblock {
    float: left; margin: 5px;
}
.infoblock {
    display: none;
}
.wideblock {
    width: 500px;
}
.smallblock {
    width: 200px;
}
</style>
<?php


if (count($blocks) == 0) {
    
} else {
    foreach ($blocks as $block)
    {
        if ($block['blocksize'] == 'wide') {
            $class = 'wideblock';
        } else {
            $class = 'smallblock';
        }
        
        echo '<div class="blockblock '.$class.' ">'.$block['module'].' / '.$block['typeofblock'].'<br />'.$this->objDynamicBlocks->showBlockFromArray($block);
        
        /*
        echo '<a href="javascript:jQuery(\'#'.$block['id'].'\').toggle();">Show/Hide</a><div id="'.$block['id'].'" class="infoblock">';
        echo '<pre>';
        print_r($block);
        echo '</pre>';
        echo '</div>';
        */
        
        echo '</div>';
    }
}

?>