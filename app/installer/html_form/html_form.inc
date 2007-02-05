<?php

/**
* Prints a submit button
*
* @param string	$name		the form name to use for the submit button
* @param string	$value		the text that should be displayed for the submit button
* @param string	$on_click	any JS that should be executed when the submit button is pressed
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function submit_button($name, $value, $on_click='', $extras='')
{
	// Make sure that they don't use 'submit' as the form field, because it clashes
	// with the submit() fn that the form has
	if ($name == 'submit') {
		trigger_localised_error('SYS0019', E_USER_ERROR);
	}

	if ($on_click) {
		$extras .= ' onclick="'.htmlspecialchars($on_click).' "';
	}

	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}

	echo '<input type="submit" name="', htmlspecialchars($name), '" id="', htmlspecialchars($name), '" value="', htmlspecialchars($value), '" ', $extras, ' />';

}//end submit_button()


/**
* Prints a reset button, used to clear forms
*
* @param string	$name		the form name to use for the reset button
* @param string	$value		the text that should be displayed for the reset button
* @param string	$on_click	any JS that should be executed when the reset button is pressed
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function reset_button($name, $value, $on_click='', $extras='')
{
	// Make sure that they don't use 'reset' as the form field, because it clashes
	// with the reset() fn that the form has
	if ($name == 'reset') {
		trigger_localised_error('SYS0018', E_USER_ERROR);
	}

	if ($on_click) {
		$extras .= ' onclick="'.htmlspecialchars($on_click).' "';
	}

	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}

	echo '<input type="reset" name="', htmlspecialchars($name), '" id="', htmlspecialchars($name), '" value="', htmlspecialchars($value), '" ', $extras, ' />';

}//end reset_button()


/**
* Prints a normal form button
*
* @param string	$name		the form name to use for the button
* @param string	$value		the text that should be displayed for the button
* @param string	$on_click	JS to execute when the button is clicked
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function normal_button($name, $value, $on_click='', $extras='')
{
	if ($on_click) {
		$extras .= ' onclick="'.htmlspecialchars($on_click).' "';
	}
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	echo '<input type="button" name="', htmlspecialchars($name), '" id="', htmlspecialchars($name), '" value="', htmlspecialchars($value), '" ', $extras, ' />';

}//end normal_button()


/**
* Prints a hidden field
*
* @param string	$name		the form name to use for the hidden field
* @param string	$value		the text that should stored in the hidden field
*
* @return void
* @access public
*/
function hidden_field($name, $value='', $extras='')
{
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	if (!stristr($extras, 'id=')) {
		$extras .= ' id="'.htmlspecialchars($name).'"';
	}
	echo '<input type="hidden" name="', htmlspecialchars($name), '" value="', htmlspecialchars($value), '" ', $extras, ' />';

}//end hidden_field()


