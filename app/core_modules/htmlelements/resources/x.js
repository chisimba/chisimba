// x.js
// X v3.10, Cross-Browser DHTML Library from Cross-Browser.com
// Copyright (c) 2002,2003 Michael Foster (mike@cross-browser.com)
// This library is distributed under the terms of the LGPL (gnu.org)

// Variables:
var xVersion='3.10',xOp7=false,xOp5or6=false,xIE4Up=false,xNN4=false,xUA=navigator.userAgent.toLowerCase();
if(window.opera){
  xOp7=(xUA.indexOf('opera 7')!=-1 || xUA.indexOf('opera/7')!=-1);
  if (!xOp7) xOp5or6=(xUA.indexOf('opera 5')!=-1 || xUA.indexOf('opera/5')!=-1 || xUA.indexOf('opera 6')!=-1 || xUA.indexOf('opera/6')!=-1);
}
else if(document.layers) xNN4=true;
else {xIE4Up=document.all && xUA.indexOf('msie')!=-1 && parseInt(navigator.appVersion)>=4;}

// Appearance:
function xShow(e) {
  if(!(e=xGetElementById(e))) return;
  if(e.style && xDef(e.style.visibility)) e.style.visibility='inherit';
  else if(xDef(e.visibility)) e.visibility='show';
}
function xHide(e) {
  if(!(e=xGetElementById(e))) return;
  if(e.style && xDef(e.style.visibility)) e.style.visibility='hidden';
  else if(xDef(e.visibility)) e.visibility='hide';
}
function xZIndex(e,uZ) {
  if(!(e=xGetElementById(e))) return 0;
  if(e.style && xDef(e.style.zIndex)) {
    if(arguments.length>1) e.style.zIndex=uZ;
    else uZ=e.style.zIndex;
  }
  else if(xDef(e.zIndex)) {
    if(arguments.length>1) e.zIndex=uZ;
    else uZ=e.zIndex;
  }
  return uZ;
}
function xColor(e,sColor) {
  if(!(e=xGetElementById(e))) return "";
  var c="";
  if(e.style && xDef(e.style.color)) {
    if(arguments.length>1) e.style.color=sColor;
    c=e.style.color;
  }
  return c;
}
function xBackground(e,sColor,sImage) {
  if(!(e=xGetElementById(e))) return "";
  var bg="";
  if(e.style) {
    if(arguments.length>1) e.style.backgroundColor=sColor;
    if(arguments.length==3) e.style.backgroundImage=(sImage && sImage!="")? "url("+sImage+")" : null;
    bg=e.style.backgroundColor;
  }
  else if(xDef(e.bgColor)) {
    if(arguments.length>1) e.bgColor=sColor;
    bg=e.bgColor;
    if(arguments.length==3) e.background.src=sImage;
  }
  return bg;
}

// Position:
function xMoveTo(e,iX,iY) {
  xLeft(e,iX);
  xTop(e,iY);
}
function xLeft(e,iX) {
  if(!(e=xGetElementById(e))) return 0;
  var css=xDef(e.style);
  if (css && xDef(e.style.left) && typeof(e.style.left)=="string") {
    if(arguments.length>1) e.style.left=iX+"px";
    else {
      iX=parseInt(e.style.left);
      if(isNaN(iX)) iX=0;
    }
  }
  else if(css && xDef(e.style.pixelLeft)) {
    if(arguments.length>1) e.style.pixelLeft=iX;
    else iX=e.style.pixelLeft;
  }
  else if(xDef(e.left)) {
    if(arguments.length>1) e.left=iX;
    else iX=e.left;
  }
  return iX;
}
function xTop(e,iY) {
  if(!(e=xGetElementById(e))) return 0;
  var css=xDef(e.style);
  if(css && xDef(e.style.top) && typeof(e.style.top)=="string") {
    if(arguments.length>1) e.style.top=iY+"px";
    else {
      iY=parseInt(e.style.top);
      if(isNaN(iY)) iY=0;
    }
  }
  else if(css && xDef(e.style.pixelTop)) {
    if(arguments.length>1) e.style.pixelTop=iY;
    else iY=e.style.pixelTop;
  }
  else if(xDef(e.top)) {
    if(arguments.length>1) e.top=iY;
    else iY=e.top;
  }
  return iY;
}
function xPageX(e) {
  if (!(e=xGetElementById(e))) return 0;
  if (xDef(e.pageX)) return e.pageX;
  var x = 0;
  while (e) {
    if (xDef(e.offsetLeft)) x += e.offsetLeft;
    e = xParent(e);
  }
  return x;
}
function xPageY(e) {
  if (!(e=xGetElementById(e))) return 0;
  if (xDef(e.pageY)) return e.pageY;
  var y = 0;
  while (e) {
    if (xDef(e.offsetTop)) y += e.offsetTop;
    e = xParent(e);
  }
  return y;
}
function xSlideTo(e,x,y,uTime) {
  if (!(e=xGetElementById(e))) return;
  if (!e.timeout) e.timeout = 25;
  e.xTarget = x; e.yTarget = y; e.slideTime = uTime; e.stop = false;
  e.yA = e.yTarget - xTop(e); e.xA = e.xTarget - xLeft(e); // A = distance
  e.B = Math.PI / (2 * e.slideTime); // B = period
  e.yD = xTop(e); e.xD = xLeft(e); // D = initial position
  var d = new Date(); e.C = d.getTime();
  if (!e.moving) xSlide(e);
}
function xSlide(e) {
  if (!(e=xGetElementById(e))) return;
  var now, s, t, newY, newX;
  now = new Date();
  t = now.getTime() - e.C;
  if (e.stop) { e.moving = false; }
  else if (t < e.slideTime) {
    setTimeout("xSlide('"+e.id+"')", e.timeout);
    s = Math.sin(e.B * t);
    newX = Math.round(e.xA * s + e.xD);
    newY = Math.round(e.yA * s + e.yD);
    xMoveTo(e, newX, newY);
    e.moving = true;
  }  
  else {
    xMoveTo(e, e.xTarget, e.yTarget);
    e.moving = false;
  }  
}

