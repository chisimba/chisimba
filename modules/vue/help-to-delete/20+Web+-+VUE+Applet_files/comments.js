var IE = /*@cc_on function(){ switch(@_jscript_version){ case 1.0:return 3; case 3.0:return 4; case 5.0:return 5; case 5.1:return 5; case 5.5:return 5.5; case 5.6:return 6; case 5.7:return 7; }}()||@*/0;

jQuery(function($) { 
    
    if (IE && IE < 7) {
        function applyPngFilter(imageSrc) {
            this.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + (imageSrc || this.src) + "', sizingMethod='scale')"
        }
        $(".logo.anonymous").each(function () {
            var div = document.createElement("div");
            div.className = "replacement";
            applyPngFilter.call(div, this.src);
            $(this).replaceWith(div);
        });
        $(".comment-actions .comment-permalink a").each(function () {
            $(this).addClass("filtered");
            var path_light = $(this).css("background-image").replace(/^url\(\"?|\"?\)$/g, ""); // remove url(...) surrounding actual URL
            var path_dark = path_light.replace("light", "dark");
            applyPngFilter.call(this, path_light);
            this.style.cursor = "pointer";
            this.style.background = "none";
            $(this).hover(function () {
                applyPngFilter.call(this, path_dark);
            }, function () {
                applyPngFilter.call(this, path_light);
            });
        });
    }

    var collapseTransition = function (comment) {
        var imageTransition = function (image, reverse) {
            return {
                animate: function (pos) {
                    image.style.height = AJS.animation.interpolate(pos, 48, 24, reverse) + "px";
                    image.style.width = image.style.height;
                    image.style.marginLeft = AJS.animation.interpolate(pos, 0, 12, reverse) + "px";
                },
                onFinish: function () {
                    image.style.height = '';
                    image.style.width = '';
                    image.style.marginLeft = '';
                }
            };
        };
        var opacityTransition = function (el, reverse) {
            return {
                start: 1.0,
                end: 0.0,
                reverse: reverse,
                animate: function (pos) {
                    el.style.opacity = pos;
                    el.style.filter = "alpha(opacity=" + (pos * 100) + ")";
                },
                onFinish: function () {
                    el.style.opacity = "";
                    el.style.filter = "alpha(opacity=" + (reverse ? 100 : 0) + ")";
                }
            };
        };
        var heightTransition = function (el, reverse) {
            if (!reverse)
                el.originalHeight = jQuery(el).height();
            return {
                start: el.originalHeight || 50,
                end: 0,
                reverse: reverse,
                animate: function (pos) {
                    el.style.height = pos + "px";
                },
                onFinish: function () {
                    el.style.height = '';
                }
            };
        };

        var body = jQuery(comment).find('.comment-body')[0];
        var reverse = comment.className.indexOf("collapsed") >= 0;
        return AJS.animation.combine([
            imageTransition(jQuery(comment).parent().find('.comment-user-logo img, .comment-user-logo .replacement')[0], reverse),
            opacityTransition(body, reverse),
            opacityTransition(jQuery(comment).find('.excerpt')[0], !reverse),
            heightTransition(body, reverse),
            {
                onFinish: function () {
                    if (reverse)
                        jQuery(comment).removeClass('collapsed');
                    else
                        jQuery(comment).addClass('collapsed');
                }
            }
        ]);
    };

    /*
     * Alternate colours of comments. Doing this with threaded comments in the backend
     * is painful.
     */
    $('.comment:odd').addClass('odd');

    /*
     * Bind collapsing comment functionality to comment-toggle class.
     */
    function commentToggle() {
        var toggle = this;
        $(toggle).unbind('click');
        AJS.animation.add(collapseTransition($(toggle).parent()[0]));
        AJS.animation.add({
            onFinish: function () { $(toggle).click(commentToggle); } /* rebind */
        });
        AJS.animation.start();
    }
    
    var toggle = $('.comment-toggle');
    toggle.css('cursor', 'pointer');
    toggle.attr("title", AJS.params.collapseTooltip);
    toggle.click(commentToggle);

    /*
     * Remove comment pop-up confirmation.
     */
    $('.comment-action-remove a').click(function() {
        if(confirm(AJS.params.deleteCommentConfirmMessage))
        {
            this.href = this.href + '&confirm=yes';
            return true;
        }
        return false;
    });

    /*
     * Toggle links for hiding and showing the comments section.
     */
    $('#comments-hide').click(function() {
        $('#page-comments').addClass("hidden");
        $(this).addClass("hidden");
        $('#comments-show').removeClass("hidden");
        $('#comments-expand-collapse').addClass("hidden");
        return false;
    });
    $('#comments-show').click(function() {
        $('#page-comments').removeClass("hidden");
        $(this).addClass("hidden");
        $('#comments-hide').removeClass("hidden");
        $('#comments-expand-collapse').removeClass("hidden");
        return false;
    });

    /*
     * Collapse- and expand-all functionality.
     *
     * We only actually animate the first 10 comments. This looks much less jerky
     * and you can't tell the difference because the buttons are only at the top.
     */
    $('#comments-collapse').click(function() {
        $(this).addClass("hidden");
        $('#collapse-wait').removeClass("hidden");
        $('.comment:lt(10):not(.collapsed,.add,.reply,.edit)').each(function () {
                AJS.animation.add(collapseTransition(this));
        });
        AJS.animation.add({
            onFinish: function () {
                $('#collapse-wait').addClass("hidden");
                $('#comments-expand').removeClass("hidden");
                $('.comment:not(.add,.reply,.edit)').addClass("collapsed");
            }
        });
        AJS.animation.start();
        return false;
    });
    $('#comments-expand').click(function() {
        $(this).addClass("hidden");
        $('#expand-wait').removeClass("hidden");
        $('.comment:lt(10).collapsed').each(function () {
            AJS.animation.add(collapseTransition(this));
        });
        AJS.animation.add({
            onFinish: function () {
                $('#expand-wait').addClass("hidden");
                $('#comments-collapse').removeClass("hidden");
                $('.comment').removeClass("collapsed");
            }
        });
        AJS.animation.start();
        return false;
    });
});