/**
* Prints a simple single line text box
*
* @param string		$name			the form name to use for the text box
* @param string		$value			the text that should be pre-entered in the text box
* @param int		$size			the width of the text box
* @param int		$max			the maximum number of characters allowed in the text box
* @param boolean	$hide_value		use the default value as indicative text only (hide it onFocus and restore onBlur
*									if needed) - useful if you want to use it as instructional text (as required by priority
*									level 3 accessability standards)
* @param string		$extras			any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function text_box($name, $value, $size='', $max='', $hide_value=false, $extras='')
{
	$size = (int) $size;
	$max  = (int) $max;
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	if (!stristr($extras, 'id=')) {
		$extras .= ' id="'.htmlspecialchars($name).'"';
	}
	echo	'<input type="text" ',
			'name="', htmlspecialchars($name), '" ',
			'value="', htmlspecialchars($value), '" ',
			(($size)   ? 'size="'.$size.'" '     : ''),
			(($max)    ? 'maxlength="'.$max.'" ' : ''),
			(($hide_value) ? 'onfocus="if (this.value == \''.htmlspecialchars($value).'\') { this.value = \'\'; }" onblur="if (this.value == \'\') { this.value=\''.htmlspecialchars($value).'\'; }" ' : ''),
			$extras,
			' />';

}//end text_box()


/**
* Prints a single line text box that only accepts integers
*
* @param string		$name			the form name to use for the text box
* @param string		$value			the text that should be pre-entered in the text box
* @param boolean	$allow_negative	whether negative values are allowed
* @param int		$size			the width of the text box
* @param int		$max			the maximum number of characters allowed in the text box
* @param boolean	$hide_value		use the default value as indicative text only (hide it onFocus and restore onBlur
*									if needed) - useful if you want to use it as instructional text (as required by priority
*									level 3 accessability standards)
* @param string		$extras			any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function int_text_box($name, $value, $allow_negative=true, $size='', $max='', $hide_value=false, $extras='')
{
	?><script type="text/javascript" src="<?php echo sq_web_path('lib')?>/html_form/html_form.js"></script><?php

	$allow_negative = $allow_negative ? 'true' : 'false';
	$javascript ='
		onkeyup="validate_numeric_text_field(this, '.$allow_negative.');"
		onchange="validate_numeric_text_field(this, '.$allow_negative.');"
	';

	text_box($name, $value, $size, $max, $hide_value, $javascript.$extras);

}//end int_text_box()


/**
* Prints a password box
*
* @param string	$name		the form name to use for the password box
* @param string	$value		the text that should be pre-entered in the password box
* @param int	$size		the width of the password box
* @param int	$max		the maximum number of characters allowed in the password box
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function password_box($name, $value='', $size='', $max='', $extras='')
{
	$size = (int) $size;
	$max  = (int) $max;
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	if (!stristr($extras, 'id=')) {
		$extras .= ' id="'.htmlspecialchars($name).'"';
	}
	echo	'<input type="password" ',
			'name="', htmlspecialchars($name), '" ',
			'value="', htmlspecialchars($value), '" ',
			(($size)   ? 'size="'.$size.'" '     : ''),
			(($max)    ? 'maxlength="'.$max.'" ' : ''),
			$extras,
			' />';

}//end password_box()


/**
* Prints a multi-line text box (text area)
*
* @param string	$name		the form name to use for the text area
* @param string	$contents	the text that should be pre-entered in the text area
* @param int	$cols		the width of the text area
* @param int	$rows		the height (in lines) of the text area
* @param int	$max		the maximum number of characters allowed in the text area
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function text_area($name, $contents, $cols=0, $rows=0, $max=0, $extras='')
{
	$cols = (int) $cols;
	$rows = (int) $rows;
	$max  = (int) $max;
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	if (!stristr($extras, 'id=')) {
		$extras .= ' id="'.htmlspecialchars($name).'"';
	}
	echo	'<textarea name="', htmlspecialchars($name), '" ',
			(($cols)  ? 'cols="'.$cols.'" ' : ''),
			(($rows)  ? 'rows="'.$rows.'" ' : ''),
			(($max)   ? 'onkeypress="if (this.value.length > '.$max.') { this.value = this.value.substr(0,'.$max.'); alert(js_translate(\'max_characters_warning\', \''.$max.'\')); this.focus()}" ' : ''),
			$extras,
			'>',
			htmlspecialchars($contents),
			'</textarea>';

}//end text_area()


/**
* Prints a radio button
*
* @param string	$name		the form name to use for the radio button
* @param string	$value		the value that will come through when the button is selected
* @param bool	$checked	whether the button is checked by default or not
* @param string	$on_click	any JS that should be executed when the submit button is pressed
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function radio_button($name, $value='1', $checked=false, $on_click='', $extras='')
{
	if ($on_click) {
		$extras .= ' onclick="'.htmlspecialchars($on_click).' "';
	}
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	if (!stristr($extras, 'id=')) {
		$extras .= ' id="'.htmlspecialchars($name).'"';
	}
	echo	'<input type="radio" ',
			'name="', htmlspecialchars($name), '" ',
			'value="', htmlspecialchars($value), '" ',
			(($checked) ? 'checked="checked" ' : ''),
			$extras,
			' />';

}//end radio_button()


/**
* Prints a check box
*
* @param string	$name		the form name to use for the check box
* @param string	$value		the value that will come through when the box is selected
* @param bool	$checked	whether the button is checked by default or not
* @param string	$on_click	any JS that should be executed when the submit button is pressed
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function check_box($name, $value='1', $checked=false, $on_click='', $extras='')
{
	if ($on_click) {
		$extras .= ' onclick="'.htmlspecialchars($on_click).' "';
	}
	if (!stristr($extras, 'id=')) {
		$extras .= ' id="'.htmlspecialchars($name).'"';
	}
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	echo	'<input type="checkbox" ',
			'name="', htmlspecialchars($name), '" ',
			'value="', htmlspecialchars($value), '" ',
			(($checked) ? 'checked="checked" ' : ''),
			$extras,
			' />';

}//end check_box()


/**
* Given a name, an array of keys and values, and a default value, prints a dropdown box
*
* @param string	$name		the form name to use for the dropdown box
* @param Array	$contents	an array of whose keys are values to be returned when that
*							option is selected and whose values are the display text for the option
* @param bool	$multiple	whether you can multiple selections can be made or not
* @param mixed	$defaults	if there is only one default can be a string, or if there are
*							multiple can be an array
* @param int	$height		the height (in lines) of the combo box
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
* @param int	$maxlength	the maximum number of characters displayed for any option,
*							anything over gets truncated with '...' appended
*
* @return void
* @access public
*/
function combo_box($name, $contents, $multiple, $defaults, $height=0, $extras='', $maxlength=0)
{
	$height    = (int) $height;
	$maxlength = (int) $maxlength;
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	if ($multiple) {
		$extras .= ' multiple="multiple"';
		$name .= '[]';
	}
	if (!stristr($extras, 'id=')) {
		$extras .= ' id="'.htmlspecialchars($name).'"';
	}

	if (!is_array($contents)) $contents = Array($contents);
	if (!is_array($defaults)) $defaults = Array($defaults);

	echo	'<select name="', htmlspecialchars($name), '" ',
			(($height > 1)? 'size="'.$height.'" ' : ''),
			$extras,
			">\n";

	foreach ($contents as $key => $value) {
		if ($maxlength) {
			$value = str_replace('&nbsp;', ' ', $value);  // we don't want a space to take 6 chars do we ?
			if (strlen($value) > $maxlength) {
				$value = substr($value, 0, $maxlength - 3).'...';
			}
		}
		$value = str_replace(' ', '&nbsp;', htmlspecialchars($value));
		echo	'<option value="', htmlspecialchars($key), '"',
				((in_array((string) $value, $defaults)) ? ' selected="selected"' : ''),
				'>',
				$value,
				"</option>\n";
	}
	echo "</select>\n";

}//end combo_box()