// Size:
function xResizeTo(e,uW,uH) {
  xWidth(e,uW);
  xHeight(e,uH);
}
function xWidth(e,uW) {
  if(!(e=xGetElementById(e)) || (uW && uW<0)) return 0;
  uW=Math.round(uW);
  var css=xDef(e.style);
  if(css && xDef(e.style.width,e.offsetWidth) && typeof(e.style.width)=="string") {
    if(arguments.length>1) xSetCW(e, uW);
    uW=e.offsetWidth;
  }
  else if(css && xDef(e.style.pixelWidth)) {
    if(arguments.length>1) e.style.pixelWidth=uW;
    uW=e.style.pixelWidth;
  }
  else if(xDef(e.clip) && xDef(e.clip.right)) {
    if(arguments.length>1) e.clip.right=uW;
    uW=e.clip.right;
  }
  return uW;
}
function xHeight(e,uH) {
  if(!(e=xGetElementById(e)) || (uH && uH<0)) return 0;
  uH=Math.round(uH);
  var css=xDef(e.style);
  if(css && xDef(e.style.height,e.offsetHeight) && typeof(e.style.height)=="string") {
    if(arguments.length>1) xSetCH(e, uH);
    uH=e.offsetHeight;
  }
  else if(css && xDef(e.style.pixelHeight)) {
    if(arguments.length>1) e.style.pixelHeight=uH;
    uH=e.style.pixelHeight;
  }
  else if(xDef(e.clip) && xDef(e.clip.bottom)) {
    if(arguments.length>1) e.clip.bottom=uH;
    uH=e.clip.bottom;
  }
  return uH;
}
// thank moz for the next 2000 bytes
function xGetCS(ele,sP){return parseInt(document.defaultView.getComputedStyle(ele,"").getPropertyValue(sP));}
function xSetCW(ele,uW){
  if(uW<0) return;
  var pl=0,pr=0,bl=0,br=0;
  if(xDef(document.defaultView) && xDef(document.defaultView.getComputedStyle)){
    pl=xGetCS(ele,"padding-left");
    pr=xGetCS(ele,"padding-right");
    bl=xGetCS(ele,"border-left-width");
    br=xGetCS(ele,"border-right-width");
  }
  else if(xDef(ele.currentStyle,document.compatMode)){
    if(document.compatMode=="CSS1Compat"){
      pl=parseInt(ele.currentStyle.paddingLeft);
      pr=parseInt(ele.currentStyle.paddingRight);
      bl=parseInt(ele.currentStyle.borderLeftWidth);
      br=parseInt(ele.currentStyle.borderRightWidth);
    }
  }
  else if(xDef(ele.offsetWidth,ele.style.width)){
    ele.style.width=uW+"px";
    pl=ele.offsetWidth-uW;
  }
  if(isNaN(pl)) pl=0; if(isNaN(pr)) pr=0; if(isNaN(bl)) bl=0; if(isNaN(br)) br=0;
  var cssW=uW-(pl+pr+bl+br);
  if(isNaN(cssW)||cssW<0) return;
  else ele.style.width=cssW+"px";
}
function xSetCH(ele,uH){
  if(uH<0) return;
  var pt=0,pb=0,bt=0,bb=0;
  if(xDef(document.defaultView) && xDef(document.defaultView.getComputedStyle)){
    pt=xGetCS(ele,"padding-top");
    pb=xGetCS(ele,"padding-bottom");
    bt=xGetCS(ele,"border-top-width");
    bb=xGetCS(ele,"border-bottom-width");
  }
  else if(xDef(ele.currentStyle,document.compatMode)){
    if(document.compatMode=="CSS1Compat"){
      pt=parseInt(ele.currentStyle.paddingTop);
      pb=parseInt(ele.currentStyle.paddingBottom);
      bt=parseInt(ele.currentStyle.borderTopWidth);
      bb=parseInt(ele.currentStyle.borderBottomWidth);
    }
  }
  else if(xDef(ele.offsetHeight,ele.style.height)){
    ele.style.height=uH+"px";
    pt=ele.offsetHeight-uH;
  }
  if(isNaN(pt)) pt=0; if(isNaN(pb)) pb=0; if(isNaN(bt)) bt=0; if(isNaN(bb)) bb=0;
  var cssH=uH-(pt+pb+bt+bb);
  if(isNaN(cssH)||cssH<0) return;
  else ele.style.height=cssH+"px";
}
function xClip(e,iTop,iRight,iBottom,iLeft) {
  if(!(e=xGetElementById(e))) return;
  if(e.style) {
    if (arguments.length == 5) e.style.clip="rect("+iTop+"px "+iRight+"px "+iBottom+"px "+iLeft+"px)";
    else e.style.clip="rect(0 "+parseInt(e.style.width)+"px "+parseInt(e.style.height)+"px 0)";
  }
  else if(e.clip) {
    if (arguments.length == 5) { e.clip.top=iTop; e.clip.right=iRight; e.clip.bottom=iBottom; e.clip.left=iLeft; }
    else { e.clip.top=0; e.clip.right=xWidth(e); e.clip.bottom=xHeight(e); e.clip.left=0; }
  }
}

