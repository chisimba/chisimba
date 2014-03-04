/**
* nlstreeext_dd.js v2.2
* NlsTree Extension : Drap-drop operations
* This file is property of AddObject.com
* You are not allowed to use this script or 
* part of this script indepently without any interaction 
* with NLSTree Professional.
* Copyright 2005-2006, addObject.com. All Rights Reserved
* Author Jack Hermanto, www.addobject.com
*/

/*global NlsDDSession object*/
var nlsddSession=null;

function NlsDDAction() {}
NlsDDAction.DD_INSERT="I";
NlsDDAction.DD_APPEND="A";

function NlsDDSession(sObj, sDt) {
  this.srcObj=sObj;
  this.srcData=sDt;
  this.destObj=null;
  this.destData=null;
  this.action=null;
  this.consume = function () {
    this.srcObj=null; this.srcData=null;this.destObj=null;this.destData=null;this.action=null;
  }
}

function I18N() {
  this.notAllowed="Not allowed!";
}

function NlsTreeDD(treeId) {
  this.tId = treeId;
  this.tree = nlsTree[treeId];
  this.tree.opt.evMouseUp=true;
  this.tree.opt.evMouseDown=true;
  this.tree.opt.evMouseOver=true;
  this.tree.opt.evMouseOut=true;
  
  this.tree.treeOnMouseUp = ddMouseUp;
  this.tree.treeOnMouseDown = ddMouseDown;
  this.tree.treeOnMouseOver = ddMouseOver;
  this.tree.treeOnMouseOut = ddMouseOut;  
  this.tree.ddHandler=this;
  
  this.startDrag=startDrag;
  this.endDrag=endDrag;
  
  this.ic=[new Image()];
  this.ic[0].src=this.tree.defImgPath+"nodrop.gif";
  
  this.i18n=new I18N();
  
  //public events
  this.onNodeDrag=onNodeDrag;
  this.onNodeDrop=onNodeDrop;
  
  this.canDrag=canDragNode; 
  this.canDrop=canDropNode;
  
  return this;
}

function startDrag(e) {
  var g=NlsGetElementById("ddGesture");
  var scrOffX = window.scrollX?window.scrollX:document.body.scrollLeft; //scrollLeft:IE, scrollX:MOZ
  var scrOffY = window.scrollY?window.scrollY:document.body.scrollTop; //scrollLeft:IE, scrollX:MOZ
  g.style.left=e.clientX+scrOffX+5+"px";
  g.style.top=e.clientY+scrOffY+5+"px";
  g.style.zIndex=1;
  if (g.style.display=="none") {
    var d=nlsddSession.srcData; var s="";
    for (var i=0; i<d.length; i++) {
      var elm=NlsGetElementById(d[i].id).childNodes[0].childNodes[0].childNodes[0].childNodes[1];
      var imgEl=null;
      if (nlsddSession.srcObj.opt.icon) { imgEl=elm.childNodes[(elm.childNodes.length>1?1:0)]; }
      s+="<table cellpadding=0 cellspacing=0><td>"+(imgEl?"<img src=\""+imgEl.src+"\">":"&nbsp;")+"</td><td><a class=\""+this.tree.opt.stlprf+"node\">"+d[i].capt+"</a></td></table>";
    
    }
    g.innerHTML=s;
    g.style.display="";  
  }

  this.onNodeDrag(e);
}

function endDrag(e) {  
  //hide gesture  
  var g=NlsGetElementById("ddGesture");
  g.innerHTML="";
  g.style.display="none";
  
  //disable all DD related events
  document.onmousemove=null;
  document.onmouseup=null;
  document.onmousedown=function() { return true;}
  document.onselectstart=function() { return true;}
  document.onselectstart=function() { return true;}
  
  //this.onNodeDrop(e);
}

function getTargetId(t, e, id) {
  if (nls_isIE) {
    var nd=t.nLst[e.srcElement.parentElement.parentElement.parentElement.parentElement.parentElement.id];
    if (nd) { return nd.orgId; } else { return id; }
  } else {
    return id;
  }
}

function ddMouseUp(e, id) {

  var nd=this.getNodeById(getTargetId(this, e, id));
  if (!nlsddSession) return false;
  if (!this.ddHandler.canDrop(nd.orgId)) return false;
  nlsddSession.destObj=this;
  nlsddSession.destData=nd;
  this.ddHandler.endDrag(e);
  this.ddHandler.onNodeDrop(e);
  nlsddSession=null;  
}