/**
* Prints a label
*
* @param string	$text	The text to go inside the label
* @param string	$for	The ID of the form element this label applies to
* @param string $extras	Any extra HTML to put in the label tag
*
* @return void
* @access public
*/
function label($text, $for='', $extras='')
{
	if (!empty($for)) $extras .= ' for="'.$for.'"';
	if (!empty($extras)) $extras = ' '.$extras;
	echo '<label'.$extras.'>'.$text.'</label>';

}//end label()


/**
* Prints an entry box for a colour picker, requires javascript and colour_picker script
*
* @param string	$name					the form name to use for the colour box
* @param Array	$default				the colour you want to display to begin with
* @param bool	$allow_blanks			allow no colour to be selected ot not
* @param mixed	$button_text			text on the button that displays the colour picker
* @param int	$show_text_box			show the text box to allow manual entry of a colour
* @param string	$show_current_colour	show a box with the current displayed
* @param int	$show_change_colour		show a tick or cross to indicate if the colour has changed
*
* @return void
* @access public
*/
function colour_box($name, $default='000000', $allow_blanks=false, $button_text='*', $show_text_box=true, $show_current_colour=true, $show_change_colour=true)
{

	$default = strtolower($default);
	$default = preg_replace('/^#/', '', $default);

	static $initialised = false;
	if (!$initialised) {
		?>
		<script type="text/javascript" src="<?php echo sq_web_path('lib')?>/html_form/colour_picker.js"></script>
		<script type="text/javascript">var colour_change_image_dir='<?php echo sq_web_path('lib');?>/web/images/'</script>
		<?php
		$initialised = true;
	}

	if ($show_current_colour) {
		?>
		<span id="colour_span_<?php echo $name; ?>" style="background-color: <?php echo $default; ?>;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
		<?php
	}

	if ($show_text_box) {
		$extras  = ' id="colour_box_'.$name.'" style="font-family: courier;"'
				 .' onchange="this.value = check_colour(this.value, '.(($allow_blanks) ? 'true' : 'false').');"';
		if ($show_current_colour) {
			 $extras .= ' show_colour_change(\''.addslashes($name).'\');';
		}
		text_box($name, $default, 7, 6, false, $extras);
	} else {
		hidden_box($name, $default);
	}

	normal_button('', " $button_text ",
					  "load_colour_picker(this.form.elements['".addslashes($name)."'],'".sq_web_path('lib')."/html_form')",
					  'style="font-family: courier;"');

	if ($show_change_colour) {
		?>
		<img id="colour_change_<?php echo $name; ?>" name="colour_change_<?php echo $name; ?>" src="<?php echo sq_web_path('lib'); ?>/web/images/cross.gif" width="15" height="15" border="0" align="bottom" />
		<?php
	}

}//end colour_box()


/**
* Returns a file upload box
* @param string	$name		the form name to use for the file upload box
* @param string	$extras		any input field attributes that should be placed in the <input ... > tag
*
* @return void
* @access public
*/
function file_upload($name, $extras='')
{
	if (!stristr($extras, 'class=')) {
		$extras .= ' class="sq-form-field"';
	}
	echo '<input type="file" name="', $name, '" ', $extras, ' />';

}//end file_upload()


/**
* Returns the information for an uploaded file if the upload was successful
*
* @param string	$name		the form name from the file upload box
*
* @return mixed (bool|array) FALSE on error, blank array on no file upload and the $_FILES[$name] if a file was uploaded successfully
* @access public
*/
function get_file_upload_info($name)
{
	if (empty($_FILES[$name])) return false;

	$nice_name = str_replace('_', ' ', $name);

	// if something went wrong, sorry bad luck
	switch ($_FILES[$name]['error']) {

		case UPLOAD_ERR_NO_FILE :
			return Array();
		break;

		case UPLOAD_ERR_INI_SIZE  :
			trigger_localised_error('SYS0022', E_USER_WARNING, $nice_name, easy_filesize(ini_get('upload_max_size')));
			return false;
		break;

		case UPLOAD_ERR_FORM_SIZE :
			trigger_localised_error('SYS0021', E_USER_WARNING, $nice_name, easy_filesize($_POST['MAX_FILE_SIZE']));
			return false;
		break;

		case UPLOAD_ERR_PARTIAL :
			trigger_localised_error('SYS0023', E_USER_WARNING, $nice_name);
			return false;
		break;

	}//end switch

	if (empty($_FILES[$name]['name']) || !is_uploaded_file($_FILES[$name]['tmp_name'])) {
		return false;
	}

	return $_FILES[$name];

}//end get_file_upload_info()


