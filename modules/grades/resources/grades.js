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

    jQuery('#addgradelink').click(function() {
       jQuery('#gradesformdiv').toggle();
       jQuery('#addgradesdiv').toggle();
       if (jQuery('#gradestablediv').length > 0)
       {
           jQuery('#gradestablediv').toggle();
       }
    });
    
    jQuery('#cancel_grade').live('click', function() {
       
       jQuery('#gradesformdiv').toggle();
       jQuery('#addgradesdiv').toggle();
       if (jQuery('#gradestablediv').length > 0)
       {
           jQuery('#gradestablediv').toggle();
       }
    });
    
    jQuery('#save_grade').live('click', function() {
       if (jQuery('#input_grade_id').val() == '')
       {
           alert (no_grade);
           return false;
       }
       else
       {
           jQuery('#form_grade').submit();
       }
    });

    jQuery('#addstrandlink').click(function() {
       jQuery('#strandsformdiv').toggle();
       jQuery('#addstrandsdiv').toggle();
       if (jQuery('#strandstablediv').length > 0)
       {
           jQuery('#strandstablediv').toggle();
       }
    });
    
    jQuery('#cancel_strand').live('click', function() {
       
       jQuery('#strandsformdiv').toggle();
       jQuery('#addstrandsdiv').toggle();
       if (jQuery('#strandstablediv').length > 0)
       {
           jQuery('#strandstablediv').toggle();
       }
    });
    
    jQuery('#save_strand').live('click', function() {
       if (jQuery('#input_strand_id').val() == '')
       {
           alert (no_strand);
           return false;
       }
       else
       {
           jQuery('#form_strand').submit();
       }
    });

    jQuery('#addclasslink').click(function() {
       jQuery('#classesformdiv').toggle();
       jQuery('#addclassesdiv').toggle();
       if (jQuery('#classestablediv').length > 0)
       {
           jQuery('#classestablediv').toggle();
       }
    });
    
    jQuery('#cancel_class').live('click', function() {
       
       jQuery('#classesformdiv').toggle();
       jQuery('#addclassesdiv').toggle();
       if (jQuery('#classestablediv').length > 0)
       {
           jQuery('#classestablediv').toggle();
       }
    });
    
    jQuery('#save_class').live('click', function() {
       if (jQuery('#input_class_id').val() == '')
       {
           alert (no_class);
           return false;
       }
       else
       {
           jQuery('#form_class').submit();
       }
    });

    jQuery('#addcontextlink').click(function() {
       jQuery('#contextsformdiv').toggle();
       jQuery('#addcontextsdiv').toggle();
       if (jQuery('#contextstablediv').length > 0)
       {
           jQuery('#contextstablediv').toggle();
       }
    });
    
    jQuery('#cancel_context').live('click', function() {
       
       jQuery('#contextsformdiv').toggle();
       jQuery('#addcontextsdiv').toggle();
       if (jQuery('#contextstablediv').length > 0)
       {
           jQuery('#contextstablediv').toggle();
       }
    });
    
    jQuery('#save_context').live('click', function() {
       if (jQuery('#input_context_id').val() == '')
       {
           alert (no_context);
           return false;
       }
       else
       {
           jQuery('#form_context').submit();
       }
    });

    jQuery('#addsubjectlink').click(function() {
       jQuery('#subjectsformdiv').toggle();
       jQuery('#addsubjectsdiv').toggle();
       if (jQuery('#subjectstablediv').length > 0)
       {
           jQuery('#subjectstablediv').toggle();
       }
    });
    
    jQuery('#cancel_subject').live('click', function() {
       
       jQuery('#subjectsformdiv').toggle();
       jQuery('#addsubjectsdiv').toggle();
       if (jQuery('#subjectstablediv').length > 0)
       {
           jQuery('#subjectstablediv').toggle();
       }
    });
    
    jQuery('#save_subject').live('click', function() {
       if (jQuery('#input_subject_id').val() == '')
       {
           alert (no_subject);
           return false;
       }
       else
       {
           jQuery('#form_subject').submit();
       }
    });

    jQuery('#addschoollink').click(function() {
       jQuery('#schoolsformdiv').toggle();
       jQuery('#addschoolsdiv').toggle();
       if (jQuery('#schoolstablediv').length > 0)
       {
           jQuery('#schoolstablediv').toggle();
       }
    });
    
    jQuery('#cancel_school').live('click', function() {
       
       jQuery('#schoolsformdiv').toggle();
       jQuery('#addschoolsdiv').toggle();
       if (jQuery('#schoolstablediv').length > 0)
       {
           jQuery('#schoolstablediv').toggle();
       }
    });
    
    jQuery('#save_school').live('click', function() {
       if (jQuery('#input_school_id').val() == '')
       {
           alert (no_school);
           return false;
       }
       else
       {
           jQuery('#form_school').submit();
       }
    });
    
});