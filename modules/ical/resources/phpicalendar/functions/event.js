<script language="JavaScript" type="text/javascript">
<!--
function openEventWindow(num) {
	// populate the hidden form
	var data = document.popup_data[num];
	var form = document.forms.eventPopupForm;
	form.elements.date.value = data.date;
	form.elements.time.value = data.time;
	form.elements.uid.value = data.uid;
	form.elements.cpath.value = data.cpath;
	form.elements.event_data.value = data.event_data;
	
	// open a new window
	var w = window.open('', 'Popup', 'scrollbars=yes,width=460,height=275');
	form.target = 'Popup';
	form.submit();
}

function EventData(date, time, uid, cpath, event_data) {
	this.date = date;
	this.time = time;
	this.uid = uid;
	this.cpath = cpath;
	this.event_data = event_data;
}

function openTodoInfo(vtodo_array) {	
	var windowW = 460;
	var windowH = 275;
	var url = "includes/todo.php?vtodo_array="+vtodo_array;
	options = "scrollbars=yes,width="+windowW+",height="+windowH;
	info = window.open(url, "Popup", options);
	info.focus();
}

document.popup_data = new Array();
//-->
</script>