/**
* Commits an uploaded file to its intended place
*
* @param string	$name			the form name from the file upload box
* @param string	$dest			the destination for the uploaded file, if this is a directory,
*								the file is put in that directory if it is a file, it is written
*								to that file (taking into account $overwrite)
* @param bool	$overwrite		whether to overwrite any existing file or not
* @param int	$max_size		the maximum number of bytes that the uploaded file can be
* @param bool	$restrict_exts	if true extension MUST BE in $extensions,
*								if false must NOT be in $extensions
* @param Array	$extensions		if not empty, the only extensions that are not allowed to be uploaded
*
* @return mixed (bool|string) FALSE on error, blank string on no file upload and the filename (basename) if a file was uploaded
* @access public
*/
function commit_file_upload($name, $dest, $overwrite=false, $max_size=0, $restrict_exts=true, $extensions=Array())
{
	$info = get_file_upload_info($name);
	if ($info === false) return false;
	if (!count($info))   return '';

	$nice_name = str_replace('_', ' ', $name);

	if ($max_size > 0 && $max_size < $info['size']) {
		trigger_localised_error('SYS0020', E_USER_WARNING, $nice_name, easy_filesize($max_size));
		return false;
	}

	//// if we get this far the file on the server OK ////

	// Are we worried about the extension ?
	if (count($extensions)) {
		$exts = Array();
		foreach ($extensions as $v) {
			$exts[] = strtolower($v);
		}
		$ext = strtolower(substr(strrchr($info['name'], '.'), 1));
		$in_arr = in_array($ext, $exts);

		// see $restrict_exts documentation in function header
		if (($restrict_exts && !$in_arr) || (!$restrict_exts && $in_arr)) {
			trigger_localised_error('SYS0002', E_USER_WARNING, $ext, $nice_name);
			return false;
		}//end if
	}//end if

	//// Let's try and find out what our final destination file is going to be ////

	// Doe dest exist and is it a directory ?
	if (is_dir($dest)) {
		if (!is_writable($dest)) {
			trigger_localised_error('SYS0007', E_USER_WARNING, $dest);
			return false;
		}
		$dest .= '/'.$info['name'];
	}

	// Now we know we have a proper filename, if it exists are we allowed to write over it
	if (file_exists($dest)) {
		if (!$overwrite) {
			trigger_localised_error('SYS0027', E_USER_WARNING, $info['name']);
			return false;
		}
		if (!is_writable($dest)) {
			trigger_localised_error('SYS0008', E_USER_WARNING, $dest);
			return false;
		}

	// the destination doesn't exist
	} else {
		require_once SQ_FUDGE_PATH.'/';
		$parent = dirname($dest);
		// if the parent isn't a directory, can we create it ?
		if (!is_dir($parent) && !create_directory($parent)) {
			trigger_localised_error('SYS0015', E_USER_WARNING, $parent);
			return false;
		} else if (!is_writable($parent)) {
			trigger_localised_error('SYS0016', E_USER_WARNING, $parent);
			return false;
		}
	}//end dest exists

	// Move file over
	if (!move_uploaded_file($info['tmp_name'], $dest)) {
		trigger_localised_error('SYS0025', E_USER_WARNING, $dest);
		return false;
	}

	return basename($dest);

}//end commit_file_upload()


/**
* Get a string version of the given array of type codes, for passing to the asset map
*
* @param array	$type_codes		The type codes to encode
*
* @access public
* @return string
*/
function get_type_codes_string($type_codes)
{
	$all_type_codes = Array();
	foreach ($type_codes as $type_code => $flag) {
		$all_type_codes[] = $type_code;
		switch ($flag) {
			case 'A' :
				$ancestors = $GLOBALS['SQ_SYSTEM']->am->getTypeAncestors($type_code);
				foreach ($ancestors as $ancestor) {
					$all_type_codes[] = $ancestor;
				}
			break;
			case 'D' :
				$descendants = $GLOBALS['SQ_SYSTEM']->am->getTypeDescendants($type_code);
				foreach ($descendants as $descendant) {
					$all_type_codes[] = $descendant;
				}
			break;
		}//end switch
	}//end foreach

	if (count($all_type_codes) == 1) {
		$type_codes = implode('', $all_type_codes).'|';
	} else {
		$type_codes = implode('|', $all_type_codes);
	}
	return $type_codes;

}//end get_type_codes_string()


