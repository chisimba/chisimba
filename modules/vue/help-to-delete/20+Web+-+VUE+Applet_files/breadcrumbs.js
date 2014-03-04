AJS.toInit(function ($) {
    $("#ellipsis").click(function () {
        try {
            $(".hidden-crumb", $("#breadcrumbs")).removeClass("hidden-crumb");
            $(this).addClass("hidden-crumb");
        } catch(e) {
            AJS.log(e);
        }
    });
});
