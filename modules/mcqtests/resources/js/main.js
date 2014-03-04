jQuery(document).ready(function() {
    hideAllForms();
    jQuery("#qnoption").change(function(){
        var val=this.value;
        var existingQ = jQuery('#existingQ').val();
        
        if(existingQ == 'oldQ') {
            processQuestionMethod(existingQ);
        }
        else if(existingQ == 'newQ') {
            checkNewQuestion(val);
        }
    });

    jQuery('#existingQ').change(function() {
        val = this.value;
        if(val == '-') {
            hideAllForms();
        }
        else {
            processQuestionMethod(val);
        }
    });
});

function processQuestionType() {
    if (document.getElementById('input_qnoption').value == '-')
    {
        alert('Please select an action');
        document.getElementById('input_qnoption').focus();
    } else {
        //document.getElementById('form_qnform').submit();
        document.getElementById('input_qnoptionlabel').textContent='Updated!';
    }
}

function checkNewQuestion(val) {
    hideAllForms();
    jQuery("#qtype").show();
    if(val == 'freeform'){
        jQuery('#freeform').show();
    }else if(val == 'mcq'){
        jQuery('#addquestion').show();
    }else if(val == 'addDescription'){
        jQuery('#addDescription').show();
    }else if(val == 'addRandomShortAnsMatching'){
        jQuery("#randomshortansmatching").show();
    }else if(val == 'addShortAns'){
        jQuery("#shortans").show();
    }
}

function processQuestionMethod(val) {
    hideAllForms();
    if(val == 'oldQ') {
        jQuery("#qtype").show();
        var type = jQuery("#qnoption").val();
        if(type == 'freeform' || type == 'mcq'){
            jQuery('#dbquestions').show();
            Ext.get('mcqGrid').show();
            getGridData();
        }
    }
    else if(val == 'newQ') {
        jQuery("#qtype").show();
        checkNewQuestion(jQuery("#qnoption").val());
    }
    else if(val == 'calcQ') {
        jQuery("#calcquestions").show();
        //showCalcQForm();
    }
    else if(val == 'matchQ') {
        jQuery("#matchingquestions").show();
    }
    else if(val == 'numericalQ') {
        jQuery("#numericalquestions").show();
    }
    else if(val == 'shortansQ') {
        jQuery("#shortanswerquestions").show();
    }
}

function hideAllForms() {
    jQuery("#shortans").hide();
    jQuery("#randomshortansmatching").hide();
    jQuery('#addquestion').hide();
    jQuery('#freeform').hide();
    jQuery("#dbquestions").hide();
    jQuery("#calcquestions").hide();    
    jQuery("#addDescription").hide();        
    jQuery("#matchingquestions").hide();
    jQuery("#numericalquestions").hide();
    jQuery("#mcqGrid").hide();
    jQuery("#qtype").hide();
    jQuery("#shortanswerquestions").hide();
}