/**
* Prints an interface for choosing an asset from the asset map
*
* @param string		$name			the name to use for the field
* @param int		$assetid		current value for this field
* @param array		$type_codes		Array('[type_code]' => '[flag]')
*									where flag = (A|D|I) -> Ancestors, Descendants or Only Itself
* @param string		$map_frame		a reference to the map_frame so we can get the asset map
									if this is an empty string, then the asset_finder will look for the asset map
									in the frame where it was loaded
* @param boolean	$new_window		is the asset finder in a different window than the asset map
* @param string		$done_fn		a function to call when the asset finder is finished
* @param array		$buttons		an array of which additional buttons to print (clear, reset)
*
* @return void
* @access public
*/
function asset_finder($name, $assetid=0, $type_codes=Array(), $map_frame='sq_sidenav', $new_window=false, $done_fn='null', $buttons=Array('clear', 'reset'))
{
	$finder_frame = ($new_window || empty($map_frame)) ? 'parent.top' : 'parent.frames["sq_main"]';

	if (!empty($map_frame)) {
		$map_frame = trim('parent.frames["'.$map_frame.'"]', '.');
	} else {
		$map_frame = 'top';
	}
	if ($new_window) {
		$map_frame = 'window.opener.'.$map_frame;
	}

	$label = '';
	if ($assetid) {
		$asset = &$GLOBALS['SQ_SYSTEM']->am->getAsset($assetid);
		if (is_null($asset)) {
			$assetid = 0;
		} else {
			$label = $asset->short_name.' (Id : #'.$asset->id.')';
		}
	}

	$safe_name = 'sq_asset_finder_'.preg_replace('/[^A-Za-z0-9]/', '_', $name);


	$type_codes = get_type_codes_string($type_codes);

	hidden_field($name.'[assetid]', $assetid);
	hidden_field($name.'[url]', '');
	// call the js function to set where the finder frame is
	text_box($safe_name.'_label', $label, 30, 0, false, 'readonly="readonly" class="sq-form-asset-finder" onfocus="this.tmp = this.value;" onblur="this.value = this.tmp;"');

	// only print the buttons if in the backend (where there's an asset map frame available)
	if (SQ_IN_BACKEND || defined('SQ_PAINTED_SIMPLE_ASSET_MAP')) {
		?>
			<script type="text/javascript">
				<?php echo $map_frame; ?>.set_finder(<?php echo $finder_frame; ?>);
			</script>
		<?php
		normal_button($safe_name.'_change_btn', translate('change'), $map_frame.'.asset_finder_change_btn_press(\''.$name.'\', \''.$safe_name.'\', \''.addslashes($type_codes).'\', '.$done_fn.');');
		if (in_array('clear', $buttons)) {
			normal_button($safe_name.'_clear_btn', translate('clear'), $map_frame.'.asset_finder_clear_btn_press(\''.$name.'\', \''.$safe_name.'\');');
		}
		if (in_array('reset', $buttons)) {
			normal_button($safe_name.'_reset_btn', translate('reset'), $map_frame.'.asset_finder_reset_btn_press(\''.$name.'\', \''.$safe_name.'\', \''.$assetid.'\', \''.addslashes($label).'\');');
		}
	}

}//end asset_finder()


/**
* Prints an interface for choosing several assets from the asset map
*
*
* @param string		$name			the base name to use for the form fields
* @param array		$assetids		current values for this field
* @param array		$type_codes		Array('[type_code]' => '[flag]')
*									where flag = (A|D|I) -> Ancestors, Descendants or Only Itself
* @param string		$map_frame		Name of the frame containing the asset map
									if this is an empty string, then the asset_finder will look for the asset map
									in the frame where it was loaded
* @param boolean	$new_window		is the asset finder in a different window than the asset map
* @param string		$done_fn		a javascript function to call when the asset finder is finished
* @param array		$buttons		an array of which additional buttons to print (clear and/or reset)
*
* @return void
* @access public
*/
function multiple_asset_finder($name, $assetids=Array(), $type_codes=Array(), $map_frame='sq_sidenav', $new_window=false, $done_fn='null', $buttons=Array('clear'))
{
	?><script type="text/javascript" src="<?php echo sq_web_path('lib')?>/html_form/html_form.js"></script><?php

	echo '<div>';
	foreach ($assetids as $index => $assetid) {
		asset_finder($name.'['.$index.']', $assetid, $type_codes, $map_frame, $new_window, $done_fn);
		echo '<br />';
	}

	if (SQ_IN_BACKEND) {
		$index = count($assetids);
		asset_finder($name.'['.$index.']', 0, $type_codes, $map_frame, $new_window, $done_fn, Array('clear'));
		$index++;

		if (!empty($map_frame)) {
			$map_frame_exp = trim('parent.frames["'.$map_frame.'"]', '.');
		} else {
			$map_frame_exp = 'top';
		}

		$safe_name = 'sq_asset_finder_'.preg_replace('/[^A-Za-z0-9]/', '_', $name);
		$type_codes_string = get_type_codes_string($type_codes);
		echo '<input type="button" value="'.translate('more').'..." onclick="addNewAssetFinder(this, \''.$name.'\', \''.$safe_name.'\', \''.addslashes($type_codes_string).'\', \''.htmlentities($map_frame_exp).'\', '.$done_fn.', '.(in_array('clear', $buttons) ? 'true' : 'false').')" />';
		echo '<br />';
		echo '</div>';
	}

}//end multiple_asset_finder()


