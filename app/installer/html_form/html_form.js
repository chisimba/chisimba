
/*
* Some useful functions for dealing with the form through javascript
* Specific to Resolve, but easy to pull out for other use
*
*/

/**
* Convenience function for submitting the form, useful from hrefs
*
* @access public
*/
function submit_form(f)
{
	if (f == null) { f = document.main_form; }
	// make sure we clean any stuff up before we submit
	if (!f.onsubmit || f.onsubmit()) {
		f.submit();
	}

}//end submit_form()


/**
* Convenience function for setting hidden fields in the form
*
* @param string	$name	the name of the hidden field
* @param string	$value	the value to set it to
*
* @access public
*/
function set_hidden_field(name, value, f)
{
	if (f == null) { f = document.main_form; }
	if (f.elements[name]) {
		f.elements[name].value = value;
	}

}//end set_hidden_field()


/**
* Convenience function for setting text fields in the form
* NOTE: works the same way as a hidden field so just alias that fn
*
* @param string	$name	the name of the text field
* @param string	$value	the value to set it to
*
* @access public
*/
function set_text_field(name, value, f)
{
	if (f == null) { f = document.main_form; }
	if (f.elements[name]) {
		f.elements[name].value = value;
	}
}//end set_text_field()


/**
* Convenience function for setting button values fields in the form
* NOTE: works the same way as a hidden field so just alias that fn
*
* @param string	$name	the name of the text field
* @param string	$value	the value to set it to
*
* @access public
*/
set_button_value = set_hidden_field;


/**
* Ensures a field contains only numbers
*
* @param string $name				the name of the text field
* @param string $allow_negative		allows negative values
*
* @access public
*/
function validate_numeric_text_field(name, allow_negative)
{
	// if the string is not a number, or if negatives aren't allowed and the first character is a '-'.
	if (name.value * 1 != name.value || (name.value.length > 0 && allow_negative == false && name.value.charAt(0) == "-" ) ) {
		var outstr = "";
		for (ii = 0; ii < name.value.length ; ii++) {
			// if a number, or if negatives are allowed, a '-' at the beginning of the string
			if ((name.value.charAt(ii) * 1 == name.value.charAt(ii)) ||
			(allow_negative == true && ii == 0 && name.value.charAt(ii) == "-") ){
				outstr += name.value.charAt(ii);
			}
		}
		name.value = outstr;
		
		if (name.createTextRange) {
			// ie support
			var range = name.createTextRange();
			range.moveStart("character", name.value.length);
			range.moveEnd("character", 0);
			range.select();
		} else if (name.selectionStart || name.selectionStart == 0) {
			// mozilla support
			name.selectionEnd = name.selectionStart = name.value.length;
		}
	}

}


/**
* return the form element that is represented by the passed name
*
* @param string	$name	the name of the field
*
* @return object Form_Element
* @access public
*/
function get_form_element(name, f)
{
	if (f == null) { f = document.main_form; }

	if (f.elements[name]) {
		return f.elements[name];

	} else {
		return null;

	}// endif

}//end get_form_element()


/**
* returns the value for a field in the form
*
* @param string	$name	the name of the field
*
* @return string
* @access public
*/
function get_form_element_value(name, f)
{
	if (f == null) { f = document.main_form; }
	return (f.elements[name]) ? form_element_value(f.elements[name]) : '';

}//get_form_element_value()


/**
* Returns the value for any type of form element
* if select box or group of radio buttons returns the selected/checked value(s)
*    -> for multi-select boxes returns an array of selected values
* if array of any other type of elements returns the value of the first element in array
*
* @param object Form_Element	$element	the element whose value to find
*
* @return string
* @access public
*/
function form_element_value(element)
{
	// if element doesn't exist, die
	if (element == null) return "";

	// if its null then probably because it's an array, take the type from the first element
	if (element.type == null) element.type = element[0].type;

	switch (element.type) {
		case "select-one" :
			if (element.selectedIndex >= 0) {
				return element.options[element.selectedIndex].value;
			}
		break;

		case "select-multiple" :

			if (element.selectedIndex >= 0) {

				var retArr = new Array();

				for(var i = 0; i < element.options.length; i++) {
					if (element.options[i].selected) {
						retArr.push(element.options[i].value);
					}// endif
				}// end for

				if (retArr.length > 0) {
					return retArr;
				}
			}
		break;

		case "radio" :

			// if its an array of radio buttons then cycle through them
			if (element.length != null) {
				for(var i = 0; i < element.length; i++) {
					if (element[i].checked) {
						return element[i].value;
					}// endif
				}// end for

			} else {
				return element.value;
			}
		break;

		case "checkbox" :
			 return (element.checked) ? element.value : "";
		break;

		default :
			// if its an array of elements return the first ones value
			if (element.length != null && element[0] != null) {
				return element[0].value ;

			// else just return the value
			} else {
				return element.value;
			}

	}// end switch

	// else something not right so return blank
	return "";

}//end form_element_value()


