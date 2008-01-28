<?php

// Add JavaScript if User can update blocks
if ($this->isValid('addblock')) {
    
    $objIcon = $this->newObject('geticon', 'htmlelements');
    $objIcon->setIcon('up');
    $upIcon = $objIcon->show();
    
    
    $objIcon->setIcon('down');
    $downIcon = $objIcon->show();
    
    $objIcon->setIcon('delete');
    $deleteIcon = $objIcon->show();
?>
<style type="text/css">
.blockoptions { text-align: right; }
#editmode {padding: 20px; text-align:center; }
</style>
<script type="text/javascript">
// <![CDATA[

    // Flag Variable - Update message or not
    var doUpdateMessage = false;
    
    
    var rightBlock = false;
    var middleBlock = false;
    
    
    var inEditMode = false;
    
    // Var Current Entered Code
    var currentCode;
    
    // Action to be taken once page has loaded
    jQuery(document).ready(function(){
        
        jQuery(".block").prepend('<div class="blockoptions"><a class="moveup" href="javascript:;">'+
            '<?php echo $upIcon; ?></a> <a class="movedown" href="javascript:;"><?php echo $downIcon; ?></a>'+
            '<a class="deleteblock" href="javascript:;"><?php echo $deleteIcon; ?></a> </div>');
        
        if (inEditMode) {
            jQuery("#rightaddblock").show();
            jQuery("#middleaddblock").show();
            jQuery(".blockoptions").show();
        } else {
            jQuery("#rightaddblock").hide();
            jQuery("#middleaddblock").hide();
            jQuery(".blockoptions").hide();
        }
        
        jQuery("#ddrightblocks").bind('change', function() {
            getPreview(jQuery("#ddrightblocks").attr('value'), 'right');
        });
        jQuery("#rightbutton").hide();
        jQuery("#rightbutton").bind('click', function() {
            addBlock(rightBlock, 'right')
        });
        
        
        jQuery("#ddmiddleblocks").bind('change', function() {
            getPreview(jQuery("#ddmiddleblocks").attr('value'), 'middle');
        });
        jQuery("#middlebutton").hide();
        jQuery("#middlebutton").bind('click', function() {
            addBlock(middleBlock, 'middle')
        });
        
        
        
        jQuery(".moveup").livequery('click', function() {
            
            moveBlock(jQuery(this).parent().parent().attr('id'), 'up');
        });
        
        jQuery(".movedown").livequery('click', function() {
            moveBlock(jQuery(this).parent().parent().attr('id'), 'down');
        });
        
        
        jQuery(".deleteblock").livequery('click', function() {
            if (confirm("<?php echo $objLanguage->languageText('mod_context_confirmremoveblock', 'context', 'Are you sure you want to remove the block'); ?>")) {
                removeBlock(jQuery(this).parent().parent().attr('id'));
            }
        });
        
        
        
        
        jQuery("#rightblocks > :first-child").livequery(function() {
            jQuery('#rightblocks .moveup').show();
            jQuery('#rightblocks .movedown').show();
            jQuery("#rightblocks > :first-child a.moveup").hide();
            jQuery("#rightblocks > :last-child a.movedown").hide();
        });
        
        jQuery("#rightblocks > :last-child").livequery(function() {
            jQuery('#rightblocks .moveup').show();
            jQuery('#rightblocks .movedown').show();
            jQuery("#rightblocks > :last-child a.movedown").hide();
            jQuery("#rightblocks > :first-child a.moveup").hide();
        });
        
        jQuery("#middleblocks > :first-child").livequery(function() {
            jQuery('#middleblocks .moveup').show();
            jQuery('#middleblocks .movedown').show();
            jQuery("#middleblocks > :first-child a.moveup").hide();
            jQuery("#middleblocks > :last-child a.movedown").hide();
        });
        
        jQuery("#middleblocks > :last-child").livequery(function() {
            jQuery('#middleblocks .moveup').show();
            jQuery('#middleblocks .movedown').show();
            jQuery("#middleblocks > :last-child a.movedown").hide();
            jQuery("#middleblocks > :first-child a.moveup").hide();
        });
        
        adjustLayout();
        
        
    });

    
    function moveBlock(blockId, direction)
    {
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=moveblock&blockid="+blockId+'&direction='+direction,
            success: function(msg){
            
                if (msg == 'ok') {
                    if (direction == 'up') {
                        
                        var div = jQuery('#'+blockId).insertBefore(jQuery('#'+blockId).prev());
                    } else {
                        var div = jQuery('#'+blockId).insertAfter(jQuery('#'+blockId).next());
                    }
                    

                } else {
                    alert('<?php echo $objLanguage->languageText('mod_context_unablemoveblock', 'context', 'Error - Unable to move block'); ?>');
                }
                
                adjustLayout();
            }
        });
        
        
    }
    
    function removeBlock(blockId)
    {
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=removeblock&blockid="+blockId,
            success: function(msg){
            
                if (msg == 'ok') {
                    jQuery('#'+blockId).remove();
                } else {
                    alert('<?php echo $objLanguage->languageText('mod_context_unabledeleteblock', 'context', 'Error - Unable to delete block'); ?>');
                }
                
                adjustLayout();
            }
        });
    }
    
    function addBlock(blockid, side)
    {
        // DO Ajax
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=addblock&blockid="+blockid+"&side="+side, 
            success: function(msg){
            
                if (msg == '') {
                    alert('<?php echo $objLanguage->languageText('mod_context_unableaddblock', 'context', 'Error - Unable to add block'); ?>');
                } else {
                    jQuery("#"+side+"previewcontent .block").attr('id', msg);
                    
                    // First Add Up/Down/Delete
                    jQuery("#"+side+"previewcontent .block").prepend('<div class="blockoptions"><a class="moveup" href="javascript:;">'+
            '<?php echo $upIcon; ?></a> <a class="movedown" href="javascript:;"><?php echo $downIcon; ?></a>'+
            '<a class="deleteblock" href="javascript:;"><?php echo $deleteIcon; ?></a> </div>');
                    
                    // Then Attach
                    jQuery("#"+side+"previewcontent .block").appendTo("#"+side+"blocks");
                    jQuery("#"+side+"button").hide();
                    

                }
                
                adjustLayout();
            }
        });
    }
    
    function getPreview(blockid, side)
    {
        jQuery("#"+side+"button").hide();
        adjustLayout();
            
        if (blockid=="") {
            jQuery("#"+side+"previewcontent").hide();
            jQuery("#"+side+"button").hide();
            adjustLayout();
        } else {
        
            // DO Ajax
            jQuery.ajax({
                type: "GET", 
                url: "index.php", 
                data: "module=context&action=renderblock&blockid="+blockid+"&side="+side, 
                success: function(msg){
                
                    jQuery("#"+side+"previewcontent").show();
                    jQuery("#"+side+"previewcontent").html(msg);
                    
                    if (side == 'right') {rightBlock = blockid; }
                    if (side == 'middle') {middleBlock = blockid; }
                    
                    if (msg != "") {
                        jQuery("#"+side+"button").show();
                    }
                    
                    adjustLayout();
                }
            });
        }
        
    }
    
    function switchEditMode()
    {
        if (inEditMode) {
            jQuery("#rightaddblock").hide();
            jQuery("#middleaddblock").hide();
            jQuery("#editmodeswitchbutton").attr('value', '<?php echo $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On'); ?>');
            jQuery(".blockoptions").hide();
            
            inEditMode = false;
        } else {
            jQuery("#rightaddblock").show();
            jQuery("#middleaddblock").show();
            jQuery("#editmodeswitchbutton").attr('value', '<?php echo $objLanguage->languageText('mod_context_turneditingoff', 'context', 'Turn Editing Off'); ?>');
            jQuery(".blockoptions").show();
            
            inEditMode = true;
        }
        
        adjustLayout();
    }

