/* Zenphoto administration javascript. */

function confirmDeleteAlbum(url) {
  if (confirm("Are you sure you want to delete this entire album?")) {
    if (confirm("Are you Absolutely Positively sure you want to delete the album? THIS CANNOT BE UNDONE!")) {
      window.location = url;
    }
  }
}

function confirmDeleteImage(url) {
  if (confirm("Are you sure you want to delete the image? THIS CANNOT BE UNDONE!")) {
    window.location = url;
  }
}

function addUploadBoxes(placeholderid, copyfromid, num) {
  var placeholder = document.getElementById(placeholderid);
  var copyfrom = document.getElementById(copyfromid);
  for (i=0; i<num; i++) {
    if (window.totalinputs >= 50) return;
    var newdiv = document.createElement('div');
    newdiv.innerHTML = copyfrom.innerHTML;
    newdiv.className = copyfrom.className;
    placeholder.parentNode.insertBefore(newdiv, placeholder);
    window.totalinputs++;
  }
}

function albumSwitch(sel) {
  var selected = sel.options[sel.selectedIndex];
  var albumtext = document.getElementById("albumtext");
  var albumbox = document.getElementById("folderdisplay");
  var titlebox = document.getElementById("albumtitle");
  var checkbox = document.getElementById("autogen");
  if (selected.value == "") {
    albumtext.style.display = "block";
    albumbox.value = "";
    titlebox.value = "";
    document.getElementById("foldererror").style.display = "none";
    checkbox.checked = true;
    toggleAutogen("folderdisplay", "albumtitle", checkbox);
  } else {
    albumtext.style.display = "none";
    albumbox.value = selected.value;
    titlebox.value = selected.text;
  }

}

function contains(arr, key) {
  for (i=0; i<arr.length; i++) {
    if (arr[i].toLowerCase() == key.toLowerCase()) {
      return true;
    }
  }
  return false;
}