/**
* Given a select box reference, returns the current text
*
* @param object Form_Select_Box	$element	the combo box element
*
* @return string
* @access public
*/
function get_combo_text(element)
{
	// just to make sure
	if (element.type != "select-one" && element.type != "select-multiple") return '';

	return element.options[element.selectedIndex].text;

}//end get_combo_text();


/**
* Checks a specific radio button with the element, that has the passed value
*
* @param object Form_Select_Box	$element	the radio button group
* @param string					$field_val	the value to look for in the radio button group
*
* @access public
*/
function check_radio_button(element, field_val)
{

	for(var i = 0; i < element.length; i++) {
		if (element[i].value == field_val) {
			element[i].checked = true;
			break;
		}
	}// end for

	return;

}//end check_radio_button()


/**
* Sets the selected var in the combo box for the option with the passed value
*
* @param object Form_Select_Box	$element	the combo box
* @param string					$field_val	the value to look for in the combo box
*
* @access public
*/
function highlight_combo_value(element, field_val) {

	// just to make sure
	if (element.type != "select-one" && element.type != "select-multiple") return;

	for(var i = 0; i < element.options.length; i++) {
		if (element.options[i].value == field_val) {
			element.options[i].selected = true;
			element.selectedIndex = i;
			break;
		}
	}// end for

}//end highlight_combo_value()


/**
* Moves the selected elements in a select box (can be multi-select) up or down one place
*
* @param object Form_Select_Box	$element	the combo box
* @param boolean				$move_up	whether to move up or down
*
* @access public
*/
function move_combo_selection(element, move_up)
{

	switch (element.type) {
		case "select-one" :
			if (element.selectedIndex >= 0) {
				if (move_up) {
					// can only move up if we ain't the first element
					if (element.selectedIndex > 0) {

						var i = element.selectedIndex;
						var tmp1 = new Option(element.options[i - 1].text, element.options[i - 1].value);
						tmp1.selected = element.options[i - 1].selected;

						var tmp2 = new Option(element.options[i].text, element.options[i].value);
						tmp2.selected = element.options[i].selected;

						element.options[i - 1] = tmp2
						element.options[i]     = tmp1;
					}// end if not first element

				// else moving down
				} else {

					// can only move down if we ain't the last element
					if (element.selectedIndex < (element.options.length - 1)) {
						var i = element.selectedIndex;
						var tmp1 = new Option(element.options[i + 1].text, element.options[i + 1].value);
						tmp1.selected = element.options[i + 1].selected;

						var tmp2 = new Option(element.options[i].text, element.options[i].value);
						tmp2.selected = element.options[i].selected;

						element.options[i + 1] = tmp2
						element.options[i]     = tmp1;

					}// end if not last element

				}// end if move_up

			}// end if selected index

		break;

		case "select-multiple" :

			if (move_up) {

				for(var i = 0; i < element.options.length; i++) {

					if (!element.options[i].selected) continue;

					// can only move up if we ain't the first element
					// and the element above it isn't selected
					if (i > 0 && !element.options[i - 1].selected) {
						var tmp1 = new Option(element.options[i - 1].text, element.options[i - 1].value);
						tmp1.selected = element.options[i - 1].selected;

						var tmp2 = new Option(element.options[i].text, element.options[i].value);
						tmp2.selected = element.options[i].selected;

						element.options[i - 1] = tmp2
						element.options[i]     = tmp1;
					}

				}// end for

			// else moving down
			} else {

				for(var i = element.options.length - 1; i > -1; i--) {

					if (!element.options[i].selected) continue;

					// can only move down if we ain't the last element
					// and the element above isn't selected
					if (i < (element.options.length - 1) && !element.options[i + 1].selected) {
						var tmp1 = new Option(element.options[i + 1].text, element.options[i + 1].value);
						tmp1.selected = element.options[i + 1].selected;

						var tmp2 = new Option(element.options[i].text, element.options[i].value);
						tmp2.selected = element.options[i].selected;

						element.options[i + 1] = tmp2
						element.options[i]     = tmp1;

					}//end if

				}// end for

			}// end if move_up

		break;

		default:
			alert(js_translate('element_not_combo_box', element.name ));

	}// end switch

}//end move_combo_selection()


