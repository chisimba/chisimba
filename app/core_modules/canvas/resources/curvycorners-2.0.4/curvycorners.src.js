 /****************************************************************
  *                                                              *
  *  CurvyCorners                                                *
  *  ------------                                                *
  *                                                              *
  *  This script generates rounded corners for your boxes.       *
  *                                                              *
  *  Version 2.0.4                                               *
  *  Copyright (c) 2009 Cameron Cooke                            *
  *  Contributors: Tim Hutchison, CPK Smithies, Terry Rigel      *
  *                                                              *
  *  Website: http://www.curvycorners.net                        *
  *  SVN:     http://curvycorners.googlecode.com/                *
  *  Email:   cameron@curvycorners.net                           *
  *  Discuss: http://groups.google.com/group/curvycorners        *
  *                                                              *
  *  Please consult the SVN for a list of changes since the last *
  *  revision.                                                   *
  *                                                              *
  *  This library is free software; you can redistribute         *
  *  it and/or modify it under the terms of the GNU              *
  *  Lesser General Public License as published by the           *
  *  Free Software Foundation; either version 2.1 of the         *
  *  License, or (at your option) any later version.             *
  *                                                              *
  *  This library is distributed in the hope that it will        *
  *  be useful, but WITHOUT ANY WARRANTY; without even the       *
  *  implied warranty of MERCHANTABILITY or FITNESS FOR A        *
  *  PARTICULAR PURPOSE. See the GNU Lesser General Public       *
  *  License for more details.                                   *
  *                                                              *
  *  You should have received a copy of the GNU Lesser           *
  *  General Public License along with this library;             *
  *  Inc., 59 Temple Place, Suite 330, Boston,                   *
  *  MA 02111-1307 USA                                           *
  *                                                              *
  ****************************************************************/

/*
Version 2.x now autoMagically applies borders via CSS rules.
Safari, Chrome and Mozilla support rounded borders via

-webkit-border-radius, -moz-border-radius

We let these browsers render their borders natively.
Firefox for Windows renders non-antialiased
borders so they look a bit ugly. Google's Chrome will render its "ugly"
borders as well. So if we let FireFox, Safari, and Chrome render their
borders natively, then we only have to support IE and Opera
for rounded borders. Fortunately IE reads CSS properties
that it doesn't understand (Opera, Firefox and Safari discard them);
so for IE and Opera we find and apply -webkit-border-radius and friends.

So to make curvycorners work with any major browser simply add the following
CSS declarations and it should be good to go...

.round {
  -webkit-border-radius: 3ex;
  -moz-border-radius: 3ex;
}

NB at present you must (for Opera's sake) include these styles in
the page itself.
*/

function browserdetect() {
  var agent = navigator.userAgent.toLowerCase();
  this.isIE      = agent.indexOf("msie") > -1;
  this.ieVer = this.isIE ? /msie\s(\d\.\d)/.exec(agent)[1] : 0;
  this.isMoz     = agent.indexOf('firefox') != -1;
  this.isSafari  = agent.indexOf('safari') != -1;
  this.quirksMode= this.isIE && (!document.compatMode || document.compatMode.indexOf("BackCompat") > -1);
  this.isOp      = 'opera' in window;
  this.isWebKit  = agent.indexOf('webkit') != -1;
  if (this.isIE) {
    this.get_style = function(obj, prop) {
      if (!(prop in obj.currentStyle)) return "";
      var matches = /^([\d.]+)(\w*)/.exec(obj.currentStyle[prop]);
      if (!matches) return obj.currentStyle[prop];
      if (matches[1] == 0) return '0';
      // now convert to pixels if necessary
      if (matches[2] && matches[2] !== 'px') {
        var style = obj.style.left;
        var rtStyle = obj.runtimeStyle.left;
        obj.runtimeStyle.left = obj.currentStyle.left;
        obj.style.left = matches[1] + matches[2];
        matches[0] = obj.style.pixelLeft;
        obj.style.left = style;
        obj.runtimeStyle.left = rtStyle;
      }
      return matches[0];
    };
  }
  else {
    this.get_style = function(obj, prop) {
      prop = prop.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
      return document.defaultView.getComputedStyle(obj, '').getPropertyValue(prop);
    };
  }
}
var curvyBrowser = new browserdetect;

/* Force caching of bg images in IE6 */
if (curvyBrowser.isIE) {
  try {
    document.execCommand("BackgroundImageCache", false, true);
  }
  catch(e) {};
}

// object that parses border-radius properties for a box

function curvyCnrSpec(selText) {
  this.selectorText = selText;
  this.tlR = this.trR = this.blR = this.brR = 0;
  this.tlu = this.tru = this.blu = this.bru = "";
  this.antiAlias = true; // default true
}
curvyCnrSpec.prototype.setcorner = function(tb, lr, radius, unit) {
  if (!tb) { // no corner specified
    this.tlR = this.trR = this.blR = this.brR = parseInt(radius);
    this.tlu = this.tru = this.blu = this.bru = unit;
  }
  else { // corner specified
    propname = tb.charAt(0) + lr.charAt(0);
    this[propname + 'R'] = parseInt(radius);
    this[propname + 'u'] = unit;
  }
}
/*
  get(propstring)
  where propstring is:
  - 'tR' or 'bR' : returns top or bottom radius.
  - 'tlR', 'trR', 'blR' or 'brR' : returns top/bottom left/right radius.
  - 'tlu', 'tru', 'blr' or 'bru' : returns t/b l/r unit (px, em...)
  - 'tRu' or 'bRu' : returns top/bottom radius+unit
  - 'tlRu', 'trRu', 'blRu', 'brRu' : returns t/b l/r radius+unit
*/
curvyCnrSpec.prototype.get = function(prop) {
  if (/^(t|b)(l|r)(R|u)$/.test(prop)) return this[prop];
  if (/^(t|b)(l|r)Ru$/.test(prop)) {
    var pname = prop.charAt(0) + prop.charAt(1);
    return this[pname + 'R'] + this[pname + 'u'];
  }
  if (/^(t|b)Ru?$/.test(prop)) {
    var tb = prop.charAt(0);
    tb += this[tb + 'lR'] > this[tb + 'rR'] ? 'l' : 'r';
    var retval = this[tb + 'R'];
    if (prop.length === 3 && prop.charAt(2) === 'u')
      retval += this[tb = 'u'];
    return retval;
  }
  throw new Error('Don\'t recognize property ' + prop);
}
curvyCnrSpec.prototype.radiusdiff = function(tb) {
  if (tb !== 't' && tb !== 'b') throw new Error("Param must be 't' or 'b'");
  return Math.abs(this[tb + 'lR'] - this[tb + 'rR']);
}
curvyCnrSpec.prototype.setfrom = function(obj) {
  this.tlu = this.tru = this.blu = this.bru = 'px'; // default to px
  if ('tl' in obj) this.tlR = obj.tl.radius;
  if ('tr' in obj) this.trR = obj.tr.radius;
  if ('bl' in obj) this.blR = obj.bl.radius;
  if ('br' in obj) this.brR = obj.br.radius;
  if ('antiAlias' in obj) this.antiAlias = obj.antiAlias;
};
curvyCnrSpec.prototype.cloneOn = function(box) { // not needed by IE
  var props = ['tl', 'tr', 'bl', 'br'];
  var converted = 0;
  var i, propu;

  for (i in props) if (!isNaN(i)) {
    propu = this[props[i] + 'u'];
    if (propu !== '' && propu !== 'px') {
      converted = new curvyCnrSpec;
      break;
    }
  }
  if (!converted)
    converted = this; // no need to clone
  else {
    var propi, propR, save = curvyBrowser.get_style(box, 'left');
    for (i in props) if (!isNaN(i)) {
      propi = props[i];
      propu = this[propi + 'u'];
      propR = this[propi + 'R'];
      if (propu !== 'px') {
        var save = box.style.left;
        box.style.left = propR + propu;
        propR = box.style.pixelLeft;
        box.style.left = save;
      }
      converted[propi + 'R'] = propR;
      converted[propi + 'u'] = 'px';
    }
    box.style.left = save;
  }
  return converted;
}
curvyCnrSpec.prototype.radiusSum = function(tb) {
  if (tb !== 't' && tb !== 'b') throw new Error("Param must be 't' or 'b'");
  return this[tb + 'lR'] + this[tb + 'rR'];
}
curvyCnrSpec.prototype.radiusCount = function(tb) {
  var count = 0;
  if (this[tb + 'lR']) ++count;
  if (this[tb + 'rR']) ++count;
  return count;
}
curvyCnrSpec.prototype.cornerNames = function() {
  var ret = [];
  if (this.tlR) ret.push('tl');
  if (this.trR) ret.push('tr');
  if (this.blR) ret.push('bl');
  if (this.brR) ret.push('br');
  return ret;
}

