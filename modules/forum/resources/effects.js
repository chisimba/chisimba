/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function() {
        var topLink = jQuery('<a>', {
                href: 'javascript:void(0);',
                class: 'floatingSideNavigation',
                id: 'top'
        });
        var bottonLink = jQuery('<a>', {
                href: 'javascript:void(0);',
                onclick: function(e) {
                        e.preventDefault();
                        jQuery('html').scrollTop();
                },
                class: 'floatingSideNavigation',
                id: 'bottom'
        });
        jQuery('body').append(topLink);
        jQuery('body').append(bottonLink);
        jQuery('a.floatingSideNavigation#top').hide();
        jQuery('a').unbind('click');
        //hide the side topics
        jQuery('ul.indicator').hide();
        jQuery('.replyForumUserPicture').hide();
        jQuery('.deleteconfirm').hide();
        jQuery('div.hiddenOptions, div.attachmentwrapper, div.file-preview').hide();
        jQuery('div.filePreview').hide();
        jQuery('a.attachmentLink').hide();
        jQuery('a.postDeleteLink, a.postEditClass').find('img').css('margin', '10px 5px');
        jQuery('a.ratings').click(function(e) {
                e.preventDefault()
        });
        /**
         * ===Confirmation message===
         * 
         * @returns {undefined}
         */
        jQuery.fn.displayConfirmationMessage = function(message) {
                if (message != '') {
                        jQuery('div#Canvas_Content_Body_Region2').append('<span class=\'jqGenerated\'  id=\'confirm\' >' + message + '<a class="floatingClose" ></a></span>');
                } else {
                        message = "Success";
                }
                setTimeout(function() {
                        jQuery('.jqGenerated').remove();
                }, 5000);
        }
        //get the number of forum posts and adjust the display
        var divs = jQuery('.newForumContainer');
        var numberOfDivs = jQuery(divs).length;
        if (numberOfDivs < 2) {
                jQuery(divs).css('margin-bottom', '100px');
        }
        /**
         * ===Send the message===
         */
        jQuery('.postReplyLink').click(function(e) {
                e.preventDefault();
                jQuery('.postEditClass').hide();
                var body_over_div = jQuery('div>', {
                        class: 'body-hider',
                        css: {
                                position: 'absolute',
                                top: 0,
                                right: 0,
                                bottom: 0,
                                left: 0,
                                background: '#000'
                        }
                });
                jQuery('body').append(body_over_div);
                jQuery('a.attachmentLink').show('slow');
                var current_link = jQuery(this);
                jQuery(current_link).toggle('fade');
                //get aattributes
                var element_Id = jQuery(this).attr('id');
                //html elements
                var message_text = '';
//                var text_area = jQuery('textarea.miniReply');
//                        jQuery(this).fadeOut('slow');
//                        jQuery('div.content').hide();
//                        jQuery('.newForumContainer.reply').append(image);
//                        jQuery('.newForumContainer.reply').append('<br/><h4>Please wait..</h1>')
//                        //get attachment value
                var attachment_id = jQuery('#hidden_fileselect').val();
                //get post text
                var message = '' /*jQuery(text_area).val()*/;
                var parent_id = element_Id;
                var topic_id = jQuery('.topicid').attr('id');
                var lang = jQuery('.lang').attr('id');
                var forum_id = jQuery('span.forumid').attr('id');
                var post_title = jQuery('.posttitle').attr('id');
//        if (jQuery(message_text).val() != '') {
//                var image = jQuery('<img>', {
//                        src: 'skins/_common/icons/301.gif',
//                        align: 'center'
//                });
                //instantiate the save button
                var save_button = jQuery('<button>', {
                        class: 'buttonLink sexybutton',
                        text: 'Save',
                        click: function(e) {
                                e.preventDefault();
                                jQuery('body').append('<div class="blurPopUp"><span id="confirm" class="centered" >Please wait..<br/><br/></span></div>');
                                jQuery(this, 'button.postReplyCancelButton').hide();
                                attachment_id = jQuery('#hidden_fileselect').val();
                                message_text = jQuery('iframe').contents().find("body.cke_show_borders").html();
                                jQuery.ajax({
                                        type: 'post',
                                        url: 'index.php?module=forum&action=savepostreply',
                                        data: {
                                                message: message_text,
                                                forum_id: forum_id,
                                                topicid: topic_id,
                                                parent: parent_id,
                                                posttitle: post_title,
                                                lang: lang,
                                                attachment: attachment_id
                                        },
                                        success: function() {
                                                jQuery('span#confirm').hide();
                                                jQuery('body').append('<div class="blurPopUp"><span id="confirm" class="centered">Post saved successfuly<br/><br/><a href="#" class="ok" >OK</a></span></div>');
                                                jQuery('.postEditClass').show();
                                        }
                                })
                        }
                });
                //cancel button
                var cancel_button = jQuery('<button>', {
                        class: 'sexybutton postReplyCancelButton',
                        text: 'Cancel',
                        click: function(e) {
                                e.preventDefault();
                                jQuery(current_link).toggle('fade');
                                jQuery('div.postMakerWrapper').hide();
                                jQuery(save_button).remove();
//                                jQuery(this).remove();
                        }
                });
//                if (jQuery(text_area).val() != '') {
                var wrapper_div = jQuery('<div>', {
                        class: 'postMakerWrapper'
                });
                //Send the data
                jQuery.ajax({
                        url: 'index.php?module=forum&action=showeditpostpopup',
                        type: 'post',
                        data: {
                                forumid: forum_id,
                                topicid: topic_id,
                                post_id: parent_id,
                                message: message,
                                posttitle: post_title,
                                lang: lang,
                                attachment: attachment_id
                        },
//                data: ' forumid=' + forum_id + '&topicid=' + topic_id + '&parent=' + parent_id + '&message=' + message_text + '&posttitle=' + post_title + '&lang=' + lang + '&attachment=' + attachment_id,
                        success: function(data) {
                                var editor = data;
                                jQuery('div.clone').append("<div class='postMakerWrapper' >" + data + "</div>");
//                                jQuery('div.clone').append(data);
                                jQuery('div.clone').append(save_button);
//                                jQuery('div.clone').append(cancel_button);
//                                jQuery('div.clone').append(wrapper_div);
//                                jQuery('.newForumContainer.reply').empty();
//                                        alert("Success");
                                //add element to another class
//                                        window.location.reload();
//                    jQuery('.content').html('<br/>' + message);
                        }
                });
//        } else {
////            jQuery(text_area).val('why you no type here?');
//        }
//                }
        });
        /**
         * ===Deleting the post===
         */
        jQuery('a.postDeleteLink').click(function(event) {
                event.preventDefault();
                var link_id = jQuery(this).attr('id');
                jQuery('div.deleteconfirm#' + link_id).toggle('fade');
        });
        /**
         * ===Display moderation options===
         */
        jQuery('a.moderatetopic').click(function(e) {
                e.preventDefault();
                jQuery('div.hiddenOptions').toggle('fade');
        });
        /**
         * ==Confirming post delete===
         */
        jQuery('a.postDeleteConfirm').click(function(e) {
                e.preventDefault();
                var link_id = jQuery(this).attr('id');
                jQuery('div.deleteconfirm#' + link_id).toggle('fade');
                var post_id = jQuery(this).attr('id');
                var topic_id = jQuery('span.topicid').attr('id');
                jQuery.ajax({
                        url: 'index.php?module=forum&action=removepost',
                        type: 'post',
                        data: {
                                postid: link_id,
                                topic_id: topic_id
                        },
                        success: function() {
                                jQuery('body').append('<span id="confirm" style="margin: 5% 40%;" >Post deleted successfuly<br/><br/><a href="#" class="ok" >OK</a></span>');
                        }
                });
        });
        //when clicking OK on the confirmation message
        jQuery('.ok').live('click', function(e) {
                e.preventDefault();
                jQuery(this).css('display', 'none');
                window.location.reload();
        });
        /**
         * ===Showing the attachment popUp===
         */
        jQuery('a.attachmentLink').click(function(e) {
                e.preventDefault();
                jQuery(this).css('class', 'buttonLink');
                var attachCancelButton = jQuery('<button>', {
                        text: 'Cancel',
                        class: 'sexybutton',
                        css: {
                                'border-bottom': '4px solid #C0C0C0',
                                background: '#FCFCFC',
                                padding: '3px',
                                'box-shadow': '0 0 1px #000'
                        },
                        click: function(e) {
                                e.preventDefault();
                                jQuery('div.attachmentwrapper').val('');
                                jQuery('div.attachmentwrapper').toggle('fade');
                                jQuery(this).remove();
                        }
                });
                jQuery('div.attachmentwrapper').toggle('fade');
                jQuery('div.attachmentwrapper').append(attachCancelButton);
        });
        /**
         * ===Moderating a post===
         */
        jQuery('#moderationSave').click(function() {
                var data_string = jQuery('#form_topicModeration').serialize();
                var forum_id = "forum=" + jQuery('.forumid').attr('id');
                jQuery(data_string).append(forum_id);
                jQuery.ajax({
                        url: 'index.php?module=forum&action=usersubscription',
                        type: 'post',
                        data: data_string,
                        success: function() {
                                jQuery('div.hiddenOptions').hide();
                                jQuery.fn.displayConfirmationMessage('Settings saved successfuly ');
                        }
                });
        });
        /**
         * ===Moderaton Cancel===
         */
        jQuery('button#moderationCancel').click(function(e) {
                e.preventDefault();
                jQuery('.hiddenOptions').toggle('fade');
        });
        /**
         * ===Canceling post delete===
         */
        jQuery('a.postDeleteCancel').click(function(e) {
                e.preventDefault();
                var link_id = jQuery(this).attr('id');
                jQuery('div.deleteconfirm#' + link_id).toggle('fade');
        });
        /**
         * @RATINGS
         */
        jQuery('a.ratings.up').click(function() {
                jQuery(this).hide();
                var post_id = jQuery(this).attr('id');
                jQuery.ajax({
                        url: 'index.php?module=forum&action=savepostratingup',
                        type: 'post',
                        data: 'post_id=' + post_id,
                        success: function() {
//                                alert(data);
                                window.location.reload()
                        }
                })
        });
        /**
         * ===Lowering ratings===
         */
        jQuery('a.ratings.down ').click(function() {
                jQuery(this).hide();
                var post_id = jQuery(this).attr('id');
                jQuery.ajax({
                        url: 'index.php?module=forum&action=savepostratingdown',
                        type: 'post',
                        data: 'post_id=' + post_id,
                        success: function() {
                                window.location.reload()
                        }
                })
        });
        /**
         * Side block
         */
        jQuery('a.indicatorparent').click(function(event) {
                event.preventDefault();
                var element_ID = jQuery(this).attr('id');
                jQuery('ul#' + element_ID).slideToggle();
        });
        /**
         * ==View the post attachment===
         */
        jQuery('.forumViewAttachment').live('click', function(e) {
                var current_text = jQuery(this).html();
                if (current_text == 'View') {
                        current_text = 'Hide'
                } else {
                        current_text = 'View';
                }
                e.preventDefault();
                var parentElement_id = jQuery(this).attr('id');
                jQuery('div.file-preview#' + parentElement_id).toggle('blind');
                jQuery(this).html(current_text);
        });
        /**
         * =======Editing post==========
         */
        jQuery('.postEditClass').on('click', function() {
                var post_id = jQuery(this).attr('id');
                var _id = jQuery(this).attr('class');
                var post_text = jQuery('div.postText#' + post_id).html();
                jQuery(this).remove();
                //wrapper div
                var popUpWrapper = jQuery('<div>', {
                        class: 'popUpWrapper',
                        css: {
                                'padding': '10px 20px 10px 10px',
                                'margin': '8% 30%',
                                'border-radius': '5px'
                        }
                });
                //textarea
                var edit_post_area = jQuery('<span>', {
                        val: post_text
                });
                jQuery(this).hide('slow');
//                var blocktext = jQuery('textarea[blocktext]');
                //save button
                var save_button = jQuery('<button>', {
                        text: 'Save',
                        class: 'buttonLink sexybutton',
                        id: post_id,
                        click: function() {
                                _id = _id.replace('postEditClass ', '');
                                var new_value = jQuery('iframe').contents().find("body.cke_show_borders").html();
//                                var new_value = jQuery('td.cke_contents iframe').conents().find('html').html();
//                                var new_text = jQuery('[name="blocktext"]').val();
                                jQuery.ajax({
                                        url: 'index.php?module=forum&action=savepostedit',
                                        type: 'post',
                                        data: {
                                                _id: _id,
                                                post_id: post_id,
                                                new_text: new_value
                                        },
                                        success: function(data) {
                                                jQuery('.postReplyLink').show();
//                                                jQuery.fn.displayConfirmationMessage(data);
                                                jQuery('div.postText#' + post_id).html(new_value);
                                                jQuery(popUpWrapper).empty();
                                                jQuery('.popUpWrapper').remove();
                                                jQuery('#Canvas_Content_Body_Region2').append(data);
                                                jQuery('body').remove(popUpWrapper);
                                                jQuery().setTimeOut(function() {
                                                        jQuery('body').remove('span#confirm');
                                                });
                                        }
                                });
                        }
                });
                //cancel button
                var cancel_button = jQuery('<button>', {
                        text: 'Canel',
                        class: 'buttonLink sexybutton',
                        click: function(e) {
                                e.preventDefault();
//                jQuery(edit_post_area).val('');
//                jQuery(popUpWrapper).empty();
                                jQuery('textarea#' + _id).remove();
                                jQuery('body').remove('textarea#' + _id);
                                jQuery(document).remove('.popUpWrapper');
                                jQuery('.popUpWrapper').remove();
                        }
                });
//            add all elements to wrapper div
//                jQuery(popUpWrapper).append(edit_post_area);
                jQuery('body').append(popUpWrapper);
                new_text = jQuery(edit_post_area).val();
                //Get and return the editor
                jQuery.ajax({
                        url: 'index.php?module=forum&action=showeditpostpopup',
                        type: 'post',
                        data: {
                                _id: _id,
                                post_id: post_id,
                                new_text: new_text
                        },
                        success: function(data) {
//                                jQuery('.postText#' + post_id).html(jQuery(edit_post_area).val());
//                                jQuery('.popUpWrapper').remove();
                                var editor = data;
                                jQuery(popUpWrapper).append(editor);
                                jQuery(popUpWrapper).append(save_button);
                                jQuery(popUpWrapper).append(cancel_button);
                        }
                });
        });
        jQuery('a.floatingSideNavigation#top').on('click', function() {
                jQuery('html, body').animate({
                        scrollTop: '0px'
                }, 500);
        });
        jQuery('a.floatingSideNavigation#bottom').on('click', function() {
                jQuery('html, body').animate({
                        scrollTop: jQuery(document).height()
                }, 500);
        });
        jQuery(document).on('scroll',function(){
                if(jQuery(this).scrollTop() >= 280){
                        jQuery('a.floatingSideNavigation#top').show('fade');
                }else{
                        jQuery('a.floatingSideNavigation#top').hide();
                }
//                console.log(jQuery(this).scrollTop());
//                console.log(jQuery('body').height());
        });
        /**
         * ===Trying to inform the user as to which file is selecteded===
         */
        jQuery('input#hidden_fileselect').on('change', function() {

        });
        jQuery('.threadReplyLink').on('click',function(){
                var id = jQuery(this).attr('id');
//                jQuery.ajax({
//                        type: 'post',
//                        url: 'index.php?module=forum&action=showeditpostpopup',
//                        data: {
//                                _id: id
//                        },
//                        success: function(data){
//                                
//                        }
//                });
        });
});