/**
* Prints an interface for entering a security key genereted inside an image
* Call validate_security_key() to check if the value entered was correct
*
* @param int		$key_len		the lenght of the random string to display in the image
* @param int		$box_len		the width of the text box used to enter the security key shown
* @param float		$zoom			each character will be randomly zoomed between x1 and this figure
* @param boolean	$img_on_newline	whether the img should appear under the box or not
* @param string		$bg				the background colour of the image (eg FFFFFF)
* @param string		$text			the colour of the text in the image (eg 000000)
* @param string		$border			the colour of the border image (eg 000000)
*									for no border, set to the same colour as the background
*
* @return void
* @access public
*/
function security_key($key_len, $box_len, $zoom=1, $img_on_newline=false, $bg='FFFFFF', $text='000000', $border='000000')
{
	// generate a random security key string
	require_once SQ_FUDGE_PATH.'/general/general.inc';
	$password = generate_security_key($key_len);

	// work out the width and height of the image
	$img_width  = $key_len * 10.5;
	$img_height = 20;

	// we need to store these vars in the session so we can get the
	// system to print the security image for us
	$security_vars = Array(
						'key'			=> $password,
						'width'			=> $img_width,
						'height'		=> $img_height,
						'bg_colour'		=> $bg,
						'text_colour'	=> $text,
						'border_colour'	=> $border,
						'zoom'			=> $zoom,
					 );
	$_SESSION['SQ_SYSTEM_SECURITY_KEY'] = $security_vars;

	// generate the url to get the security image to print
	$img_url = $_SERVER['PHP_SELF'].'?SQ_ACTION=send_security_key';
	?>
	<table border="0">
		<tr>
			<td valign="top"><?php echo text_box('SQ_SYSTEM_SECURITY_KEY_VALUE', '', $box_len); ?></td>
	<?php
	if ($img_on_newline) {
	?>
		</tr>
		<tr>
	<?php
	}//end if
	?>
			<td valign="top"><img id="sq_security_key" src="<?php echo $img_url; ?>" border="0" /></td>
		</tr>
		<tr>
		<?php
			ob_start();
				?><a href="#" onclick="var img = document.getElementById('sq_security_key'); img.src = '<?php echo $img_url; ?>&regen_key=1'; return false;"><?php echo translate('security_key_link'); ?></a><?php
				$replacements_link = ob_get_contents();
			ob_end_clean()
		?>
			<td class="sq-backend-data" colspan="<?php echo (($img_on_newline) ? '1' : '2'); ?>"><i><?php echo translate('security_key_question', $replacements_link); ?></i></td>
		</tr>
	</table>
	<?php

}//end security_key()


/**
* Returns true if the security key entered using the interface in security_key() is correct
*
* @return boolean
* @access public
*/
function validate_security_key()
{
	if (!isset($_POST['SQ_SYSTEM_SECURITY_KEY_VALUE'])) {
		return false;
	}
	if (!isset($_SESSION['SQ_SYSTEM_SECURITY_KEY']['key'])) {
		return false;
	}
	$entered_value = $_POST['SQ_SYSTEM_SECURITY_KEY_VALUE'];
	$real_value = $_SESSION['SQ_SYSTEM_SECURITY_KEY']['key'];
	unset($_SESSION['SQ_SYSTEM_SECURITY_KEY']);
	return ($entered_value === $real_value);

}//end validate_security_key()


/**
* Takes a variable and outputs as many hidden fields as necessary to recreate it
*
* @param string	$name			the form name to use for the field
* @param int	$assetid		current value for this field
* @param array	$type_codes		Array('[type_code]' => '[flag]')
*								where flag = (A|D|I) -> Ancestors, Descendants or Only Itself
*
* @return void
* @access public
*/
function var_to_hidden_field($var, $name='')
{
	if (is_array($var) || is_object($var)) {
		foreach ($var as $k => $v) {
			var_to_hidden_field($v, (($name) ? $name.'['.$k.']': $k));
		}
	} else {
		if ($name) hidden_field($name, $var);
	}

}//end var_to_hidden_field()