/*
  Object that parses Opera CSS
*/
function operasheet(sheetnumber) {
  var txt = document.styleSheets.item(sheetnumber).ownerNode.text;
  txt = txt.replace(/\/\*(\n|\r|.)*?\*\//g, ''); // strip comments
  // this pattern extracts all border-radius-containing rulesets
  // matches will be:
  // [0] = the whole lot
  // [1] = the selector text
  // [2] = all the rule text between braces
  // [3] = top/bottom and left/right parts if present (only if webkit/CSS3)
  // [4] = top|bottom
  // [5] = left|right
  // .. but 3..5 are useless as they're only the first match.
  var pat = new RegExp("^\s*([\\w.#][-\\w.#, ]+)[\\n\\s]*\\{([^}]+border-((top|bottom)-(left|right)-)?radius[^}]*)\\}", "mg");
  var matches;
  this.rules = [];
  while ((matches = pat.exec(txt)) !== null) {
    var pat2 = new RegExp("(..)border-((top|bottom)-(left|right)-)?radius:\\s*([\\d.]+)(in|em|px|ex|pt)", "g");
    var submatches, cornerspec = new curvyCnrSpec(matches[1]);
    while ((submatches = pat2.exec(matches[2])) !== null)
      if (submatches[1] !== "z-")
        cornerspec.setcorner(submatches[3], submatches[4], submatches[5], submatches[6]);
    this.rules.push(cornerspec);
  }
}
// static class function to determine if the sheet is worth parsing
operasheet.contains_border_radius = function(sheetnumber) {
  return /border-((top|bottom)-(left|right)-)?radius/.test(document.styleSheets.item(sheetnumber).ownerNode.text);
}

/*
Usage:

  curvyCorners(settingsObj, "selectorStr");
  curvyCorners(settingsObj, domObj1[, domObj2[, domObj3[, . . . [, domObjN]]]]);
  selectorStr::= "<complexSelector>[, <complexSelector>]..."
  complexSelector::= <selector>[ <selector]
  selector::= "[<elementname>].classname" | "#id"
*/

function curvyCorners() {
  var i, j, boxCol, settings, startIndex;
  // Check parameters
  if (typeof arguments[0] !== "object") throw curvyCorners.newError("First parameter of curvyCorners() must be an object.");
  if (arguments[0] instanceof curvyCnrSpec) {
    settings = arguments[0];
    if (!settings.selectorText && typeof arguments[1] === 'string')
      settings.selectorText = arguments[1];
  }
  else {
    if (typeof arguments[1] !== "object" && typeof arguments[1] !== "string") throw curvyCorners.newError("Second parameter of curvyCorners() must be an object or a class name.");
    j = arguments[1];
    if (typeof j !== 'string') j = '';
    if (j !== '' && j.charAt(0) !== '.' && 'autoPad' in arguments[0]) j = '.' + j; // for compatibility, prefix with dot
    settings = new curvyCnrSpec(j);
    settings.setfrom(arguments[0]);
  }

  // Get object(s)
  if (settings.selectorText) {
    startIndex = 0;
    var args = settings.selectorText.replace(/\s+$/,'').split(/,\s*/); // handle comma-separated selector list
    boxCol = new Array;

    // converts div#mybox to #mybox
    function idof(str) {
      var ret = str.split('#');
      return (ret.length === 2 ? "#" : "") + ret.pop();
    }

    for (i = 0; i < args.length; ++i) {
      var arg = idof(args[i]);
      var argbits = arg.split(' ');
      switch (arg.charAt(0)) {
        case '#' : // id
          j = argbits.length === 1 ? arg : argbits[0];
          j = document.getElementById(j.substr(1));
          if (j === null)
            curvyCorners.alert("No object with ID " + arg + " exists yet.\nCall curvyCorners(settings, obj) when it is created.");
          else if (argbits.length === 1)
            boxCol.push(j);
          else
            boxCol = boxCol.concat(curvyCorners.getElementsByClass(argbits[1], j));
        break;
        default :
          if (argbits.length === 1)
            boxCol = boxCol.concat(curvyCorners.getElementsByClass(arg));
          else {
            var encloser = curvyCorners.getElementsByClass(argbits[0]);
            for (j = 0; j < encloser.length; ++j) {
              boxCol = boxCol.concat(curvyCorners.getElementsByClass(argbits[1], encloser));
            }
          }
        //break;
      }
    }
  }
  else {
    // Get objects
    startIndex = 1;
    boxCol = arguments;
  }

  // Loop through each argument
  for (i = startIndex, j = boxCol.length; i < j; ++i) {
    if (boxCol[i] && (!('IEborderRadius' in boxCol[i].style) || boxCol[i].style.IEborderRadius != 'set')) {
      if (boxCol[i].className && boxCol[i].className.indexOf('curvyRedraw') !== -1) {
        if (typeof curvyCorners.redrawList === 'undefined') curvyCorners.redrawList = new Array;
        curvyCorners.redrawList.push({
          node : boxCol[i],
          spec : settings,
          copy : boxCol[i].cloneNode(false)
        });
      }
      boxCol[i].style.IEborderRadius = 'set';
      var obj = new curvyObject(settings, boxCol[i]);
      obj.applyCorners();
    }
  }
}
curvyCorners.prototype.applyCornersToAll = function () { // now redundant
  curvyCorners.alert('This function is now redundant. Just call curvyCorners(). See documentation.');
};

curvyCorners.redraw = function() {
  if (!curvyBrowser.isOp && !curvyBrowser.isIE) return;
  if (!curvyCorners.redrawList) throw curvyCorners.newError('curvyCorners.redraw() has nothing to redraw.');
  var old_block_value = curvyCorners.bock_redraw;
  curvyCorners.block_redraw = true;
  for (var i in curvyCorners.redrawList) {
    if (isNaN(i)) continue; // in case of added prototype methods
    var o = curvyCorners.redrawList[i];
    if (!o.node.clientWidth) continue; // don't resize hidden boxes
    var newchild = o.copy.cloneNode(false);
    for (var contents = o.node.firstChild; contents != null; contents = contents.nextSibling)
      if (contents.className === 'autoPadDiv') break;
    if (!contents) {
      curvyCorners.alert('Couldn\'t find autoPad DIV');
      break;
    }
    o.node.parentNode.replaceChild(newchild, o.node);
    while (contents.firstChild) newchild.appendChild(contents.removeChild(contents.firstChild));
    o = new curvyObject(o.spec, o.node = newchild);
    o.applyCorners();
  }
  curvyCorners.block_redraw = old_block_value;
}
curvyCorners.adjust = function(obj, prop, newval) {
  if (curvyBrowser.isOp || curvyBrowser.isIE) {
    if (!curvyCorners.redrawList) throw curvyCorners.newError('curvyCorners.adjust() has nothing to adjust.');
    var i, j = curvyCorners.redrawList.length;
    for (i = 0; i < j; ++i) if (curvyCorners.redrawList[i].node === obj) break;
    if (i === j) throw curvyCorners.newError('Object not redrawable');
    obj = curvyCorners.redrawList[i].copy;
  }
  if (prop.indexOf('.') === -1)
    obj[prop] = newval;
  else eval('obj.' + prop + "='" + newval + "'");
}
curvyCorners.handleWinResize = function() {
  if (!curvyCorners.block_redraw) curvyCorners.redraw();
}
curvyCorners.setWinResize = function(onoff) {
  curvyCorners.block_redraw = !onoff;
}
curvyCorners.newError = function(errorMessage) {
  return new Error("curvyCorners Error:\n" + errorMessage)
}
curvyCorners.alert = function(errorMessage) {
  if (typeof curvyCornersVerbose === 'undefined' || curvyCornersVerbose) alert(errorMessage);
}

// curvyCorners object (can be called directly)

function curvyObject() {
  var boxDisp;
  this.box              = arguments[1];
  this.settings         = arguments[0];
  this.topContainer = this.bottomContainer = this.shell = boxDisp = null;
  var boxWidth = this.box.clientWidth; // browser-independent IE-emulation (NB includes padding)

  // if no client width, maybe the box or a parent has 'display:none'.

  if (!boxWidth && curvyBrowser.isIE) {
    this.box.style.zoom = 1; // can force IE to calculate width
    boxWidth = this.box.clientWidth;
  }
  if (!boxWidth) {
    if (!this.box.parentNode) throw this.newError("box has no parent!"); // unlikely...
    for (boxDisp = this.box; ; boxDisp = boxDisp.parentNode) {
      if (!boxDisp || boxDisp.tagName === 'BODY') { // we've hit the buffers
        this.applyCorners = function() {} // make the error benign
        curvyCorners.alert(this.errmsg("zero-width box with no accountable parent", "warning"));
        return;
      }
      if (boxDisp.style.display === 'none') break;
    }
    // here, we've found the box whose display is set to 'none'.
    boxDisp.style.display = 'block'; // display in order to get browser to calculate clientWidth
    boxWidth = this.box.clientWidth;
  }
  if (arguments[0] instanceof curvyCnrSpec)
    this.spec = arguments[0].cloneOn(this.box); // convert non-pixel units
  else {
    this.spec = new curvyCnrSpec('');
    this.spec.setfrom(this.settings); // no need for unit conversion, use settings param. directly
  }

  // Get box formatting details
  var borderWidth     = curvyBrowser.get_style(this.box, "borderTopWidth");
  var borderWidthB    = curvyBrowser.get_style(this.box, "borderBottomWidth");
  var borderWidthL    = curvyBrowser.get_style(this.box, "borderLeftWidth");
  var borderWidthR    = curvyBrowser.get_style(this.box, "borderRightWidth");
  var borderColour    = curvyBrowser.get_style(this.box, "borderTopColor");
  var borderColourB   = curvyBrowser.get_style(this.box, "borderBottomColor");
  var borderColourL   = curvyBrowser.get_style(this.box, "borderLeftColor");
  var boxColour       = curvyBrowser.get_style(this.box, "backgroundColor");
  var backgroundImage = curvyBrowser.get_style(this.box, "backgroundImage");
  var backgroundRepeat= curvyBrowser.get_style(this.box, "backgroundRepeat");
  if (this.box.currentStyle && this.box.currentStyle.backgroundPositionX) {
  var backgroundPosX  = curvyBrowser.get_style(this.box, "backgroundPositionX");
  var backgroundPosY  = curvyBrowser.get_style(this.box, "backgroundPositionY");
  }
  else {
    var backgroundPosX = curvyBrowser.get_style(this.box, 'backgroundPosition');
    backgroundPosX = backgroundPosX.split(' ');
    var backgroundPosY = backgroundPosX[1];
    backgroundPosX = backgroundPosX[0];
  }
  var boxPosition     = curvyBrowser.get_style(this.box, "position");
  var topPadding      = curvyBrowser.get_style(this.box, "paddingTop");
  var bottomPadding   = curvyBrowser.get_style(this.box, "paddingBottom");
  var leftPadding     = curvyBrowser.get_style(this.box, "paddingLeft");
  var rightPadding    = curvyBrowser.get_style(this.box, "paddingRight");
  var border          = curvyBrowser.get_style(this.box, "border");
  filter = curvyBrowser.ieVer > 7 ? curvyBrowser.get_style(this.box, 'filter') : null; // IE8 bug fix

  var topMaxRadius    = this.spec.get('tR');
  var botMaxRadius    = this.spec.get('bR');
  var styleToNPx = function(val) {
    if (typeof val === 'number') return val;
    if (typeof val !== 'string') throw new Error('unexpected styleToNPx type ' + typeof val);
    var matches = /^[-\d.]([a-z]+)$/.exec(val);
    if (matches && matches[1] != 'px') throw new Error('Unexpected unit ' + matches[1]);
    if (isNaN(val = parseInt(val))) val = 0;
    return val;
  }
  var min0Px = function(val) {
    return val <= 0 ? "0" : val + "px";
  }

  // Set formatting properties
  try {
    this.borderWidth     = styleToNPx(borderWidth);
    this.borderWidthB    = styleToNPx(borderWidthB);
    this.borderWidthL    = styleToNPx(borderWidthL);
    this.borderWidthR    = styleToNPx(borderWidthR);
    this.boxColour       = curvyObject.format_colour(boxColour);
    this.topPadding      = styleToNPx(topPadding);
    this.bottomPadding   = styleToNPx(bottomPadding);
    this.leftPadding     = styleToNPx(leftPadding);
    this.rightPadding    = styleToNPx(rightPadding);
    this.boxWidth        = boxWidth;
    this.boxHeight       = this.box.clientHeight;
    this.borderColour    = curvyObject.format_colour(borderColour);
    this.borderColourB   = curvyObject.format_colour(borderColourB);
    this.borderColourL   = curvyObject.format_colour(borderColourL);
    this.borderString    = this.borderWidth + "px" + " solid " + this.borderColour;
    this.borderStringB   = this.borderWidthB + "px" + " solid " + this.borderColourB;
    this.backgroundImage = ((backgroundImage != "none")? backgroundImage : "");
    this.backgroundRepeat= backgroundRepeat;
  }
  catch(e) {
    throw this.newError('getMessage' in e ? e.getMessage() : e.message);
  }
  var clientHeight = this.boxHeight;
  var clientWidth = boxWidth; // save it as it gets trampled on later
  if (curvyBrowser.isOp) {
    backgroundPosX = styleToNPx(backgroundPosX);
    backgroundPosY = styleToNPx(backgroundPosY);
    if (backgroundPosX) {
      var t = clientWidth + this.borderWidthL + this.borderWidthR;
      if (backgroundPosX > t) backgroundPosX = t;
      backgroundPosX = (t / backgroundPosX * 100) + '%'; // convert to percentage
    }
    if (backgroundPosY) {
      var t = clientHeight + this.borderWidth + this.borderWidthB;
      if (backgroundPosY > t) backgroundPosY = t;
      backgroundPosY = (t / backgroundPosY * 100) + '%'; // convert to percentage
    }
  }
  if (curvyBrowser.quirksMode) {
  }
  else {
    this.boxWidth -= this.leftPadding + this.rightPadding;
    this.boxHeight -= this.topPadding + this.bottomPadding;
  }

  // Create content container
  this.contentContainer = document.createElement("div");
  if (filter) this.contentContainer.style.filter = filter; // IE8 bug fix
  while (this.box.firstChild) this.contentContainer.appendChild(this.box.removeChild(this.box.firstChild));

  if (boxPosition != "absolute") this.box.style.position = "relative";
  this.box.style.padding = '0';
  this.box.style.border = this.box.style.backgroundImage = 'none';
  this.box.style.backgroundColor = 'transparent';

  this.box.style.width   = (clientWidth + this.borderWidthL + this.borderWidthR) + 'px';
  this.box.style.height  = (clientHeight + this.borderWidth + this.borderWidthB) + 'px';

  // Ok we add an inner div to actually put things into this will allow us to keep the height

  var newMainContainer = document.createElement("div");
  newMainContainer.style.position = "absolute";
  if (filter) newMainContainer.style.filter = filter; // IE8 bug fix
  if (curvyBrowser.quirksMode) {
    newMainContainer.style.width  = (clientWidth + this.borderWidthL + this.borderWidthR) + 'px';
  } else {
    newMainContainer.style.width  = clientWidth + 'px';
  }
  newMainContainer.style.height = min0Px(clientHeight + this.borderWidth + this.borderWidthB - topMaxRadius - botMaxRadius);
  newMainContainer.style.padding  = "0";
  newMainContainer.style.top    = topMaxRadius + "px";
  newMainContainer.style.left   = "0";
  if (this.borderWidthL)
    newMainContainer.style.borderLeft = this.borderWidthL + "px solid " + this.borderColourL;
  if (this.borderWidth && !topMaxRadius)
    newMainContainer.style.borderTop = this.borderWidth + "px solid " + this.borderColour;
  if (this.borderWidthR)
    newMainContainer.style.borderRight = this.borderWidthR + "px solid " + this.borderColourL;
  if (this.borderWidthB && !botMaxRadius)
    newMainContainer.style.borderBottom = this.borderWidthB + "px solid " + this.borderColourB;
  newMainContainer.style.backgroundColor    = boxColour;
  newMainContainer.style.backgroundImage    = this.backgroundImage;
  newMainContainer.style.backgroundRepeat   = this.backgroundRepeat;
  this.shell = this.box.appendChild(newMainContainer);

  boxWidth = curvyBrowser.get_style(this.shell, "width");
  if (boxWidth === "" || boxWidth === "auto" || boxWidth.indexOf("%") !== -1) throw this.newError('Shell width is ' + boxWidth);
  this.boxWidth = (boxWidth != "" && boxWidth != "auto" && boxWidth.indexOf("%") == -1) ? parseInt(boxWidth) : this.shell.clientWidth;

  /*
    This method creates the corners and
    applies them to the div element.
  */
  this.applyCorners = function() {
    /*
      Set up background offsets. This may need to be delayed until
      the background image is loaded.
    */
    if (this.backgroundObject) {
      var bgOffset = function(style, imglen, boxlen) {
        if (style === 0) return 0;
        var retval;
        if (style === 'right' || style === 'bottom') return boxlen - imglen;
        if (style === 'center') return (boxlen - imglen) / 2;
        if (style.indexOf('%') > 0) return (boxlen - imglen) * 100 / parseInt(style);
        return styleToNPx(style);
      }
      this.backgroundPosX  = bgOffset(backgroundPosX, this.backgroundObject.width, clientWidth);
      this.backgroundPosY  = bgOffset(backgroundPosY, this.backgroundObject.height, clientHeight);
    }
    else if (this.backgroundImage) {
      this.backgroundPosX = styleToNPx(backgroundPosX);
      this.backgroundPosY = styleToNPx(backgroundPosY);
    }
    /*
      Create top and bottom containers.
      These will be used as a parent for the corners and bars.
    */
    // Build top bar only if a top corner is to be drawn
    if (topMaxRadius) {
      newMainContainer = document.createElement("div");
      newMainContainer.style.width = this.boxWidth + "px";
      newMainContainer.style.fontSize = "1px";
      newMainContainer.style.overflow = "hidden";
      newMainContainer.style.position = "absolute";
      newMainContainer.style.paddingLeft  = this.borderWidth + "px";
      newMainContainer.style.paddingRight = this.borderWidth + "px";
      newMainContainer.style.height = topMaxRadius + "px";
      newMainContainer.style.top    = -topMaxRadius + "px";
      newMainContainer.style.left   = -this.borderWidthL + "px";
      this.topContainer = this.shell.appendChild(newMainContainer);
    }
    // Build bottom bar only if a bottom corner is to be drawn
    if (botMaxRadius) {
      var newMainContainer = document.createElement("div");
      newMainContainer.style.width = this.boxWidth + "px";
      newMainContainer.style.fontSize = "1px";
      newMainContainer.style.overflow = "hidden";
      newMainContainer.style.position = "absolute";
      newMainContainer.style.paddingLeft  = this.borderWidthB + "px";
      newMainContainer.style.paddingRight = this.borderWidthB + "px";
      newMainContainer.style.height   =  botMaxRadius + "px";
      newMainContainer.style.bottom   = -botMaxRadius + "px";
      newMainContainer.style.left     = -this.borderWidthL + "px";
      this.bottomContainer = this.shell.appendChild(newMainContainer);
    }

    var corners = this.spec.cornerNames();  // array of available corners

    /*
    Loop for each corner
    */
    for (var i in corners) if (!isNaN(i)) {
      // Get current corner type from array
      var cc = corners[i];
      var specRadius = this.spec[cc + 'R'];
      // Has the user requested the currentCorner be round?
      // Code to apply correct color to top or bottom
      var bwidth, bcolor, borderRadius, borderWidthTB;
      if (cc == "tr" || cc == "tl") {
        bwidth = this.borderWidth;
        bcolor = this.borderColour;
        borderWidthTB = this.borderWidth;
      } else {
        bwidth = this.borderWidthB;
        bcolor = this.borderColourB;
        borderWidthTB = this.borderWidthB;
      }
      borderRadius = specRadius - borderWidthTB;
      var newCorner = document.createElement("div");
      newCorner.style.height = this.spec.get(cc + 'Ru');
      newCorner.style.width  = this.spec.get(cc + 'Ru');
      newCorner.style.position = "absolute";
      newCorner.style.fontSize = "1px";
      newCorner.style.overflow = "hidden";
      // THE FOLLOWING BLOCK OF CODE CREATES A ROUNDED CORNER
      // ---------------------------------------------------- TOP
      var intx, inty, outsideColour;
      var trans = filter ? parseInt(/alpha\(opacity.(\d+)\)/.exec(filter)[1]) : 100; // IE8 bug fix
      // Cycle the x-axis
      for (intx = 0; intx < specRadius; ++intx) {
        // Calculate the value of y1 which identifies the pixels inside the border
        var y1 = (intx + 1 >= borderRadius) ? -1 : Math.floor(Math.sqrt(Math.pow(borderRadius, 2) - Math.pow(intx + 1, 2))) - 1;
        // Calculate y2 and y3 only if there is a border defined
        if (borderRadius != specRadius) {
          var y2 = (intx >= borderRadius) ? -1 : Math.ceil(Math.sqrt(Math.pow(borderRadius, 2) - Math.pow(intx, 2)));
          var y3 = (intx + 1 >= specRadius) ? -1 : Math.floor(Math.sqrt(Math.pow(specRadius, 2) - Math.pow((intx+1), 2))) - 1;
        }
        // Calculate y4
        var y4 = (intx >= specRadius) ? -1 : Math.ceil(Math.sqrt(Math.pow(specRadius, 2) - Math.pow(intx, 2)));
        // Draw bar on inside of the border with foreground colour
        if (y1 > -1) this.drawPixel(intx, 0, this.boxColour, trans, (y1 + 1), newCorner, true, specRadius);
        // Draw border/foreground antialiased pixels and border only if there is a border defined
        if (borderRadius != specRadius) {
          // Cycle the y-axis
          if (this.spec.antiAlias) {
            for (inty = y1 + 1; inty < y2; ++inty) {
              // For each of the pixels that need anti aliasing between the foreground and border colour draw single pixel divs
              if (this.backgroundImage != "") {
                var borderFract = curvyObject.pixelFraction(intx, inty, borderRadius) * 100;
                this.drawPixel(intx, inty, bcolor, trans, 1, newCorner, borderFract >= 30, specRadius);
              }
              else if (this.boxColour !== 'transparent') {
                var pixelcolour = curvyObject.BlendColour(this.boxColour, bcolor, curvyObject.pixelFraction(intx, inty, borderRadius));
                this.drawPixel(intx, inty, pixelcolour, trans, 1, newCorner, false, specRadius);
              }
              else this.drawPixel(intx, inty, bcolor, trans >> 1, 1, newCorner, false, specRadius);
            }
            // Draw bar for the border
            if (y3 >= y2) {
              if (y2 == -1) y2 = 0;
              this.drawPixel(intx, y2, bcolor, trans, (y3 - y2 + 1), newCorner, false, 0);
            }
            outsideColour = bcolor;  // Set the colour for the outside AA curve
            inty = y3;               // start_pos - 1 for y-axis AA pixels
          }
          else { // no antiAlias
            if (y3 > y1) { // NB condition was >=, changed to avoid zero-height divs
              this.drawPixel(intx, (y1 + 1), bcolor, trans, (y3 - y1), newCorner, false, 0);
            }
          }
        }
        else {
          outsideColour = this.boxColour;  // Set the colour for the outside curve
          inty = y1;               // start_pos - 1 for y-axis AA pixels
        }
        // Draw aa pixels?
        if (this.spec.antiAlias) {
          // Cycle the y-axis and draw the anti aliased pixels on the outside of the curve
          while (++inty < y4) {
            // For each of the pixels that need anti aliasing between the foreground/border colour & background draw single pixel divs
            this.drawPixel(intx, inty, outsideColour, (curvyObject.pixelFraction(intx, inty , specRadius) * trans), 1, newCorner, borderWidthTB <= 0, specRadius);
          }
        }
      }
      // END OF CORNER CREATION
      // ---------------------------------------------------- END

      /*
      Now we have a new corner we need to reposition all the pixels unless
      the current corner is the bottom right.
      */
      // Loop through all children (pixel bars)
      for (var t = 0, k = newCorner.childNodes.length; t < k; ++t) {
        // Get current pixel bar
        var pixelBar = newCorner.childNodes[t];
        // Get current top and left properties
        var pixelBarTop    = parseInt(pixelBar.style.top);
        var pixelBarLeft   = parseInt(pixelBar.style.left);
        var pixelBarHeight = parseInt(pixelBar.style.height);
        // Reposition pixels
        if (cc == "tl" || cc == "bl") {
          pixelBar.style.left = (specRadius - pixelBarLeft - 1) + "px"; // Left
        }
        if (cc == "tr" || cc == "tl"){
          pixelBar.style.top =  (specRadius - pixelBarHeight - pixelBarTop) + "px"; // Top
        }
        pixelBar.style.backgroundRepeat = this.backgroundRepeat;

        if (this.backgroundImage) switch(cc) {
          case "tr":
            pixelBar.style.backgroundPosition = (this.backgroundPosX - this.borderWidthL + specRadius - clientWidth - pixelBarLeft) + "px " + (this.backgroundPosY + pixelBarHeight + pixelBarTop + this.borderWidth - specRadius) + "px";
          break;
          case "tl":
            pixelBar.style.backgroundPosition = (this.backgroundPosX - specRadius + pixelBarLeft + this.borderWidthL) + "px " + (this.backgroundPosY - specRadius + pixelBarHeight + pixelBarTop + this.borderWidth) + "px";
          break;
          case "bl":
            pixelBar.style.backgroundPosition = (this.backgroundPosX - specRadius + pixelBarLeft + 1 + this.borderWidthL) + "px " + (this.backgroundPosY - clientHeight - this.borderWidth + (curvyBrowser.quirksMode ? pixelBarTop : -pixelBarTop) + specRadius) + "px";
          break;
          case "br":
            if (curvyBrowser.quirksMode) {
              pixelBar.style.backgroundPosition = (this.backgroundPosX + this.borderWidthL - clientWidth + specRadius - pixelBarLeft) + "px " + (this.backgroundPosY - clientHeight - this.borderWidth + pixelBarTop + specRadius) + "px";
            } else {
              pixelBar.style.backgroundPosition = (this.backgroundPosX - this.borderWidthL - clientWidth + specRadius - pixelBarLeft) + "px " + (this.backgroundPosY - clientHeight - this.borderWidth + specRadius - pixelBarTop) + "px";
            }
          //break;
        }
      }

      // Position the container
      switch (cc) {
        case "tl":
          newCorner.style.top = newCorner.style.left = "0";
          this.topContainer.appendChild(newCorner);
        break;
        case "tr":
          newCorner.style.top = newCorner.style.right = "0";
          this.topContainer.appendChild(newCorner);
        break;
        case "bl":
          newCorner.style.bottom = newCorner.style.left = "0";
          this.bottomContainer.appendChild(newCorner);
        break;
        case "br":
          newCorner.style.bottom = newCorner.style.right = "0";
          this.bottomContainer.appendChild(newCorner);
        //break;
      }
    }

    /*
      The last thing to do is draw the rest of the filler DIVs.
    */

    // Find out which corner has the bigger radius and get the difference amount
    var radiusDiff = {
      t : this.spec.radiusdiff('t'),
      b : this.spec.radiusdiff('b')
    };

    for (z in radiusDiff) {
      if (typeof z === 'function') continue; // for prototype, mootools frameworks
      if (!this.spec.get(z + 'R')) continue; // no need if no corners
      if (radiusDiff[z]) {
        // check unsupported feature and warn if necessary
        if (this.backgroundImage && this.spec.radiusSum(z) !== radiusDiff[z])
          curvyCorners.alert(this.errmsg('Not supported: unequal non-zero top/bottom radii with background image'));
        // Get the type of corner that is the smaller one
        var smallerCornerType = (this.spec[z + "lR"] < this.spec[z + "rR"]) ? z + "l" : z + "r";

        // First we need to create a DIV for the space under the smaller corner
        var newFiller = document.createElement("div");
        newFiller.style.height = radiusDiff[z] + "px";
        newFiller.style.width  =  this.spec.get(smallerCornerType + 'Ru');
        newFiller.style.position = "absolute";
        newFiller.style.fontSize = "1px";
        newFiller.style.overflow = "hidden";
        newFiller.style.backgroundColor = this.boxColour;

        // Position filler
        switch (smallerCornerType) {
          case "tl":
            newFiller.style.bottom =
            newFiller.style.left   = "0";
            newFiller.style.borderLeft = this.borderString;
            this.topContainer.appendChild(newFiller);
          break;
          case "tr":
            newFiller.style.bottom =
            newFiller.style.right  = "0";
            newFiller.style.borderRight = this.borderString;
            this.topContainer.appendChild(newFiller);
          break;
          case "bl":
            newFiller.style.top    =
            newFiller.style.left   = "0";
            newFiller.style.borderLeft = this.borderStringB;
            this.bottomContainer.appendChild(newFiller);
          break;
          case "br":
            newFiller.style.top    =
            newFiller.style.right  = "0";
            newFiller.style.borderRight = this.borderStringB;
            this.bottomContainer.appendChild(newFiller);
          //break;
        }
      }

      // Create the bar to fill the gap between each corner horizontally
      var newFillerBar = document.createElement("div");
      if (filter) newFillerBar.style.filter = filter; // IE8 bug fix
      newFillerBar.style.position = "relative";
      newFillerBar.style.fontSize = "1px";
      newFillerBar.style.overflow = "hidden";
      newFillerBar.style.width = this.fillerWidth(z);
      newFillerBar.style.backgroundColor = this.boxColour;
      newFillerBar.style.backgroundImage = this.backgroundImage;
      newFillerBar.style.backgroundRepeat= this.backgroundRepeat;

      switch (z) {
        case "t":
          // Top Bar
          if (this.topContainer) {
            if (curvyBrowser.quirksMode) {
              newFillerBar.style.height = 100 + topMaxRadius + "px";
            } else {
              newFillerBar.style.height = 100 + topMaxRadius - this.borderWidth + "px";
            }
            newFillerBar.style.marginLeft  = this.spec.tlR ? (this.spec.tlR - this.borderWidthL) + "px" : "0";
            newFillerBar.style.borderTop   = this.borderString;
            if (this.backgroundImage) {
              var x_offset = this.spec.tlR ?
                (this.backgroundPosX - (topMaxRadius - this.borderWidthL)) + "px " : "0 ";
              newFillerBar.style.backgroundPosition  = x_offset + this.backgroundPosY + "px";
              // Reposition the box's background image
              this.shell.style.backgroundPosition = this.backgroundPosX + "px " + (this.backgroundPosY - topMaxRadius + this.borderWidthL) + "px";
            }
            this.topContainer.appendChild(newFillerBar);
          }
        break;
        case "b":
          if (this.bottomContainer) {
            // Bottom Bar
            if (curvyBrowser.quirksMode) {
              newFillerBar.style.height     = botMaxRadius + "px";
            } else {
              newFillerBar.style.height     = botMaxRadius - this.borderWidthB + "px";
            }
            newFillerBar.style.marginLeft   = this.spec.blR ? (this.spec.blR - this.borderWidthL) + "px" : "0";
            newFillerBar.style.borderBottom = this.borderStringB;
            if (this.backgroundImage) {
              var x_offset = this.spec.blR ?
                (this.backgroundPosX + this.borderWidthL - botMaxRadius) + "px " : this.backgroundPosX + "px ";
              newFillerBar.style.backgroundPosition = x_offset + (this.backgroundPosY - clientHeight - this.borderWidth + botMaxRadius) + "px";
            }
            this.bottomContainer.appendChild(newFillerBar);
          }
        //break;
      }
    }

    // style content container
    this.contentContainer.style.position = "absolute";
    // contentContainer.style.border = "1px dotted #000"; // DEBUG, comment for production
    this.contentContainer.className    = "autoPadDiv";
    this.contentContainer.style.left   = this.borderWidthL + "px";
    // Get padding amounts
    // Apply top padding
    this.contentContainer.style.paddingTop = this.topPadding + "px";
    this.contentContainer.style.top = this.borderWidth + "px";
    // skip bottom padding - it doesn't show!
    // Apply left and right padding
    this.contentContainer.style.paddingLeft = this.leftPadding + "px";
    this.contentContainer.style.paddingRight = this.rightPadding + "px";
    z = clientWidth;
    if (!curvyBrowser.quirksMode) z -= this.leftPadding + this.rightPadding;
    this.contentContainer.style.width = z + "px";
    this.contentContainer.style.textAlign = curvyBrowser.get_style(this.box, 'textAlign');
    this.box.style.textAlign = 'left'; // important otherwise layout goes wild

    this.box.appendChild(this.contentContainer);
    if (boxDisp) boxDisp.style.display = 'none';
  }
  if (this.backgroundImage) {
    backgroundPosX = this.backgroundCheck(backgroundPosX);
    backgroundPosY = this.backgroundCheck(backgroundPosY);
    if (this.backgroundObject) {
      this.backgroundObject.holdingElement = this;
      this.dispatch = this.applyCorners;
      this.applyCorners = function() {
        if (this.backgroundObject.complete)
          this.dispatch();
        else this.backgroundObject.onload = new Function('curvyObject.dispatch(this.holdingElement);');
      }
    }
  }
}

curvyObject.prototype.backgroundCheck = function(style) {
  if (style === 'top' || style === 'left' || parseInt(style) === 0) return 0;
  if (!(/^[-\d.]+px$/.test(style))  && !this.backgroundObject) {
    this.backgroundObject = new Image;
    var imgName = function(str) {
      var matches = /url\("?([^'"]+)"?\)/.exec(str);
      return (matches ? matches[1] : str);
    }
    this.backgroundObject.src = imgName(this.backgroundImage);
  }
  return style;
}

