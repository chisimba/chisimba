/**
* nlstree.js v2.2
* Copyright 2005-2006, addobject.com. All Rights Reserved
* Author Jack Hermanto, www.addobject.com
*/
var nlsTree = new Object();
var nlsTreeIc = new Object();

var _ua=window.navigator.userAgent;
var nls_isIE = (_ua.indexOf("MSIE") >=0);
var nls_isSafari = (_ua.indexOf("Safari") >=0);
var nls_isOpera = (_ua.indexOf("Opera") >=0);
if (nls_isOpera) nls_isIE=false;

var NLSTREE = NlsTree.prototype;
function NlsTree(tId) {
  this.tId = tId;
  this.opt = new StdOpt();
  this.nLst = new Object();
  this.ctxMenu = null;
  this.rt = null;
  this.selNd = null;
  this.defImgPath="";
  
  /*internal*/
  this.selElm=null;
  this.tmId = null;
  this.nCnt = 0;
  this._fl=true;
  this.msNds=new Object();

  if (nlsTree[this.tId]!=null) {
      alert("The tree with id " + this.tId + " already exist, please change the tree id.");
  } else {
      nlsTree[this.tId] = this;
  }

  var allScs = (document.getElementsByTagName ? document.getElementsByTagName("SCRIPT"): document.scripts);
  for (var i=0;i<allScs.length;i++) {
    if (allScs[i].src.toLowerCase().indexOf("nlstree.js")>=0) {this.defImgPath=allScs[i].src.replace(/nlstree.js/gi, "img/");break;}
  }

  this.useIconSet(new StdIco(this.defImgPath));
  this.renderer = new DefRenderer(tId);

  return this;
};

function StdIco(path) {
  this.pnb = path+"plusnb.gif";
  this.pb  = path+"plusb.gif";
  this.mnb = path+"minusnb.gif";
  this.mb  = path+"minusb.gif";
  this.opf = path+"folderopen.gif";
  this.clf = path+"folder.gif";
  this.chd = path+"leaf.gif";
  this.rot = path+"root.gif";
  this.lnb = path+"lineang.gif";
  this.lb  = path+"lineints.gif";
  this.lin = path+"line.gif";
  this.bln = path+"blank.gif";
  this.pnl = path+"blank.gif";
  this.mnl = path+"blank.gif";

  this.toString = function() { return "Standard Icons"}  ;  
  return this;
};

function StdOpt() {
  this.trg = "_self";
  this.stlprf = "";
  this.sort = "asc"; /*desc, no, asc*/
  this.icon = true;
  this.check = false;
  this.editable = false;
  this.selRow = false;
  this.editKey = 113;
  this.oneExp = false;
  this.enableCtx = true;
  this.oneClick = false;
  this.mntState = false;
  this.icAsSel=false;
  this.checkIncSub=true;
  this.indent=true;
  this.hideRoot=false;
  this.showExpdr=false;
  this.checkOnLeaf=false;
  
  this.evDblClick=false;
  this.evCtxMenu=false;
  this.evMouseUp=false;
  this.evMouseDown=false;
  this.evMouseMove=false;
  this.evMouseOut=false;
  this.evMouseOver=false;
  
  return this;
};

function NlsNode(orgId, capt, url, ic, exp, chk, xtra, title) {
  this.orgId = orgId;
  this.id = "";
  this.capt = capt;
  this.url = (url==null || url=="") ? "" : url;
  this.ic = (ic==null || ic=="")?null:ic.split(",");
  this.exp = exp==null ? false : exp;
  this.chk = (chk?chk:false);
  this.xtra = xtra==null ? false : xtra;
  this.ctxMenu = null;
  this.cstStyle = "";
  this.trg=null;
  this.custom=null;
  this.title=title==null?capt:title;
  this.editablity=true;
  this.checkbox=true;
  this.sortFunc=NLSTREE.compareNode;

  if (this.ic) {preloadIcon(this.ic[0]); if (this.ic.length>1) preloadIcon(this.ic[1]);};
  
  this.isCustomNode=function() {return this.custom==null;};
  this.nx = null; this.pv = null; this.fc = null; this.lc = null; this.pr = null;
  this.equals = function (nd) { return (this.id == nd.id); };
};

NLSTREE.genIntId = function(id) { return this.tId+id; };
NLSTREE.genOrgId = function(intId) { return intId.substr(this.tId.length); };
NLSTREE.compareNode = function(aN, bN) { return (aN.capt >= bN.capt); };
NLSTREE.compareNodeById = function(aN, bN) { return (aN.id >= bN.id); };