// Event:
function xAddEventListener(e,eventType,eventListener,useCapture) {
  if(!(e=xGetElementById(e))) return;
  eventType=eventType.toLowerCase();
  if((!xIE4Up && !xOp7) && e==window) {
    if(eventType=='resize') { window.xPCW=xClientWidth(); window.xPCH=xClientHeight(); window.xREL=eventListener; xResizeEvent(); return; }
    if(eventType=='scroll') { window.xPSL=xScrollLeft(); window.xPST=xScrollTop(); window.xSEL=eventListener; xScrollEvent(); return; }
  }
  var eh="e.on"+eventType+"=eventListener";
  if(e.addEventListener) e.addEventListener(eventType,eventListener,useCapture);
  else if(e.attachEvent) e.attachEvent("on"+eventType,eventListener);
  else if(e.captureEvents) {
    if(useCapture||(eventType.indexOf('mousemove')!=-1)) { e.captureEvents(eval("Event."+eventType.toUpperCase())); }
    eval(eh);
  }
  else eval(eh);
}
function xRemoveEventListener(e,eventType,eventListener,useCapture) {
  if(!(e=xGetElementById(e))) return;
  eventType=eventType.toLowerCase();
  if((!xIE4Up && !xOp7) && e==window) {
    if(eventType=='resize') { window.xREL=null; return; }
    if(eventType=='scroll') { window.xSEL=null; return; }
  }
  var eh="e.on"+eventType+"=null";
  if(e.removeEventListener) e.removeEventListener(eventType,eventListener,useCapture);
  else if(e.detachEvent) e.detachEvent("on"+eventType,eventListener);
  else if(e.releaseEvents) {
    if(useCapture||(eventType.indexOf('mousemove')!=-1)) { e.releaseEvents(eval("Event."+eventType.toUpperCase())); }
    eval(eh);
  }
  else eval(eh);
}
function xEvent(evt) { // cross-browser event object prototype
  this.type = "";
  this.target = null;
  this.pageX = 0;
  this.pageY = 0;
  this.offsetX = 0;
  this.offsetY = 0;
  this.keyCode = 0;
  var e = evt ? evt : window.event;
  if(!e) return;
  // type
  if(e.type) this.type = e.type;
  // target
  if(xNN4) this.target = xLayerFromPoint(e.pageX, e.pageY);
  else if(e.target) this.target = e.target;
  else if(e.srcElement) this.target = e.srcElement;
  // pageX, pageY
  if(xOp5or6) { this.pageX = e.clientX; this.pageY = e.clientY; }
  else if(xDef(e.pageX,e.pageY)) { this.pageX = e.pageX; this.pageY = e.pageY; }
  else if(xDef(e.clientX,e.clientY)) { this.pageX = e.clientX + xScrollLeft(); this.pageY = e.clientY + xScrollTop(); }
  // offsetX, offsetY
  if(xDef(e.layerX,e.layerY)) { this.offsetX = e.layerX; this.offsetY = e.layerY; }
  else if(xDef(e.offsetX,e.offsetY)) { this.offsetX = e.offsetX; this.offsetY = e.offsetY; }
  else { this.offsetX = this.pageX - xPageX(this.target); this.offsetY = this.pageY - xPageY(this.target); }
  // keycode
  if (xDef(e.keyCode)) { this.keyCode = e.keyCode; }
  else if (xDef(e.which)) { this.keyCode = e.which; }
}
function xResizeEvent() { // window resize event simulation
  if (window.xREL) setTimeout("xResizeEvent()", 250);
  var cw = xClientWidth(), ch = xClientHeight();
  if (window.xPCW != cw || window.xPCH != ch) { window.xPCW = cw; window.xPCH = ch; if (window.xREL) window.xREL(); }
}
function xScrollEvent() { // window scroll event simulation
  if (window.xSEL) setTimeout("xScrollEvent()", 250);
  var sl = xScrollLeft(), st = xScrollTop();
  if (window.xPSL != sl || window.xPST != st) { window.xPSL = sl; window.xPST = st; if (window.xSEL) window.xSEL(); }
}