curvyObject.dispatch = function(obj) {
  if ('dispatch' in obj)
    obj.dispatch();
  else throw obj.newError('No dispatch function');
}

// append a pixel DIV to newCorner

curvyObject.prototype.drawPixel = function(intx, inty, colour, transAmount, height, newCorner, image, cornerRadius) {
  var pixel = document.createElement("div");
  pixel.style.height   = height + "px";
  pixel.style.width    = "1px";
  pixel.style.position = "absolute";
  pixel.style.fontSize = "1px";
  pixel.style.overflow = "hidden";
  var topMaxRadius = this.spec.get('tR');
  pixel.style.backgroundColor = colour;
  // Don't apply background image to border pixels
  if (image && this.backgroundImage != "") {
    pixel.style.backgroundImage = this.backgroundImage;
    pixel.style.backgroundPosition  = "-" + (this.boxWidth - (cornerRadius - intx) + this.borderWidth) + "px -" + ((this.boxHeight + topMaxRadius + inty) - this.borderWidth) + "px";
  }
  // Set opacity if the transparency is anything other than 100
  if (transAmount != 100) curvyObject.setOpacity(pixel, transAmount);
  // Set position
  pixel.style.top = inty + "px";
  pixel.style.left = intx + "px";
  //pixel.nodeValue = ' ';
  newCorner.appendChild(pixel);
}

