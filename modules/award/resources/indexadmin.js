var editLock = false;

function updateIndexValue(valueId, value, month, year, update, back, errorMsg) {
	if (!editLock) {
		editLock = true;var formelement = $("indexCell_"+year+"_"+month);
		if(formelement != null) {
			htmlContent = "<input name='txtinputValue' type='text' value='"+value+"' size='4' class='text' id='inputValue' ><br><input value='"+update+"' type='button' name='select' id='submit' class='button' onclick='javascript:updateTheValue(\""+valueId+"\", \"inputValue\", "+month+", "+year+", \""+errorMsg+"\")' />&nbsp<input value='"+back+"' type='button' name='back' id='cancel' class='button' onclick='javascript:indexBack(\""+valueId+"\", \""+value+"\", "+month+", "+year+", \""+update+"\", \""+back+"\", \""+errorMsg+"\")' />";
			formelement.innerHTML = htmlContent;
		}
	}
}

function indexBack(valueId, value, month, year, update, back, errorMsg) {
	var formelement = $("indexCell_"+year+"_"+month);
	if(formelement != null) {
		htmlContent = "<a href='#' onclick = 'javascript:updateIndexValue(\""+valueId+"\", \""+value+"\", "+month+", "+year+", \""+update+"\", \""+back+"\", \""+errorMsg+"\")'>"+value+"</a>";
		formelement.innerHTML = htmlContent;
	}
	editLock = false;
}

function updateTheValue(valueId, val, month, year, errorMsg) {
	var obj1 = $(val);
	if(obj1.value!='') {
		var url = "index.php";
		var target = $("indexCell_"+year+"_"+month);
		var pars = "module=award&action=ajax_indexvalue&id="+valueId+"&value="+obj1.value+"&year="+year+"&month="+month;
		var updateIndexAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
		editLock = false;
	} else {
		alert(errorMsg);
	}
}