<?php

$objForm = & $this->newObject('form' , 'htmlelements');
$colour = & $this->newObject('textinput' , 'htmlelements');
$title = & $this->newObject('textinput' , 'htmlelements');
$button = & $this->newObject('button' , 'htmlelements');
$objIcon = & $this->newObject('geticon', 'htmlelements');

if($this->getParam('mode') == '')
{
    $mode = 'add';
} else {
    $mode = 'edit';
}

//setup form
$objForm->name = 'addcat';
$objForm->action = $this->uri(array('action' => 'savecat', 'mode' => $mode, 'id' => $this->getParam('id')));
$objForm->extra = ' class = "f-wrap-1" ';
$objForm->displayType = 2 ;

if($mode == 'edit')
{
    $catLine = $this->_objDBCategories->getRow('id', $this->getParam('id'));
    if($catLine)
    {
        $title->value = $catLine['title'];
        $colour->extra = ' style = "background:'.$catLine['colour'].'" ';
        $colour->value = $catLine['colour'];
    } else {
        $title->value = '';
        $colour->value = '';
    }
    
} else {
    $title->value = '';
    $colour->value = '';
}


//title
$title->size = "20";
$title->label = '<span class="req">*</span>Category Title :';

$title->name = 'title';
$objForm->addRule('title','required', "The Title is required");

//colour
$colour->label = '<span class="req">*</span>Colour :';
$colour->name = "colour";
$objForm->addRule('colour','required', "The Colour is required");

$colourStr = "<a href=\"javascript:pickColor('pick1159262324');\" id=\"pick1159262324\"
					style=\"border: 1px solid #000000; font-family:Verdana; font-size:10px;
					text-decoration: none;\">&nbsp;&nbsp;&nbsp;</a>
					<input id=\"pick1159262324field\" size=\"7\" 
					onChange=\"relateColor('pick1159262324', this.value);\">
					<script language=\"javascript\">relateColor('pick1159262324', getObj('pick1159262324field').value);</script>";
//the button
$button->setToSubmit();
$button->value = "Save";
$button->label = "&nbsp;";
$button->setCSS("f-submit");

$objForm->AddToForm('<div class="req"><b>*</b> Indicates required field</div>');
$objForm->addToForm($title);
$objForm->addToForm('*Colour </td><td>'.$colourStr);
$objForm->addToForm($button);

print "<h1> ".$mode." Categories </h1>";
print $objForm->show();
?>

<script type="text/javascript">
	
