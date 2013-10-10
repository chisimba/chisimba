jQuery(document).ready(function() {
    var markedRadio = jQuery("input[name=unitmarked]:radio"),
        onlyUnitMarkedRadio = jQuery("input[name=onlyunitmarked]:radio"),
        dispUnit = jQuery("input[name=dispUnit]:radio");

    if(markedRadio.attr('checked')) {
        // make sure the other option is not checked then
        onlyUnitMarkedRadio.attr('checked', false);
        dispUnit.attr('checked', false);
    }
    
    markedRadio.bind('click',function(){
        changeMarked();
    });

    onlyUnitMarkedRadio.bind('click',function(){
        changeUnitOnlyMarked();
    });
});

var changeMarked = function() {
    // make sure the other option is not checked then
    jQuery("input[name=onlyunitmarked]:radio").attr('checked', false);
    jQuery("input[name=dispUnit]:radio").attr('checked', false);
}

var changeUnitOnlyMarked = function() {
    jQuery("input[name=unitmarked]:radio").attr('checked', false);
}