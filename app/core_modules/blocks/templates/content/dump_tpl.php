<style type="text/css">
.blockblock {
	float: left;
	margin: 5px;
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
if (count ( $blocks ) == 0) {
    echo '<div class="error">' . $this->objLanguage->languageText ( 'mod_blocks_blocknotfound', 'blocks', 'Block not found!' ) . '</div>';
} else {
    foreach ( $blocks as $block ) {
        if ($block ['blocksize'] == 'wide') {
            $class = 'wideblock';
        } else {
            $class = 'smallblock';
        }
        echo '<div class="blockblock ' . $class . ' ">' . $block ['module'] . ' / ' . $block ['typeofblock'] . '<br />' . $this->objDynamicBlocks->showBlockFromArray ( $block );
        echo '</div>';
    }
}

?>