NLSTREE.add = function(id, prn, capt, url, ic, exp, chk, xtra, title) {
  var nNd = new NlsNode(((id==null||String(id)=="")?("int"+ (++this.nCnt)):id), capt, url, ic, exp, chk, xtra, title);
  nNd.id = this.genIntId(nNd.orgId);
  if (this.nLst[nNd.id]!=null) { return; }
  if (this.rt==null) { this.rt = nNd; } else {
    var pnd = this.nLst[this.genIntId(prn)];
    if (pnd==null ) { return; }
    nNd.pr = pnd;
    if (pnd.lc==null) {
        pnd.fc = nNd; pnd.lc = nNd; 
    } else {
      var t=pnd.fc;
      if (this.opt.sort!="no") {
        do {
          if (pnd.sortFunc!=null) if (this.opt.sort=="asc" ? pnd.sortFunc(t, nNd) : pnd.sortFunc(nNd, t)) break;
          t = t.nx;
        } while (t!=null);
        if (t!=null) {
          if (t.pv==null) { t.pv=nNd; pnd.fc=nNd; } else { nNd.pv=t.pv; t.pv.nx=nNd; t.pv=nNd; }
          nNd.nx=t;
        }
      }
      if (this.opt.sort=="no" || t==null) { nNd.pv = pnd.lc; pnd.lc.nx = nNd; pnd.lc = nNd; }
    }
  }
  this.nLst[nNd.id] = nNd;
  return nNd;
};

NLSTREE.addCustomNode=function(id, prn, custom) {
  var nd=this.add(id, prn, ""); nd.capt=""; nd.custom=custom;
  return nd;
};

NLSTREE.addBefore = function (id, sib, capt, url, ic, exp, chk, xtra, title) {
  var nd=this.getNodeById(sib);
  if (nd==null) return;

  var nNd = new NlsNode(((id==null||id=="")?("int"+ (++this.nCnt)):id), capt, url, ic, exp, chk, xtra, title);
  nNd.id = this.genIntId(nNd.orgId);
  if (this.nLst[nNd.id]!=null) { alert("Item with id " + id + " already exist"); return; }
  this.nLst[nNd.id] = nNd; nNd.pr=nd.pr; nNd.nx=nd;
  if (nd.pv==null) { nd.pv=nNd; nd.pr.fc=nNd; } else { nNd.pv=nd.pv; nd.pv.nx=nNd; nd.pv=nNd; }
  return nNd;
};

NLSTREE.addAfter = function (id, sib, capt, url, ic, exp, chk, xtra, title) {
  var nd=this.getNodeById(sib);
  if (nd==null) return;

  var nNd = new NlsNode(((id==null||id=="")?("int"+ (++this.nCnt)):id), capt, url, ic, exp, chk, xtra, title);
  nNd.id = this.genIntId(nNd.orgId);
  if (this.nLst[nNd.id]!=null) { alert("Item with id " + id + " already exist"); return; }
  this.nLst[nNd.id] = nNd; nNd.pr=nd.pr; nNd.pv=nd;
  if (nd.nx==null) { nd.nx=nNd; nd.pr.lc=nNd; } else { nNd.nx=nd.nx; nd.nx.pv=nNd; nd.nx=nNd; }
  return nNd;
};

NLSTREE.append = function(id, prn, capt, url, ic, exp, chk, xtra, title) {
  var nd = this.add(id, prn, capt, url, ic, exp, chk, xtra, title);
  this.reloadNode(prn);
  return nd;
};

NLSTREE.remove = function(id, reload) {
  var rNd = (id!=null ? this.nLst[this.genIntId(id)] : this.selNd);
  if (rNd!=null) {
    if (this.rt.equals(rNd)) { this.rt=null; this.nLst=new Object(); this.selNd=null; this.selElm=null; return rNd};
    if (this.selNd && rNd.equals(this.selNd)) { this.selNd=null; this.selElm=null; }
    var pr = rNd.pr;
    if (pr.lc.equals(rNd)) pr.lc=rNd.pv; 
    if (pr.fc.equals(rNd)) pr.fc=rNd.nx;
    if (rNd.pv!=null) rNd.pv.nx=rNd.nx; 
    if (rNd.nx!=null) rNd.nx.pv=rNd.pv;
    rNd.nx=null;rNd.pv=null;rNd.pr=null;
    var treeId = this.tId;
    this.loopTree(rNd, function (n) { nlsTree[treeId].nLst[n.id]=null;});
    if (this.opt.multiSel) { delete this.msNds[rNd.id]; }
    if (reload==null || reload) this.reloadNode(this.genOrgId(pr.id));
  }
  return rNd;
};

NLSTREE.removeSelected = function() {
  var sNds=this.getSelNodes();
  for (var i=0; i<sNds.length; i++) {
    this.remove(sNds[i].orgId);
  }
}

NLSTREE.removeChilds = function(id, reload) {
  var rNd = (id!=null ? this.nLst[this.genIntId(id)] : this.selNd);
  if (rNd!=null) {
    while(rNd.fc) this.remove(rNd.fc.orgId, false);
    if (reload==null || reload) this.reloadNode(id);
  }
};

NLSTREE.getSelNode = function() { return this.selNd; };

NLSTREE.getSelNodes=function() {
  var a=[];
  if (this.opt.multiSel) { for (var it in this.msNds) { a[a.length]=this.msNds[it]; } } else
  if (this.selNd!=null) { a[0]=this.selNd; }
  return a;
}