curvyObject.prototype.fillerWidth = function(tb) {
  var bWidth = curvyBrowser.quirksMode ? 0 : this.spec.radiusCount(tb) * this.borderWidthL;
  return (this.boxWidth - this.spec.radiusSum(tb) + bWidth) + 'px';
}

curvyObject.prototype.errmsg = function(msg, gravity) {
  var extradata = "\ntag: " + this.box.tagName;
  if (this.box.id) extradata += "\nid: " + this.box.id;
  if (this.box.className) extradata += "\nclass: " + this.box.className;
  var parent;
  if ((parent = this.box.parentNode) === null)
    extradata += "\n(box has no parent)";
  else {
    extradata += "\nParent tag: " + parent.tagName;
    if (parent.id) extradata += "\nParent ID: " + parent.id;
    if (parent.className) extradata += "\nParent class: " + parent.className;
  }
  if (gravity === undefined) gravity = 'warning';
  return 'curvyObject ' + gravity + ":\n" + msg + extradata;
}

curvyObject.prototype.newError = function(msg) {
  return new Error(this.errmsg(msg, 'exception'));
}

// ------------- UTILITY FUNCTIONS

//  Convert a number 0..255 to hex


curvyObject.IntToHex = function(strNum) {
  var hexdig = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F' ];

  return hexdig[strNum >>> 4] + '' + hexdig[strNum & 15];
}

