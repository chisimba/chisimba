/* 
 * Javascript to support adaptation add/edit forms
 *
 * @author Paul Mungai
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
        jQuery("#form_adaptationForm1").validate();
        jQuery("#form_adaptationForm2").validate();
        jQuery("#form_adaptationForm3").validate();
        jQuery("#form_adaptationForm4").validate();
        
        
        jQuery('ul#nav-secondary li').click(function() {
            jQuery(this).html ('<img src="skins/_common/icons/loading_bar.gif" alt=""Loading..." />');
          
        });
        
        
    });
    
    
    
    jQuery("a[class=confirmdeleteadaptation]").click(function(){

        var answer=confirm(confirm_delete_adaptation);
        if (answer==true){
            var link = this.href;
            window.location=link;
        }
        return false;
    });
    
    

    
    
    // Function for saving the institutional data
    jQuery("#form_originalProductForm").submit(function(e) {
        if(jQuery("#form_originalProductForm").valid()){ 
           
            e.preventDefault();
            jQuery("#submitInstitution").attr("disabled", "disabled");
            jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt=""Saving..." />');
            data_string = jQuery("#form_originalProductForm").serialize();
            jQuery.ajax({
                url: 'index.php?module=oer&action=saveoriginalproduct',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    jQuery("#submitInstitution").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area 
                        // (msg is the id of the record on success)
                        jQuery("#save_results").html('<span class="success">' + status_success + ": " + msg + '</span>');
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        jQuery("#mode").val('edit');
                        alert("relocating....");
                        window.location="?module=oer&action=editoriginalproductstep5";
                    } else {
                        //alert(msg);
                        alert(status_fail);
                    }
                }
            });
        }
    });

});

/**
 * Submits step 1 details
 */
function saveStep1(){
    if(jQuery("#form_adaptationForm1").valid()){
        jQuery("#saveStep1Button").attr("disabled", "disabled");
        jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt="'+loading+'" />');
        data_string = jQuery("#form_adaptationForm1").serialize();
        jQuery.ajax({
            url: 'index.php?module=oer&action=saveadaptationstep1',
            type: "POST",
            data: data_string,
            success: function(msg) {
                jQuery("#saveStep1Button").attr("disabled", "");
                if(msg !== "ERROR_DATA_INSERT_FAIL") {
                    var id=msg;
                    jQuery("#save_results").html('<span class="success">' + status_success + '</span>');
                       
                    window.location="?module=oer&action=editadaptationstep2&id="+id;
                } else {
                    alert(status_fail);
                }
            }
        });
    }
    return false;
}
        
/**
 * Submits step 2 details
 */
function saveStep2(){
    if(jQuery("#form_adaptationForm2").valid()){
        jQuery("#saveStep2Button").attr("disabled", "disabled");
        jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt="'+loading+'" />');
                data_string = jQuery("#form_adaptationForm2").serialize();
        jQuery.ajax({
            url: 'index.php?module=oer&action=saveadaptationstep2',
            type: "POST",
            data: data_string,
            success: function(msg) {
                jQuery("#saveStep1Button").attr("disabled", "");
                if(msg !== "ERROR_DATA_INSERT_FAIL") {
                   
                    var id=msg;
                    jQuery("#save_results").html('<span class="success">' + status_success + '</span>');
                    window.location="?module=oer&action=editadaptationstep3&id="+id;
                } else {
                    alert(status_fail);
                }
            }
        });
    }
    return false;
}
    
/**
 * Submits step 3 details
 */
function saveStep3(){
    if(jQuery("#form_adaptationForm3").valid()){
        jQuery("#saveStep3Button").attr("disabled", "disabled");
        jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt="'+loading+'" />');
               data_string = jQuery("#form_adaptationForm3").serialize();
        jQuery.ajax({
            url: 'index.php?module=oer&action=saveadaptationstep3',
            type: "POST",
            data: data_string,
            success: function(msg) {
                jQuery("#saveStep3Button").attr("disabled", "");
                if(msg !== "ERROR_DATA_INSERT_FAIL") {
                    var id=msg;
                    jQuery("#save_results").html('<span class="success">' + status_success + '</span>');
                    window.location="?module=oer&action=editadaptationstep4&id="+id;
                } else {
                    alert(status_fail);
                }
            }
        });
    }
    return false;
}
    

/**
 * Submits step 4 details
 */
function saveStep4(){
    if(jQuery("#form_adaptationForm4").valid()){
        jQuery("#saveStep4Button").attr("disabled", "disabled");
        jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt="'+loading+'" />');
                data_string = jQuery("#form_adaptationForm4").serialize();
        jQuery.ajax({
            url: 'index.php?module=oer&action=saveoriginalproductstep4',
            type: "POST",
            data: data_string,
            success: function(msg) {
                jQuery("#saveStep1Button").attr("disabled", "");
                if(msg !== "ERROR_DATA_INSERT_FAIL") {
                    var id=msg;
                    jQuery("#save_results").html('<span class="success">' + status_success + '</span>');
                    window.location="?module=oer&action=editadaptationstep4&id="+id;
                } else {
                    alert(status_fail);
                }
            }
        });
    }
    return false;
}

function updateStep1(){
    if(jQuery("#form_adaptationForm1").valid()){
        jQuery("#saveStep1Button").attr("disabled", "disabled");
        jQuery("#save_results").html('<img src="skins/_common/icons/loading_bar.gif" alt="'+loading+'" />');
        data_string = jQuery("#form_adaptationForm1").serialize();
        jQuery.ajax({
            url: 'index.php?module=oer&action=updateoriginalproductstep1',
            type: "POST",
            data: data_string,
            success: function(msg) {
                jQuery("#saveStep1Button").attr("disabled", "");
                if(msg !== "ERROR_DATA_INSERT_FAIL") {

                    var id=msg;
                    jQuery("#save_results").html('<span class="success">' + status_success + '</span>');
                    window.location="?module=oer&action=editoriginalproductstep2&id="+id;
                } else {
                    alert(status_fail);
                }
            }
        });
    }
    return false;
}

    