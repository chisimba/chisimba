/* 
 * Javascript to support add/edit note
 *
 * Written by Nguni Phakela nguni52@gmail.com
 * Started on: April 30, 2012, 5:51 pm
 *
 */

jQuery(function() {
    // Things to do on loading the page.
    jQuery(document).ready(function() {
        jQuery("#responsearea").hide();
    });
});


function checkFormSubmit() {
    // update ckeditor elements
    for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
    validateForm();
}

/*
 * This function validates the form elements. 
 *
 */
function validateForm() {
    var message = "Are you sure you want to save this note without any tags?";
    var myTitle = jQuery("#input_title").val().trim();
    
    if(myTitle.length == 0) {
        alert("Please enter the title");
        jQuery("#input_title").focus();
        return false;
    }
    
    var myTags = jQuery("#input_tags").val().trim();
    if(myTags.length == 0) {
        var answer = confirm(message);
        if (!answer){
            jQuery("#input_tags").focus();
            return false;
        }
    }
    var checked = jQuery("#input_public").is(':checked');
    var myPublic = "false";
    if(checked) {
        myPublic = "true";
    }
    
    var myContent = CKEDITOR.instances.NoteContent.getData().trim();
    
    if(myContent.length == 0) {
        alert("Please enter content");
        return false;
    }
    
    // ajax submit form
    ajaxSubmitForm(myTitle, myTags, myPublic, myContent);
    
    return true;
}

/*
 * This function is used to submit the form using ajax when adding or editing a
 * note. It appends a message about the success/failure of the submit, which 
 * then fades out slowly.
 * 
 */
function ajaxSubmitForm(myTitle, myTags, myPublic, myContent) {
    var myUrl = jQuery("#input_uri").val();
    var myId = jQuery("#input_id").val();
    
    jQuery.ajax({
        type: "POST",
        url: myUrl,
        data: {
            title: myTitle, 
            tags: myTags,
            isPublic: myPublic,
            content: myContent, 
            id: myId
        },
        success: function(data) {
            if(data == "FALSE") {
                jQuery("#responsearea").fadeIn("There was a problem saving your note");
            } else {
                //alert(data);
                jQuery("#responsearea").text("Your note was saved Successfully");
                jQuery("#responsearea").show();
                jQuery('#responsearea').fadeOut(5000);
                
                jQuery("#input_id").val(data);
                tmpUrl = jQuery("#input_uri").val();
                var mode = jQuery.getUrlVar('mode', tmpUrl);
                
                if(mode == "add") {
                    
                    // Put in the link to view the current page.
                    var baseUri = 'index.php?module=mynotes&action=showNote&id=' + data;
                    baseUri = '<a href="' + baseUri + '">View this note</a>';
                    jQuery("#mynotes_dyn").append(baseUri);
                    
                    // change mode so that we now editing data, not adding new 
                    // data
                    tmpUrl = tmpUrl.replace("add", "edit");
                    tmpUrl += "&id=" + data;
                    jQuery("#input_uri").val(tmpUrl);
                }
                
                // refresh the tags
                //refreshTags();
            }
        },
        error: function() {
            jQuery("#responsearea").fadeIn("There was a problem saving your note");
        }
    });
}

/* 
 * create jQuery plugin to retrieve the param values from query string
 * Usage: getUrlVars(url)
 * 
 * then to get a specific variable from url
 * var allVars = $.getUrlVars();
 * myVar = allVars["name"];
 *  
 * Usage: $.getUrlVar("name");
 * 
 * var byName = $.getUrlVar("name");
 *    
*/
;(function ($) {
    $.extend({
        getUrlVars: function(myUrl){
            var vars = [], hash;
            var hashes = myUrl.slice(myUrl.indexOf('?') + 1).split('&');
            for(var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        },
        getUrlVar: function(name, myUrl){
            return $.getUrlVars(myUrl)[name];
        }
    })
})(jQuery);

function refreshTags() {
    alert("refreshing tags");
}