/*
  Blends the two colours by the fraction
  returns the resulting colour as a string in the format "#FFFFFF"
*/

curvyObject.BlendColour = function(Col1, Col2, Col1Fraction) {
  if (Col1 === 'transparent' || Col2 === 'transparent') throw this.newError('Cannot blend with transparent');
  if (Col1.charAt(0) !== '#') {
    //curvyCorners.alert('Found colour1 ' + Col1 + ': please let us know you saw this report.');
    Col1 = curvyObject.format_colour(Col1);
  }
  if (Col2.charAt(0) !== '#') {
    //curvyCorners.alert('Found colour2 ' + Col2 + ': please let us know you saw this report.');
    Col2 = curvyObject.format_colour(Col2);
  }
  var red1 = parseInt(Col1.substr(1, 2), 16);
  var green1 = parseInt(Col1.substr(3, 2), 16);
  var blue1 = parseInt(Col1.substr(5, 2), 16);
  var red2 = parseInt(Col2.substr(1, 2), 16);
  var green2 = parseInt(Col2.substr(3, 2), 16);
  var blue2 = parseInt(Col2.substr(5, 2), 16);

  if (Col1Fraction > 1 || Col1Fraction < 0) Col1Fraction = 1;

  var endRed = Math.round((red1 * Col1Fraction) + (red2 * (1 - Col1Fraction)));
  if (endRed > 255) endRed = 255;
  if (endRed < 0) endRed = 0;

  var endGreen = Math.round((green1 * Col1Fraction) + (green2 * (1 - Col1Fraction)));
  if (endGreen > 255) endGreen = 255;
  if (endGreen < 0) endGreen = 0;

  var endBlue = Math.round((blue1 * Col1Fraction) + (blue2 * (1 - Col1Fraction)));
  if (endBlue > 255) endBlue = 255;
  if (endBlue < 0) endBlue = 0;

  return "#" + curvyObject.IntToHex(endRed) + curvyObject.IntToHex(endGreen)+ curvyObject.IntToHex(endBlue);
}

