/* 
 * Javascript to support group edit
 *
 * @author Derek Keats derek@dkeats.com
 * @author Paul Mungai paulwando@gmail.com
 * Started on: December 18, 2011, 8:48 am
 *
 *
 */

/**
 *
 * jQuery code belongs inside this function.
 *
 */
jQuery(function() {

    // Things to do on loading the page.
    jQuery(document).ready(function() {
        // Add jQuery Validation to form
        jQuery("#form_downloadereditor").validate({
            rules: {
                field: {
                    required: true,
                    email: true
                }
            }
        });
        jQuery("#form_downloadproductform").validate();
        jQuery("#save_results").hide();
    });

    // Function for saving the downloader data
    jQuery("#form_downloadereditor").submit(function(e) {
        if(jQuery("#form_downloadereditor").valid()){
            e.preventDefault();
            var productid = jQuery("#productid").val();
            var producttype = jQuery("#producttype").val();
            jQuery("#submit").attr("disabled", "disabled");
            jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
            jQuery("#save_results").show();
            data_string = jQuery("#form_downloadereditor").serialize();
            
            jQuery.ajax({
                url: 'index.php?module=oer&action=downloadersave',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submit").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area
                        // (msg is the id of the record on success)
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        var id=msg;
                        jQuery("#mode").val('edit');
                        jQuery("#downloaderinfoform").hide();
                        jQuery("#save_results").html('<span class="success">' + status_success + ": " + msg + '</span>');
                        jQuery("#downloadproductform").show();
                        window.location="?module=oer&action=downloaderedit&id="+id+"&productid="+productid+"&producttype="+producttype+"&mode=edit";
                    } else {
                        //alert(msg);
                        alert(status_fail);
                    }
                }
            });
        }
    });
    // Function for saving the downloader data
    jQuery("#form_downloadproductform").submit(function(e) {
        if(jQuery("#form_downloadproductform").valid()){
            e.preventDefault();
            var productid = jQuery("#productid").val();
            var producttype = jQuery("#producttype").val();
            var id = jQuery("#id").val();
            var downloadformat = jQuery('input[name=downloadformat]:checked').val();
            jQuery("#submit").attr("disabled", "disabled");
            jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
            jQuery("#save_results").show();
            data_string = jQuery("#form_downloadproductform").serialize();
            jQuery.ajax({
                url: 'index.php?module=oer&action=printproduct&'+id+'&productid='+productid+'&downloadformat='+downloadformat+'&producttype='+producttype+'&mode=edit',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submit").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {                        
                        // Update the information area
                        // (msg is the id of the record on success)
                        if(msg !=""){
                            jQuery("#save_results").html('<span class="success">Document printed successfully</span>');
                        } else {
                            jQuery("#save_results").html('<span class="success">Unfortunately an error was encountered.</span>');
                        }
                        
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        jQuery("#mode").val('edit');
                        if(msg !=""){
                            //window.open(msg,"","width=450,height=300,status=yes,toolbar=no,menubar=no");
                            //window.open(msg,"_blank");
                            window.location=msg;
                        }
                    } else {
                        //alert(msg);
                        jQuery("#save_results").html('<span class="fail">Unfortunately an error was encountered.</span>');
                    //alert(status_fail);
                    }
                }
            });
        }
    });
});