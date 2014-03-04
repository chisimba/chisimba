/*
 * Javascript to support the wall module in Chisimba
 *
 * Written by Derek Keats based on ideas, some functions and
 * studying the code of
 *
 */


// Remove HTML from wall posts
function stripHTML(source){
	var strippedText = source.replace(/<\/?[^>]+(>|$)/g, "");
	return strippedText;
}

// Turn links into active links
function replaceURLWithHTMLLinks(source) {
  var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
   replaced = source.replace(exp,"<a href='$1' target='_blank'>$1</a>");
   return replaced;
}


// The main jQuery for the wall
jQuery(function() {

    var id;
    var status_text;
    var fixedid;
    var target;
    var wallid;

    // Function for getting additional wall posts.
    var dataStrBase = "walltype="+wallType+"&page=";
    jQuery(document).on("click", ".wall_posts_more", function(){
        id=jQuery(this).attr("id");
        fixedid = id.replace("more_posts_", "");
        jQuery("#"+id).html('<img src="skins/_common/icons/loading_bar.gif" alt=""Loading..." />');
        jQuery.ajax({
            url: "index.php?module=wall&action=getmoreposts&wallid="+fixedid,
            type: "GET",
            data: dataStrBase+page+"&key="+fixedid,
            success: function(ret) {
                jQuery("#"+id).remove();
                ret ='<div class="wall_post_append">'+ret+'</div>';
                jQuery("#wall_"+fixedid).append(ret);
                page=page+1;
            }
        });
    });

    // Function for posting a wall post
    jQuery(document).on("click", ".shareBtn", function(){
        id=jQuery(this).attr("id");
        status_text = jQuery("#wallpost_"+id).val();
        if(status_text.length == 0) {
                return;
        } else {
            jQuery(".shareBtn").attr("disabled", "disabled");
            var tmpOnlytxt = jQuery("#wall_onlytext_"+id).html();
            target = jQuery("#target_"+id).val();
            //alert(target);
            jQuery("#wall_onlytext_"+id).html('<img src="skins/_common/icons/loading_bar.gif" alt=""Loading..." />');
            status_text = stripHTML(status_text); // clean all html tags
            status_text = replaceURLWithHTMLLinks(status_text); // replace links with HTML anchor tags.
            status_text = status_text.replace(/\n/g,'<br />');
            jQuery.ajax({
                    url: target,
                    type: "POST",
                    data: "wallpost="+status_text,
                    success: function(msg) {
                        jQuery("#wallpost_"+id).val("");
                        jQuery(".shareBtn").removeAttr("disabled");
                        jQuery("#wall_onlytext_"+id).html(tmpOnlytxt);
                        if(msg == "true") {
                            jQuery("#wall_"+id).prepend("<div class='wallpostrow'><span class='wallposter'>"+me+"</span><div class='msg'>"+status_text+"</div></div>");
                            jQuery(".msg:first a").oembed(null, {maxWidth: 480, embedMethod: "append"});
                        } else {
                            alert(msg);
                            //alert("Cannot be posted at the moment! Please try again later.");
                        }
                    }
            });
        }
    });

    // The function for deleting a post
    jQuery(document).on("click", ".delpost", function(){
        var commentContainer = jQuery(this).parent();
        id = jQuery(this).attr("id");
        var string = 'id='+ id;
        jQuery.ajax({
           type: "POST",
           url: "index.php?module=wall&action=delete&id=" + id,
           data: string,
           cache: false,
           success: function(ret){
               if(ret == "true") {
                   commentContainer.slideUp('slow', function() {jQuery(this).remove();});
               } else {
                   alert(ret);
               }
          }
        });
        return false;
    });

    //  The javascript for running the comments.
    //
    // Show the post box and submit button
    jQuery(document).on("click", ".wall_comment_button", function(){
        var element = jQuery(this);
        id = element.attr("id");
        jQuery("#c__"+id).slideToggle(300);
        jQuery(this).toggleClass("active");
        return false;
    });

    // Get additional comments via ajax
    jQuery(document).on("click", ".wall_comments_more", function(){
        id = jQuery(this).attr("id");
        fixedid = id.replace("mrep__", "");
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=wall&action=morecomments&id=" + fixedid,
            success: function(ret) {
                jQuery("#wct_"+fixedid).append(ret);
                jQuery("#"+id).slideToggle(300);
            }
        });

    });

    // Delete a comment
    jQuery(document).on("click", ".wall_delcomment", function(){
        id = jQuery(this).attr("id");
        jQuery.ajax({
            type: "POST",
            url: "index.php?module=wall&action=deletecomment&id="+id,
            success: function(ret) {
                if (ret=='true') {
                    //alert("#wct__"+id);
                    jQuery("#cmt__"+id).slideToggle(300);
                } else {
                    alert(ret);
                }
            }
        });
    });

    // Post the comment
    jQuery(document).on("click", ".comment_submit", function(){
        id = jQuery(this).attr("id");
        fixedid = id.replace("cb_", "");
        var comment_text = jQuery("#ct_"+id).val();
        if(comment_text.length == 0) {
            return;
        } else {
            jQuery("#ct_"+id).attr("disabled", "disabled");
            var tmpHolder = jQuery("#c__"+fixedid).html();
            jQuery("#c__"+fixedid).html('<img src="skins/_common/icons/loading_bar.gif" alt=""Loading..." />');
            comment_text = stripHTML(comment_text); // clean all html tags
            comment_text = replaceURLWithHTMLLinks(comment_text); // replace links with HTML anchor tags.
            comment_text = comment_text.replace(/\n/g,'<br />');
            jQuery.ajax({
                type: "POST",
                url: "index.php?module=wall&action=addcomment&id=" + id,
                data: "comment_text="+comment_text,
                success: function(ret) {
                    if(ret == "true") {
                        // The comment blocks have ids starting with wct_
                        if ( jQuery("#wct_"+fixedid).length > 0 ) {
                            jQuery("#wct_"+fixedid).prepend('<li><b><span class="wall_comment_author">'+youSaid+'</span></b>&nbsp;<div id=cmt_'+fixedid+'>'+comment_text+'<div>&nbsp;<div class="wall_comment_when"><strong>'+secsAgo+'</strong></div></li>');
                            jQuery("#c__"+fixedid).slideToggle(300);
                            jQuery("#cmt_"+fixedid+":first a").oembed(null, {maxWidth: 480, embedMethod: "append"});
                        } else {
                            if ( jQuery("#wpr__"+fixedid).length > 0 ) {
                                jQuery("#wpr__"+fixedid).append('<br /><br /><div class="wall_comments_top"></div><ol class="wall_replies" id="wct_'+fixedid+'"><li><b><span class="wall_comment_author">'+youSaid+'</span></b>&nbsp;'+comment_text+'<div class="wall_comment_when"><strong>'+secsAgo+'</strong></div></li></ol>');
                                jQuery("#c__"+fixedid).slideToggle(300);
                                jQuery("#cmt_"+fixedid+":first a").oembed(null, {maxWidth: 480, embedMethod: "append"});
                            } else {
                                // We should never be able to get here
                                alert(nothingApppendTo);
                            }
                        }
                    } else {
                        alert(ret);
                    }
                    jQuery("#c__"+fixedid).html(tmpHolder);
                    jQuery("#ct_"+id).val("");
                    jQuery("#ct_"+id).removeAttr("disabled");
                }
            });
            return false;
        }
    });

    // Next one here.....

});