/*
  For a pixel cut by the line determines the fraction of the pixel on the 'inside' of the
  line.  Returns a number between 0 and 1
*/

curvyObject.pixelFraction = function(x, y, r) {
  var fraction;
  var rsquared = r * r;

  /*
    determine the co-ordinates of the two points on the perimeter of the pixel that the
    circle crosses
  */
  var xvalues = new Array(2);
  var yvalues = new Array(2);
  var point = 0;
  var whatsides = "";

  // x + 0 = Left
  var intersect = Math.sqrt(rsquared - Math.pow(x, 2));

  if (intersect >= y && intersect < (y + 1)) {
    whatsides = "Left";
    xvalues[point] = 0;
    yvalues[point] = intersect - y;
    ++point;
  }
  // y + 1 = Top
  intersect = Math.sqrt(rsquared - Math.pow(y + 1, 2));

  if (intersect >= x && intersect < (x + 1)) {
    whatsides += "Top";
    xvalues[point] = intersect - x;
    yvalues[point] = 1;
    ++point;
  }
  // x + 1 = Right
  intersect = Math.sqrt(rsquared - Math.pow(x + 1, 2));

  if (intersect >= y && intersect < (y + 1)) {
    whatsides += "Right";
    xvalues[point] = 1;
    yvalues[point] = intersect - y;
    ++point;
  }
  // y + 0 = Bottom
  intersect = Math.sqrt(rsquared - Math.pow(y, 2));

  if (intersect >= x && intersect < (x + 1)) {
    whatsides += "Bottom";
    xvalues[point] = intersect - x;
    yvalues[point] = 0;
  }

  /*
    depending on which sides of the perimeter of the pixel the circle crosses calculate the
    fraction of the pixel inside the circle
  */
  switch (whatsides) {
    case "LeftRight":
      fraction = Math.min(yvalues[0], yvalues[1]) + ((Math.max(yvalues[0], yvalues[1]) - Math.min(yvalues[0], yvalues[1])) / 2);
    break;

    case "TopRight":
      fraction = 1 - (((1 - xvalues[0]) * (1 - yvalues[1])) / 2);
    break;

    case "TopBottom":
      fraction = Math.min(xvalues[0], xvalues[1]) + ((Math.max(xvalues[0], xvalues[1]) - Math.min(xvalues[0], xvalues[1])) / 2);
    break;

    case "LeftBottom":
      fraction = yvalues[0] * xvalues[1] / 2;
    break;

    default:
      fraction = 1;
  }

  return fraction;
}

