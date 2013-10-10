var editLock = false;

function cancelEdit(id,val) {
	$('div_'+id).innerHTML = val;
	editLock = false;
}

function editMenuItem(id,wordGo,wordCancel) {
	if (editLock == false) {
		var edit;
		edit = "<input id='input_name' type='text' value='"+$('div_'+id).innerHTML+"' /><input value='"+wordGo+"' type='button' name='enter' id='input_enter' class='button' onclick = 'javascript:{updateItem(\""+id+"\",document.getElementById(\"input_name\").value);editLock = false;}' /> <input value='"+wordCancel+"' type='button' name='enter' id='input_enter' class='button' onclick = 'javascript:cancelEdit(\""+id+"\",\""+document.getElementById('div_'+id).innerHTML+"\")' />";
		document.getElementById('div_'+id).innerHTML = edit;
		editLock = true;
	}
}

function updateItem(id, name) {
    var url = "index.php";
    var target = "div_"+id;
    var pars = "module=award&action=ajax_updatemenuitem&id="+id+"&name="+name;
    var updateMenuAjax = new Ajax.Updater(target, url, {method: 'post', parameters: pars});
}

function populateConditions() {
	jQuery('.vibe').each(function(i) {
		var old = jQuery('#old_'+this.id).text();
		if (old != '--' && this.value == '') {
			this.value = old;
		}
	});
}

function changeExport(value) {
	if (value == 'conditions') {
		jQuery('.export_index').hide();
	} else {
		jQuery('.export_index').show();
	}
}