/**
* Used by the date_box to verify that the date the user entered is correct
* and to set the hidden var
*
* @param string		$date_name	the name of the date field as passed into date_box()
* @param boolean	$show_time	whether the time is shown or not
*
* @access public
*/
function check_date(date_name, show_time)
{
	var f = document.main_form;

	var day_box   = get_form_element('day_'   + date_name);
	var month_box = get_form_element('month_' + date_name);
	var year_box  = get_form_element('year_'  + date_name);

	var day     = form_element_value(day_box);
	var month   = form_element_value(month_box);
	var year    = form_element_value(year_box);

	if (month == 2) {
		if (day == 29) {
			// if not leap year
			if (((year % 4) != 0) || ( ((year % 100) == 0) && ((year % 400) != 0))) {
				alert (js_translate('not_leap_year', year, day ));
				highlight_combo_element(day_box, 1);
				day_box.focus();
				return 0;
			}

		} else if (day > 29) {
			alert (js_translate('day_not_in_feburary', day));
			highlight_combo_element(day_box, 1);
			day_box.focus();
			return 0;
		}// end if
	}// end if

	if ((month == 4 || month == 6 || month == 9 || month == 11) && day == 31) {
		alert (js_translate('no_31st_of_month', get_combo_text(month_box)));
		highlight_combo_element(day_box, 1);
		day_box.focus();
		return 0;
	}

	var hour = 0;
	var min  = 0;

	// if we're showing the time boxes get them as well
	if(show_time) {
		var hour_box = get_form_element('hour_'   + date_name);
		var min_box  = get_form_element('min_' + date_name);

		hour = form_element_value(hour_box);
		min  = form_element_value(min_box);

	}// end if

	var time = new Date(year, month - 1, day, hour, min, 0);
	// divide by 1000, because getTime()
	// returns milliseconds since epoch not seconds
	var timestamp = time.getTime() / 1000;
	set_hidden_field(date_name, timestamp);

	return 1;

}//end check_date()


/**
* Used by the JS Calendar
*
* @param int	$d		the day field of the date
* @param int	$m		the month field of the date
* @param int	$y		the year field of the date
* @param string	$prefix	the prefix of the datetime field
*
* @access public
* @return void
*/
function datetime_set_date(d, m, y, prefix)
{
	var units = new Array('d', 'm', 'y');
	for (u in units) {
		eval('var value = ' + units[u] + ';');
		var id = prefix + 'value[' + units[u] + ']';
		var unit = document.getElementById(id);

		if (unit.type == 'text') {
			unit.value = value;
		} else {
			for (var i = 0; i < unit.options.length; i++) {
				if (value == unit.options[i].value) {
					unit.selectedIndex = i;
				}
			}
		}
	}

}//end datetime_set_date()


/**
* Create an input type="hidden" element to add to the DOM
*
* @param string		name	The name and ID for the field
* @param string		value	The value to put in it
*
* @access public
* @return object
*/
function createHiddenField(name, value)
{
	var newElt = document.createElement('INPUT');
	newElt.type = 'hidden';
	newElt.name = name;
	newElt.id = name;
	newElt.value = value;
	return newElt;

}//end createHiddenField()


/**
* Create an input type="text" element to add to the DOM
*
* @param string		name		The name and ID for the field
* @param int		size		The display size
* @param int		maxLength	Max number of chars it's alllowed to hold
* @param string		className	The CSS class to apply to it
* @param string		onFocus		Javascript code to be executed when the element gets the focus
* @param string		onBlur		Javascript code to be executed when the element loses the focus
*
* @access public
* @return object
*/
function createTextBox(name, value, size, maxLength, className, onFocus, onBlur)
{
	var newElt = document.createElement('INPUT');
	newElt.type = 'text';
	newElt.name = name;
	newElt.id = name;
	newElt.size = size;
	if (maxLength > 0) {
		newElt.maxLength = maxLength;
	}
	newElt.className = className;
	newElt.onfocus = new Function('', onFocus);
	newElt.onblur = new Function('', onBlur);
	return newElt;

}//end createTextBox()


/**
* Create an input type="button" element to add to the DOM
*
* @param string		name		The name and ID for the field
* @param string		label		What to show on the button
* @param string		onClick		Javascript code to be executed when the button is clicked
*
* @access public
* @return object
*/
function createButton(name, label, onClick)
{
	var newElt = document.createElement('INPUT');
	newElt.type = 'button';
	newElt.name = name;
	newElt.id = name;
	newElt.value = label;
	newElt.onclick = new Function('', onClick);
	return newElt;

}//end createTextBox()


