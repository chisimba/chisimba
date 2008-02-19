
    // Flag Variable - Update message or not
    var doUpdateMessage = false;
    
    
    var leftBlock = false;
    var rightBlock = false;
    var middleBlock = false;
    
    
    var inEditMode = false;
    
    // Var Current Entered Code
    var currentCode;
    
    // Action to be taken once page has loaded
    jQuery(document).ready(function(){
        
        jQuery(".block").prepend('<div class="blockoptions"><a class="moveup" href="javascript:;">'+upIcon+
            '</a> <a class="movedown" href="javascript:;">'+downIcon+'</a>'+
            '<a class="deleteblock" href="javascript:;">'+deleteIcon+'</a> </div>');
        
        if (inEditMode) {
            jQuery("#rightaddblock").show();
            jQuery("#middleaddblock").show();
            jQuery(".blockoptions").show();
        } else {
            jQuery("#rightaddblock").hide();
            jQuery("#middleaddblock").hide();
            jQuery(".blockoptions").hide();
        }
        
        
        setUpSide('right');
        
        
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
            if (confirm(deleteConfirm)) {
                removeBlock(jQuery(this).parent().parent().attr('id'));
            }
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
    
    function setUpSide(side)
    {
        jQuery("#dd"+side+"blocks").bind('change', function() {
            getPreview(jQuery("#dd"+side+"blocks").attr('value'), 'right');
        });
        
        jQuery("#"+side+"button").hide();
        jQuery("#"+side+"button").bind('click', function() {
            addBlock(window[side+'Block'], side)
        });
        
        jQuery("#"+side+"blocks > :first-child").livequery(function() {
            jQuery("#"+side+"blocks .moveup").show();
            jQuery("#"+side+"blocks .movedown").show();
            jQuery("#"+side+"blocks > :first-child a.moveup").hide();
            jQuery("#"+side+"blocks > :last-child a.movedown").hide();
        });
        
        jQuery("#"+side+"blocks > :last-child").livequery(function() {
            jQuery("#"+side+"blocks .moveup").show();
            jQuery("#"+side+"blocks .movedown").show();
            jQuery("#"+side+"blocks > :last-child a.movedown").hide();
            jQuery("#"+side+"blocks > :first-child a.moveup").hide();
        });
    }

    
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
                    alert(unableMoveBlock);
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
                    alert(unableDeleteBlock);
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
                    alert(unableAddBlock);
                } else {
                    jQuery("#"+side+"previewcontent .block").attr('id', msg);
                    
                    // First Add Up/Down/Delete
                    jQuery("#"+side+"previewcontent .block").prepend('<div class="blockoptions"><a class="moveup" href="javascript:;">'+upIcon+
            '</a> <a class="movedown" href="javascript:;">'+downIcon+'</a>'+
            '<a class="deleteblock" href="javascript:;">'+deleteIcon+'</a> </div>');
                    
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
            jQuery("#editmodeswitchbutton").attr('value', turnEditingOn);
            jQuery(".blockoptions").hide();
            
            inEditMode = false;
        } else {
            jQuery("#rightaddblock").show();
            jQuery("#middleaddblock").show();
            jQuery("#editmodeswitchbutton").attr('value', turnEditingOff);
            jQuery(".blockoptions").show();
            
            inEditMode = true;
        }
        
        adjustLayout();
    }