NLSTREE.isSelected=function(orgId) {
  var nd=this.getNodeById(orgId);
  var sNds=this.getSelNodes();
  for (var i=0; i<sNds.length; i++) {  if (nd.id==sNds[i].id) return true; }
  return false;
}

NLSTREE.genTree = function() { return this.renderer.genTree(); };

NLSTREE.renderAttributes = function(plc) {
  if (plc && plc!="") NlsGetElementById(plc).innerHTML="";
  
  var attr="<input id='ndedt"+this.tId+"' type='text' class='"+this.opt.stlprf+"nodeedit' style='display:none' value='' onblur='if (nlsTree."+this.tId+"._fl) nlsTree."+this.tId+".liveNodeWrite()'>" +
    (!NlsGetElementById("ddGesture")?"<div id='ddGesture' style='position:absolute;border:#f0f0f0 1px solid;left:0px;top:0px;display:none'></div>":"");
  if (typeof(nlsctxmenu)!="undefined") { for (it in nlsctxmenu) { if(!NlsGetElementById(it)) attr+=nlsctxmenu[it].genMenu(); } }  
    
  if (plc && plc!="") { NlsGetElementById(plc).innerHTML=attr; } else { document.write(attr); }  
};

NLSTREE.render = function(plc) { 
  this.renderer.render(plc); 
  this.initEvent();

  var sid=null;
  if (this.opt.mntState && nls_getCookie) {
    var sid=nls_getCookie(this.tId+"_selnd");
    nls_maintainNodeState(this.tId, true);
  }

  if(sid && sid!="") this.selectNodeById(sid);
};

