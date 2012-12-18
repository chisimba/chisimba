/**

**/
jQuery(document).ready(function(){
    
    });
//
jQuery(".patchLink").live('click',function(){
     jQuery.ajax({
        url: 'index.php?module=modulecatalogue&action=update&mod='+jQuery(this).attr('id')+'&patchver='+jQuery(this).attr('value'),
        type: 'POST',
        data: '',
        success: function(){
            return TRUE;
        }
    })
    //hide the paragraph tag
   jQuery(this).parent().hide('slow')
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
    },700)
});
//Apply all patches
jQuery("#linkUpdateAll").live('click',function(){
        jQuery.ajax({
            url: 'index.php?module=modulecatalogue&action=patchall',
            type: 'POST',
            success:  jQuery(this).closest('div').html("<span class='success' >All updates applied successfully.</span>")
        })
})