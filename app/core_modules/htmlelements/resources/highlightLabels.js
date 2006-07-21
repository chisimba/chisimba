/*
Highlighting Labels

When designing HTML forms it's important to correlate a field's label with it's input, select or textarea element. With this is mind, this page demos some javascript which enhances the UI of a form which uses correctly marked-up inputs and labels. The javascript simply highlights a checkbox or radio's corresponding label, giving each label a nice visual clue to the selected options.

- Philip Lindsay 

http://www.xlab.co.uk/weblog/623
*/
function in_array(stringToSearch, arrayToSearch) {
	for (s = 0; s < arrayToSearch.length; s++) {
		thisEntry = arrayToSearch[s].toString();
		if (thisEntry == stringToSearch) {
			return true;
		}
	}
	return false;
}

function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	}
	else {
		window.onload = function() {
			oldonload();
			func();
		}
	}
}

function addEvent(elm, evType, fn, useCapture) {
	// cross-browser event handling for IE5+, NS6 and Mozilla 
	// By Scott Andrew 
	if (elm.addEventListener) { 
		elm.addEventListener(evType, fn, useCapture); 
		return true; 
	} else if (elm.attachEvent) { 
		var r = elm.attachEvent('on' + evType, fn); 
		return r; 
	} else {
		elm['on' + evType] = fn;
	}
}

function findTarget(e) {
	// cross-browser function to find the event target
	var target;
	if (window.event && window.event.srcElement) {
		// IE does it differently... stores the event in a window.event object
		target = window.event.srcElement;
	}
	if (e && e.target) {
		target = e.target;
	}
	return target;
}

function toggleLabel(e) {

	// find the target
	var target = findTarget(e);
	
	// get some of the targets attributes
	var targetID = target.getAttribute('id');
	var targetName = target.getAttribute('name');
	var targetType = target.getAttribute('type');
	
	if (targetType == 'radio' && targetName != '') {
	
		// build up array of related radios
		var relatedRadioIDs = new Array();
		var inputs = document.getElementsByTagName('input');
		var arrayCount = 0;
			
		// loop through all the inputs
		for (var j = 0; j < inputs.length; j++) {
			var inputElement = inputs[j];
			var inputName = inputElement.getAttribute('name');
			var inputID = inputElement.getAttribute('id');
			// if the name matches the targetName (i.e. a grouping of radios), store it's ID in the relatedRadioIDs array
			if (inputName == targetName) {
				relatedRadioIDs[arrayCount] = inputID;
				arrayCount++;
			}
		}
		
	}
	
	// find all labels
	var labels = document.getElementsByTagName('label');
	// loop through all label elements
	for (var i = 0; i < labels.length; i++) {
		var label = labels[i];
		//var labelFor = label.getAttribute('for');
		var labelFor = label.htmlFor;
		
		if (targetType == 'checkbox') {
			if (labelFor == targetID) {
				if (target.checked) {
					// add class
					label.className += ' checked';
				} else {
					// remove the class from the label
					label.className = label.className.replace(/\b ?checked\b/,'');
				}
			}
		}
		if (targetType == 'radio') {
			// remove class from all related Radios
			if (in_array(labelFor, relatedRadioIDs)) {
				// remove the class from the label
				label.className = label.className.replace(/\b ?checked\b/,'');
			}
				
			if (target.checked) {
				if (labelFor == targetID) {
					label.className += ' checked';
				}
			}
		}
	}
}

function setUpLabelHighlight() {
	// find all checkboxes
	var inputs = document.getElementsByTagName('input');
	for (var i = 0; i < inputs.length; i++) {
		inputElement = inputs[i];
		if (inputElement.getAttribute('type') == 'checkbox' || inputElement.getAttribute('type') == 'radio') {
			// attach onclick function
			addEvent(inputElement, 'click', toggleLabel, false);
			
			var targetID = inputElement.getAttribute('id');
			// find all labels
			var labels = document.getElementsByTagName('label');
			for (var x = 0; x < labels.length; x++) {
				label = labels[x];
				if (label.getAttribute('for') == targetID) {
					if (inputElement.checked) {
						// add class
						label.className += ' checked';
					} else {
						// remove class
						label.className = label.className.replace(/checked/g,'');
					}
				}
			}
		}
	}
}

