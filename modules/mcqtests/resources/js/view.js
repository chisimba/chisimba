jQuery(document).ready(function() {
    jQuery("#existingQ").change(function() {
        goToPage(jQuery(this).val());
    });
    hideAll();
});

var goToPage = function(page) {
    if(page != '-') {
        switch(page) {
            case 'mcq':
                hideAll();
                jQuery("#simpleDesc").show();
                break;
            case 'calcQ':
                hideAll();
                jQuery('#calcQDesc').show();
                break;
            case 'matchQ':
                hideAll();
                jQuery('#matchQDesc').show();
                break;
            case 'numericalQ':
                hideAll();
                jQuery('#numericalQDesc').show();
                break;
            case 'shortansQ':
                hideAll();
                jQuery('#shortansQDesc').show();
                break;
            case 'category':
                hideAll();
                jQuery('#categoryDesc').show();
                break;
            case 'descriptionlist':
                hideAll();
                jQuery('#adddescriptionDesc').show();
                break;
            case 'showRSA':
                hideAll();
                jQuery('#RSA').show();
                break;
            case 'showSCQ':
                hideAll();
                jQuery('#SCQ').show();
                break;
            default:
                hideAll();
        }
    }
}

var hideAll = function() {
    jQuery('#calcQDesc').hide();
    jQuery('#matchQDesc').hide();
    jQuery('#numericalQDesc').hide();
    jQuery('#shortansQDesc').hide();
    jQuery('#categoryDesc').hide();
    jQuery('#adddescriptionDesc').hide();
    jQuery("#simpleDesc").hide();
    jQuery("#RSA").hide();
    jQuery("#SCQ").hide();
}

var goAddQuestion = function() {
    var page = jQuery("#existingQ").val();
    if(page != '-') {
        switch(page) {
            case 'calcQ':
                showCalcQ();
                break;
            case 'matchQ':
                showMatchQ();
                break;
            case 'numericalQ':
                showNumericalQ();
                break;
            case 'shortansQ':
                showShortAnsQ();
                break;
            case 'category':
                showCategory();
                break;
            case 'descriptionlist':
                showDescription();
                break;
            case 'showRSA':
                showRSA();
                break;
            case 'showSCQ':
                showSCQ();
                break;
            default:
                showSimpleMCQ();
        }
    }
}
var showSCQ = function() {
    window.location.href = scqUrl;
}
var showCalcQ = function() {
    window.location.href = calqUrl;
}

var showMatchQ = function() {
    window.location.href = matchingqUrl;
}

var showDescription = function() {
   window.location.href = descriptionUrl;
}

var showCategory = function() {
    window.location.href = categoryUrl;
}

var showSimpleMCQ = function() {
    window.location.href = mcqUrl;
}

var showNumericalQ = function() {
    window.location.href = numericalqUrl;
}

var showShortAnsQ = function() {
    window.location.href = shortanswerqUrl;
}
var showRSA = function() {
    window.location.href = randomSAUrl;
}