// ]]>
</script>
<?php

} // End Addition of JavaScript

$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$objCssLayout = $this->getObject('csslayout', 'htmlelements');
$objCssLayout->setNumColumns(3);

if ($this->isValid('addblock')) {

    $smallBlocksDropDown = new dropdown ('rightblocks');
    $smallBlocksDropDown->cssId = 'ddrightblocks';
    $smallBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'phrase', 'Select One').'...');
    
    
    foreach ($smallDynamicBlocks as $smallBlock)
    {
        $smallBlocksDropDown->addOption('dynamicblock|'.$smallBlock['id'].'|'.$smallBlock['module'], htmlentities($smallBlock['title']));
    }
    
    foreach ($smallBlocks as $smallBlock)
    {
        $block = $this->newObject('block_'.$smallBlock['blockname'], $smallBlock['moduleid']);
        $title = $block->title;
        
        $smallBlocksDropDown->addOption('block|'.$smallBlock['blockname'].'|'.$smallBlock['moduleid'], htmlentities($title));
    }
    
    
    $wideBlocksDropDown = new dropdown ('middleblocks');
    $wideBlocksDropDown->cssId = 'ddmiddleblocks';
    $wideBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'phrase', 'Select One').'...');
    
    foreach ($wideDynamicBlocks as $wideBlock)
    {
        $wideBlocksDropDown->addOption('dynamicblock|'.$wideBlock['id'].'|'.$wideBlock['module'], htmlentities($wideBlock['title']));
    }
    
    foreach ($wideBlocks as $wideBlock)
    {
        $block = $this->newObject('block_'.$wideBlock['blockname'], $wideBlock['moduleid']);
        $title = $block->title;
        
        $wideBlocksDropDown->addOption('block|'.$wideBlock['blockname'].'|'.$wideBlock['moduleid'], htmlentities($title));
    }
    
    
    $button = new button ('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'prelogin', 'Add Block'));
    $button->cssId = 'rightbutton';
    
    
    $editOnButton = new button ('editonbutton', $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On'));
    $editOnButton->cssId = 'editmodeswitchbutton';
    $editOnButton->setOnClick("switchEditMode();");

}

$header = new htmlheading();
$header->type = 3;
$header->str = $objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');

$toolbar = $this->getObject('contextsidebar');
$objCssLayout->setLeftColumnContent($toolbar->show());

$objCssLayout->rightColumnContent = '';

if ($this->isValid('addblock')) {
    $objCssLayout->rightColumnContent .= '<div id="editmode">'.$editOnButton->show().'</div>';
}
$objCssLayout->rightColumnContent .= '<div id="rightblocks">'.$rightBlocksStr.'</div>';

if ($this->isValid('addblock')) {
    $objCssLayout->rightColumnContent .= '<div id="rightaddblock">'.$header->show().$smallBlocksDropDown->show();
    $objCssLayout->rightColumnContent .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> '.$button->show().' </div>';
    $objCssLayout->rightColumnContent .= '</div>';
}

$button = new button ('addmiddleblock', $objLanguage->languageText('mod_prelogin_addblock', 'prelogin', 'Add Block'));
$button->cssId = 'middlebutton';

$objCssLayout->middleColumnContent = '<div id="middleblocks">'.$middleBlocksStr.'</div>';

if ($this->isValid('addblock')) {
    $objCssLayout->middleColumnContent .= '<div id="middleaddblock">'.$header->show().$wideBlocksDropDown->show();
    $objCssLayout->middleColumnContent .= '<div id="middlepreview"><div id="middlepreviewcontent"></div> '.$button->show().' </div>';
    $objCssLayout->middleColumnContent .= '</div>';
}

echo $objCssLayout->show();







?>