function DefRenderer(tId) {
  var tr=nlsTree[tId];
  
  this.rat=new Object();
  this.dsp=new Object();
  
  this.initRender = function() {
    var opt=tr.opt; var ico=tr.ico;
    var ev="onclick=\"return nls_c2(event, '"+tId+"','@id')\" ";
    if (opt.evDblClick) ev+="ondblclick=\"return nls_c3(event, '"+tId+"', '@id')\" "; 
    if (opt.evCtxMenu) ev+="oncontextmenu=\"return nls_c4(event, '"+tId+"', '@id')\" ";
    if (opt.evMouseUp) ev+="onmouseup=\"return nls_c5(event, '"+tId+"', '@id')\" ";
    if (opt.evMouseDown) ev+="onmousedown=\"return nls_c6(event, '"+tId+"', '@id')\" ";
    if (opt.evMouseOver) ev+="onmouseover=\"return nls_c7(event, '"+tId+"', '@id')\" ";
    if (opt.evMouseOut) ev+="onmouseout=\"return nls_c8(event, '"+tId+"', '@id')\" ";
    if (opt.evMouseMove) ev+="onmousemove=\"return nls_c9(event, '"+tId+"', '@id')\" ";

    var cbev="onclick=\"nls_cb1(event, '"+tId+"','@id')\"";
    var ex=["<img src='@expdr' onclick=\"nls_c1(event, '"+tId+"', '@id')\" class=\"pagetree2 plusminus\">", "<img src='@expdr' class='pagetree2 angleline'>", "<img src='@ic' class='pagetree2 icon' "+ev+">"];

    this.rat["cnt"]=["<div id='@id' class='"+opt.stlprf+"row @indent' title=\"@title\">", "<div id='@id' class='"+opt.stlprf+"row @indent' title=\"@title\" style='display:none'>", "</div><div style='display:block' id='ch_@id'>", "</div><div style='display:none' id='ch_@id'>"];
    this.rat["ex"]=[[[ex[1].replace(/@expdr/gi,ico.lnb),ex[1].replace(/@expdr/gi,ico.lb)],[ex[1].replace(/@expdr/gi,ico.lnb),ex[1].replace(/@expdr/gi,ico.lb)]],
                    [[ex[0].replace(/@expdr/gi,ico.pnb),ex[0].replace(/@expdr/gi,ico.pb)],[ex[0].replace(/@expdr/gi,ico.mnb),ex[0].replace(/@expdr/gi,ico.mb)]]];    
    this.rat["mn"]=["<table cellspacing=0 cellpadding=0><tr><td class=\"pagetree2 lines\">","</td><td class=\"pagetree2 plusminus-and-icon\">", "</td><td class=\"pagetree2 cell3\">","</td><td class=\"pagetree2 nodename\">", "</td><td class=\"pagetree2 cell5\" width='100%'>", "</td></tr></table>"];
    this.rat["ics"]=ex[2];
    this.rat["ic"]=[[ex[2].replace(/@ic/gi, ico.chd), ex[2].replace(/@ic/gi, ico.chd)], [ex[2].replace(/@ic/gi, ico.clf), ex[2].replace(/@ic/gi, (tr.opt.icAsSel?ico.clf:ico.opf))]];
    this.rat["rt"]=ex[2].replace(/@ic/gi, ico.rot);
    this.rat["ln"]=["<img src=\""+ico.bln+"\" class='pagetree2 icon'>", "<img src=\""+ico.lin+"\" class='pagetree2 line'>"];
    this.rat["ac"]=["<a class=\"@nstyle\" style='display:block' href=\"javascript:void(0);\" "+ev+" >@capt</a>", "<a class=\"@nstyle\" style='display:block' target='@trg' "+ev+" href=\""];
    this.rat["st"]=[opt.stlprf+"node", opt.stlprf+"prnnode"];
    this.rat["cb"]=["<input style='height:14px;margin:1px' type='checkbox' id='cb_@id' name='cb_@id' "+cbev+" >", "<input style='height:14px;margin:1px' type='checkbox' checked id='cb_@id' name='cb_@id' "+cbev+" >"];
    this.rat["rd"]=["<input style='height:14px;margin:1px' type='radio' name='rd_@id'>", "<input style='height:14px;margin:1px' type='radio' checked name='rd_@id'>"];
  };
  
  this.genANode = function(sNd) {
    this.dsp["ln"]="";this.dsp["ic"]="";this.dsp["chk"]=""; 
    var n=sNd.pr;
    if (tr.opt.indent) while (true) {if (!n || n.equals(tr.rt) || (tr.opt.hideRoot && !tr.opt.showExpdr && n.pr.equals(tr.rt))) break; this.dsp["ln"]=this.rat["ln"][(n.nx!=null?1:0)]+this.dsp["ln"]; n=n.pr;}
    
    if (sNd.custom!=null) {
      this.dsp["nd"]=sNd.custom;
      this.dsp["ip"]=this.rat["ln"][0];
      return this.rat["mn"][0]+this.dsp["ln"]+this.rat["mn"][1]+this.dsp["ip"]+this.dsp["ic"]+this.rat["mn"][2]+this.dsp["chk"]+this.rat["mn"][4]+this.dsp["nd"]+this.rat["mn"][5];
    } else {
      var fc=1,ex=0,nx=1, rt=0; if (!sNd.fc) {fc=0; sNd.exp=false;}; if (sNd.exp) ex=1; if (!sNd.nx) nx=0;if(sNd.id==tr.rt.id) rt=1;      
      
      this.dsp["ip"]=this.rat["ex"][fc][ex][nx];
      if (rt==1 || (tr.opt.hideRoot && !tr.opt.showExpdr && sNd.pr.id==tr.rt.id)) {this.dsp["ip"]="";}
      if (tr.opt.icon||rt==1) {
        if (sNd.ic!=null) { this.dsp["ic"]=this.rat["ics"].replace(/@ic/gi,sNd.ic[(!tr.opt.icAsSel && sNd.ic[ex]?ex:0)]); } 
        else {
          this.dsp["ic"]=this.rat["ic"][fc][ex];
          if (rt==1) this.dsp["ic"]=this.rat["rt"];
        }
      }

      if (sNd.url) { this.dsp["nd"]=this.rat["ac"][1].replace(/@trg/gi, (sNd.trg==null?tr.opt.trg:sNd.trg))+ sNd.url+"\">"+sNd.capt+"</a>"; } 
      else { this.dsp["nd"]=this.rat["ac"][0].replace(/@capt/gi, sNd.capt);}
      
      if (sNd.cstStyle!="") {this.dsp["nd"]=this.dsp["nd"].replace(/@nstyle/gi, sNd.cstStyle);} else {this.dsp["nd"]=this.dsp["nd"].replace(/@nstyle/gi, this.rat["st"][fc]);}

      if (tr.opt.check && sNd.checkbox) {var c=tr.opt.checkOnLeaf; if (!c || (c && fc==0)) this.dsp["chk"]=this.rat["cb"][(sNd.chk?1:0)];}
      if (sNd.pr && sNd.pr.rad) { this.dsp["chk"]=this.rat["rd"][(sNd.chk?1:0)].replace(/@id/gi, sNd.pr.id);}

      return this.rat["mn"][0]+this.dsp["ln"]+this.rat["mn"][1]+this.dsp["ip"]+this.dsp["ic"]+this.rat["mn"][2]+this.dsp["chk"]+this.rat["mn"][3]+this.dsp["nd"]+this.rat["mn"][5];
    }
  };

  this.genNodes = function(sNd, incpar, wrt) {
    var s="";
    var depth=0;
    var n=sNd.pr;
    while (n!=null) {
        n=n.pr;
        depth++;
    }
    if (incpar) { s=this.rat["cnt"][(tr.opt.hideRoot && sNd.equals(tr.rt)?1:0)]+this.genANode(sNd)+this.rat["cnt"][(sNd.fc && sNd.exp?2:3)]; };
    s=s.replace(/@id/gi,sNd.id).replace(/@title/gi, sNd.title).replace(/@indent/gi,"indent"+depth);
    if (wrt) document.write(s);
    if (sNd.fc !=null) {
        var chNode = sNd.fc;
        do {
          if (wrt) this.genNodes(chNode, true, wrt); else s=s+this.genNodes(chNode, true, wrt);
          chNode = chNode.nx;
        } while (chNode != null)
    }
    if (wrt) {
      if (incpar) document.write("</div>"); return "";
    } else {
      s= incpar ? (s+"</div>") : s; return s;
    }
  };
  
  this.genTree = function() {
    this.initRender();
    return this.genNodes(tr.rt, true, false);
  };
  
  this.render = function(plc) {
    if (plc && plc!="") {
      NlsGetElementById(plc).innerHTML = "<div id=\""+tId+"\">" + this.genTree() + "</div>";
    } else {
      this.initRender();
      document.write("<div id=\""+tId+"\">");
      this.genNodes(tr.rt, true, true);
      document.write("</div>");
    }
  };
  return this;
};