<!--
     var perline = 9;
     var divSet = false;
     var curId;
     var colorLevels = Array('0', '3', '6', '9', 'C', 'F');
     var colorArray = Array();
     var ie = false;
     var nocolor = 'none';
	 if (document.all) { ie = true; nocolor = ''; }
	 function getObj(id) {
		if (ie) { return document.all[id]; } 
		else {	return document.getElementById(id);	}
	 }

     function addColor(r, g, b) {
     	var red = colorLevels[r];
     	var green = colorLevels[g];
     	var blue = colorLevels[b];
     	addColorValue(red, green, blue);
     }

     function addColorValue(r, g, b) {
     	colorArray[colorArray.length] = '#' + r + r + g + g + b + b;
     }
     
     function setColor(color) {
     	var link = getObj(curId);
     	var field = getObj(curId + 'field');
     	var picker = getObj('colorpicker');
     	field.value = color;
     	if (color == '') {
	     	link.style.background = nocolor;
	     	link.style.color = nocolor;
	     	color = nocolor;
     	} else {
	     	link.style.background = color;
	     	link.style.color = color;
	    }
     	picker.style.display = 'none';
	    eval(getObj(curId + 'field').title);
     }
        
     function setDiv() {     
     	if (!document.createElement) { return; }
        var elemDiv = document.createElement('div');
        if (typeof(elemDiv.innerHTML) != 'string') { return; }
        genColors();
        elemDiv.id = 'colorpicker';
	    elemDiv.style.position = 'absolute';
        elemDiv.style.display = 'none';
        elemDiv.style.border = '#000000 1px solid';
        elemDiv.style.background = '#FFFFFF';
        elemDiv.innerHTML = '<span style="font-family:Verdana; font-size:11px;">Pick a color: ' 
          	+ '(<a href="javascript:setColor(\'\');">No color</a>)<br>' 
        	+ getColorTable() 
        	+ '</span>';

        document.body.appendChild(elemDiv);
        divSet = true;
     }
     
     function pickColor(id) {
     	if (!divSet) { setDiv(); }
     	var picker = getObj('colorpicker');     	
		if (id == curId && picker.style.display == 'block') {
			picker.style.display = 'none';
			return;
		}
     	curId = id;
     	var thelink = getObj(id);
     	picker.style.top = getAbsoluteOffsetTop(thelink) + 20;
     	picker.style.left = getAbsoluteOffsetLeft(thelink);     
	picker.style.display = 'block';
     }
     
     function genColors() {
        addColorValue('0','0','0');
        addColorValue('3','3','3');
        addColorValue('6','6','6');
        addColorValue('8','8','8');
        addColorValue('9','9','9');                
        addColorValue('A','A','A');
        addColorValue('C','C','C');
        addColorValue('E','E','E');
        addColorValue('F','F','F');                                
			
        for (a = 1; a < colorLevels.length; a++)
			addColor(0,0,a);
        for (a = 1; a < colorLevels.length - 1; a++)
			addColor(a,a,5);

        for (a = 1; a < colorLevels.length; a++)
			addColor(0,a,0);
        for (a = 1; a < colorLevels.length - 1; a++)
			addColor(a,5,a);
			
        for (a = 1; a < colorLevels.length; a++)
			addColor(a,0,0);
        for (a = 1; a < colorLevels.length - 1; a++)
			addColor(5,a,a);
			
			
        for (a = 1; a < colorLevels.length; a++)
			addColor(a,a,0);
        for (a = 1; a < colorLevels.length - 1; a++)
			addColor(5,5,a);
			
        for (a = 1; a < colorLevels.length; a++)
			addColor(0,a,a);
        for (a = 1; a < colorLevels.length - 1; a++)
			addColor(a,5,5);

        for (a = 1; a < colorLevels.length; a++)
			addColor(a,0,a);			
        for (a = 1; a < colorLevels.length - 1; a++)
			addColor(5,a,5);
			
       	return colorArray;
     }
     function getColorTable() {
         var colors = colorArray;
      	 var tableCode = '';
         tableCode += '<table border="0" cellspacing="1" cellpadding="1">';
         for (i = 0; i < colors.length; i++) {
              if (i % perline == 0) { tableCode += '<tr>'; }
              tableCode += '<td bgcolor="#000000"><a style="outline: 1px solid #000000; color: ' 
              	  + colors[i] + '; background: ' + colors[i] + ';font-size: 10px;" title="' 
              	  + colors[i] + '" href="javascript:setColor(\'' + colors[i] + '\');">&nbsp;&nbsp;&nbsp;</a></td>';
              if (i % perline == perline - 1) { tableCode += '</tr>'; }
         }
         if (i % perline != 0) { tableCode += '</tr>'; }
         tableCode += '</table>';
      	 return tableCode;
     }
     function relateColor(id, color) {
     	var link = getObj(id);
     	if (color == '') {
	     	link.style.background = nocolor;
	     	link.style.color = nocolor;
	     	color = nocolor;
     	} else {
	     	link.style.background = color;
	     	link.style.color = color;
	    }
	    eval(getObj(id + 'field').title);
     }
     function getAbsoluteOffsetTop(obj) {
     	var top = obj.offsetTop;
     	var parent = obj.offsetParent;
     	while (parent != document.body) {
     		top += parent.offsetTop;
     		parent = parent.offsetParent;
     	}
     	return top;
     }
     
     function getAbsoluteOffsetLeft(obj) {
     	var left = obj.offsetLeft;
     	var parent = obj.offsetParent;
     	while (parent != document.body) {
     		left += parent.offsetLeft;
     		parent = parent.offsetParent;
     	}
     	return left;
     }

-->
</script>

