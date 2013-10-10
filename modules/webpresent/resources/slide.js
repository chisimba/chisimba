/*
 * (c) Copyright 2006, Klika, all rights reserved.
 *
 * This code is the property of Klika d.o.o. The code 
 * may not be included in, invoked from, or otherwise 
 * used in any software, service, device, or process 
 * which is sold, exchanged for profit, or for which 
 * a license, subscription, or royalty fee is charged.
 *
 * Permission is granted to use this code for personal,
 * educational, research, or commercial purposes, provided 
 * this notice is included, and provided this code is not 
 * used as described in the above paragraph.
 *
 * This code may not be modified without express
 * permission of Klika. You may not delete, disable, or in
 * any manner alter distinctive brand features rendered
 * by the code. The use of this code in derivative work is
 * permitted, provided that the code and this notice are 
 * included in full, and provided that the code is used in
 * accordance with these terms.
 *
 * Email: info at triptracker.net
 * Web:   http://slideshow.triptracker.net
 */

var MESSAGES = {
  "format.date":                     "MM/dd/yyyy",
  "format.time":                     "h:mm a",

  "photoviewer.toolbar.first":       "Go to Start (Home)",
  "photoviewer.toolbar.prev":        "Previous Photo (Left arrow)",
  "photoviewer.toolbar.slideShow":   "Start/Pause Slide Show (Space)",
  "photoviewer.toolbar.next":        "Next Photo (Right arrow)",
  "photoviewer.toolbar.last":        "Go to End (End)",
  "photoviewer.toolbar.email":       "Email Photo",
  "photoviewer.toolbar.permalink":   "Link to Photo",
  "photoviewer.toolbar.close":       "Close (Esc)",
  "photoviewer.email.subject.photo": "Photo",

  "gallery.nophotos":                "No photos",
  "gallery.thumbs.start":            "Start",
  "gallery.thumbs.end":              "End",
  "gallery.toolbar.first":           "First Photo",
  "gallery.toolbar.prev":            "Previous Photo",
  "gallery.toolbar.view":            "View Photo",
  "gallery.toolbar.next":            "Next Photo",
  "gallery.toolbar.last":            "Last Photo",
  "gallery.view.full":               "Maximize Window",
  "gallery.view.photo":              "Show Photo Only",
  "gallery.view.text":               "Show Description Only",
  "gallery.view.close":              "Close Window"
};

