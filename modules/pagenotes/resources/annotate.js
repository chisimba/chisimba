/* 
 * Javascript to support pagenotes
 *
 * Written by Derek Keats <derek@dkeats.com>
 * STarted on: February 23, 2012, 12:06 pm
 *
 */

/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {
    jQuery(document).ready(function() {
        // Apply annotations to this css id only.
        annotaterInit("#Canvas_Content_Body_Region2 p");
    });
    
    var annotations;
    function annotaterInit(cssSelector) {
        var options = {};
        options.form = '<ul><li><input type="checkbox" name="errorType" value="1" /> ' + interest + '</li><li><input type="checkbox" name="errorType" value="2" /> ' + noimp + '</li><li><textarea class="annotate_input" name="comment"></textarea></li></ul>';
        if (window.annotation !== undefined) {
            //alert(annotation);
            options.annotations = JSON.parse(annotation);
        }
        options.onInit=function(global_annotations){
            annotations = global_annotations;
        }
        options.onAddAnnotation=function(addedAnnotation, global_annotations){
            /* add a Word-style comment */
            addBasicWordStyleComment(addedAnnotation);
        }
        options.onRemoveAnnotation=function(removedAnnotation, global_annotations){
            /* remove the Word-style comment */
            removeBasicWordStyleComment(removedAnnotation);
        }
        options.onSaveAnnotation=function(savedAnnotation, serializedForm){
            /* update the Word-style comment */
            updateBasicWordStyleComment(savedAnnotation);
        }
        jQuery(cssSelector).textAnnotate(options);
        jQuery(cssSelector).disableTextSelect();
        jQuery('#annotationDefinitionsLoaded').val(JSON.stringify(options.annotations));
    }
    
    function save(){
        jQuery('#annotation').val(JSON.stringify(annotations));
        return true;
    }			

    /**
     * Adds a Word-style comment on the right of the screen
     **/			 			
    function addBasicWordStyleComment(annotation){
        var comment = _getAnnotationComment(annotation);


        /* get comment */
        if (comment){
            /* put comment in container */
            var firstElement = jQuery('#'+annotation[0].elementId);
            var firstElementTop  = firstElement.offset().top;
            var firstElementHeight = firstElement.height(); 
            var firstElementLeft = firstElement.offset().left;

            var commentContainer = jQuery('<div class="annotator_comment"></div>');
            commentContainer.html(comment);
            jQuery('body').append(commentContainer);
            jQuery(commentContainer).css({
                "top":firstElementTop+firstElementHeight, 
                "right": 0
            });						
            var commentContainerLeft = commentContainer.offset().left;						

            var commentLine = jQuery('<div class="comment_line"></div>');
            jQuery('body').append(commentLine);						
            jQuery(commentLine).css({
                "top":firstElementTop+firstElementHeight, 
                "left": firstElementLeft, 
                "width":(commentContainerLeft-firstElementLeft)
                });
            /*console.log(global_annotations);*/

            /* store it in first element, so it can be removed */
            firstElement.data('WordStyleComment', {
                "container": commentContainer, 
                "line": commentLine
            });
        }					
    }

    /**
     * Removes a Word-style comment on the right of the screen
     **/
    function removeBasicWordStyleComment(annotation){
        var firstElement = jQuery('#'+annotation[0].elementId);
        var wordStyleComment = firstElement.data('WordStyleComment');
        if (wordStyleComment){
            jQuery(wordStyleComment.container).remove();
            jQuery(wordStyleComment.line).remove();						
        }
    }

    /**
     * Updates the Word-style comment
     **/			 			
    function updateBasicWordStyleComment(annotation){
        var firstElement = jQuery('#'+annotation[0].elementId);				
        var wordStyleComment = firstElement.data('WordStyleComment');
        if (wordStyleComment==undefined) {
            addBasicWordStyleComment(annotation)
        }else{					
            var comment = _getAnnotationComment(annotation);
            jQuery(wordStyleComment.container).html(comment);
        }
    }

    /**
     * Fetches a comment from an annotation
     **/			 			
    function _getAnnotationComment(annotation){
        var comment;
        if (annotation[0]){
            var formValues = annotation[0].formValues;
            if (formValues){
                for (var i=0; i<formValues.length; i++){
                    if (formValues[i].name=='comment'){
                        comment = formValues[i].value;
                        break;
                    }
                }
            }
        }	
        return comment;	
    }
    
    jQuery("#saveAnnotations").click(function(e) {
        e.preventDefault();
        save();
        data_string = jQuery("#form_annotations").serialize();
        //alert("DATA: " + data_string);
        jQuery.ajax({
                url: 'index.php?module=pagenotes&action=saveannotations',
                type: "POST",
                data: data_string,
                success: function(msg) {
                    //alert(msg);
                    jQuery("#saveAnnotations").attr("disabled", "");
                    if(msg !== "ERROR_DATA_INSERT_FAIL") {
                        // Update the information area 
                        // (msg is the id of the record on success)
                        jQuery("#save_results_annotation").html('<span class="success">' + status_successa + '</span>').fadeOut(3000, function() {
                            // Animation complete, reset the div
                            jQuery("#save_results_annotation").html("").show();
                        });
                        // Change the id field to be the id that is returned as msg & mode to edit
                        jQuery("#id").val(msg);
                        jQuery("#annotation_mode").val('edit');
                    } else {
                        //alert(msg);
                        jQuery("#save_results_annotation").html('<span class="error">' + status_faila + '</span>').fadeOut(3000, function() {
                            // Animation complete, reset the div
                            jQuery("#save_results_annotation").html("").show();
                        });
                    }
                }
            });
    });
    
    // Toggle the visibility of the annotations
    jQuery("#toggleAnnotations").click(function(e) {
        e.preventDefault();
        jQuery(".annotator_comment").toggle();
        jQuery(".comment_line").toggle();
    });

});