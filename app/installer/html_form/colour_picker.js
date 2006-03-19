/**
* +--------------------------------------------------------------------+
* | Squiz.net Open Source Licence                                      |
* +--------------------------------------------------------------------+
* | Copyright (c), 2003 Squiz Pty Ltd (ABN 77 084 670 600).            |
* +--------------------------------------------------------------------+
* | This source file may be used subject to, and only in accordance    |
* | with, the Squiz Open Source Licence Agreement found at             |
* | http://www.squiz.net/licence.                                      |
* | Make sure you have read and accept the terms of that licence,      |
* | including its limitations of liability and disclaimers, before     |
* | using this software in any way. Your use of this software is       |
* | deemed to constitute agreement to be bound by that licence. If you |
* | modify, adapt or enhance this software, you agree to assign your   |
* | intellectual property rights in the modification, adaptation and   |
* | enhancement to Squiz Pty Ltd for use and distribution under that   |
* | licence.                                                           |
* +--------------------------------------------------------------------+
*
* $Id$
*
*/

var colour_fields = new Array();
var colour_pickers = new Array();
var colour_picker_count = 0;
function load_colour_picker(field,picker_path) {
	colour_picker_count++;
	colour_fields[colour_picker_count] = field;
	colour_pickers[colour_picker_count] = window.open(picker_path + '/colour_picker.php?color=' + field.value + '&pickerid='+colour_picker_count, colour_picker_count, 'toolbar=no,width=238,height=164,titlebar=false,status=no,scrollbars=no,resizeable=yes');
}

function update_colour(colour,id) {
	if (colour_fields[id].value != colour) {
		colour_fields[id].value = colour;
		show_colour_change(colour_fields[id].name);
	} else {
		colour_fields[id].value = colour;
	}
}

function show_colour_change(name) {
	if (document.getElementById) {
		var changed_image = document.getElementById('colour_change_' + name);
		if (changed_image) { changed_image.src = colour_change_image_dir + 'tick.gif'; }
		var changed_span = document.getElementById('colour_span_' + name);
		if (changed_span) {
			colour_box = document.getElementById(name);
			changed_span.style.backgroundColor = colour_box.value;
		}
	} else {
		var changed_image = document['colour_change_' + name];
		if (changed_image) { changed_image.src = colour_change_image_dir + 'tick.gif'; }
	}
}

var nonhexdigits  = new RegExp('[^0-9a-fA-F]');
var nonhexletters = new RegExp('[g-zG-Z]');

function check_colour(value, allow_blanks) {
	if (value.length == 0 && allow_blanks) return '';

	var c;
	for (i=0;i<value.length;i++) {
		c = value.substring(i,i+1);
		if (c.match(nonhexdigits)) {
			if (c.match(nonhexletters)) {
				value = value.substring(0,i) + 'f' + value.substring(i+1,value.length);
			} else {
				value = value.substring(0,i-1) + '0' + value.substring(i+1,value.length);
			}
		}
	}
	var extra = 6 - value.length;
	for (i=0;i<extra;i++) value += '0';
	return value.toLowerCase();
}