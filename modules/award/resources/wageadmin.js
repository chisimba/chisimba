function updateUnitList(str) {
    if (str.length > 3) {
        var url = "index.php";
        var target = "myDiv";
        var pars = "module=award&action=ajax_updateunitlist&str="+str;
        var updateListAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
    }
}

function validSelection(dropdownId, message) {
    selection = $(dropdownId).options[$(dropdownId).selectedIndex];
    if (selection.value == -1) {
        alert(message);
        return false;
    } else {
        return true;
    }
}

function showCreate() {
    $('form_lrsCreate').style.visibility = 'visible'
    $('createunitbutton').disabled = true;
    $('selectexistingbutton').disabled = true;
    $('input_unitfilter').disabled = true;
    $('input_id').disabled = true;
    $('input_addUnit').value = $('input_unitfilter').value;
}

function hideCreate() {
    $('form_lrsCreate').style.visibility = 'hidden'
    $('createunitbutton').disabled = false;
    $('selectexistingbutton').disabled = false;
    $('input_unitfilter').disabled = false;
    $('input_id').disabled = false;    
}

function populatePB(tuId) {
    var url = "index.php";
    var target = "myDiv_input_branch";
    var pars = "module=award&action=ajax_updatepblist&tuId="+tuId;
    var updateListAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function populateSicDiv(smgId) {
    $('input_majGrp').disabled = true;
    $('input_majGrp').selectedIndex = 0;
    $('input_grp').disabled = true;
    $('input_grp').selectedIndex = 0;
    $('input_subGrp').disabled = true;
    $('input_subGrp').selectedIndex = 0;
    var url = "index.php";
    var target = "myDiv_input_div";
    var pars = "module=award&action=ajax_updatesicdivlist&id="+smgId;
    var updateListAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function populateSicMajGrp(divId) {
    $('input_grp').disabled = true;
    $('input_grp').selectedIndex = 0;
    $('input_subGrp').disabled = true;
    $('input_subGrp').selectedIndex = 0;
    var url = "index.php";
    var target = "myDiv_input_majGrp";
    var pars = "module=award&action=ajax_updatesicmajgrplist&id="+divId;
    var updateListAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function populateSicGrp(mGrpId) {
    $('input_subGrp').disabled = true;
    $('input_subGrp').selectedIndex = 0;
    var url = "index.php";
    var target = "myDiv_input_grp";
    var pars = "module=award&action=ajax_updatesicgrplist&id="+mGrpId;
    var updateListAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function populateSicSubGrp(grpId) {
    var url = "index.php";
    var target = "myDiv_input_subGrp";
    var pars = "module=award&action=ajax_updatesicsubgrplist&id="+grpId;
    var updateListAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function updateOccupationList(str) {
    if (str.length > 3) {
        var url = "index.php";
        var target = "myDiv";
        var pars = "module=award&action=ajax_updatesoclist&str="+str;
        var updateListAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
    }
}