// Returns an array of rgb values

curvyObject.rgb2Array = function(rgbColour) {
  // Remove rgb()
  var rgbValues = rgbColour.substring(4, rgbColour.indexOf(")"));

  // Split RGB into array
  return rgbValues.split(", ");
}

// This function converts CSS rgb(x, x, x) to hexadecimal

curvyObject.rgb2Hex = function(rgbColour) {
  try {
    // Get array of RGB values
    var rgbArray = curvyObject.rgb2Array(rgbColour);

    // Get RGB values
    var red   = parseInt(rgbArray[0]);
    var green = parseInt(rgbArray[1]);
    var blue  = parseInt(rgbArray[2]);

    // Build hex colour code
    var hexColour = "#" + curvyObject.IntToHex(red) + curvyObject.IntToHex(green) + curvyObject.IntToHex(blue);
  }
  catch (e) {
    var msg = 'getMessage' in e ? e.getMessage() : e.message;
    throw new Error("Error (" + msg + ") converting RGB value to Hex in rgb2Hex");
  }

  return hexColour;
}

/*
  Function by Simon Willison from sitepoint.com
  Modified by Cameron Cooke adding Safari's rgba support
*/

curvyObject.setOpacity = function(obj, opacity) {
  opacity = (opacity == 100) ? 99.999 : opacity;

  if (curvyBrowser.isSafari && obj.tagName != "IFRAME") {
    // Get array of RGB values
    var rgbArray = curvyObject.rgb2Array(obj.style.backgroundColor);

    // Get RGB values
    var red   = parseInt(rgbArray[0]);
    var green = parseInt(rgbArray[1]);
    var blue  = parseInt(rgbArray[2]);

    // Safari using RGBA support
    obj.style.backgroundColor = "rgba(" + red + ", " + green + ", " + blue + ", " + opacity/100 + ")";
  }
  else if (typeof obj.style.opacity !== "undefined") { // W3C
    obj.style.opacity = opacity / 100;
  }
  else if (typeof obj.style.MozOpacity !== "undefined") { // Older Mozilla
    obj.style.MozOpacity = opacity / 100;
  }
  else if (typeof obj.style.filter != "undefined") { // IE
    obj.style.filter = "alpha(opacity=" + opacity + ")";
  }
  else if (typeof obj.style.KHTMLOpacity != "undefined") { // Older KHTML Based curvyBrowsers
    obj.style.KHTMLOpacity = opacity / 100;
  }
}


