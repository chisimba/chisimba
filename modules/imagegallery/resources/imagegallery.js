/* 
 * Javascript to support imagegallery
 *
 * Written by Kevin Cyster kcyster@gmail.com
 * STarted on: June 19, 2012, 8:18 pm
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
    var path = jQuery(location).attr('pathname');
    
    // Things to do on loading the page.
    jQuery(document).ready(function() {
	jQuery(function() {
            jQuery( "#sortable" ).sortable();
            jQuery( "#sortable" ).disableSelection();
	});
    });
    
    jQuery(".gallery_user, .gallery_context").live("click", function() {
        var myClass = jQuery(this).attr("class");
        var id = this.id;
        if (myClass == "gallery_user")
        {
            jQuery("#input_gallery_add_user_id").val(id)
        }
        else
        {
            jQuery("#input_gallery_add_context_code").val(id)
        }
        if (jQuery("#main_tabs").length > 0)
        {
            var tabs = jQuery("#main_tabs").tabs("option", "selected");
            tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
            jQuery("#input_gallery_add_tabs").val(tabs);        
        }
        jQuery("#dialog_gallery_add").dialog("open");
        return false;
    });

    jQuery("#gallery_add_cancel").live("click", function() {
        jQuery("#dialog_gallery_add").dialog("close");
        return false;
    });
    
    jQuery("#form_gallery_add").submit(function() {
        if (jQuery("#input_gallery_add_title").val() == '')
        {
            alert(no_gallery_title);
            jQuery("#input_gallery_add_title").select();
            return false;
        }
        if (jQuery("#input_gallery_add_description").val() == '')
        {
            alert(no_gallery_desc);
            jQuery("#input_gallery_add_description").select();
            return false;
        }
    }); 
    
    jQuery(".gallery_albums").live("click", function() {
        var id = jQuery(this).parent().attr('id');
        if (id != '')
        {
            if (jQuery("#main_tabs").length > 0)
            {
                var tabs = jQuery("#main_tabs").tabs("option", "selected");
                tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
                jQuery("#input_gallery_add_tabs").val(tabs);        
            }
            window.location.assign(path + '?module=imagegallery&action=view&gallery_id=' + id + '&tabs=' + tabs);
        }
    });
    
    jQuery(".gallery_empty").live("click", function() {
        var id = jQuery(this).parent().attr('id');
        var dialog_id = "dialog_gallery_options_" + id;
        jQuery("#" + dialog_id).dialog("open");
        return false;
    });
    
    jQuery(".gallery_options").live("click", function() {
        var id = this.id;
        var dialog_id = "dialog_gallery_options_" + id;
        jQuery("#" + dialog_id).dialog("open");
        return false;
    });

    jQuery(".gallery_edit").live("click", function() {
        var id = this.id;
        var tabs;
        tabs = jQuery("#main_tabs").tabs("option", "selected");
        tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxGetGalleryData",
            data: "gallery_id=" + id + "&tabs=" + tabs,
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery("#input_gallery_edit_gallery_id").val(obj.gallery_id);                
                jQuery("#input_gallery_edit_title").val(obj.title);                
                jQuery("#input_gallery_edit_description").val(obj.desc);                
                jQuery("#input_gallery_edit_shared").val([obj.shared]);
                jQuery("#input_gallery_edit_tabs").val(obj.tabs);
                jQuery("#dialog_gallery_edit").dialog("open");
                jQuery("#input_gallery_edit_title").select();                
                return false;
            }
        });
        return false;
    });

    jQuery("#gallery_edit_cancel").live("click", function() {
        jQuery("#dialog_gallery_edit").dialog("close");
        return false;
    });
    
    jQuery("#form_gallery_edit").submit(function() {
        if (jQuery("#input_gallery_edit_title").val() == '')
        {
            alert(no_gallery_title);
            jQuery("#input_gallery_edit_title").select();
            return false;
        }
        if (jQuery("#input_gallery_edit_description").val() == '')
        {
            alert(no_gallery_desc);
            jQuery("#input_gallery_edit_description").select();
            return false;
        }
    }); 
    
    jQuery(".album_add").live("click", function() {
        var id = this.id;
        var tabs;
        jQuery("#input_album_add_gallery_id").val(id)
        if (jQuery("#main_tabs").length > 0)
        {
            tabs = jQuery("#main_tabs").tabs("option", "selected");
            tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
            jQuery("#input_album_add_tabs").val(tabs);        
        }
        jQuery("#dialog_album_add").dialog("open");
        return false;
    });

    jQuery("#album_add_cancel").live("click", function() {
        jQuery("#dialog_album_add").dialog("close");
        return false;
    });
    
    jQuery("#form_album_add").submit(function() {
        if (jQuery("#input_album_add_title").val() == '')
        {
            alert(no_album_title);
            jQuery("#input_album_add_title").select();
            return false;
        }
        if (jQuery("#input_album_add_description").val() == '')
        {
            alert(no_album_desc);
            jQuery("#input_album_add_description").select();
            return false;
        }
    }); 
    
    jQuery(".gallery_details").live("click", function() {
        var id = this.id;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxGetGalleryViewData",
            data: "gallery_id=" + id,
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery("#gallery_title").html(obj.title);                
                jQuery("#gallery_description").html(obj.desc);                
                jQuery("#gallery_shared").html(obj.shared);
                jQuery("#dialog_gallery_view").dialog("open");
                return false;
            }
        });
        return false;
    });
    
    jQuery(".album_details").live("click", function() {
        var id = this.id;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxGetAlbumViewData",
            data: "album_id=" + id,
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery("#album_title").html(obj.title);                
                jQuery("#album_description").html(obj.desc);                
                jQuery("#album_shared").html(obj.shared);
                jQuery("#dialog_album_view").dialog("open");
                return false;
            }
        });
        return false;
    });
    
    jQuery(".album_empty").live("click", function() {
        var id = jQuery(".album_empty").parent().attr("id");
        var dialog_id = "dialog_album_options_" + id;
        jQuery("#" + dialog_id).dialog("open");
        return false;
    });
    
    jQuery(".album_options").live("click", function() {
        var id = this.id;
        var dialog_id = "dialog_album_options_" + id;
        jQuery("#" + dialog_id).dialog("open");
        return false;
    });
    
    jQuery(".album_edit").live("click", function() {
        var id = this.id;
        var tabs;
        tabs = jQuery("#main_tabs").tabs("option", "selected");
        tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxGetAlbumData",
            data: "album_id=" + id + "&tabs=" + tabs,
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery("#input_album_edit_gallery_id").val(obj.gallery_id);                
                jQuery("#input_album_edit_album_id").val(obj.album_id);                
                jQuery("#input_album_edit_title").val(obj.title);                
                jQuery("#input_album_edit_description").val(obj.desc);                
                jQuery("#input_album_edit_shared").val([obj.shared]);                
                jQuery("#input_album_edit_tabs").val(obj.tabs);
                jQuery("#dialog_album_edit").dialog("open");
                jQuery("#input_album_edit_title").select();                
                return false;
            }
        });
        return false;
    });

    jQuery("#album_edit_cancel").live("click", function() {
        jQuery("#dialog_album_edit").dialog("close");
        return false;
    });
    
    jQuery("#form_album_edit").submit(function() {
        if (jQuery("#input_album_edit_title").val() == '')
        {
            alert(no_album_title);
            jQuery("#input_album_edit_title").select();
            return false;
        }
        if (jQuery("#input_album_edit_description").val() == '')
        {
            alert(no_album_desc);
            jQuery("#input_album_edit_description").select();
            return false;
        }
    }); 
    
    jQuery(".gallery_image_upload").live("click", function() {
        var id = this.id;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxShowUpload",
            data: "gallery_id=" + id,
            success: function(ret) {
                jQuery("#upload_dialog").html(ret);
                var tabs;
                tabs = jQuery("#main_tabs").tabs("option", "selected");
                tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
                jQuery("#input_upload_tabs").val(tabs);
                jQuery("#dialog_image_add").dialog("open");
                return false;
            }
        });
    });
    
    jQuery("#image_add_cancel").live("click", function() {
        jQuery("#upload_dialog").html();
        jQuery("#dialog_image_add").dialog("close");
        return false;
    });
    
    jQuery("#more_images").live("click", function() {
        jQuery(".more_boxes").show();
        jQuery("#more_images").hide();
        jQuery("#less_images").show();
    });

    jQuery("#less_images").live("click", function() {
        jQuery(".more_boxes").hide();
        jQuery(".more_boxes input").val('');
        jQuery("#more_images").show();
        jQuery("#less_images").hide();
    });
    
    jQuery(".album_image_upload").live("click", function() {
        var id = this.id;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxShowUpload",
            data: "album_id=" + id,
            success: function(ret) {
                jQuery("#upload_dialog").html(ret);
                var tabs;
                tabs = jQuery("#main_tabs").tabs("option", "selected");
                tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
                jQuery("#input_upload_tabs").val(tabs);
                jQuery("#dialog_image_add").dialog("open");
                return false;
            }
        });
    });
    
    jQuery("#input_image_album_id").live("change", function() {
        if (jQuery("#input_image_album_id").val() != '')
        {
            jQuery("#upload_new").hide();
        }
        else
        {
            jQuery("#upload_new").show();
        }
    });
    
    jQuery("#form_image_add").live("submit", function() {        
        if (jQuery("#input_image_album_id").val() == '')
        {
            if (jQuery("#input_image_album_title").val() == '')
            {
                alert(no_album_title);
                jQuery("#input_image_album_title").focus();
                return false;
            }
            if (jQuery("#input_image_album_description").val() == '')
            {
                alert(no_album_desc);
                jQuery("#input_image_album_description").focus();
                return false;
            }
        }
        var files = false;
        var fileArray = jQuery("input[id^=input_file]");
        var len = fileArray.length;
        for (var i = 0; i < len; i++)
        {
            if (fileArray.eq(i).val() != '')
            {
                files = true;
            }
        }
        if (!files)
        {
            alert(no_image);
            return false;
        }
        for (i = 0; i < len; i++)
        {
            if (fileArray.eq(i).val() == '')
            {
                fileArray.eq(i).detach();
            }
        }
    });
    
    jQuery(".image_options").live("click", function() {
        var id = this.id;
        var dialog_id = "dialog_image_options_" + id;
        jQuery("#" + dialog_id).dialog("open");
        return false;
    });
    
    jQuery(".image_details").live("click", function() {
        var id = this.id;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxGetImageViewData",
            data: "image_id=" + id,
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery("#image_caption").html(obj.caption);                
                jQuery("#image_description").html(obj.desc);                
                jQuery("#image_shared").html(obj.shared);
                jQuery("#dialog_image_view").dialog("open");
                return false;
            }
        });
        return false;
    });
    
    jQuery(".image_edit").live("click", function() {
        var id = this.id;
        var tabs;
        tabs = jQuery("#main_tabs").tabs("option", "selected");
        tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxGetImageData",
            data: "image_id=" + id + "&tabs=" + tabs,
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery("#input_image_edit_image_id").val(obj.image_id);                
                jQuery("#input_image_edit_album_id").val(obj.album_id);                
                jQuery("#input_image_edit_caption").val(obj.caption);                
                jQuery("#input_image_edit_description").val(obj.desc);                
                jQuery("#input_image_edit_shared").val([obj.shared]);                
                jQuery("#input_tabs").val(obj.tabs);
                jQuery("#dialog_image_edit").dialog("open");
                jQuery("#input_image_edit_title").select();                
                return false;
            }
        });
        return false;
    });

    jQuery("#form_image_edit").submit(function() {
        if (jQuery("#input_image_edit_caption").val() == '')
        {
            alert(no_image_caption);
            jQuery("#input_image_edit_caption").select();
            return false;
        }
        if (jQuery("#input_image_edit_description").val() == '')
        {
            alert(no_image_desc);
            jQuery("#input_image_edit_description").select();
            return false;
        }
    }); 
    
    jQuery("#image_edit_cancel").live("click", function() {
        jQuery("#dialog_image_edit").dialog("close");
        return false;
    });
    
    jQuery(".view_image").live("click", function() {
        jQuery("#dialog_view_image").dialog("open");
        return false;
    });
    
    jQuery(".image_edit_location").live("click", function() {
        var id = this.id;
        var tabs;
        tabs = jQuery("#main_tabs").tabs("option", "selected");
        tabs = tabs + "|" + jQuery("#context_tabs").tabs("option", "selected");
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxGetImageData",
            data: "image_id=" + id + "&tabs=" + tabs,
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery("#input_image_edit_image_id").val(obj.image_id);                
                jQuery("#input_image_edit_album_id").val(obj.album_id);                
                jQuery("#input_image_edit_caption").val(obj.caption);                
                jQuery("#input_image_edit_description").val(obj.desc);                
                jQuery("#input_image_edit_shared").val([obj.shared]);                
                jQuery("#input_tabs").val(obj.tabs);
                jQuery("#form_image_edit").append("<input id=\"input_location\" name=\"location\" type=\"hidden\" value=\"display\" />");
                jQuery("#dialog_image_edit").dialog("open");
                jQuery("#input_image_edit_title").select();                
                return false;
            }
        });
        return false;
    });
    
    jQuery(".image").live("click", function() {
        var id = this.id;
        var tabs = jQuery("#main_tabs").tabs("option", "selected");
        if (tabs == 1)
        {
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=imagegallery&action=ajaxUpdateViewCount",
                data: "image_id=" + id,
                success: function(ret) {
                    return false;
                }
            });
        }
    });
    
    jQuery(".image_add_comment").live("click", function() {
        var id = this.id;
        jQuery("#input_add_comment_image_id").val(id);
        jQuery("#dialog_add_comment").dialog("open");
        return false;
    });

    jQuery("#form_comment_add").submit(function() {
        if (jQuery("#input_add_comment_comment").val() == '')
        {
            alert(no_comment);
            jQuery("#input_add_comment_comment").select();
            return false;
        }
    }); 
    
    jQuery("#add_comment_cancel").live("click", function() {
        jQuery("#dialog_add_comment").dialog("close");
        return false;
    });
    
    jQuery(".image_edit_comment").live("click", function() {
        var id = this.id;
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=imagegallery&action=ajaxGetCommentData",
            data: "comment_id=" + id,
            success: function(ret) {
                var obj = jQuery.parseJSON(ret)
                jQuery("#input_edit_comment_id").val(obj.comment_id);
                jQuery("#input_edit_comment_image_id").val(obj.image_id);
                jQuery("#input_edit_comment_comment").val(obj.comment);
                jQuery("#dialog_edit_comment").dialog("open");
                return false;
            }
        });
    });

    jQuery("#form_comment_edit").submit(function() {
        if (jQuery("#input_edit_comment_comment").val() == '')
        {
            alert(no_comment);
            jQuery("#input_edit_comment_comment").select();
            return false;
        }
    }); 
    
    jQuery("#edit_comment_cancel").live("click", function() {
        jQuery("#dialog_edit_comment").dialog("close");
        return false;
    });
    
});