NLSTREE.initEvent = function() {  
  var orgEvent = (nls_isIE?document.body.onkeydown:window.onkeydown);
  if (!orgEvent || orgEvent.toString().search(/orgEvent/gi) < 0) {
    var newEvent = function(e) { if (nlsTree.selectedTree) nlsTree.selectedTree.liveNodePress(nls_isIE?event:e); if (orgEvent) return orgEvent();};
    if (nls_isIE) document.body.onkeydown=newEvent; else window.onkeydown=newEvent;
  }
};

NLSTREE.reloadNode = function(id, incChd) {
  this.renderer.initRender();
  var intId = this.genIntId(id);
  var cNode=this.nLst[intId];
  var dvN = NlsGetElementById("ch_"+intId);
  if (incChd!=false) {
    var s = this.renderer.genNodes(cNode, false);
    dvN.innerHTML = s;
    if (dvN.innerHTML=="") dvN.style.display="none";
  }
  if (cNode.exp==true && cNode.fc!=null) dvN.style.display="";
  dvN = NlsGetElementById(intId);
  dvN.innerHTML = "";
  dvN.innerHTML = this.renderer.genANode(cNode).replace(/@id/gi, intId);
  if (this.selNd!=null) {var sId=this.selNd.id; this.selNd=null; this.selElm=null; this.selectNode(sId); }
};

NLSTREE.selNToggle = function(id) {
  this.toggleNode(id);
  if (!this.selNd || this.selNd.id!=id) this.selectNode(id);
  if (this.tmId!=null) { clearTimeout(this.tmId); this.tmId=null; }
};

function nls_setStyle(tree, sNd, sElm, selected) {
  var prf= (selected?"sel":"");
  var depth=0;
  var n=sNd.pr;
  while (n!=null) {
    n=n.pr;
    depth++;
  }
  if (tree.opt.selRow) sElm.className = tree.opt.stlprf+prf+"row indent"+depth;
  ac = sElm.childNodes[0].childNodes[0].childNodes[0].childNodes[3].childNodes[0];
  ac.className = (sNd.cstStyle!=""?prf+sNd.cstStyle:tree.opt.stlprf+prf+(sNd.fc?"prnnode":"node"));
};

function nls_setNodeIcon(tree, sNd, sElm, selected) {
  if (tree.opt.icon) { 
    ic=sElm.childNodes[0].childNodes[0].childNodes[0].childNodes[1];
    if (ic.childNodes.length==2) {ic=ic.childNodes[1];} else {ic=ic.childNodes[0];} 
    if (selected) {
      if (sNd.ic!=null) { ic.src=nlsTreeIc[sNd.ic[1]?sNd.ic[1]:sNd.ic[0]].src } else { ic.src=nlsTreeIc[(sNd.id==tree.rt.id?tree.ico.rot:(sNd.fc?tree.ico.opf:tree.ico.chd))].src; } 
    } else {
      if (sNd.ic!=null) { ic.src=nlsTreeIc[sNd.ic[0]].src } else { ic.src=nlsTreeIc[(sNd.id==tree.rt.id?tree.ico.rot:(sNd.fc?tree.ico.clf:tree.ico.chd))].src; } 
    }   
  }    
};

NLSTREE.selectNode = function (id) { /*id is internal id*/ 
  nlsTree.selectedTree=this;
  if (this.opt.editable) {
    var sNd=this.selNd;    
    if (sNd!=null && sNd.id!=id) { if (this.tmId) { clearTimeout(this.tmId); this.tmId=null;}  }
    if (sNd!=null && sNd.id==id && !NlsTree._blockEdit) { this.tmId=setTimeout("nlsTree."+this.tId+".liveNodeEdit('"+id+"')", 1000); }
    var edt=NlsGetElementById("ndedt"+this.tId);
    if (edt && edt.style.display=="") {  edt.style.display="none"; edt.disabled=true;} 
  }
  
  var ac=null;var ic=null;
  var sNd=this.selNd;
  var sElm=this.selElm;
  if (sElm!=null) {
    nls_setStyle(this, sNd, sElm, false);
    if (this.opt.icAsSel) nls_setNodeIcon(this, sNd, sElm, false);
  }
  sNd = this.nLst[id];
  this.selNd = sNd;
  this.selElm= NlsGetElementById(id);
  sElm=this.selElm;
  nls_setStyle(this, sNd, sElm, true);
  if (this.opt.icAsSel) nls_setNodeIcon(this, sNd, sElm, true);
  if (this.opt.mntState && nls_setCookie) nls_setCookie(this.tId+"_selnd", sNd.orgId);

  if (this.opt.multiSel) {
    this.msRemoveAll();
    this.msAdd(sNd);
  }
  
};