var agent=navigator.userAgent.toLowerCase();var IE=(agent.indexOf("msie")!=-1&&agent.indexOf("opera")==-1);var IE7=(agent.indexOf("msie 7")!=-1);var OPERA=(agent.indexOf("opera")!=-1);var SAFARI=(agent.indexOf("safari")!=-1);var FIREFOX=(agent.indexOf("gecko")!=-1);var STRICT_MODE=(document.compatMode=="CSS1Compat");var _DOMAIN=undefined;var GALLERY_W=650;var GALLERY_H=530;if(USE_GOOGLE_MAPS==undefined){var USE_GOOGLE_MAPS=true;}
var USE_OLD_MAPS=!USE_GOOGLE_MAPS;var TESTING=false;var log=getLogger();if(document.location.href.indexOf("#jslog")!=-1)
log.enable();function Logger(){this.enable=loggerEnable;this.clear=loggerClear;this.log=loggerLog;this.debug=loggerDebug;this.info=loggerInfo;this.error=loggerError;var console=undefined;try{console=document.createElement("textarea");console.style.display="none";console.style.position="absolute";console.style.right="2px";console.style.bottom="2px";console.style.width="23em";console.style.height="40em";console.style.fontFamily="monospace";console.style.fontSize="9px";console.style.color="#000000";setOpacity(console,0.7);console.border="1px solid #808080";console.ondblclick=clearLogger;}catch(e){}
this.console=console;this.enabled=false;this.logTimeStart=getTimeMillis();}
function getLogger(){var log=undefined;var win=window;while(log==undefined){try{log=win.document.log;}catch(e){break;}
if(win==win.parent)
break;win=win.parent;}
if(log==undefined){log=new Logger();document.log=log;}
return log;}
function clearLogger(){getLogger().clear();}
function loggerEnable(){if(this.enabled||this.console==undefined)
return;if(window.document.body!=undefined){window.document.body.appendChild(this.console);this.console.style.display="";this.enabled=true;}}
function loggerDebug(msg){this.log("DEBUG",msg);}
function loggerInfo(msg){this.log("INFO",msg);}
function loggerError(msg,e){this.log("ERROR",msg,e);}
function loggerLog(level,msg,e){if(!this.enabled||this.console==undefined)
return;var millis=(getTimeMillis()-this.logTimeStart)+"";while(millis.length<6)
millis+=" ";var m=millis+" ";if(msg!=undefined)
m+=msg+" ";if(e!=undefined)
m+=e.name+": "+e.message;this.console.value+=m+"\n";}
function loggerClear(){if(!this.enabled||this.console==undefined)
return;this.console.value="";}
function getTimeMillis(){var t=new Date();return Date.UTC(t.getFullYear(),t.getMonth(),t.getDay(),t.getHours(),t.getMinutes(),t.getSeconds(),t.getMilliseconds());}
function getEvent(event){return(event!=undefined?event:window.event);}
function preventDefault(event){if(event.preventDefault){event.preventDefault();event.stopPropagation();}else{event.returnValue=false;event.cancelBubble=true;}}
function getEventTarget(event){if(event==undefined)
return undefined;if(event.srcElement!=undefined)
return event.srcElement;else
return event.target;}
function getResponse(url,async,getXML,callback,data){var req=undefined;try{req=new ActiveXObject("Msxml2.XMLHTTP");}catch(e1){try{req=new ActiveXObject("Microsoft.XMLHTTP");}catch(e2){req=new XMLHttpRequest();}}
if(req==undefined){log.error("Failed to initialize XML/HTTP");return undefined;}
req.open("GET",url,async);if(!async){req.send(undefined);if(req.readyState!=4){log.error("Request failed: "+req.readyState);return undefined;}
if(!getXML)
return req.responseText;else
return req.responseXML;}else{pollResponse(req,callback,data);req.send(undefined);return undefined;}}
function pollResponse(req,callback,data){if(req.readyState!=4)
window.setTimeout(function(){pollResponse(req,callback,data);},100);else
callback(req,data);}
function getElementsByTagName(node,tag){if(node==undefined)
return undefined;if(IE){return node.getElementsByTagName(tag);}
if(tag.indexOf(":")!=-1){tag=tag.split(":")[1];}
return node.getElementsByTagNameNS("*",tag);}
function getFirstElementsValue(node,tag){if(node==undefined)
return undefined;var nodes=getElementsByTagName(node,tag);if(nodes.length===0)
return undefined;else
return getElementValue(nodes[0]);}
function getElementValue(node){var i;var val="";for(i=0;i<node.childNodes.length;i++){if(node.childNodes[i].nodeValue!==null)
val+=node.childNodes[i].nodeValue;}
return val;}
function trim(str){if(str==undefined)
return undefined;return str.replace(/^\s*([\s\S]*\S+)\s*$|^\s*$/,'$1');}
function trimToLen(str,len){if(str==undefined){return undefined;}
if(str.length>len){str=str.substring(0,len)+"...";}
return str;}
function getRootWindow(){var win=window;while(win!=undefined){try{if(win===win.parent){break;}else if(win.parent!=undefined&&win.parent.document.location.href.indexOf("/selenium-server/")!=-1){break;}
win=win.parent;}catch(e){win.permissionDenied=true;break;}}
return win;}
function getURLParams(){var i,params=[];var url=window.location.search;if(url==undefined||url.length===0)
return undefined;url=url.substring(1);var namevals=url.replace(/\+/g," ").split("&");for(i=0;i<namevals.length;i++){var name,val;var pos=namevals[i].indexOf("=");if(pos!=-1){name=namevals[i].substring(0,pos);val=unescape(namevals[i].substring(pos+1));}else{name=namevals[i];val=undefined;}
params[name]=val;}
return params;}
function joinLists(list1,list2){var i;var size=0;var result=[];if(list1!=undefined&&list1.length>0){for(i=0;i<list1.length;i++)
result[i]=list1[i];size=list1.length;}
if(list2!=undefined&&list2.length>0){for(i=0;i<list2.length;i++)
result[i+size]=list2[i];}
return result;}
function setCookie(name,value,expire){var expiry=(expire==undefined)?"":("; expires="+expire.toGMTString());document.cookie=name+"="+value+expiry;}
function getCookie(name){if(document.cookie==undefined||document.cookie.length===0)
return undefined;var search=name+"=";var index=document.cookie.indexOf(search);if(index!=-1){index+=search.length;var end=document.cookie.indexOf(";",index);if(end==-1)
end=document.cookie.length;return unescape(document.cookie.substring(index,end));}}
function removeCookie(name){var today=new Date();var expires=new Date();expires.setTime(today.getTime()-1);setCookie(name,"",expires);}
function getMessage(id){if(MESSAGES[id]==undefined){return"("+id+")";}else{return MESSAGES[id];}}
function localizeNodeAttribs(node){var i;if(node==undefined)
return;if(node.alt!=undefined&&node.alt.indexOf("#")===0){node.alt=getMessage(node.alt.substring(1));}
if(node.title!=undefined&&node.title.indexOf("#")===0){node.title=getMessage(node.title.substring(1));}
if(node.childNodes!=undefined){for(i=0;i<node.childNodes.length;i++){localizeNodeAttribs(node.childNodes[i]);}}}
function padNumber(n,pad){n=n+"";while(n.length<pad){n="0"+n;}
return n;}
function isArray(obj){if(obj instanceof Array)
return true;else
return false;}
function simpleDateFormatter(date,pattern){var d=pattern;d=d.replace(/yyyy/g,date.getFullYear());d=d.replace(/yy/g,padNumber(date.getFullYear()%100,2));d=d.replace(/MM/g,padNumber(date.getMonth()+1,2));d=d.replace(/M/g,date.getMonth()+1);d=d.replace(/dd/g,padNumber(date.getDate(),2));d=d.replace(/d/g,date.getDate());d=d.replace(/HH/g,padNumber(date.getHours(),2));d=d.replace(/H/g,date.getHours());d=d.replace(/hh/g,padNumber(date.getHours()%12,2));d=d.replace(/h/g,date.getHours()%12);d=d.replace(/mm/g,padNumber(date.getMinutes(),2));d=d.replace(/m/g,date.getMinutes());d=d.replace(/ss/g,padNumber(date.getSeconds(),2));d=d.replace(/s/g,date.getSeconds());var am=(date.getHours()<12?"AM":"PM");d=d.replace(/a/g,am);return d;}
function formatDateTime(date){if(date==undefined)
return undefined;return formatDate(date)+" "+formatTime(date);}
function formatDate(date){var datePattern=getMessage("format.date");return simpleDateFormatter(date,datePattern);}
function formatTime(date){var timePattern=getMessage("format.time");return simpleDateFormatter(date,timePattern);}
function parseISOTime(strTime){if(strTime==undefined)
return undefined;var isoRE=/^(\d{4})-(\d\d)-(\d\d)T(\d\d):(\d\d):(\d\d)(\.\d{3})?([Z+-])?(\d\d)?:?(\d\d)?$/;if(!isoRE.test(strTime)){return undefined;}else{return new Date(RegExp.$1,RegExp.$2-1,RegExp.$3,RegExp.$4,RegExp.$5,RegExp.$6);}}
function setOpacity(elt,opacity){if(IE){elt.style.filter="alpha(opacity="+parseInt(opacity*100)+")";}
elt.style.KhtmlOpacity=opacity;elt.style.opacity=opacity;}
function validCoordinates(lat,lon){if(Math.abs(lat)>90||Math.abs(lon)>180){return false;}
if(lat===0.0&&lon===0.0){return false;}
return true;}
function isHosted(){var host=document.location.host;if(host==undefined)
host="";return((host.indexOf("triptracker.net")==-1||host.indexOf("slideshow.triptracker.net")!=-1)&&host.indexOf("rtvslo.si")==-1&&!checkDomain());}
function checkDomain(){try{if(_DOMAIN==undefined)
return false;var protocol=document.location.protocol;if(protocol==undefined)
protocol="http:";var host=document.location.host;if(host==undefined)
host="";host=host.toLowerCase();if(protocol.toLowerCase().indexOf("file")===0){return true;}
var pos=host.lastIndexOf(":");if(pos!=-1){host=host.substring(0,pos);}
if(host.indexOf("www.")===0){host=host.substring(4);}
if(host===""||host=="localhost"||host=="127.0.0.1")
return true;var domain=_DOMAIN.toLowerCase();pos=domain.indexOf("://");if(pos!=-1){domain=domain.substring(pos+3);}
pos=domain.indexOf("/");if(pos!=-1){domain=domain.substring(0,pos);}
if(domain.indexOf("www.")===0){domain=domain.substring(4);}
if(host==domain){return true;}else if(new RegExp(domain).test(host)){return true;}
return false;}catch(e){return true;}}
function getWindowSize(win){var availW=win.innerWidth;if(availW==undefined||availW===0||isNaN(availW))
availW=win.document.documentElement.clientWidth;if(availW==undefined||availW===0||isNaN(availW))
availW=win.document.body.clientWidth;var availH=win.innerHeight;if(availH==undefined||availH===0||isNaN(availH))
availH=win.document.documentElement.clientHeight;if(availH==undefined||availH===0||isNaN(availH))
availH=win.document.body.clientHeight;return{w:availW,h:availH};}
function getDocumentSize(win){var winSize=getWindowSize(win);var scrollPos=getScrollPos(win);var w=winSize.w+scrollPos.left;var h=winSize.h+scrollPos.top;w=Math.max(w,win.document.body.offsetWidth);h=Math.max(h,win.document.body.offsetHeight);w=Math.max(w,win.document.body.scrollWidth);h=Math.max(h,win.document.body.scrollHeight);return{w:w,h:h};}
function getScrollPos(win){var scrollTop=win.pageYOffset;if(scrollTop==undefined||scrollTop===0)
scrollTop=win.document.documentElement.scrollTop;if(scrollTop==undefined||scrollTop===0)
scrollTop=win.document.body.scrollTop;var scrollLeft=win.pageXOffset;if(scrollLeft==undefined||scrollLeft===0)
scrollLeft=win.document.documentElement.scrollLeft;if(scrollLeft==undefined||scrollLeft===0)
scrollLeft=win.document.body.scrollLeft;return{top:scrollTop,left:scrollLeft};}
var CLEAR_EVENTS=["onclick","ondblclick","onkeydown","onkeypress","onmousedown","onmouseup","onmousemove","onmouseover","onmouseout","onmousewheeldown","oncontextmenu"];function clearEvents(){var i,j;var count=0;if(document.all==undefined)
return;for(i=0;i<document.all.length;i++){for(j=0;j<CLEAR_EVENTS.length;j++){var event=document.all[i][CLEAR_EVENTS[j]];if(event!=undefined){document.all[i][CLEAR_EVENTS[j]]=null;count++;}}}}
if(window.attachEvent)
window.attachEvent("onunload",clearEvents);function getGallery(){var gallery=undefined;var win=window;while(gallery==undefined){try{gallery=win.document.gallery;}catch(e){break;}
var tmpWin=win;win=win.parent;if(tmpWin===win){break;}}
return gallery;}
function viewerCloseCallback(photoIndex){var i,j,n=0;var gallery=getGallery();for(i=0;i<gallery.sets.length;i++){for(j=0;j<gallery.sets[i].photos.length;j++){var p=gallery.sets[i].photos[j];if(p==undefined||p.orig==undefined||p.orig.src==undefined)
continue;if(n==photoIndex){gallery.setIndex=i;gallery.photoIndex=j;gallery.renderPhotos();gallery.win.focus();return;}
n++;}}}
var VIEWER_INDEX=0;var SLIDE_DURATION=4000;var SLIDE_OFFSET=50;var SLIDE_PHOTOS=true;var FADE_BORDER=false;var FADE_STEPS=10;var MOVE_STEP=1;var PRELOAD_TIMEOUT=60000;var BORDER_WIDTH=5;var FONT_SIZE=10;var OFFSET_LEFT=0;var OFFSET_TOP=0;var REST_URL="/rest/";var P_IMG_ROOT="http://static.triptracker.net/jsmap/images/photoviewer";var TOOLBAR_IMG="toolbar.png";var TOOLBAR_IMG_RUNNING="toolbar2.png";var TOOLBAR_IMG_BACK="toolbar-back";var TOOLBAR_IMG_MASK="toolbar-mask.png";var TOOLBAR_IMG_LOADING="loading-anim.gif";var TOOLBAR_W=440;var TOOLBAR_H=75;var TOOLBAR_IMG_W=420;var TOOLBAR_IMG_H=44;var TOOLBAR_LINK="http://slideshow.triptracker.net";var TOOLBAR_FONT_COLOR="#c0c0c0";var TOOLBAR_FONT_STYLE="tahoma, verdana, arial, helvetica, sans-serif";var VIEWER_ID_PREFIX="PhotoViewer";var VIEWER_ID_BACK=VIEWER_ID_PREFIX+"Back";var VIEWER_ID_TOOLBAR=VIEWER_ID_PREFIX+"Toolbar";var VIEWER_ID_TOOLBAR_MAP=VIEWER_ID_PREFIX+"ToolbarMap";var VIEWER_ID_TOOLBAR_IMG=VIEWER_ID_PREFIX+"ToolbarImg";var VIEWER_ID_LOADING=VIEWER_ID_PREFIX+"Loading";var VIEWER_ID_TIME=VIEWER_ID_PREFIX+"Time";var VIEWER_ID_TITLE=VIEWER_ID_PREFIX+"Title";var VIEWER_ID_BYLINE=VIEWER_ID_PREFIX+"Byline";var TITLE_MAX_LENGTH=140;function PhotoViewer(win,handleKeys){this.setImageRoot=setImageRoot;this.add=addPhoto;this.show=showPhoto;this.close=closePhoto;this.isShown=isPhotoShown;this.setBackground=setPhotoBackground;this.setShowToolbar=setShowToolbar;this.setToolbarImage=setToolbarImage;this.setShowCallback=setShowCallback;this.setCloseCallback=setCloseCallback;this.setEndCallback=setEndCallback;this.setLoading=setPhotoLoading;this.addBackShade=addBackShade;this.addToolbar=addToolbar;this.addCaptions=addCaptions;this.next=nextPhoto;this.prev=prevPhoto;this.first=firstPhoto;this.last=lastPhoto;this.slideShow=slideShow;this.slideShowStop=slideShowStop;this.startSlideShow=startSlideShow;this.handleKey=viewerHandleKey;this.checkStartFragmentIdentifier=checkStartFragmentIdentifier;this.checkStopFragmentIdentifier=checkStopFragmentIdentifier;this.setStartFragmentIdentifier=setStartFragmentIdentifier;this.setStopFragmentIdentifier=setStopFragmentIdentifier;this.email=emailPhoto;this.favorite=favoritePhoto;this.permalink=linkPhoto;this.setBackgroundColor=setBackgroundColor;this.setBorderWidth=setBorderWidth;this.setSlideDuration=setSlideDuration;this.disablePanning=disablePanning;this.enablePanning=enablePanning;this.disableFading=disableFading;this.enableFading=enableFading;this.disableShade=disableShade;this.enableShade=enableShade;this.setShadeColor=setShadeColor;this.setShadeOpacity=setShadeOpacity;this.setFontSize=setFontSize;this.setFont=setFont;this.enableAutoPlay=enableAutoPlay;this.disableAutoPlay=disableAutoPlay;this.enableEmailLink=enableEmailLink;this.disableEmailLink=disableEmailLink;this.enablePhotoLink=enablePhotoLink;this.disablePhotoLink=disablePhotoLink;this.setOnClickEvent=setOnClickEvent;this.enableLoop=enableLoop;this.disableLoop=disableLoop;this.hideOverlappingElements=hideOverlappingElements;this.showOverlappingElements=showOverlappingElements;this.id=VIEWER_ID_PREFIX+VIEWER_INDEX;VIEWER_INDEX++;this.photos=[];this.index=0;this.win=(win!=undefined?win:window);this.shown=false;this.showToolbar=true;this.backgroundColor="#000000";this.shadeColor="#000000";this.shadeOpacity=0.7;this.borderColor="#000000";this.shadeColor="#000000";this.shadeOpacity=0.7;this.borderWidth=BORDER_WIDTH;this.backgroundShade=true;this.fadePhotos=true;this.autoPlay=false;this.enableEmailLink=true;this.enablePhotoLink=true;this.slideDuration=SLIDE_DURATION;this.panPhotos=SLIDE_PHOTOS;this.fontSize=FONT_SIZE;this.font=undefined;if(handleKeys==undefined||handleKeys){if(this.win.addEventListener){this.win.addEventListener("keydown",viewerHandleKey,false);}else{this.win.document.attachEvent("onkeydown",viewerHandleKey);}}
this.win.document.viewer=this;if(OPERA)
this.disableFading();}
function PhotoImg(id,src,w,h,time,title,byline){this.id=id;this.src=src;this.w=parseInt(w);this.h=parseInt(h);this.time=time;this.title=title;this.byline=byline;}
function getViewer(){var viewer=undefined;var win=window;while(viewer==undefined){try{viewer=win.document.viewer;}catch(e){break;}
if(win===win.parent){break;}
win=win.parent;}
return viewer;}
function setImageRoot(root){P_IMG_ROOT=root;}
function addPhoto(photo,title,time,byline){var type=typeof photo;if(typeof photo=="string"){photo=new PhotoImg(undefined,photo,undefined,undefined,time,title,byline);}
this.photos.push(photo);}
function setPhotoBackground(color,border,doShade){if(color!=undefined)
this.backgroundColor=color;if(border!=undefined)
this.borderColor=border;if(doShade!=undefined)
this.backgroundShade=doShade;}
function setPhotoLoading(isLoading){this.isLoading=isLoading;var elt=this.win.document.getElementById(VIEWER_ID_LOADING);if(elt==undefined)
return;elt.style.display=isLoading?"":"none";}
function setBackgroundColor(color){this.backgroundColor=color;this.borderColor=color;}
function setBorderWidth(width){this.borderWidth=width;}
function setSlideDuration(duration){this.slideDuration=duration;}
function disableShade(){this.backgroundShade=false;}
function enableShade(){this.backgroundShade=true;}
function setShadeColor(color){this.shadeColor=color;}
function setShadeOpacity(opacity){this.shadeOpacity=opacity;}
function disableFading(){this.fadePhotos=false;}
function enableFading(){this.fadePhotos=true;}
function disablePanning(){this.panPhotos=false;}
function enablePanning(){this.panPhotos=true;}
function setFontSize(size){this.fontSize=size;}
function setFont(font){this.font=font;}
function enableAutoPlay(){this.autoPlay=true;}
function disableAutoPlay(){this.autoPlay=false;}
function enableEmailLink(){this.enableEmailLink=true;}
function disableEmailLink(){this.enableEmailLink=false;}
function enablePhotoLink(){this.enablePhotoLink=true;}
function disablePhotoLink(){this.enablePhotoLink=false;}
function setOnClickEvent(newfunc){this.customOnClickEvent=newfunc;}
function enableLoop(){this.loop=true;}
function disableLoop(){this.loop=false;}
function showPhoto(index,cropWidth,opacity){if(this.photos.length===0){return true;}
if(getRootWindow().permissionDenied&&this.badgeMode==undefined){this.setStartFragmentIdentifier(index);return true;}
if(index!=undefined)
this.index=index;if(this.index<0||this.index>=this.photos.length){log.error("Invalid photo index");return true;}
var doc=this.win.document;var firstShow=false;if(!this.shown){firstShow=true;doc.viewer=this;try{this.hideOverlappingElements();}catch(e){}}
var zIndex=16384;var winSize=getWindowSize(this.win);var availW=winSize.w-20;var availH=winSize.h-20;var scrollPos=getScrollPos(this.win);var scrollLeft=scrollPos.left;var scrollTop=scrollPos.top;this.addBackShade(zIndex);if(this.showToolbar){this.addToolbar(availW,zIndex);this.addCaptions();}
var photo=this.photos[this.index];if(isNaN(photo.w)||isNaN(photo.h)){if(photo.preloadImage!=undefined){if(isNaN(photo.w)&&photo.preloadImage.width>0)
photo.w=photo.preloadImage.width;if(isNaN(photo.h)&&photo.preloadImage.height>0)
photo.h=photo.preloadImage.height;}else{this.index--;this.next();return false;}}
this.shown=true;var offset=20;var pw=-1;var ph=-1;if(parseInt(photo.w)>availW||parseInt(photo.h)>availH){if(parseInt(photo.w)/availW>parseInt(photo.h)/availH){pw=availW-offset;ph=parseInt(pw*photo.h/photo.w);}else{ph=availH-offset;pw=parseInt(ph*photo.w/photo.h);}}else{pw=parseInt(photo.w);ph=parseInt(photo.h);}
if(pw<=0||ph<=0){if(!this.showToolbar)
throw"Missing photo dimension";}
if(cropWidth==undefined)
cropWidth=0;var photoDiv=doc.createElement("div");photoDiv.style.visibility="hidden";photoDiv.style.position="absolute";photoDiv.style.zIndex=zIndex;photoDiv.style.overflow="hidden";photoDiv.style.border=this.borderWidth+"px solid "+this.borderColor;photoDiv.style.textAlign="center";photoDiv.style.backgroundColor=this.backgroundColor;var photoElt=doc.createElement("img");photoElt.style.visibility="hidden";photoElt.style.position="relative";photoElt.style.backgroundColor=this.backgroundColor;photoElt.style.border="none";photoElt.style.cursor="pointer";photoElt.style.zIndex=(parseInt(photoDiv.style.zIndex)+1)+"";photoElt.onclick=onClickEvent;if(opacity!=undefined&&this.fadePhotos){var fadeElt=(FADE_BORDER?photoDiv:photoElt);setOpacity(fadeElt,opacity);}
var left=parseInt((availW-pw)/2)+OFFSET_LEFT;photoDiv.style.left=(left+scrollLeft+cropWidth/2)+"px";var top=parseInt((availH-ph)/2)+OFFSET_TOP;photoDiv.style.top=(top+scrollTop)+"px";photoElt.style.visibility="hidden";photoDiv.style.width=(pw-cropWidth)+"px";photoDiv.style.height=ph+"px";photoElt.style.width=pw+"px";photoElt.style.height=ph+"px";photoElt.src=photo.src;photoDiv.style.visibility="visible";photoElt.style.visibility="visible";photoDiv.appendChild(photoElt);doc.body.appendChild(photoDiv);if(this.photoDiv!=undefined){try{doc.body.removeChild(this.photoDiv);}catch(e){}}
this.photoDiv=photoDiv;this.photoImg=photoElt;this.setLoading(false);if(this.showCallback!=undefined)
this.showCallback(this.index);if(firstShow&&this.autoPlay){this.slideShow(true);}
return false;}
function isPhotoShown(){return this.shown;}
function closeViewer(){getViewer().close();}
function onPhotoLoad(event){var viewer=getViewer();if(viewer!=undefined){if(flickrHack(viewer,viewer.index)){viewer.setLoading(false);viewer.index--;viewer.next();return;}
viewer.show();}}
function closePhoto(){var win=this.win;if(win==undefined)
win=window;var doc=win.document;var elt=this.photoDiv;if(elt!=undefined)
doc.body.removeChild(elt);elt=doc.getElementById(VIEWER_ID_BACK);if(elt!=undefined)
doc.body.removeChild(elt);elt=doc.getElementById(VIEWER_ID_TOOLBAR);if(elt!=undefined)
doc.body.removeChild(elt);this.shown=false;this.slideShowRunning=false;this.slideShowPaused=false;try{this.showOverlappingElements();}catch(e){}
if(this.closeCallback!=undefined)
this.closeCallback(this.index);}
function nextPhoto(n){if(this.isLoading)
return;if(n==undefined)
n=1;var oldIndex=this.index;if(this.index+n>=this.photos.length){if(this.loop&&n!=this.photos.length){this.index=0;}else{this.index=this.photos.length-1;}}else if(this.index+n<0){if(n<-1)
this.index=0;else if(this.loop)
this.index=this.photos.length-1;else
return;}else{this.index+=n;}
if(this.index==oldIndex)
return;this.slideShowStop();var img=new Image();this.photos[this.index].preloadImage=img;this.setLoading(true);img.onload=onPhotoLoad;img.onerror=onPhotoLoad;if(this.photos[this.index].src!=undefined){img.src=this.photos[this.index].src;}else{onPhotoLoad();}}
function prevPhoto(n){if(n==undefined)
n=1;this.next(-n);}
function firstPhoto(){this.prev(this.photos.length);}
function lastPhoto(){this.next(this.photos.length);}
function startSlideShow(){getViewer().slideShow(true);}
var slideTimeout;var slidePreloadImageLoaded=false;var slidePreloadTime=undefined;function slideShow(start){var nextIndex=this.index+1;if(nextIndex>=this.photos.length){if(this.loop)
nextIndex=0;else if(!this.slideShowPaused&&!this.slideShowRunning){this.setToolbarImage(P_IMG_ROOT+"/"+TOOLBAR_IMG);return;}}
var doc=this.win.document;var viewer=this;var photoElt=this.photoImg;if(photoElt==undefined)
return;var photoDiv=this.photoDiv;var fadeElt=(FADE_BORDER?photoDiv:photoElt);if(start!=undefined&&start===true){if(this.slideShowPaused){this.slideShowPaused=false;this.setToolbarImage(P_IMG_ROOT+"/"+TOOLBAR_IMG_RUNNING);return;}else if(this.slideShowRunning){this.slideShowPaused=true;this.setToolbarImage(P_IMG_ROOT+"/"+TOOLBAR_IMG);return;}else{this.slideShowRunning=true;this.slideShowPaused=false;this.slideFirstPhoto=true;this.setToolbarImage(P_IMG_ROOT+"/"+TOOLBAR_IMG_RUNNING);}
if(this.isLoading||this.index>=this.photos.length-1){return;}}else if(this.slideShowPaused){window.setTimeout(function(){viewer.slideShow(false);},200);return;}else if(!this.slideShowRunning){this.setToolbarImage(P_IMG_ROOT+"/"+TOOLBAR_IMG);return;}
var left=0;if(photoElt.leftOffset!=undefined){left=parseFloat(photoElt.leftOffset);}
if(left===0){if(nextIndex<this.photos.length){slidePreloadImageLoaded=false;var slidePreloadImage=new Image();this.photos[nextIndex].preloadImage=slidePreloadImage;slidePreloadTime=getTimeMillis();slidePreloadImage.onload=onSlideLoad;slidePreloadImage.onerror=onSlideLoad;slidePreloadImage.src=this.photos[nextIndex].src;}}
if(left>-SLIDE_OFFSET){left-=MOVE_STEP;if(-left<=FADE_STEPS){if(fadeElt.style.opacity!=undefined&&parseFloat(fadeElt.style.opacity)<1){if(this.fadePhotos&&this.photos[this.index].src!=undefined)
setOpacity(fadeElt,-left/FADE_STEPS);}}else if(left+SLIDE_OFFSET<FADE_STEPS){if(nextIndex<this.photos.length&&!slidePreloadImageLoaded){if(slidePreloadTime!=undefined&&getTimeMillis()-slidePreloadTime>PRELOAD_TIMEOUT)
slidePreloadImageLoaded=true;left++;this.setLoading(true);}else{if(nextIndex<this.photos.length&&this.fadePhotos&&this.photos[this.index].src!=undefined)
setOpacity(fadeElt,(left+SLIDE_OFFSET)/FADE_STEPS);}}
photoElt.leftOffset=left;if(this.panPhotos&&!this.slideFirstPhoto){photoElt.style.left=left+"px";}}else{if(nextIndex>=this.photos.length){this.slideShowRunning=false;this.slideShowPaused=false;this.setToolbarImage(P_IMG_ROOT+"/"+TOOLBAR_IMG);if(this.endCallback!=undefined)
this.endCallback();return;}
this.index=nextIndex;this.slideFirstPhoto=false;this.show(undefined,(this.panPhotos?SLIDE_OFFSET:0),0);fadeElt=(FADE_BORDER?this.photoDiv:this.photoImg);if(this.fadePhotos)
setOpacity(fadeElt,0);this.photoImg.leftOffset=0;if(this.panPhotos)
this.photoImg.style.left="0px";}
var pause=this.slideDuration/SLIDE_OFFSET;if(this.slideFirstPhoto){pause/=2;}
slideTimeout=window.setTimeout(function(){viewer.slideShow(false);},pause);}
function onSlideLoad(event){var viewer=getViewer();if(viewer!=undefined){if(flickrHack(viewer,viewer.index+1)){var slidePreloadImage=viewer.photos[viewer.index+1].preloadImage;slidePreloadImage.src=viewer.photos[viewer.index+1].src;slidePreloadTime=getTimeMillis();return;}
slidePreloadImageLoaded=true;viewer.setLoading(false);}}
function slideShowStop(){this.slideShowRunning=false;this.slideShowPaused=false;var doc=this.win.document;var photoElt=this.photoImg;if(photoElt!=undefined){if(this.fadePhotos){var fadeElt=(FADE_BORDER?this.photoDiv:photoElt);setOpacity(fadeElt,1);}
photoElt.style.left="0px";}}
function addBackShade(zIndex){var doc=this.win.document;if(doc.getElementById(VIEWER_ID_BACK)!=undefined){return;}
var photoBack=doc.createElement("div");photoBack.id=VIEWER_ID_BACK;photoBack.style.top="0px";photoBack.style.left="0px";photoBack.style.bottom="0px";photoBack.style.right="0px";photoBack.style.margin="0";photoBack.style.padding="0";photoBack.style.border="none";photoBack.style.cursor="pointer";if(IE&&!(IE7&&STRICT_MODE)){photoBack.style.position="absolute";var docSize=getDocumentSize(this.win);photoBack.style.width=(docSize.w-21)+"px";photoBack.style.height=(docSize.h-4)+"px";}else{photoBack.style.position="fixed";photoBack.style.width="100%";photoBack.style.height="100%";}
photoBack.style.zIndex=zIndex-1;photoBack.style.backgroundColor=this.shadeColor;if(this.backgroundShade)
setOpacity(photoBack,this.shadeOpacity);else
setOpacity(photoBack,0.0);photoBack.onclick=onClickEvent;doc.body.appendChild(photoBack);}
function addToolbar(availW,zIndex){var doc=this.win.document;var i;if(doc.getElementById(VIEWER_ID_TOOLBAR)!=undefined)
return;var photoToolbar=doc.createElement("div");photoToolbar.id=VIEWER_ID_TOOLBAR;var bottom=10;if(IE&&!(IE7&&STRICT_MODE)){photoToolbar.style.position="absolute";if(IE7){var top=getWindowSize(this.win).h+getScrollPos(this.win).top;photoToolbar.style.top=(top-TOOLBAR_H-10)+"px";}else{photoToolbar.style.bottom=bottom+"px";}}else{photoToolbar.style.position="fixed";photoToolbar.style.bottom=bottom+"px";}
photoToolbar.style.left=(availW-TOOLBAR_W+10)/2+"px";photoToolbar.style.width=TOOLBAR_W+"px";photoToolbar.style.height=TOOLBAR_H+"px";photoToolbar.style.textAlign="center";setOpacity(photoToolbar,0.7);photoToolbar.style.zIndex=zIndex+1;var imgBack=TOOLBAR_IMG_BACK;if(!isHosted()){imgBack+="-nologo";}
if(IE&&!IE7){imgBack+="-indexed";}
imgBack+=".png";photoToolbar.style.backgroundImage="url('"+P_IMG_ROOT+"/"+imgBack+"')";photoToolbar.style.backgroundPosition="50% 0%";photoToolbar.style.backgroundRepeat="no-repeat";photoToolbar.style.lineHeight="0.7em";var toolbarMask=undefined;if(!this.enableEmailLink){toolbarMask=doc.createElement("img");toolbarMask.style.position="absolute";toolbarMask.style.width=44;toolbarMask.style.height=44;toolbarMask.style.left="289px";toolbarMask.style.top="0px";toolbarMask.src=P_IMG_ROOT+"/"+TOOLBAR_IMG_MASK;photoToolbar.appendChild(toolbarMask);}
if(!this.enablePhotoLink){toolbarMask=doc.createElement("img");toolbarMask.style.position="absolute";toolbarMask.style.width=44;toolbarMask.style.height=44;toolbarMask.style.left="339px";toolbarMask.style.top="0px";toolbarMask.src=P_IMG_ROOT+"/"+TOOLBAR_IMG_MASK;photoToolbar.appendChild(toolbarMask);}
var imgMap=doc.createElement("map");imgMap.name=VIEWER_ID_TOOLBAR_MAP;imgMap.id=VIEWER_ID_TOOLBAR_MAP;var areas=[];areas.push(["getViewer().first()","17",getMessage("photoviewer.toolbar.first")]);areas.push(["getViewer().prev()","68",getMessage("photoviewer.toolbar.prev")]);areas.push(["getViewer().slideShow(true)","122",getMessage("photoviewer.toolbar.slideShow")]);areas.push(["getViewer().next()","175",getMessage("photoviewer.toolbar.next")]);areas.push(["getViewer().last()","227",getMessage("photoviewer.toolbar.last")]);if(this.enableEmailLink)
areas.push(["getViewer().email()","300",getMessage("photoviewer.toolbar.email")]);if(this.enablePhotoLink)
areas.push(["getViewer().permalink()","350",getMessage("photoviewer.toolbar.permalink")]);areas.push(["getViewer().close()","402",getMessage("photoviewer.toolbar.close")]);for(i=0;i<areas.length;i++){var area=doc.createElement("area");area.href="javascript:void(0)";area.alt=areas[i][2];area.title=area.alt;area.shape="circle";area.coords=areas[i][1]+", 21, 22";area.onclick=buildAreaMapClosure(areas[i][0]);imgMap.appendChild(area);}
var img=doc.createElement("img");img.id=VIEWER_ID_TOOLBAR_IMG;img.src=P_IMG_ROOT+"/"+TOOLBAR_IMG;img.width=TOOLBAR_IMG_W;img.height=TOOLBAR_IMG_H;img.style.border="none";img.style.background="none";if(STRICT_MODE){img.style.margin="4px 0px 0px 0px";}else{img.style.margin="4px";}
img.useMap="#"+VIEWER_ID_TOOLBAR_MAP;photoToolbar.appendChild(imgMap);photoToolbar.appendChild(img);if(isHosted()){var ttLink=doc.createElement("a");ttLink.style.position="absolute";ttLink.style.bottom="0px";ttLink.style.right="0px";ttLink.style.width="25px";ttLink.style.height="25px";ttLink.style.background="none";ttLink.alt="TripTracker.net";ttLink.title=ttLink.alt;ttLink.cursor=ttLink.alt;ttLink.href=TOOLBAR_LINK;ttLink.target="_new";ttLink.alt="TripTracker Slideshow";ttLink.title=ttLink.alt;photoToolbar.appendChild(ttLink);}
var loadingIcon=doc.createElement("img");loadingIcon.id=VIEWER_ID_LOADING;loadingIcon.width=16;loadingIcon.height=16;loadingIcon.style.display="none";loadingIcon.style.position="absolute";loadingIcon.style.left=(273-8)+"px";loadingIcon.style.top=(24-8)+"px";loadingIcon.src=P_IMG_ROOT+"/"+TOOLBAR_IMG_LOADING;loadingIcon.style.border="none";loadingIcon.style.background="none";photoToolbar.appendChild(loadingIcon);photoToolbar.appendChild(doc.createElement("br"));var photoTime=doc.createElement("span");photoTime.id=VIEWER_ID_TIME;photoTime.position="relative";photoTime.style.color=TOOLBAR_FONT_COLOR;photoTime.style.fontFamily=TOOLBAR_FONT_STYLE;photoTime.style.fontSize=this.fontSize+"px";if(STRICT_MODE){photoTime.style.lineHeight=this.fontSize+"px";}
if(this.font!=undefined){photoTime.style.font=this.font;}
photoTime.style.cssFloat="none";photoTime.style.textAlign="right";photoTime.style.padding="0px 10px";photoTime.appendChild(doc.createTextNode(" "));photoToolbar.appendChild(photoTime);var photoTitle=doc.createElement("span");photoTitle.id=VIEWER_ID_TITLE;photoTitle.position="relative";photoTitle.style.color=TOOLBAR_FONT_COLOR;photoTitle.style.fontFamily=TOOLBAR_FONT_STYLE;photoTitle.style.fontSize=this.fontSize+"px";if(STRICT_MODE){photoTitle.style.lineHeight=this.fontSize+"px";}
if(this.font!=undefined){photoTitle.style.font=this.font;}
photoTitle.style.cssFloat="none";photoTitle.style.textAlign="left";photoTitle.style.paddingRight="20px";photoTitle.appendChild(doc.createTextNode(" "));photoToolbar.appendChild(photoTitle);doc.body.appendChild(photoToolbar);var photoByline=doc.createElement("div");photoByline.appendChild(doc.createTextNode(""));photoByline.style.color=TOOLBAR_FONT_COLOR;photoByline.style.fontFamily=TOOLBAR_FONT_STYLE;photoByline.style.fontSize=this.fontSize+"px";if(this.font!=undefined){photoByline.style.font=this.font;}
photoByline.id=VIEWER_ID_BYLINE;photoByline.style.position="absolute";photoByline.style.right="5px";photoByline.style.bottom="5px";photoByline.style.zIndex=zIndex+1;photoByline.appendChild(doc.createTextNode(" "));doc.body.appendChild(photoByline);}
function buildAreaMapClosure(func){return function(event){eval(func);blurElement(event);return false;};}
function blurElement(event){var target=getEventTarget(getEvent(event));if(target!=undefined)
target.blur();}
function setToolbarImage(img){var doc=this.win.document;var elt=doc.getElementById(VIEWER_ID_TOOLBAR_IMG);if(elt!=undefined)
elt.src=img;}
function setShowToolbar(doShow){this.showToolbar=doShow;}
function addCaptions(){var photo=this.photos[this.index];var doc=this.win.document;var photoTime=doc.getElementById(VIEWER_ID_TIME);var photoTitle=doc.getElementById(VIEWER_ID_TITLE);var photoByline=doc.getElementById(VIEWER_ID_BYLINE);var time=(this.index+1)+"/"+this.photos.length;if(photo.time!=undefined){time+=" ["+photo.time+"]";}
photoTime.firstChild.nodeValue=time;var title=(photo.title!=undefined?photo.title:"");photoTitle.title="";photoTitle.alt="";if(title.length>TITLE_MAX_LENGTH){photoTitle.title=title;photoTitle.alt=title;title=title.substring(0,TITLE_MAX_LENGTH)+" ...";}
if(title.indexOf("\n")!==0){title=title.replace("\n","<br />");photoTitle.innerHTML=title;}else{photoTitle.nodeValue=title;}
if(photo.byline!=undefined&&photo.byline.length>0){photoByline.firstChild.nodeValue=photo.byline;}else{photoByline.firstChild.nodeValue="";}}
function setCloseCallback(callback){this.closeCallback=callback;}
function setShowCallback(callback){this.showCallback=callback;}
function setEndCallback(callback){this.endCallback=callback;}
function emailPhoto(){var photo=this.photos[this.index];var doc=this.win.document;var title=(photo.title!=undefined?photo.title:getMessage("photoviewer.email.subject.photo"));var mailtoLink="mailto:?subject="+title+"&body="+
getPhotoURL(photo.src);doc.location.href=mailtoLink;}
function getPhotoURL(url){var loc=document.location;if(/\w+:\/\/.+/.test(url)){return url;}else if(url.indexOf("/")===0){return loc.protocol+"//"+loc.host+url;}else{var path=loc.pathname;var pos=path.lastIndexOf("/");if(pos!=-1){path=path.substring(0,pos);}
return loc.protocol+"//"+loc.host+path+"/"+url;}}
function linkPhoto(){var photo=this.photos[this.index];window.open(photo.src);}
function favoritePhoto(){var photo=this.photos[this.index];var doc=this.win.document;var restURL=REST_URL+"markfeatured?id"+photo.id;try{var res=getResponse(restURL,false,true);}catch(e){return;}}
function hideOverlappingElements(node){if(node==undefined){node=this.win.document.body;this.hideOverlappingElements(node);return;}
if(node.style!=undefined&&node.style.visibility!="hidden"){var nodeName=node.nodeName.toLowerCase();if((node.className!=undefined&&node.className.indexOf("SlideshowDoHide")!=-1)||(IE&&(nodeName=="select"||nodeName=="object"||nodeName=="embed"))){node.style.visibility="hidden";if(this.hiddenElements==undefined)
this.hiddenElements=[];this.hiddenElements.push(node);}}
if(node.childNodes!=undefined){var i;for(i=0;i<node.childNodes.length;i++){this.hideOverlappingElements(node.childNodes[i]);}}}
function showOverlappingElements(){var i;if(this.hiddenElements!=undefined){for(i=0;i<this.hiddenElements.length;i++){this.hiddenElements[i].style.visibility="visible";}
this.hiddenElements=[];}}
function viewerHandleKey(event){if(!getViewer)
return true;var viewer=getViewer();if(viewer==undefined||!viewer.shown)
return true;event=getEvent(event);if(event.ctrlKey||event.altKey)
return true;var keyCode=event.keyCode;switch(keyCode){case 37:case 38:viewer.prev();break;case 39:case 40:viewer.next();break;case 33:viewer.prev(10);break;case 34:viewer.next(10);break;case 36:viewer.first();break;case 35:viewer.last();break;case 32:case 13:viewer.slideShow(true);break;case 27:viewer.close();break;default:return true;}
preventDefault(event);return false;}
function flickrHack(viewer,index){if(viewer.photos[index]!=undefined){var preloadPhoto=viewer.photos[index].preloadImage;if(preloadPhoto!=undefined&&preloadPhoto.width==500&&preloadPhoto.height==375){var flickrRE=/.+static\.flickr\.com.+_b\.jpg/;if(flickrRE.test(preloadPhoto.src)){viewer.photos[index].src=viewer.photos[index].src.replace(/_b\.jpg/,"_o.jpg");return true;}}}
return false;}
function findPhotosTT(viewer,node){var i;if(node.nodeName.toLowerCase()=="a"){var onclick=node.getAttribute("onclick");if(onclick==undefined){onclick=node.onclick;}
if(onclick!=undefined&&new String(onclick).indexOf("popupImg")!=-1){var popupRE=/.*popupImg\((.+?),(.+?),(.+?)\).*/;if(popupRE.test(onclick)){var url,w,h;if(node.photoUrl!=undefined){url=node.photoUrl;w=node.photoW;h=node.photoH;}else{url=RegExp.$1;if(url.charAt(0)=="'"&&url.charAt(url.length-1)=="'")
url=url.substring(1,url.length-1);w=parseInt(RegExp.$2);h=parseInt(RegExp.$3);}
var photo=new PhotoImg(undefined,url,w,h);var found=false;for(i=0;i<viewer.photos.length;i++){if(viewer.photos[i].src==photo.src){found=true;break;}}
if(!found)
viewer.add(photo);}}}
if(node.childNodes!=undefined){for(i=0;i<node.childNodes.length;i++){findPhotosTT(viewer,node.childNodes[i]);}}}
var defaultViewer=undefined;function popupImg(url,w,h,backColor,showToolbar){var i;if(defaultViewer==undefined)
defaultViewer=new PhotoViewer();else{defaultViewer.photos=[];defaultViewer.index=0;}
if(backColor!=undefined)
defaultViewer.setBackground(backColor,backColor,false);if(showToolbar==undefined||showToolbar){findPhotosTT(defaultViewer,window.document.body);for(i=0;i<defaultViewer.photos.length;i++){if(defaultViewer.photos[i].src==url){defaultViewer.show(i);}}}
if(defaultViewer.photos===undefined||defaultViewer.photos.length===0){defaultViewer.setShowToolbar(false);defaultViewer.add(new PhotoImg(undefined,url,w,h));defaultViewer.show();}
return false;}
function onClickEvent()
{var v=getViewer();if(v.customOnClickEvent!=undefined)
v.customOnClickEvent();else
closeViewer();}
function setupFragmentIdentifierModePhotoViewer(iframeLocation,iframename,viewerJSONArray)
{var viewer=new PhotoViewer();viewer.origRootLocation=document.location.href;viewer.origIFrameLocation=iframeLocation;viewer.iframename=iframename;viewer.setCloseCallback(viewer.setStopFragmentIdentifier);for(var i=0;i<viewerJSONArray.length;i++){viewer.add(viewerJSONArray[i].url,viewerJSONArray[i].title,viewerJSONArray[i].date,viewerJSONArray[i].byline);}
window.frames[viewer.iframename].location=viewer.origIFrameLocation+"#"+viewer.origRootLocation;viewer.checkStartFragmentIdentifier();}
function checkStartFragmentIdentifier(){var href=document.location.href;if(href.indexOf("#startphoto=")==-1){window.setTimeout(checkStartFragmentIdentifier,500);}else{var startPhoto=parseInt(href.substring(href.lastIndexOf("=")+1));var viewer=getViewer();if(viewer.origRootLocation.indexOf("#")==-1)
viewer.origRootLocation+="#";if(FIREFOX){window.history.back();}else{document.location.href=viewer.origRootLocation;}
viewer.show(startPhoto);}}
function setStopFragmentIdentifier(index){window.frames[getViewer().iframename].location=this.origIFrameLocation+"#stopphoto="+index;checkStartFragmentIdentifier();}
function setStartFragmentIdentifier(index){var rootWin=getRootWindow();if(this.origIFrameLocation==undefined)
this.origIFrameLocation=rootWin.location.href.substring(0,rootWin.location.href.indexOf("#"));if(this.origRootLocation==undefined)
this.origRootLocation=rootWin.location.href.substring(rootWin.location.href.indexOf("#")+1);this.checkStopFragmentIdentifier();var frIdentifier="#startphoto="+index;rootWin.parent.location=this.origRootLocation+frIdentifier;}
function checkStopFragmentIdentifier(){var href=getRootWindow().location.href;if(href.indexOf("#stopphoto")==-1){window.setTimeout(checkStopFragmentIdentifier,500);}else{var viewer=getViewer();var index=href.substring(href.lastIndexOf("=")+1);if(viewer.origIFrameLocation.indexOf("#")==-1)
viewer.origIFrameLocation+="#";if(FIREFOX){window.history.back();}else{getRootWindow().location.href=viewer.origIFrameLocation;}
viewerCloseCallback(index);}}