// Cross browser add event wrapper

function addEvent(elm, evType, fn, useCapture) {
  if (elm.addEventListener) {
    elm.addEventListener(evType, fn, useCapture);
    return true;
  }
  if (elm.attachEvent) return elm.attachEvent('on' + evType, fn);
  elm['on' + evType] = fn;
  return false;
}

// Gets the computed colour.
curvyObject.getComputedColour = function(colour) {
  var d = document.createElement('DIV');
  d.style.backgroundColor = colour;
  document.body.appendChild(d);

  if(window.getComputedStyle) { // Mozilla, Opera, Chrome, Safari
    var rtn = document.defaultView.getComputedStyle(d, null).getPropertyValue('background-color');
    d.parentNode.removeChild(d);
    if(rtn.substr(0, 3) === "rgb") rtn = curvyObject.rgb2Hex(rtn);
    return rtn;
  }
  else { // IE
    var rng = document.body.createTextRange();
    rng.moveToElementText(d);
    rng.execCommand('ForeColor', false, colour);
    var iClr = rng.queryCommandValue('ForeColor');
    var rgb = "rgb("+(iClr & 0xFF)+", "+((iClr & 0xFF00)>>8)+", "+((iClr & 0xFF0000)>>16)+")";
    d.parentNode.removeChild(d);
    rng = null;
    return curvyObject.rgb2Hex(rgb);
  }
}

// convert colour name, rgb() and #RGB to #RRGGBB
curvyObject.format_colour = function(colour) {
  // Make sure colour is set and not transparent
  if (colour != "" && colour != "transparent") {
    // RGB Value?
    if (colour.substr(0, 3) === "rgb") {
      // Get HEX aquiv.
      colour = curvyObject.rgb2Hex(colour);
    }
    else if (colour.charAt(0) !== '#') {
      // Convert colour name to hex value
      colour = curvyObject.getComputedColour(colour);
    }
    else if (colour.length === 4) {
      // 3 chr colour code add remainder
      colour = "#" + colour.charAt(1) + colour.charAt(1) + colour.charAt(2) + colour.charAt(2) + colour.charAt(3) + colour.charAt(3);
    }
  }
  return colour;
}

// Get elements by class by Dustin Diaz / CPKS
// NB if searchClass is a class name, it MUST be preceded by '.'

curvyCorners.getElementsByClass = function(searchClass, node) {
  var classElements = new Array;
  if (node === undefined) node = document;
  searchClass = searchClass.split('.'); // see if there's a tag in there
  var tag = '*'; // prepare for no tag
  if (searchClass.length === 1) {
    tag = searchClass[0];
    searchClass = false;
  }
  else {
    if (searchClass[0]) tag = searchClass[0];
    searchClass = searchClass[1];
  }
  var i, els, elsLen;
  if (tag.charAt(0) === '#') {
    els = document.getElementById(tag.substr(1));
    if (els) classElements.push(els);
  }
  else {
    els = node.getElementsByTagName(tag);
    elsLen = els.length;
    if (searchClass) {
      var pattern = new RegExp("(^|\\s)" + searchClass + "(\\s|$)");
      for (i = 0; i < elsLen; ++i) {
        if (pattern.test(els[i].className)) classElements.push(els[i]);
      }
    }
    else for (i = 0; i < elsLen; ++i) classElements.push(els[i]);
  }
  return classElements;
}

if (curvyBrowser.isMoz || curvyBrowser.isWebKit)
  var curvyCornersNoAutoScan = true; // it won't do anything anyway.
else {

  // autoscan code

  curvyCorners.scanStyles = function() {
    function units(num) {
      var matches = /^[\d.]+(\w+)$/.exec(num);
      return matches[1];
    }
    var t, i, j;

    if (curvyBrowser.isIE) {
      function procIEStyles(rule) {
        var style = rule.style;

        if(curvyBrowser.ieVer > 6.0) {
          var allR = style['-webkit-border-radius'] || 0;
          var tR   = style['-webkit-border-top-right-radius'] || 0;
          var tL   = style['-webkit-border-top-left-radius'] || 0;
          var bR   = style['-webkit-border-bottom-right-radius'] || 0;
          var bL   = style['-webkit-border-bottom-left-radius'] || 0;
        }
        else {
          var allR = style['webkit-border-radius'] || 0;
          var tR   = style['webkit-border-top-right-radius'] || 0;
          var tL   = style['webkit-border-top-left-radius'] || 0;
          var bR   = style['webkit-border-bottom-right-radius'] || 0;
          var bL   = style['webkit-border-bottom-left-radius'] || 0;
        }
        if (allR || tL || tR || bR || bL) {
          var settings = new curvyCnrSpec(rule.selectorText);
          if (allR)
            settings.setcorner(null, null, parseInt(allR), units(allR));
          else {
            if (tR) settings.setcorner('t', 'r', parseInt(tR), units(tR));
            if (tL) settings.setcorner('t', 'l', parseInt(tL), units(tL));
            if (bL) settings.setcorner('b', 'l', parseInt(bL), units(bL));
            if (bR) settings.setcorner('b', 'r', parseInt(bR), units(bR));
          }
          curvyCorners(settings);
        }
      }
      for (t = 0; t < document.styleSheets.length; ++t) {
        if (document.styleSheets[t].imports) {
          for (i = 0; i < document.styleSheets[t].imports.length; ++i)
            for (j = 0; j < document.styleSheets[t].imports[i].rules.length; ++j)
              procIEStyles(document.styleSheets[t].imports[i].rules[j]);
        }
        for (i = 0; i < document.styleSheets[t].rules.length; ++i)
          procIEStyles(document.styleSheets[t].rules[i]);
      }
    }
    else if (curvyBrowser.isOp) {
      for (t = 0; t < document.styleSheets.length; ++t) {
        if (operasheet.contains_border_radius(t)) {
          j = new operasheet(t);
          for (i in j.rules) if (!isNaN(i))
            curvyCorners(j.rules[i]);
        }
      }
    }
    else curvyCorners.alert('Scanstyles does nothing in Webkit/Firefox');
  };

  // Dean Edwards/Matthias Miller/John Resig

  curvyCorners.init = function() {
    // quit if this function has already been called
    if (arguments.callee.done) return;

    // flag this function so we don't do the same thing twice
    arguments.callee.done = true;

    // kill the timer
    if (curvyBrowser.isWebKit && curvyCorners.init.timer) {
      clearInterval(curvyCorners.init.timer);
      curvyCorners.init.timer = null;
    }

    // do stuff
    curvyCorners.scanStyles();
  };
}

if (typeof curvyCornersNoAutoScan === 'undefined' || curvyCornersNoAutoScan === false) {
  if (curvyBrowser.isOp)
    document.addEventListener("DOMContentLoaded", curvyCorners.init, false);
  else addEvent(window, 'load', curvyCorners.init, false);
}