function updateFolder(nameObj, folderID, checkboxID) {
  var autogen = document.getElementById(checkboxID).checked;
  var folder = document.getElementById(folderID);
  var name = nameObj.value;
  var fname = "";
  var fnamesuffix = "";
  var count = 1;
  if (autogen && name != "") {
    fname = name;
    fname = fname.toLowerCase();
    fname = fname.replace(/[\!@#$\%\^&*()\~`\'\"]/g, "");
    fname = fname.replace(/^\s+|\s+$/g, "");
    fname = fname.replace(/[^a-zA-Z0-9]/g, "-");
    fname = fname.replace(/--*/g, "-");
    while (contains(albumArray, fname+fnamesuffix)) {
      fnamesuffix = "-"+count;
      count++;
    }
  }
  folder.value = fname+fnamesuffix;
}

function validateFolder(folderObj) {
  var errorDiv = document.getElementById("foldererror");
  if (albumArray && contains(albumArray, folderObj.value)) {
    errorDiv.style.display = "block";
  } else {
    errorDiv.style.display = "none";
  }
}

function toggleAutogen(fieldID, nameID, checkbox) {
  var field = document.getElementById(fieldID);
  var name = document.getElementById(nameID);
  if (checkbox.checked) {
    window.folderbackup = field.value;
    field.disabled = true;
    updateFolder(name, fieldID, checkbox.id);
  } else {
    if (window.folderbackup && window.folderbackup != "")
      field.value = window.folderbackup;
    field.disabled = false;
  }
}


// Checks all the checkboxes in a group (with the specified name);
function checkAll(form, arr, mark) { 
  for (i = 0; i <= form.elements.length; i++) { 
    try { 
      if(form.elements[i].name == arr) { 
        form.elements[i].checked = mark; 
      }
    } catch(e) {} 
  }
}

function triggerAllBox(form, arr, allbox) { 
  for (i = 0; i <= form.elements.length; i++) { 
    try { 
      if(form.elements[i].name == arr) { 
        if(form.elements[i].checked == false) { 
          allbox.checked = false; return;
        }
      }
    } catch(e) {}
  }
  allbox.checked = true;
}


function toggleBigImage(id, largepath) {
  var imageobj = document.getElementById(id);
  if (!imageobj.sizedlarge) {
    imageobj.src2 = imageobj.src;
    imageobj.src = largepath;
    imageobj.style.position = 'absolute';
    imageobj.style.zIndex = '1000';
    imageobj.sizedlarge = true;
  } else {
    imageobj.style.position = 'relative';
    imageobj.style.zIndex = '0';
    imageobj.src = imageobj.src2;
    imageobj.sizedlarge = false;
  }
}


function updateThumbPreview(selectObj) {
  var thumb = selectObj.options[selectObj.selectedIndex].style.backgroundImage;
  selectObj.style.backgroundImage = thumb;
}



// @name      The Fade Anything Technique
// @namespace http://www.axentric.com/aside/fat/
// @version   1.0-RC1
// @author    Adam Michela
// Modified by Tristan Harward; added new method "fade_and_hide_element"

var Fat = {
	make_hex : function (r,g,b) 
	{
		r = r.toString(16); if (r.length == 1) r = '0' + r;
		g = g.toString(16); if (g.length == 1) g = '0' + g;
		b = b.toString(16); if (b.length == 1) b = '0' + b;
		return "#" + r + g + b;
	},
	fade_all : function ()
	{
		var a = document.getElementsByTagName("*");
		for (var i = 0; i < a.length; i++) 
		{
			var o = a[i];
			var r = /fade-?(\w{3,6})?/.exec(o.className);
			if (r)
			{
				if (!r[1]) r[1] = "";
				if (o.id) Fat.fade_element(o.id,null,null,"#"+r[1]);
			}
		}
	},
	fade_element : function (id, fps, duration, from, to) 
	{
		if (!fps) fps = 30;
		if (!duration) duration = 3000;
		if (!from || from=="#") from = "#FFFF33";
		if (!to) to = this.get_bgcolor(id);
		
		var frames = Math.round(fps * (duration / 1000));
		var interval = duration / frames;
		var delay = interval;
		var frame = 0;
		
		if (from.length < 7) from += from.substr(1,3);
		if (to.length < 7) to += to.substr(1,3);
		
		var rf = parseInt(from.substr(1,2),16);
		var gf = parseInt(from.substr(3,2),16);
		var bf = parseInt(from.substr(5,2),16);
		var rt = parseInt(to.substr(1,2),16);
		var gt = parseInt(to.substr(3,2),16);
		var bt = parseInt(to.substr(5,2),16);
		
		var r,g,b,h;
		while (frame < frames)
		{
			r = Math.floor(rf * ((frames-frame)/frames) + rt * (frame/frames));
			g = Math.floor(gf * ((frames-frame)/frames) + gt * (frame/frames));
			b = Math.floor(bf * ((frames-frame)/frames) + bt * (frame/frames));
			h = this.make_hex(r,g,b);
		
			setTimeout("Fat.set_bgcolor('"+id+"','"+h+"')", delay);

			frame++;
			delay = interval * frame; 
		}
		setTimeout("Fat.set_bgcolor('"+id+"','"+to+"')", delay);
	},
  fade_and_hide_element : function (id, fps, duration, delay, from, to) {
    setTimeout("Fat.fade_element('"+id+"', '"+fps+"', '"+duration+"', '"+from+"', '"+to+"')", delay);
    setTimeout("document.getElementById('"+id+"').style.display='none'", delay+duration);
  },
	set_bgcolor : function (id, c)
	{
		var o = document.getElementById(id);
		o.style.backgroundColor = c;
	},
	get_bgcolor : function (id)
	{
		var o = document.getElementById(id);
		while(o)
		{
			var c;
			if (window.getComputedStyle) c = window.getComputedStyle(o,null).getPropertyValue("background-color");
			if (o.currentStyle) c = o.currentStyle.backgroundColor;
			if ((c != "" && c != "transparent") || o.tagName == "BODY") { break; }
			o = o.parentNode;
		}
		if (c == undefined || c == "" || c == "transparent") c = "#FFFFFF";
		var rgb = c.match(/rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/);
		if (rgb) c = this.make_hex(parseInt(rgb[1]),parseInt(rgb[2]),parseInt(rgb[3]));
		return c;
	}
}

        
