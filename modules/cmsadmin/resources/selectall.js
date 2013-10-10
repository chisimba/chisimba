function SetAllCheckBoxes(FormName, FieldName, CheckValue)
{
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++){
			objCheckBoxes[i].checked = CheckValue;
                }
}

/**
* Toggle checkboxes - if ToggleName is checked, check all boxes. if ToggleName is unchecked, uncheck all boxes
*/
function ToggleCheckBoxes(FormName, FieldName, ToggleName)
{
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	var objToggle = document.forms[FormName].elements[ToggleName];
	
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	
	// if box has been checked - check all boxes
	if(objToggle.checked == true){
	    CheckValue = true;
	}else{
	    CheckValue = false;
	}
	
	if(!countCheckBoxes){
		objCheckBoxes.checked = CheckValue;
	}else{
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++){
			objCheckBoxes[i].checked = CheckValue;
		}
	}
}

/**
* If checkbox is unchecked then uncheck ToggleName
*/
function ToggleMainBox(FormName, ToggleName, isChecked)
{
    if(!document.forms[FormName])
		return;
	var objToggle = document.forms[FormName].elements[ToggleName];
	
    if(isChecked == false){
        objToggle.checked = false;
    }
}

function checkSelect(FormName, FieldName)
{
	if(!document.forms[FormName])
		return false;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return false;
		
	var countCheckBoxes = objCheckBoxes.length;
	var check=false;
	if(!countCheckBoxes){
	    if(objCheckBoxes.checked == true){
	        return true;
	    }
	    return false;
	}
	    
	for(var i = 0; i < countCheckBoxes; i++){
	    if(objCheckBoxes[i].checked == true){
		    check = true;
		    break;
	    }
	}
	return check;	
}
/**
* Default function.  Usually would be overriden by the component
*/
function submitbutton(FormName,pressbutton) {
	submitform(FormName,pressbutton);
}

/**
* Submit the admin form
*/
function submitform(FormName,pressbutton){
	document.forms[FormName].task.value=pressbutton;
	try {
		document.forms[FormName].onsubmit();
		}
	catch(e){}
	document.forms[FormName].submit();
}