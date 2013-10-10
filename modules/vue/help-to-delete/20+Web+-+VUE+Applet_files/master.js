// ============================
// = Search field placeholder =
// ============================
AJS.toInit(function ($) {
    var $search = $("#quick-search-query");
    if (!$search.length) {
        return;
    }

    var search = $search.get(0);
    search.placeholder = AJS.params.quickSearchPlaceholder;
    search.placeholded = true;
    search.value = search.placeholder;

    if (!$.browser.safari) {

        $(search).addClass("placeholded");

        $("#quick-search-query").focus(function () {
            if (this.placeholded) {
                this.placeholded = false;
                this.value = "";
                $(this).removeClass("placeholded");
            }
        });

        $("#quick-search-query").blur(function () {
            if (this.placeholder && (/^\s*$/).test(this.value)) {
                this.value = this.placeholder;
                this.placeholded = true;
                $(this).addClass("placeholded");
            }
        });
    } else {
        search.type = "search";
        search.setAttribute("results", 10);
        search.setAttribute("placeholder", AJS.params.quickSearchPlaceholder);
        search.value = "";
    }
});
