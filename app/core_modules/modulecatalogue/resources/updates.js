/**

**/
jQuery(document).ready(function(){
    
    });
//
jQuery(".patchLink").click(function(){
    elementID = jQuery(this).attr('id')
    jQuery.ajax({
        url: 'index.php?module=modulecatalogue&action=update&mod='+jQuery(this).attr('id')+'&patchver='+jQuery(this).attr('value'),
        type: 'POST',
        data: '',
        success:  jQuery(this).parent().html("<span class='success' >Update successful</span>")
    })
    
    //hide the paragraph tag
    setTimeout(function(){
        jQuery("#div_updates").children('p#'+elementID).hide('slow')
    }, 9000)
    
    setTimeout(function(){
        valueA = jQuery("#div_updates").children('p').length;
        valueB = jQuery("#div_updates").children(":hidden").length;
        //if there is one update paragraph element left, hide the update all link
        if(valueA - valueB  == 1){
            jQuery("#div_updates").children("#linkUpdateAll").hide('slow')
        }
        //if all update paragraph tags are hidden, diplay message indicating no updates available
        if(jQuery("#div_updates").children('p:visible').length == 0){
            jQuery("#div_updates").html("<p>There are no updates available at pesent")
        }
    },10200)
});

//Apply all patches
jQuery("#linkUpdateAll").live('click',function(){
    jQuery.ajax({
        url: 'index.php?module=modulecatalogue&action=patchall',
        type: 'POST',
        success:  jQuery('#div_updates').html("Please wait....")
    });
    setTimeout(function(){
        jQuery('#div_updates').html("<span class='success' >All updates applied successfully.</span>");
    },12000)
    setTimeout(function(){
        jQuery('#div_updates').html('There are no updates available at present');
    },20200);
})