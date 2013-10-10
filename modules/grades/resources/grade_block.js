/* 
 * Javascript to support grades
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: March 17, 2012, 5:59 pm
 *
 * The following parameters need to be set in the
 * PHP code for this to work:
 *
 * @todo
 *   List your parameters here so you won't forget to add them
 *
 */

/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {

    
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        
    });

    jQuery('#input_grade_id').change( function() {
        if (jQuery('#input_grade_id').val() != '')
        {
            var id = jQuery("#input_grade_id").val();
            var mydata = "grade_id=" + id;
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=grades&action=ajaxShowSubject",
                data: mydata,
                success: function(ret) {
                    jQuery("#subjectdiv").html(ret); 
                }
            });
        }
        else
        {
            jQuery("#subjectdiv").html(''); 
            jQuery("#contextdiv").html(''); 
            jQuery("#buttondiv").hide(); 
            return false;
        }
    });
    
    jQuery('#input_subject_id').live( 'change', function() {
        if (jQuery('#input_subject_id').val() != '')
        {
            var id = jQuery("#input_subject_id").val();
            var mydata = "subject_id=" + id;
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=grades&action=ajaxShowContext",
                data: mydata,
                success: function(ret) {
                    jQuery("#contextdiv").html(ret); 
                }
            });
        }
        else
        {
            jQuery("#contextdiv").html(''); 
            jQuery("#buttondiv").hide(); 
            return false;
        }
    });
    
    jQuery('#input_contextcode').live( 'change', function() {
        if (jQuery('#input_contextcode').val() != '')
        {
            jQuery('#buttondiv').show();
        }
        else
        {
            jQuery('#buttondiv').hide();
        }
    });
    
});