function ddMouseDown(e, id) {
  if (this.$editing) return;
  var ddHd = this.ddHandler;
  var intId=this.genIntId(id);
  NlsTree._blockEdit=false;
  if (this.opt.multiSel) {
    if (!this.isSelected(id) && ! (e.ctrlKey || e.shiftKey )) { NlsTree._blockEdit=true; this.selectNode(intId); }
  } else {
    if(this.selNd==null || this.selNd.id!=intId) { this.selectNode(intId); NlsTree._blockEdit=true;}
  }
  
  var nd=this.getSelNodes();
  
  if (ddHd.canDrag(id)) {
    nlsddSession=new NlsDDSession(this, nd);

    document.onmousemove=function(ev) {ddHd.startDrag((ev?ev:event));}
    document.onmouseup=function(ev) {nlsddSession.action=null;ddHd.endDrag((ev?ev:event));}
    document.onselectstart=function() { return false;}
    document.onmousedown=function() { return false;}
    document.ondragstart=function() { return false;}
  }  
}

function ddMouseOver(e, id) {
  if (nlsddSession!=null && document.onmousemove!=null) {} else {return;}
  
  var ddHd = this.ddHandler;
  var trgId = getTargetId(this, e, id);
  if (!ddHd.canDrop(trgId)) {
    var g=NlsGetElementById("ddGesture");
    g.style.display="";
    g.innerHTML="<table cellpadding=0 cellspacing=0><td><img src=\""+ddHd.ic[0].src+"\"></td><td><a class=\""+this.opt.stlprf+"node\">"+ddHd.i18n.notAllowed+"</a></td></table>";
  }
}

function ddMouseOut(e, id) {
  if (nlsddSession!=null && document.onmousemove!=null) {} else {return;}
  var g=NlsGetElementById("ddGesture");
  g.style.display="none";
}

//=========================================
//NlsTree standard implementation for 
//drag and drop
//=========================================

function onNodeDrag(e) {
  if (e.shiftKey) {
    nlsddSession.action=NlsDDAction.DD_INSERT;
  } else {
    nlsddSession.action=NlsDDAction.DD_APPEND;
  }
}

//custom drop function
//you can override this function to perform your custom operation
function onNodeDrop(e) {
  //process
  if (!nlsddSession) return;
  var sData, dData, sObj, dObj;
  with (nlsddSession) {
    if(!action) return;
    sData=srcData; sObj=srcObj;
    dData=destData; dObj=destObj;
  }
  if (sObj.tId==dObj.tId) { //drag drop in a tree
    switch (nlsddSession.action) {
      case NlsDDAction.DD_INSERT:
        sObj.moveChild(sData, dData, 2); break;
      case NlsDDAction.DD_APPEND: 
        sObj.moveChild(sData, dData, 1); break;
    }
  } else { // drag drop between tree
    switch (nlsddSession.action) {
      case NlsDDAction.DD_INSERT:
        for (i=0;i<sData.length;i++) {
          with (sData[i]) { 
            var nNd=dObj.addBefore(null, dData.orgId, capt, url, (ic?ic.join(","):ic), exp, chk, xtra); 
            if (fc) duplicateNode(fc, nNd);
          } 
        }
        dObj.reloadNode(dData.pr.orgId);
        break;
      case NlsDDAction.DD_APPEND: 
        for (i=0;i<sData.length;i++) {
          with (sData[i]) { 
            var nNd=dObj.append(null, dData.orgId, capt, url, (ic?ic.join(","):ic), exp, chk, xtra); 
            if (fc) duplicateNode(fc, nNd);
          }
        }
        dObj.reloadNode(nNd.orgId);
        dObj.expandNode(dData.orgId);
        break;
    }
  }
}

function duplicateNode(n, newNd) {
  do {
    var cN=nlsddSession.destObj.add(null, newNd.orgId, n.capt, n.url,  (n.ic?n.ic.join(","):n.ic), n.exp, n.chk, n.xtra);
    if (n.fc) { duplicateNode(n.fc, cN); }
    n=n.nx;
  } while (n);
}

function canDragNode(id) {
  if (this.tree.opt.multiSel) {
    var sNds=this.tree.getSelNodes();
    for (var i=0; i<sNds.length; i++) {
      if (sNds[i].allowDrag==false) return false;
    }
  } else {
    if (this.tree.getNodeById(id).allowDrag==false) return false;
  }
  return true;
}