/**
* Print an interface widget to choose asset type(s)
*
* If $show_inherit_checkbox=false, then $_REQUEST[$name] will be type code, or array of type codes.
* Otherwise it will be an Array('type_code' => (Array of) type code(s), 'inherit' => (Array of) 1(s) or 0(s))
*
* @param string		$name					Name for the field
* @param boolean	$multiple				Whether we want to be able to choose several types
* @param Array		$defaults				The default values
* @param boolean	$instatiable			Do we want instantiable or non-instantiable assets? (null means both)
* @param boolean	$system_accessible		Do we want to include assets that only have system access?
* @param boolean	$show_inherit_checkbox	Do we want to show a checkbox for inheriting from a type?
* @param Array		$specified_types 		Limit the listing to specified types and their decendants
*
* @access public
* @return void
*/
function asset_type_chooser($name, $multiple=false, $defaults=Array(), $instantiable=null, $system_accessible=false, $show_inherit_checkbox=false, $specified_types=Array())
{
	//if we specified types for the listing, build a list with these
	if (!empty($specified_types)) {

		foreach ($specified_types as $specified_type) {
			$new_types = $GLOBALS['SQ_SYSTEM']->am->getTypeDescendants($specified_type, true);

			foreach ($new_types as $new_type) {
				$types[$new_type] = $GLOBALS['SQ_SYSTEM']->am->getTypeInfo($new_type, 'name');
			}
		}
	} else {
		$types = $GLOBALS['SQ_SYSTEM']->am->getAssetTypes($instantiable, !$system_accessible);

		foreach ($types as $code => $details) {
			$types[$code] = $details['name'];
		}
	}

	asort($types);
	$types = Array('' => '') + $types;
	if ($multiple) {
		if ($show_inherit_checkbox) {
			if (!empty($defaults)) {
				foreach ($defaults['type_code'] as $i => $type_code) {
					if (empty($type_code)) continue;
					combo_box($name.'[type_code][]', $types, false, $type_code);
					hidden_field($name.'[inherit][]', $defaults['inherit'][$i]);
					check_box('', 1, $defaults['inherit'][$i], 'toggleLastHiddenField(this)');
					label(strtolower(translate('inherit')), '');
					normal_button('', translate('clear'), 'resetLastSelect(this)');
					echo '<br />';
				}
			}
			combo_box($name.'[type_code][]', $types, false, '');
			hidden_field($name.'[inherit][]', 0);
			check_box('', 1, false, 'toggleLastHiddenField(this)');
			label(strtolower(translate('inherit')), '');
			normal_button('', translate('clear'), 'resetLastSelect(this); clearLastCheckbox(this);');
			normal_button('', translate('more').'...', 'prependTypeSelector(this); prependInheritSelector(this); prependClearButton(this, true)');
		} else {
			foreach ($defaults as $default) {
				if (!empty($default)) {
					combo_box($name.'[]', $types, false, $default);
					normal_button('', translate('clear'), 'resetLastSelect(this)');
					echo '<br />';
				}
			}
			combo_box($name.'[]', $types, false, '');
			normal_button('', translate('clear'), 'resetLastSelect(this)');
			normal_button('', translate('more').'...', 'prependTypeSelector(this); prependClearButton(this, false)');
		}
		echo '<br />';
	} else {
		if ($show_inherit_checkbox) {
			combo_box($name.'[type_code]', $types, false, array_get_index($defaults, 'type_code', ''));
			hidden_field($name.'[inherit]', array_get_index($defaults, 'inherit', '0'));
			check_box('', 1, array_get_index($defaults, 'inherit', '0'), 'toggleLastHiddenField(this)');
			label(strtolower(translate('inherit')), '');
		} else {
			combo_box($name, $types, false, $defaults);
		}
	}

}//end asset_type_chooser()