// Object:
function xGetElementById(e) {
  if(typeof(e)!="string") return e;
  if(document.getElementById) e=document.getElementById(e);
  else if(document.all) e=document.all[e];
  else if(document.layers) e=xLayer(e);
  else e=null;
  return e;
}
function xLayer(id,root) { // only for nn4
  var i,layer,found=null;
  if (!root) root=window;
  for(i=0; i<root.document.layers.length; i++) {
    layer=root.document.layers[i];
    if(layer.id==id) return layer;
    if(layer.document.layers.length) found=xLayer(id,layer);
    if(found) return found;
  }
  return null;
}
function xLayerFromPoint(x,y,root) { // only for nn4
  var i, hn=null, hz=-1, cn;
  if (!root) root = window;
  for (i=0; i < root.document.layers.length; ++i) {
    cn = root.document.layers[i];
    if (cn.visibility != "hide" && x >= cn.pageX && x <= cn.pageX + cn.clip.right && y >= cn.pageY && y <= cn.pageY + cn.clip.bottom ) {
      if (cn.zIndex > hz) { hz = cn.zIndex; hn = cn; }
    }
  }
  if (hn) {
    cn = xLayerFromPoint(x,y,hn);
    if (cn) hn = cn;
  }
  return hn;
}
function xParent(e){
  if (!(e=xGetElementById(e))) return null;
  var p=null;
  if (e.parentLayer){if (e.parentLayer!=window) p=e.parentLayer;}
  else{
    if (e.offsetParent) p=e.offsetParent;
    else if (e.parentNode) p=e.parentNode;
    else if (e.parentElement) p=e.parentElement;
  }
  return p;
}
function xDef() {
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])=="" || typeof(arguments[i])=="undefined") return false;}
  return true;
}

// Window:
function xScrollLeft() {
  var offset=0;
  if(xDef(window.pageXOffset)) offset=window.pageXOffset;
  else if(document.documentElement && document.documentElement.scrollLeft) offset=document.documentElement.scrollLeft;
  else if(document.body && xDef(document.body.scrollLeft)) offset=document.body.scrollLeft;
  return offset;
}
function xScrollTop() {
  var offset=0;
  if(xDef(window.pageYOffset)) offset=window.pageYOffset;
  else if(document.documentElement && document.documentElement.scrollTop) offset=document.documentElement.scrollTop;
  else if(document.body && xDef(document.body.scrollTop)) offset=document.body.scrollTop;
  return offset;
}
function xClientWidth() {
  var w=0;
  if(xOp5or6) w=window.innerWidth;
  else if(xIE4Up && document.documentElement && document.documentElement.clientWidth)
    w=document.documentElement.clientWidth;
  else if(document.body && document.body.clientWidth)
    w=document.body.clientWidth;
  else if(xDef(window.innerWidth,window.innerHeight,document.height)) {
    w=window.innerWidth;
    if(document.height>window.innerHeight) w-=16;
  }
  return w;
}
function xClientHeight() {
  var h=0;
  if(xOp5or6) h=window.innerHeight;
  else if(xIE4Up && document.documentElement && document.documentElement.clientHeight)
    h=document.documentElement.clientHeight;
  else if(document.body && document.body.clientHeight)
    h=document.body.clientHeight;
  else if(xDef(window.innerWidth,window.innerHeight,document.width)) {
    h=window.innerHeight;
    if(document.width>window.innerWidth) h-=16;
  }
  return h;
}

// end x.js