NLSTREE.selectNodeById = function(id) {
  var node = this.getNodeById(id);
  if (!node) return;
  var tmp = node.pr;
  while (tmp!=null) { this.expandNode(tmp.orgId); tmp=tmp.pr; }
  this.selectNode(node.id);  /*select the node*/ 
};

NLSTREE.unselectNodeById=function(id) {
  var nd=this.getNodeById(id);
  if (this.selNd!=null && this.selNd.equals(nd)) {
    var oNode=NlsGetElementById(nd.id)
    if (this.opt.icAsSel) nls_setNodeIcon(this, nd, oNode , false);
    if (this.opt.mntState && nls_setCookie) nls_removeCookie(this.tId+"_selnd");    
    this.selNd=null; this.selElm=null;
    nls_setStyle(this, nd, oNode, false);
  }
};

NLSTREE.isChild = function(c, p) {
  var nd=this.getNodeById(c);
  if (!nd) return false;
  var tmp=nd.pr;
  while (tmp!=null) { if (tmp.orgId==p) return true; tmp=tmp.pr;}
  return false;
};

NLSTREE.hasChild=function(id) {
  var nd=this.getNodeById(id);
  return (nd.fc!=null);
};

NLSTREE.expandNode = function(id) {
  var sNd = this.nLst[this.genIntId(id)];
  if (!sNd.exp && sNd.fc) this.toggleNode(sNd.id);
};

NLSTREE.collapseNode = function(id) {
  var sNd = this.nLst[this.genIntId(id)];
  if (sNd.exp && sNd.fc) this.toggleNode(sNd.id);
};

NLSTREE.prepareToggle = function(id) {
  var sNd = this.selNd;
  if (sNd==null) { this.selectNode(id); return; }
  if (sNd.id==id) return;
  while(sNd!=null && sNd.id!=id) {sNd=sNd.pr;}
  if (sNd==null) return;
  if (sNd.id==id) this.selectNode(id);
};

NLSTREE.toggleNode = function(id) {
  var nd = NlsGetElementById("ch_" + id);
  var ip = null;
  if (id!=this.rt.id) {ip=NlsGetElementById(id).childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0];}
  var sNd = this.nLst[id];
  if (this.opt.hideRoot && !this.opt.showExpdr && sNd.pr && sNd.pr.equals(this.rt)) ip=null;
  if (sNd.exp) {
      sNd.exp = false;
      nd.style.display="none";
      if (ip!=null && sNd.fc!=null) ip.src=sNd.nx ? this.ico.pb : this.ico.pnb;
      if (!this.opt.icAsSel) nls_setNodeIcon(this, sNd, NlsGetElementById(id), false);      
      if (this.opt.mntState && nls_delExpandedId) nls_delExpandedId(this.tId+"_ndstate", sNd.orgId);      
      this.treeOnCollapse(sNd.orgId);
  } else {
      if (this.opt.oneExp && sNd.pr) { 
        var tNd = sNd.pr.fc; 
        
        while (tNd) { if (tNd.id!=id && tNd.exp) this.collapseNode(tNd.orgId); tNd=tNd.nx;}
      }
      sNd.exp = true;
      nd.style.display="block";
      if (ip!=null && sNd.fc!=null) ip.src=sNd.nx ? this.ico.mb : this.ico.mnb;
      if (!this.opt.icAsSel) nls_setNodeIcon(this, sNd, NlsGetElementById(id), true);      
      if (this.opt.mntState && nls_addExpandedId) nls_addExpandedId(this.tId+"_ndstate", sNd.orgId);
      this.treeOnExpand(sNd.orgId);
  }
};

NLSTREE.expandAll = function (id) {
  var treeId=this.tId;
  var startNode=(!id?this.rt:this.getNodeById(id));
  this.loopTree(startNode, function(n) { if (n.fc) nlsTree[treeId].expandNode(n.orgId); });
};

NLSTREE.collapseAll = function (incPr, id) {
  var treeId=this.tId;
  var startNode=(!id?this.rt:this.getNodeById(id));
  this.loopTree(startNode, function(n) { if (n.fc && (!startNode.equals(n) || incPr)) nlsTree[treeId].collapseNode(n.orgId); });
};

NLSTREE.checkNode = function(intId, chkOvr) {
  var nd = NlsGetElementById("cb_" + intId);
  var sNd = this.nLst[intId];
  if (arguments.length>1) nd.checked=chkOvr;
  sNd.chk = nd.checked;
  
  if (this.opt.checkIncSub) { if(nd.checked==true) { this.loopTree(sNd, actCheckNode); } else { this.loopTree(sNd, actUncheckNode);} }
};

