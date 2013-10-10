// ==================
// = Drop-down menu =
// ==================
AJS.menuShowCount = 0;

AJS.toInit(function ($) {
    $(".ajs-menu-bar").each(function () {
        var hideDropDown = function (e) {
            if (typeof AJS.dropDownTimer != "undefined" && AJS.dropDownHider) {
                clearTimeout(AJS.dropDownTimer);
                delete AJS.dropDownTimer;
                AJS.dropDownHider();
                AJS.dropDownHider = null;
            }
        };
        var el = this;
        $(".ajs-button", el).each(function () {
            $(this).mouseover(hideDropDown);
        });
        $(".ajs-menu-item", el).each(function () {
            var it = this, $it = $(this),
                dd = $(".ajs-drop-down", it);
            if (!dd.length) return;

            dd = dd[0];
            dd.hidden = true;
            dd.focused = -1;
            dd.hide = function () {
                if (!this.hidden) {
                    $it.toggleClass("opened");
                    var as = $("a", this);
                    $(this).toggleClass("hidden");
                    this.hidden = true;
                    $(document).unbind("click", this.fhide).unbind("keydown", this.fmovefocus).unbind("keypress", this.blocker);
                    if (this.focused + 1) {
                        $(as[this.focused]).removeClass("active");
                    }
                    this.focused = -1;
                }
            };
            dd.show = function () {
                if (typeof this.hidden == "undefined" || this.hidden) {
                    $(this).toggleClass("hidden");
                    $it.toggleClass("opened");
                    this.hidden = false;
                    var dd = this, $dd = $(this);
                    this.timer = setTimeout(function () {$(document).click(dd.fhide);}, 1);
                    $(document).keydown(dd.fmovefocus).keypress(dd.blocker);
                    var as = $("a", dd);
                    as.each(function (i) {
                        var grandpa = this.parentNode.parentNode;
                        $(this).hover(function (e) {
                            if (grandpa.focused + 1) {
                                $(as[grandpa.focused].parentNode).removeClass("active");
                            }
                            $(this.parentNode).addClass("active");
                            grandpa.focused = i;
                        }, function (e) {
                            if (grandpa.focused + 1) {
                                $(as[grandpa.focused].parentNode).removeClass("active");
                            }
                            grandpa.focused = -1;
                        });
                    });
                }
            };
            dd.fmovefocus = function (e) {dd.movefocus(e);};
            dd.fhide = function (e) {dd.hide(e);};
            dd.blocker = function (e) {
                var c = e.which;
                if (c == 40 || c == 38) {
                    return false;
                }
            };
            dd.movefocus = function (e) {
                var c = e.which,
                    a = this.getElementsByTagName("a");
                if (this.focused + 1) {
                    $(a[this.focused].parentNode).removeClass("active");
                }
                switch (c) {
                    case 40:
                    case 9: {
                        this.focused++;
                        break;
                    }
                    case 38: {
                        this.focused--;
                        break;
                    }
                    case 27: {
                        this.hide();
                        return false;
                    }
                    default: {
                        return true;
                    }
                }
                if (this.focused < 0) {
                    this.focused = a.length - 1;
                }
                if (this.focused > a.length - 1) {
                    this.focused = 0;
                }
                a[this.focused].focus();
                $(a[this.focused].parentNode).addClass("active");
                e.stopPropagation();
                e.preventDefault();
                return false;
            };
            dd.show();
            clearTimeout(dd.timer);
            var $dd = $(dd),
                offset = $dd.offset();
            dd.hide();
            if (offset.left + $dd.width() > $(window).width()) {
                $dd.css("margin-left", "-" + (($dd.width()) - ($it.width())) + "px");
            }
            var a = $(".trigger", it);
            if (a.length) {
                var killHideTimerAndShow = function() {
                    clearTimeout(AJS.dropDownTimer);
                    delete AJS.dropDownTimer;
                    AJS.dropDownHider();
                    AJS.dropDownHider = null;
                    dd.show();
                };

                var overHandler = function (e) {
                    var changingMenu = typeof AJS.dropDownTimer != "undefined";
                    if (changingMenu) {
                        killHideTimerAndShow();
                    }
                    else {
                        AJS.dropDownShower = function () {dd.show(); delete AJS.dropDownShowerTimer;};
                        AJS.dropDownShowerTimer = setTimeout(AJS.dropDownShower, 500);
                    }
                };
                var outHandler = function (e) {
                    var passingThrough = typeof AJS.dropDownShowerTimer != "undefined";
                    if (passingThrough) {
                        clearTimeout(AJS.dropDownShowerTimer);
                        delete AJS.dropDownShowerTimer;
                    }
                    if (typeof AJS.dropDownTimer != "undefined") {
                        clearTimeout(AJS.dropDownTimer);
                        delete AJS.dropDownHider;
                    }
                    AJS.dropDownHider = function () {dd.hide(); delete AJS.dropDownTimer;};
                    AJS.dropDownTimer = setTimeout(AJS.dropDownHider, 300);
                };
                a.click(function (e) { return false; });
                $it.mouseover(overHandler);
                $it.mouseout(outHandler);
            }
        });
    });

    /* TODO: Restore this once JQuery is integrated and HTMLUnit is upgraded to work with JQuery. */
    /*jQuery(function ($) {
        $(".popup-link").bind("click", function() {
            window.open(this.href, this.id + '-popupwindow', 'width=600, height=400, scrollbars, resizable');
            return false;
        });
    });*/

    var ids = ["action-view-source-link", "view-user-history-link"];
    for (var i = 0; i < ids.length; i++) {
        $("#" + ids[i]).click(function(e) {
            window.open(this.href, this.id + '-popupwindow', 'width=600, height=400, scrollbars, resizable');
            e.preventDefault();
            return false;
        });
    }

    var favourite = $("#page-favourite");
    favourite.click(function(e) {
        favourite.addClass("waiting");
        var params = {
            callback: function () {
                favourite.removeClass("waiting");
                favourite.toggleClass("selected");
                favourite.toggleClass("ie-page-favourite-selected");
            },
            errorHandler: function () {
                AJS.log("Error updating favourite");
            }
        };
        if (!favourite.hasClass("selected")) {
            AddLabelToEntity.addFavourite(AJS.params.pageId, params);
        }
        else {
            RemoveLabelFromEntity.removeFavourite(AJS.params.pageId, params);
        }
        return AJS.stopEvent(e);
    });

    var watch = $("#page-watch");
    watch.click(function(e) {
        watch.addClass("waiting");
        var params = {
            callback: function () {
                watch.removeClass("waiting").toggleClass("selected").toggleClass("ie-page-watching-selected");
            },
            errorHandler: function () {
                AJS.log("Error updating watch");
            }
        };
        if (!watch.hasClass("selected")) {
            PageNotification.startWatching(AJS.params.pageId, params);
        } else {
            PageNotification.stopWatching(AJS.params.pageId, params);
        }
        return AJS.stopEvent(e);
    });
});
