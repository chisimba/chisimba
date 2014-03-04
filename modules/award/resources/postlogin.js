function showLoading(button, divId) {
    document.getElementById(divId).innerHTML = "<img src='skins/_common/icons/loading_bar.gif' />";
    button.disabled = true;
}

function ajax_getAggregates(sicId,indexId,aggregate,socId,wageTypeId,agreeTypeId,startYear,years,minorSic,subSic) {
    var url = "index.php";
    var target = "aggregatesTab";
    var pars = "module=award&action=ajax_updateaggregates&sicId="+sicId+"&indexId="+indexId+"&aggregate="+aggregate+"&socId="+socId+"&wageTypeId="+wageTypeId+"&agreeTypeId="+agreeTypeId+"&startYear="+startYear+"&years="+years
    if (minorSic) {
        pars +="&minorSic="+minorSic;
    }
    if (subSic) {
        pars += "&subSic="+subSic;
    }
    var updateAggregateAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function validateAgreeDrop(dropdown,message) {
    if (dropdown[dropdown.selectedIndex].value == -1) {
        alert(message);
        return false;
    } else {
        return true;
    }
}

function changeSearch(radio) {
   if (radio == 0) {
        document.getElementById('label_filter').style.display = '';
        document.getElementById('input_filter').style.display = '';
        document.getElementById('label_sic').style.display = 'none';
        document.getElementById('input_sic').style.display = 'none';
        document.getElementById('label_sicDiv').style.display = 'none';
        document.getElementById('input_sicDiv').style.display = 'none';
        document.getElementById('label_type').style.display = 'none';
        document.getElementById('input_type').style.display = 'none';
        var search = document.getElementById('input_filter').value;
		if (search.length >= 2) {
			unitSearchByStr(search);
		}
    } else {
        document.getElementById('label_filter').style.display = 'none';
        document.getElementById('input_filter').style.display = 'none';
        document.getElementById('label_sic').style.display = '';
        document.getElementById('input_sic').style.display = '';
        document.getElementById('label_sicDiv').style.display = '';
        document.getElementById('input_sicDiv').style.display = '';
        document.getElementById('label_type').style.display = '';
        document.getElementById('input_type').style.display = '';
        unitSearchBySic();
    }
}

function updateSic(sicId) {
	var url = "index.php";
    var target = "dropdown_sicDiv";
    var pars = "module=award&action=ajax_updatesicdiv&sicId="+sicId;
    var updateSicAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars, onComplete: unitSearchBySic});
}

function unitSearchByStr(str) {
	var url = "index.php";
    var target = "unitSelect";
    var pars = "module=award&action=ajax_unitdropdowntext&search="+str;
    var unitSelectAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function unitSearchBySic() {
	var sicId = document.getElementById("input_sic").value;
    var sicDivId = document.getElementById("input_sicDiv").value;
    var agreeTypeId = document.getElementById('input_type').value;
    var url = "index.php";
    var target = "unitSelect";
    var pars = "module=award&action=ajax_unitdropdownsic&sic="+sicId+"&sicDiv="+sicDivId+"&agreeType="+agreeTypeId;
    var unitSelectAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function changeInflationIndex(indexId) {
    var url = "index.php";
    var target = "inflationDiv";
    var pars = "module=award&action=ajax_updateInflation&indexId="+indexId;
    var indexSelectAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
        
}

function editSummary(indexId,summary,saveLinkText,cancelLinkText) {
    document.getElementById("inflationSummary").innerHTML =
        "<br /><textarea id='summary' rows='3' cols='45'>"+summary+"</textarea><br />"+
        "<a href='javascript:;' onclick='javascript:saveSummary("+indexId+");'>"+saveLinkText+"</a> "+
        "<a href='javascript:;' onclick='javascript:cancelSummary("+indexId+");'>"+cancelLinkText+"</a>";
}

function cancelSummary(indexId) {
    var url = "index.php";
    var target = "inflationDiv";
    var pars = "module=award&action=ajax_updateInflation&indexId="+indexId;
    var summaryAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function saveSummary(indexId) {
    var summary = escape(document.getElementById("summary").value);
    var url = "index.php";
    var target = "inflationDiv";
    var pars = "module=award&action=ajax_saveinflationsummary&summary="+summary+"&indexId="+indexId;
    var summaryAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function updateConditions(agreeSelect,selectedTab) {
    var agreeId = agreeSelect.options[agreeSelect.selectedIndex].value;
    var target = "conditionsTab";
    var url = "index.php";
    var pars = "module=award&action=ajax_updateconditions&agreeId="+agreeId+"&selectedTab="+selectedTab;
    document.getElementById(target).innerHTML ='<br><img src="skins/_common/icons/loading_bar.gif">';
    var conditionsAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars, onComplete: function() { tabberAutomatic({addLinkId: true})} });
}

function updateOccupationWages(aggregateType, agreeTypeId, year, socText, industry, subsic) {
    document.getElementById("sub_button").disabled = true;
    document.getElementById("socWageTable").innerHTML = "<img src='skins/_common/icons/loading_bar.gif' />";
    var url = "index.php";
    var target = "socAggregates";
    var pars = "module=award&action=ajax_updatesocaggregates&aggregate="+aggregateType+"&agreeTypeId="+agreeTypeId+"&socText="+socText+"&year="+year+"&industry="+industry+"&subsic="+subsic;
    var socAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function updateIndustryAggregates(sicDrop,socId,aggregateType,agreeTypeId,wageTypeId,period,startYear) {
	var arrStr = '';
    var a_buttons = document.getElementsByName("ind_update");
    for (var j=0;j<a_buttons.length;j++) {
        a_buttons[j].disabled=true;
    }
    document.getElementById("ind_tables").innerHTML = "<img src='skins/_common/icons/loading_bar.gif' />";
	for (var i=0;i<sicDrop.options.length;i++) {
		if (sicDrop.options[i].selected) {
			arrStr += '|'+sicDrop.options[i].value;
		}
	}
    arrStr = arrStr.substr(1);
    var url = "index.php";
    var target = "industryAggregates";
    var pars = "module=award&action=ajax_updatesicaggregates&sicList="+arrStr+"&socId="+socId+"&aggregate="+aggregateType+"&agreeTypeId="+agreeTypeId+"&wageTypeId="+wageTypeId+"&period="+period+"&startYear="+startYear;
    var sicAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}