function actCheckNode(sNd) { if(!sNd.checkbox)return; var nd = NlsGetElementById("cb_" + sNd.id); nd.checked=true; sNd.chk = true; }
function actUncheckNode(sNd) {if(!sNd.checkbox)return; var nd = NlsGetElementById("cb_" + sNd.id); nd.checked=false; sNd.chk = false; }

NLSTREE.setNodeStyle = function (id, cls, rt) {
  var nd = this.getNodeById(id);
  nd.cstStyle=cls;
  if (rt) {  
    var oNd = NlsGetElementById(nd.id); 
    if (oNd) {
      var ac = oNd.childNodes[0].childNodes[0].childNodes[0].childNodes[3].childNodes[0];
      ac.className =cls;
    }
  }
};

NLSTREE.setNodeCaption = function(id, capt) {
  var intId = this.genIntId(id);
  var nd = NlsGetElementById(intId).childNodes[0].childNodes[0].childNodes[0].childNodes[3].childNodes[0];
  var sNd = this.nLst[intId];
  nd.innerHTML = capt;
  sNd.capt = capt;
};

NLSTREE.getNodeById = function(id) {
  return this.nLst[this.genIntId(id)]
};

NLSTREE.setGlobalCtxMenu = function(ctx) {
  this.opt.evCtxMenu=true;
  this.ctxMenu = ctx;
  ctx.container=this;
};

NLSTREE.setNodeCtxMenu = function(id, ctx) {
  this.opt.evCtxMenu=true;
  var nd = this.nLst[this.genIntId(id)];
  nd.ctxMenu = ctx;
  if (ctx.mId) ctx.container=this;
};

NLSTREE.setNodeTarget = function(id, trg) {
  var nd = this.nLst[this.genIntId(id)];  
  nd.trg=trg;
};

NLSTREE.setEditablity = function(id, v) {
  var nd = this.nLst[this.genIntId(id)];  
  nd.editablity=v;
};

NLSTREE.enableCheckbox = function(id, v) {
  var nd = this.nLst[this.genIntId(id)];  
  nd.checkbox=v;
};

NLSTREE.useIconSet = function(icSet) {
  this.ico=icSet;
  preloadIcon(this.ico.pnb,this.ico.pb,this.ico.pnl,this.ico.mnb,
    this.ico.mb,this.ico.mnl,this.ico.opf,this.ico.clf,this.ico.chd,
    this.ico.rot,this.ico.lnb,this.ico.lb,this.ico.lin,this.ico.bln);
};

NLSTREE.contextMenu = function(ev, id) {
  if (!this.opt.enableCtx) return false;
  var sNd=this.nLst[id]; var ctx=null;
  if (sNd.ctxMenu && sNd.ctxMenu.mId) ctx=sNd.ctxMenu; else 
  if (sNd.ctxMenu=="DEFAULT") ctx=null; else
  if (sNd.ctxMenu=="NONE") return false; else ctx=this.ctxMenu;
  if (!ctx) return true;
  
  if (this.opt.multiSel && this.isSelected(sNd.orgId)) {
    /*check if all the ctx menu*/
    var sNds=this.getSelNodes();
    for (var i=0; i<sNds.length; i++) {
      var t=(sNds[i].ctxMenu==null?this.ctxMenu:sNds[i].ctxMenu);
      if (t!=null && t.mId!=ctx.mId) {this.selectNode(id); break;}
    }
  } else {
    this.selectNode(id);
  }
  
  if (this.tmId) clearTimeout(this.tmId);
  ctx.showMenu(ev.clientX, ev.clientY);
  
  return false;
};

NLSTREE.loopTree = function(sNd, act) {
  act(sNd);
  if (sNd.fc !=null) {
      var chNode = sNd.fc;
      do {
          this.loopTree(chNode, act);
          chNode = chNode.nx;
      } while (chNode != null)
  }
};

NLSTREE.liveNodeEditStart = function(id) {
  this.tmId = setTimeout("nlsTree."+this.tId+".liveNodeEdit('"+id+"')", 0)
};

NLSTREE.liveNodeEdit = function(id) {
  if (!this.nLst[id].editablity) {this.tmId=null; return;}
  if (this.tmId!=null) {
    var edt = NlsGetElementById("ndedt"+this.tId);
    var ac = NlsGetElementById(id).childNodes[0].childNodes[0].childNodes[0].childNodes[3].childNodes[0];
    var x=0,y=0,elm=ac;
    while(elm) { x += elm.offsetLeft; y+=elm.offsetTop; elm=elm.offsetParent; }
    elm=NlsGetElementById(this.tId);
    if (elm) {y-=elm.scrollTop; x-=elm.scrollLeft;}

    edt.disabled=false;
    var posAdj=this.editBoxPosAdj();
    edt.style.top=y+posAdj[1]+"px"; edt.style.left=x+posAdj[0]+"px"; edt.style.display="block";
    edt.focus();
    //value = ac.innerHTML;
    edt.value=this.nLst[id].capt;
    
    this.tmId = null;
    this.$editing=true;
  }
};