/**
* Draws a structured dropdown list for selecting assets
*
* @param int					$root_id		the ID of the asset to start from
* @param Array(string)			$type_codes		An array of type codes to restrict
*												options to.
* @param bool					$include_root	Whether or not to list the root node
* @param int					$max_depth		Maximum depth to display nodes to
* @param string					$seperator		Character(s) to display between child branches
* @param bool 					$allow_multiple	Allow Multiple Entries
* @param string					$seperator		String to display between entries
* @param bool					$include_root	Include root node in the search
* @param string					$root_text		Text to display for root node
* @param bool					$check_boxes	Display as check boxes (false = dropdown)
* @param Array					$statuses		statuses to filter from list
* @param Array					$link_types		Allowed link types(Empty for all types)
* @param Array					$allow_empty	Add an empty entry at the start
*
* @access protected
* @return array()
*/
function structured_drop_down($name, $root_id, $type_codes, $selected=Array(), $width=0, $height=0, $max_depth=0, $allow_multiple=false, $seperator='-', $include_root=false, $root_text='', $check_boxes=false, $statuses=Array(), $structured=true, $link_types=Array(), $allow_empty=false)
{
	$all_child_links = $GLOBALS['SQ_SYSTEM']->am->getAllChildLinks($root_id);
	$child_links = $all_child_links;
	$search_tree = Array();

	$asset_info_ids = Array();

	$invalid = Array();

	//weed out any unwanted entries
	foreach ($all_child_links as $treeid => $data) {
		// build an array of assetids as we go
		$asset_info_ids[] = $data['assetid'];

		if (!in_array($data['type_code'], $type_codes) && !empty($type_codes)) {
			unset($child_links[$treeid]);
			$invalid[] = $treeid;
			continue;
		}

		if ($structured) {
			foreach ($invalid as $banned) {
				if (strlen($banned) > strlen($treeid)) continue;
				if (substr($treeid, 0, strlen($banned)) == $banned) {
					unset($child_links[$treeid]);
					continue 1;
				}
			}
		}

		if (!empty($link_types) && !in_array($data['link_type'], $link_types)) {
			unset($child_links[$treeid]);
			$invalid[] = $treeid;
		}
	}

	$asset_info = $GLOBALS['SQ_SYSTEM']->am->getAssetInfo($asset_info_ids);

	// check if we have statuses we want to filter out
	if (!empty($statuses) && count($statuses) < 9) {
		foreach ($child_links as $treeid => $data) {
			if (!in_array($asset_info[$data['assetid']]['status'], $statuses)) {
				unset($child_links[$treeid]);
			}
		}
	}

	$options = Array();
	if ($allow_empty) $options[''] = '';
	$level = 1;
	if ($include_root) {
		if (!empty($root_text)) {
			$options[$root_id] = $root_text;
		} else {
			$options[$root_id] = 'All';
		}
		$level = 0;
	}
	if ($structured) {
		//Insert each node into the tree
		$search_tree = Array();
		foreach ($child_links as $treeid => $child) {
			id_list_to_tree($treeid, $search_tree);
		}
		draw_tree($search_tree, $all_child_links, $options, '', $level, $seperator, $max_depth);
	} else {
		foreach ($child_links as $data) {
			$options[$data['assetid']] = $data['short_name'];
		}
	}
	if ($check_boxes) {
		foreach ($options as $id => $output) {
			if ($allow_multiple) {
				check_box($name.'['.$id.']', $id, in_array($id, $selected));
			} else {
				radio_button($name, $id, in_array($id, $selected));
			}
			label($output, $name.'['.$id.']');
			echo '<br />';
		}
	} else {
		combo_box($name, $options, $allow_multiple, $selected, $height, ($width != '0') ? 'style="width:'.$width.'px;"' : '');
	}

}//end structured_drop_down()


/**
* Takes a tree ID and inserts it into a tree
*
* $tree is in the following format:
* <pre>
* Array(
*	'0001'	=> 0,
*	'0002'	=> Array(
*				'00020001'	=> 0,
*			   ),
* )
* </pre>
*
* @param string		`			$treeid			the ID of the tree to format
* @param Array(string=>mixed)	&$tree			the tree to insert the formatted ID into
* @param string					$chunk_prefix	optional, used internally
*
* @access private
* @return void
*/
function id_list_to_tree($treeid, &$tree, $chunk_prefix='')
{
	$branch_str_len = 4;

	if (strlen($treeid) < $branch_str_len) return false;

	$first_chunk = substr($treeid, 0, $branch_str_len);

	if (strlen($treeid) > $branch_str_len) {
		$remaining_chunk = substr($treeid, $branch_str_len, strlen($treeid));
		if (!isset($tree[$chunk_prefix.$first_chunk]) || !is_array($tree[$chunk_prefix.$first_chunk])) {
			$tree[$chunk_prefix.$first_chunk] = Array();
		}
		id_list_to_tree($remaining_chunk, $tree[$chunk_prefix.$first_chunk], $chunk_prefix.$first_chunk);
	} else {
		$tree[$chunk_prefix.$first_chunk] = 0;
	}

}//end id_list_to_tree()


/**
* Takes a tree array and an output array, and fills the output array with an ascii tree,
* with each line of the tree as an element in the array
*
* @param Array(string=>mixed)	$tree				the tree to format
* @param Array(string=>mixed)	$all_child_links	assetInfo() for use when printing
* @param Array(string=>mixed)	$output_tree		the array to store the output array
* @param string					$branch				optional, used internally
* @param Array(string)			&$level				Level in the tree to print as the base
* @param string					$seperator			String to display between
* @param int					$max_depth			Maximum depth to print
*
* @access private
* @return void
*/
function draw_tree($tree, &$all_child_links, &$output_tree, $branch='', $level=0, $seperator='-', $max_depth=0)
{
	if (empty($tree)) return false;
	foreach ($tree as $branch => $branch_data) {
		$prefix = '';

		$lvl = $all_child_links[$branch]['lvl'] - $level;
		if ($max_depth != 0 && $lvl > $max_depth) {
			return;
		}
		for ($i = 0; $i < $lvl; $i++) {
			$prefix .= ' '.$seperator.' ';
		}
		$output_tree[$all_child_links[$branch]['assetid']] = $prefix.$all_child_links[$branch]['short_name'];
		if (is_array($branch_data) && !empty($branch_data)) {
			draw_tree($branch_data, $all_child_links, $output_tree, $branch, $level, $seperator, $max_depth);
		}
	}
}//end draw_tree()


?>