function canDropNode(id) {
  var dest=this.tree.getNodeById(id);
  var src=nlsddSession.srcData;
  var inTree = (nlsddSession.srcObj.tId==this.tree.tId);
  
  if (dest.allowDrop==false) return false;
  
  if (!nlsddSession) return false;
  if(!nlsddSession.action) return false;
  if (!src || !dest || src.length==0) return false;
  
  if (inTree) {
    if (this.tree.isSelected(dest.orgId)) return false;

    var tmp=dest;
    while(tmp.pr) { if (this.tree.isSelected(tmp.orgId)) return false; tmp=tmp.pr; }
    switch (nlsddSession.action) {
      case NlsDDAction.DD_INSERT:
        if (dest.equals(this.tree.rt)) return false;

        for (var i=0; i<src.length; i++) { if (src[i].nx && dest.equals(src[i].nx)) return false; }
        break;
      case NlsDDAction.DD_APPEND: 
        if (this.tree.isSelected(this.tree.rt.orgId)) return false;      
        for (var i=0; i<src.length; i++) { if (dest.equals(src[i].pr)) return false; }      
        break;
    }
  } else {
    switch (nlsddSession.action) {
      case NlsDDAction.DD_INSERT: if (dest.equals(this.tree.rt)) return false;
      case NlsDDAction.DD_APPEND: break;
    }
  }
  return true;
};

//=========================================
//NlsTree extension for drag and drop
//=========================================

NlsTree._allowEdit=false;

NLSTREE.ddHandler=null;

NLSTREE.unloadChild = function(src) {
  var pr = src.pr;
  if (pr.lc.equals(src)) pr.lc=src.pv; 
  if (pr.fc.equals(src)) pr.fc=src.nx;
  if (src.pv!=null) src.pv.nx=src.nx; 
  if (src.nx!=null) src.nx.pv=src.pv;
  src.nx=null;src.pv=null;src.pr=null;
  if (this.selNd) { this.selNd=null; this.selElm=null; }
  if (this.opt.multiSel) { this.msRemove(src.orgId); }
}

//move a node
//type: 1 append child 2: insert before, 3: insert after
NLSTREE.moveChild = function (src, dest, type) {
  //validation
  if (!src || !dest || src.length==0) return;
  if (this.isSelected(dest.orgId)) return;
  var tmp=dest;
  while(tmp.pr) { if (this.isSelected(tmp.orgId)) return; tmp=tmp.pr; }
  
  switch (type) {
    case 1:
      if (this.isSelected(this.rt.orgId)) return;
      for (var i=0; i<src.length; i++) { if (dest.equals(src[i].pr)) false; }
      
      for (var i=0; i<src.length; i++) {
        /*unreference source node*/
        var srcPr=src[i].pr;      
        this.unloadChild(src[i]);
        this.reloadNode(srcPr.orgId);

        /*add to new parent*/
        src[i].pr=dest;
        if (dest.lc==null) {dest.fc=src[i];dest.lc=src[i];} else {
          var t=dest.fc;
          if (this.opt.sort!="no") { 
            do { if (this.opt.sort=="asc" ? this.compareNode(t, src[i]) : this.compareNode(src[i], t)) break; t = t.nx;
            } while (t!=null);
            if (t!=null) { if (t.pv==null) { t.pv=src[i]; dest.fc=src[i]; } else { src[i].pv=t.pv; t.pv.nx=src[i]; t.pv=src[i]; } src[i].nx=t; }
          }
          if (this.opt.sort=="no" || t==null) { src[i].pv = dest.lc; dest.lc.nx = src[i]; dest.lc = src[i]; }
        }
      }
      this.reloadNode(dest.orgId);
      this.expandNode(dest.orgId);      
      break;
    case 2: /*before*/
    case 3: /*after*/
      var sCh="pv"; var dCh="nx"; var ch="lc";
      if (type==2) {sCh="nx"; dCh="pv"; ch="fc";}
      if (dest.equals(this.rt)) return;
      for (var i=0; i<src.length; i++) { if (src[i][sCh] && dest.equals(src[i][sCh])) return; }

      /*unreference source node*/
      for (var i=0; i<src.length; i++) {
        var srcPr=src[i].pr;
        this.unloadChild(src[i]);
        this.reloadNode(srcPr.orgId);

        src[i].pr=dest.pr;
        if (dest[dCh]==null) { dest[dCh]=src[i]; dest.pr[ch]=src[i]; } else { src[i][dCh]=dest[dCh]; dest[dCh][sCh]=src[i]; dest[dCh]=src[i]; } src[i][sCh]=dest;
      }
      this.reloadNode(dest.pr.orgId);
      break;

  }
}

function nls_setNodeDnD(t, id, prop, v, incSub) {
  if (incSub==true) {
    t.loopTree(t.getNodeById(id), function(nd) {nd[prop]=v;});
  } else {
    t.getNodeById(id)[prop]=v;
  }  
}

NLSTREE.setDrag=function(id, v, incSub) {
  nls_setNodeDnD(this, id, "allowDrag", v, incSub);
};

NLSTREE.setDrop=function(id, v, incSub) {
  nls_setNodeDnD(this, id, "allowDrop", v, incSub);
};

NlsNode.prototype.allowDrag=true;
NlsNode.prototype.allowDrop=true;
