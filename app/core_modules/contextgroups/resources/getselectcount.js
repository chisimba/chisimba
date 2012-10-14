// (C) 2009 AVOIR
// Author: Jeremy O'Connor (based on core_modules/htmlelements/resources/selectall.js)

function getSelectCount(FormName, FieldName)
{
	if(!document.forms[FormName])
		return 0;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
	{
		return 0;
	}
	var countCheckBoxes = objCheckBoxes.length;
	if (!countCheckBoxes)
		return objCheckBoxes.checked?1:0;
    var T = 0;
    for (var i=0; i<countCheckBoxes; ++i) {
        T += objCheckBoxes[i].checked?1:0;
    }
    return T;
}