NLSTREE.liveNodeWrite = function() {
  var edt = NlsGetElementById("ndedt"+this.tId);
  if (edt.style.display=="none") return;
  var ac = NlsGetElementById(this.selNd.id).childNodes[0].childNodes[0].childNodes[0].childNodes[3].childNodes[0];

  if (edt.value != "" && edt.value!=this.selNd.capt) { if (this.treeOnBeforeNodeChange(this.selNd.orgId)) { ac.innerHTML=edt.value; this.selNd.capt=edt.value; this.treeOnNodeChange(this.selNd.orgId);} else {return;} }
  edt.style.display="none";
  edt.disabled=true;
  this.$editing=false;
};

NLSTREE.liveNodePress = function(e) {
  if (!this.opt.editable) return;
  if (e.keyCode==13) {
    this._fl=false;
    this.liveNodeWrite();
    this._fl=true;
  } else if(e.keyCode==27) {
    var edt=NlsGetElementById("ndedt"+this.tId); edt.style.display="none"; edt.disabled=true;
  } else if(e.keyCode==this.opt.editKey) {/*f2*/
      this.tmId = setTimeout("nlsTree."+this.tId+".liveNodeEdit('"+this.selNd.id+"')", 10);
  }
};

NLSTREE.editBoxPosAdj=function() { return [0,0]; };
/*NLSTREE.ctxPosAdj=function() { return [0,0]; };*/
/*NLSTREE.ddPosAdj=function() { return [0,0]; };*/

function nls_c1(e, tId, nId){
  nlsTree[tId].prepareToggle(nId);
  nlsTree[tId].toggleNode(nId);
};

function nls_c2(e, tId, nId){  
  var t=nlsTree[tId]; var nd=t.nLst[nId];
  if (e.ctrlKey && e.altKey) { if (e.stopPropagation) {e.stopPropagation();} else {e.cancelBubble=true}; return t.contextMenu(e, nId); }
  if (t.opt.multiSel) { if (!nls_msTreeOnClick(e, tId, nId)) return false; }
  if ((t.opt.oneClick || (t.opt.hideRoot && t.rt.equals(nd.pr))) && t.nLst[nId].fc) { t.selNToggle(nId); } else { t.selectNode(nId); }
  return t.treeOnClick(e, t.genOrgId(nId));
};

function nls_c3(e, tId, nId){
  var t=nlsTree[tId];
  if (t.nLst[nId].fc) t.selNToggle(nId);
  return t.treeOnDblClick(e, t.genOrgId(nId));
};

function nls_c4(e, tId, nId) {
  var t=nlsTree[tId]; return t.contextMenu(e, nId);
};

function nls_c5(e, tId, nId) {
  var t=nlsTree[tId]; return t.treeOnMouseUp(e, t.genOrgId(nId));
};

function nls_c6(e, tId, nId) {
  var t=nlsTree[tId]; return t.treeOnMouseDown(e, t.genOrgId(nId));
};

function nls_c7(e, tId, nId) {
  var t=nlsTree[tId]; return t.treeOnMouseOver(e, t.genOrgId(nId));
};

function nls_c8(e, tId, nId) {
  var t=nlsTree[tId]; return t.treeOnMouseOut(e, t.genOrgId(nId));
};

function nls_c9(e, tId, nId) {
  var t=nlsTree[tId]; return t.treeOnMouseMove(e, t.genOrgId(nId));
};

function nls_cb1(e, tId, nId) {
  var t=nlsTree[tId];
  t.checkNode(nId);
  t.treeOnCheck(t.genOrgId(nId));
};

NLSTREE.treeOnClick = function(e, id) {return true;};
NLSTREE.treeOnDblClick = function(e, id) {};
NLSTREE.treeOnMouseOver = function (e, id) {};
NLSTREE.treeOnMouseMove = function (e, id) {};
NLSTREE.treeOnMouseOut = function (e, id) {};
NLSTREE.treeOnMouseDown = function (e, id) {};
NLSTREE.treeOnMouseUp = function (e, id) {};
NLSTREE.treeOnCheck = function (id) {};
NLSTREE.treeOnExpand = function (id) {};
NLSTREE.treeOnCollapse = function (id) {};
NLSTREE.treeOnNodeChange = function (id) {};
NLSTREE.treeOnBeforeNodeChange = function (id) {return true;};

function preloadIcon() {
  var arg = preloadIcon.arguments;
  for (var i=0;i<arg.length;i++) {
    if (!nlsTreeIc[arg[i]]) {
      nlsTreeIc[arg[i]] = new Image();
      nlsTreeIc[arg[i]].src=arg[i];
    }
  }
};

/**Cross browser related methods*/
function NlsGetElementById(id) {
  if (document.all) {
      return document.all(id);
  } else
  if (document.getElementById) {
      return document.getElementById(id);
  }
};