/**
* Add a new asset finder widget above the 'more' button specified
*
* @param object		moreButton		The more button that was clicked to call this function
* @param string		nameBase		The base name to use for the actual fields
* @param string		safeNameBase	The base name to use for the buttons and text area
* @param string		typeCodesString	String indicating which type codes are allowed
* @param string		mapFrame		The javascript expression used to refer to the asset map's frame
* @param string		doneFn			Javascript function to be called when the finding process is finished
* @param boolean	showClear		Whether to show the 'clear' button in this asset finder
*
* @access public
* @return void
*/
function addNewAssetFinder(moreButton, nameBase, safeNameBase, typeCodesString, mapFrame, doneFn, showClear)
{
	var next_index = 0;
	while (document.getElementById(safeNameBase+'_'+next_index+'__label') != null) next_index++;
	parentElt = moreButton.parentNode;
	name = nameBase + '[' + next_index + ']';
	safeName = safeNameBase + '_' + next_index + '_';
	parentElt.insertBefore(document.createElement('BR'), moreButton);
	parentElt.insertBefore(createHiddenField(name+'[assetid]', 0), moreButton);
	parentElt.insertBefore(createHiddenField(name+'[url]', 0), moreButton);
	parentElt.insertBefore(createTextBox(safeName+'_label', '', 30, 0, 'sq-form-asset-finder', 'this.tmp = this.value;', 'this.value = this.tmp;'), moreButton);
	var changeButton = createButton(safeName+'_change_btn', 'Change', mapFrame+'.asset_finder_change_btn_press(\''+name+"', '"+safeName+"', '"+typeCodesString+"', "+doneFn+");");
	parentElt.insertBefore(changeButton, moreButton);
	if (showClear) {
		parentElt.insertBefore(createButton(safeName+'_clear_btn', 'Clear', mapFrame+'.asset_finder_clear_btn_press(\''+name+'\', \''+safeName+'\');'), moreButton);
	}

}//end addNewAssetFinder()


/**
* Make all submit buttons and normal buttons on the page look disabled
*
* Because some form processing relies on the submit value
* being in the POST, we will hide all the submits and buttons,
* inserting dummy disabled buttons and submits in their places
*
* @access public
* @return void
*/
function disable_buttons()
{
	inputs = document.getElementsByTagName('input');
	buttons = Array();
	for (i=0; i<inputs.length; i++) {
		if ((inputs[i].type == 'submit') || (inputs[i].type == 'button')) {
			buttons[buttons.length] = inputs[i];
		}
	}
	for (i=0; i<buttons.length; i++) {
		newElt = document.createElement('INPUT');
		newElt.type = buttons[i].type;
		newElt.value = buttons[i].value;
		newElt.disabled = 'disabled';
		buttons[i].style.display = 'none';
		buttons[i].parentNode.insertBefore(newElt, buttons[i]);
	}

}//end disable_buttons()


//--         FUNCTIONS FOR MULTI-ASSET-TYPES CHOOSER         --//


function prependClearButton(elt, inherit)
{
	newButton = document.createElement('input');
	newButton.type = 'button';
	newButton.value = js_translate('clear');
	if (inherit) {
		newButton.onclick = new Function("resetLastSelect(this); clearLastCheckbox(this);");
	} else {
		newButton.onclick = new Function("resetLastSelect(this)");
	}
	elt.parentNode.insertBefore(newButton, elt);
}

function prependTypeSelector(elt, inherit)
{
	var lastSelect = elt.previousSibling;
	while (lastSelect.tagName != 'SELECT') {
		lastSelect = lastSelect.previousSibling;
	}
	elt.parentNode.insertBefore(document.createElement('br'), elt);
	elt.parentNode.insertBefore(lastSelect.cloneNode(true), elt);
}

function prependInheritSelector(elt)
{
	hiddenField = elt.previousSibling;
	while ((hiddenField.tagName != 'INPUT') || (hiddenField.type.toUpperCase() != 'HIDDEN')) {
		hiddenField = hiddenField.previousSibling;
	}
	checkbox = elt.previousSibling;
	while ((checkbox.tagName != 'INPUT') || (checkbox.type.toUpperCase() != 'CHECKBOX')) {
		checkbox = checkbox.previousSibling;
	}
	newHiddenField = hiddenField.cloneNode(true);
	newHiddenField.value = '0';
	newCheckbox = checkbox.cloneNode(true);
	newCheckbox.checked = 0;
	newText = document.createTextNode('inherit ');
	elt.parentNode.insertBefore(newHiddenField, elt);
	elt.parentNode.insertBefore(newCheckbox, elt);
	elt.parentNode.insertBefore(newText, elt);
}

function resetLastSelect(elt)
{
	select = elt.previousSibling;
	while (select.tagName != 'SELECT') {
		select = select.previousSibling;
	}
	select.selectedIndex = 0;
}

function toggleLastHiddenField(checkbox)
{
	hiddenField = checkbox.previousSibling;
	while (!(hiddenField.tagName == 'INPUT') && (hiddenField.type.toUpperCase == 'HIDDEN')) {
		hiddenField = hiddenField.previousSibling;
	}
	hiddenField.value = checkbox.checked ? '1' : '0';
}

function clearLastCheckbox(elt)
{
	checkbox = elt.previousSibling;
	while ((checkbox.tagName != 'INPUT') || (checkbox.type.toUpperCase() != 'CHECKBOX')) {
		checkbox = checkbox.previousSibling;
	}
	if (checkbox.checked) {
		checkbox.click();
	}
}