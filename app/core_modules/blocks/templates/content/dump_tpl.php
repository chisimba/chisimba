<?php

echo '<pre>';
print_r($blocks);
echo '</pre>';

if (count($blocks) == 0) {
    
} else {
    foreach ($blocks as $block)
    {
        echo $this->objDynamicBlocks->showBlockFromArray($block);
    }
}

?>