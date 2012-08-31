/*
 * jqGrid 3.2.4
 * Tony Tomov.
 * http://www.trirand.com/blog
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

;(function($){$.jgrid.search={caption:"Search...",Find:"Find",Reset:"Reset",odata:['equal','not equal','less','less or equal','greater','greater or equal','begins with','ends with','contains']};$.jgrid.edit={addCaption:"Add Record",editCaption:"Edit Record",bSubmit:"Submit",bCancel:"Cancel",processData:"Processing...",mtype:"POST",msg:{required:"Field is required",number:"Please enter valid number!",minValue:"value must be greater than or equal to ",maxValue:"value must be less than or equal to",email:"is not a valid e-mail"}};$.jgrid.del={caption:"Delete",msg:"Delete selected record(s)?",bSubmit:"Delete",bCancel:"Cancel",processData:"Processing...",mtype:"POST"};$.jgrid.nav={edittext:" ",edittitle:"Edit selected row",addtext:" ",addtitle:"Add new row",deltext:" ",deltitle:"Delete selected row",searchtext:" ",searchtitle:"Find records",refreshtext:"",refreshtitle:"Reload Grid",alertcap:"Warning!",alerttext:"Please, select Row!"};$.fn.extend({searchGrid:function(p){p=$.extend({top:0,left:0,width:360,height:70,modal:false,drag:true,closeicon:'ico-close.gif',dirty:false,sField:'searchField',sValue:'searchString',sOper:'searchOper',processData:"",sopt:null},$.jgrid.search,p||{});return this.each(function(){var $t=this;if(!$t.grid)return;if(!p.imgpath)p.imgpath=$t.p.imgpath;var gID=$("table:first",$t.grid.bDiv).attr("id");var IDs={themodal:'srchmod'+gID,modalhead:'srchhead'+gID,modalcontent:'srchcnt'+gID};if($("#"+IDs.themodal).html()!=null){viewModal("#"+IDs.themodal,{modal:p.modal});}else{var cM=$t.p.colModel;var cNames="<select id='snames' class='search'>";var nm,hc,sf;for(var i=0;i<cM.length;i++){nm=cM[i].name;hc=(cM[i].hidden==true)?true:false;sf=(cM[i].search==false)?false:true;if(nm!=='cb'&&nm!=='subgrid'&&sf&&!hc){var sname=(cM[i].index)?cM[i].index:nm;cNames+="<option value='"+sname+"'>"+$t.p.colNames[i]+"</option>";}}
cNames+="</select>";var getopt=p.sopt||['bw','eq','ne','lt','le','gt','ge','ew','cn'];var sOpt="<select id='sopt' class='search'>";for(var i=0;i<getopt.length;i++){sOpt+=getopt[i]=='eq'?"<option value='eq'>"+p.odata[0]+"</option>":"";sOpt+=getopt[i]=='ne'?"<option value='ne'>"+p.odata[1]+"</option>":"";sOpt+=getopt[i]=='lt'?"<option value='lt'>"+p.odata[2]+"</option>":"";sOpt+=getopt[i]=='le'?"<option value='le'>"+p.odata[3]+"</option>":"";sOpt+=getopt[i]=='gt'?"<option value='gt'>"+p.odata[4]+"</option>":"";sOpt+=getopt[i]=='ge'?"<option value='ge'>"+p.odata[5]+"</option>":"";sOpt+=getopt[i]=='bw'?"<option value='bw'>"+p.odata[6]+"</option>":"";sOpt+=getopt[i]=='ew'?"<option value='ew'>"+p.odata[7]+"</option>":"";sOpt+=getopt[i]=='cn'?"<option value='cn'>"+p.odata[8]+"</option>":"";};sOpt+="</select>";var sField="<input id='sval' class='search' type='text' size='20' maxlength='100'/>";var bSearch="<input id='sbut' class='buttonsearch' type='button' value='"+p.Find+"'/>";var bReset="<input id='sreset' class='buttonsearch' type='button' value='"+p.Reset+"'/>";var cnt=$("<table width='100%'><tbody><tr><td>"+cNames+"</td><td>"+sOpt+"</td><td>"+sField+"</td><td>"+bSearch+"</td><td>"+bReset+"</td></tr></tbody></table>");createModal(IDs,cnt,p,$t.grid.hDiv,$t.grid.hDiv);viewModal("#"+IDs.themodal,{modal:p.modal})
if(p.drag)DnRModal("#"+IDs.themodal,"#"+IDs.modalhead+" td.modaltext");$("#sbut","#"+IDs.themodal).click(function(){if($("#sval","#"+IDs.themodal).val()!=""){$t.p.search=true;$t.p.searchdata[p.sField]=$("option[@selected]","#snames").val();$t.p.searchdata[p.sOper]=$("option[@selected]","#sopt").val();$t.p.searchdata[p.sValue]=$("#sval","#"+IDs.modalcontent).val();if(p.dirty)$(".no-dirty-cell",$t.p.pager).addClass("dirty-cell");$t.p.page=1;$($t).trigger("reloadGrid");}});$("#sreset","#"+IDs.themodal).click(function(){if($t.p.search){$t.p.search=false;$t.p.searchdata={};$t.p.page=1;$("#sval","#"+IDs.themodal).val("");if(p.dirty)$(".no-dirty-cell",$t.p.pager).removeClass("dirty-cell");$($t).trigger("reloadGrid");}});}});},editGridRow:function(rowid,p){p=$.extend({top:0,left:0,width:0,height:0,modal:false,drag:true,closeicon:'ico-close.gif',imgpath:'',url:null,closeAfterAdd:false,clearAfterAdd:true,closeAfterEdit:false,reloadAfterSubmit:true,onInitializeForm:null,beforeInitData:null,beforeShowForm:null,afterShowForm:null,beforeSubmit:null,afterSubmit:null,onclickSubmit:null,editData:{}},$.jgrid.edit,p||{});return this.each(function(){var $t=this;if(!$t.grid||!rowid)return;if(!p.imgpath)p.imgpath=$t.p.imgpath;var gID=$("table:first",$t.grid.bDiv).attr("id");var IDs={themodal:'editmod'+gID,modalhead:'edithd'+gID,modalcontent:'editcnt'+gID};var onBeforeShow=typeof p.beforeShowForm==='function'?true:false;var onAfterShow=typeof p.afterShowForm==='function'?true:false;var onBeforeInit=typeof p.beforeInitData==='function'?true:false;if(rowid=="new"){rowid="_empty";p.caption=p.addCaption;}else{p.caption=p.editCaption};var frmgr="FrmGrid_"+gID;var frmtb="TblGrid_"+gID;if($("#"+IDs.themodal).html()!=null){$(".modaltext","#"+IDs.modalhead).text(p.caption);$("#FormError","#"+frmtb).hide();if(onBeforeInit)p.beforeInitData($("#"+frmgr));fillData(rowid,$t);if(onBeforeShow)p.beforeShowForm($("#"+frmgr));viewModal("#"+IDs.themodal,{modal:p.modal});if(rowid=="_empty")$("#pData, #nData","#"+frmtb).hide();else $("#pData, #nData","#"+frmtb).show();if(onAfterShow)p.afterShowForm($("#"+frmgr));}else{var frm=$("<form name='FormPost' id='"+frmgr+"' class='FormGrid'></form>");var tbl=$("<table id='"+frmtb+"' class='EditTable' cellspacing='0' cellpading='0' border='0'><tbody></tbody></table>");$(frm).append(tbl);$(tbl).append("<tr id='FormError' style='display:none'><td colspan='2'>"+"&nbsp;"+"</td></tr>");var valref=createData(rowid,$t,tbl);var imp=$t.p.imgpath;var bP="<img id='pData' src='"+imp+$t.p.previmg+"'/>";var bN="<img id='nData' src='"+imp+$t.p.nextimg+"'/>";var bS="<input id='sData' type='button' class='EditButton' value='"+p.bSubmit+"'/>";var bC="<input id='cData' type='button'  class='EditButton' value='"+p.bCancel+"'/>";$(tbl).append("<tr id='Act_Buttons'><td class='navButton'>"+bP+"&nbsp;"+bN+"</td><td class='EditButton'>"+bS+"&nbsp;"+bC+"</td></tr>");createModal(IDs,frm,p,$t.grid.hDiv,$t.grid.hDiv);if(typeof p.onInitializeForm==='function')p.onInitializeForm($("#"+frmgr));if(p.drag)DnRModal("#"+IDs.themodal,"#"+IDs.modalhead+" td.modaltext");if(onBeforeShow)p.beforeShowForm($("#"+frmgr));viewModal("#"+IDs.themodal,{modal:p.modal});if(onAfterShow)p.afterShowForm($("#"+frmgr));if(rowid=="_empty")$("#pData,#nData","#"+frmtb).hide();else $("#pData,#nData","#"+frmtb).show();$("#sData","#"+frmtb).click(function(e){var postdata={},ret=[true,"",""],extpost={};$("#FormError","#"+frmtb).hide();var j=0;$(".FormElement","#"+frmtb).each(function(i){var suc=true;switch($(this).get(0).type){case"checkbox":if($(this).attr("checked")){postdata[this.name]=$(this).val()}else{postdata[this.name]="";extpost[this.name]=$(this).attr("offval");}
break;case"select-one":postdata[this.name]=$("option:selected",this).val();extpost[this.name]=$("option:selected",this).text();break;case"text":case"textarea":postdata[this.name]=$(this).val();ret=checkValues($(this).val(),valref[i]);if(ret[0]==false)suc=false;break;}
j++;if(!suc)return false;});if(j==0){ret[0]=false;ret[1]="No records to process";}
if(typeof p.onclickSubmit==='function')p.editData=p.onclickSubmit(p)||{};if(ret[0])
if(typeof p.beforeSubmit==='function')ret=p.beforeSubmit(postdata,$("#"+frmgr));var gurl=p.url?p.url:$t.p.editurl;if(ret[0])
if(!gurl){ret[0]=false;ret[1]+=" No url set!";};if(ret[0]===false){$("#FormError>td","#"+frmtb).text(ret[1]);$("#FormError","#"+frmtb).show();}else{if(!p.processing){p.processing=true;$("div.loading","#"+IDs.themodal).fadeIn("fast");$(this).attr("disabled",true);postdata.oper=postdata.id=="_empty"?"add":"edit";postdata=$.extend(postdata,p.editData);$.ajax({url:gurl,type:p.mtype,data:postdata,complete:function(data,Status){if(Status!="success"){ret[0]=false;ret[1]=Status+" Status: "+data.statusText+" Error code: "+data.status}else{if(typeof p.afterSubmit==='function'){ret=p.afterSubmit(data,postdata);};}
if(ret[0]==false){$("#FormError>td","#"+frmtb).text(ret[1]);$("#FormError","#"+frmtb).show();}else{postdata=$.extend(postdata,extpost);if(postdata.id=="_empty"){if(!ret[2])ret[2]=parseInt($($t).getGridParam('records'))+1;postdata.id=ret[2];if(p.closeAfterAdd){if(p.reloadAfterSubmit)$($t).trigger("reloadGrid");else $($t).addRowData(ret[2],postdata,"first");$("#"+IDs.themodal).jqmHide();}else if(p.clearAfterAdd){if(p.reloadAfterSubmit)$($t).trigger("reloadGrid");else $($t).addRowData(ret[2],postdata,"first");$(".FormElement","#"+frmtb).each(function(i){switch($(this).get(0).type){case"checkbox":$(this).attr("checked",0);break;case"select-one":$("option",this).attr("selected","");break;case"text":case"textarea":if(this.name=='id')$(this).val("_empty");else $(this).val("");break;}});}else{if(p.reloadAfterSubmit)$($t).trigger("reloadGrid");else $($t).addRowData(ret[2],postdata,"first");}}else{if(p.reloadAfterSubmit){$($t).trigger("reloadGrid");if(!p.closeAfterEdit)$($t).setSelection(postdata.id);}
else $($t).setRowData(postdata.id,postdata);if(p.closeAfterEdit)$("#"+IDs.themodal).jqmHide();}}
p.processing=false;$("#sData","#"+frmtb).attr("disabled",false);$("div.loading","#"+IDs.themodal).fadeOut("fast");}});}}
e.stopPropagation();});$("#cData","#"+frmtb).click(function(e){$("#"+IDs.themodal).jqmHide();e.stopPropagation();});$("#nData","#"+frmtb).click(function(e){$("#FormError","#"+frmtb).hide();var npos=getCurrPos();npos[0]=parseInt(npos[0]);if(npos[0]!=-1&&npos[1][npos[0]+1]){fillData(npos[1][npos[0]+1],$t);$($t).setSelection(npos[1][npos[0]+1]);updateNav(npos[0]+1,npos[1].length-1);};return false;});$("#pData","#"+frmtb).click(function(e){$("#FormError","#"+frmtb).hide();var ppos=getCurrPos();if(ppos[0]!=-1&&ppos[1][ppos[0]-1]){fillData(ppos[1][ppos[0]-1],$t);$($t).setSelection(ppos[1][ppos[0]-1]);updateNav(ppos[0]-1,ppos[1].length-1);};return false;});};var posInit=getCurrPos();updateNav(posInit[0],posInit[1].length-1);function updateNav(cr,totr,rid){var imp=$t.p.imgpath;if(cr==0)$("#pData","#"+frmtb).attr("src",imp+"off-"+$t.p.previmg);else $("#pData","#"+frmtb).attr("src",imp+$t.p.previmg);if(cr==totr)$("#nData","#"+frmtb).attr("src",imp+"off-"+$t.p.nextimg);else $("#nData","#"+frmtb).attr("src",imp+$t.p.nextimg);};function getCurrPos(){var rowsInGrid=$($t).getDataIDs();var selrow=$("#id_g","#"+frmtb).val();var pos=$.inArray(selrow,rowsInGrid);return[pos,rowsInGrid];};function createData(rowid,obj,tb){var nm,hc,trdata,tdl,tde,cnt=0,tmp,dc,elc,retpos=[];$('#'+rowid+' td',obj.grid.bDiv).each(function(i){nm=obj.p.colModel[i].name;if(obj.p.colModel[i].editrules&&obj.p.colModel[i].editrules.edithidden==true)
hc=false;else
hc=obj.p.colModel[i].hidden==true?true:false;dc=hc?"style='display:none'":"";if(nm!=='cb'&&nm!=='subgrid'&&obj.p.colModel[i].editable===true){tmp=$(this).html().replace(/\&nbsp\;/ig,'');var opt=$.extend(obj.p.colModel[i].editoptions||{},{id:nm,name:nm});if(!obj.p.colModel[i].edittype)obj.p.colModel[i].edittype="text";elc=createEl(obj.p.colModel[i].edittype,opt,tmp);$(elc).addClass("FormElement");trdata=$("<tr "+dc+"></tr>").addClass("FormData");tdl=$("<td></td>").addClass("CaptionTD");tde=$("<td></td>").addClass("DataTD")
$(tdl).html(obj.p.colNames[i]+": ");$(tde).append(elc);trdata.append(tdl);trdata.append(tde);if(tb)$(tb).append(trdata);else $(trdata).insertBefore("#Act_Buttons");retpos[cnt]=i;cnt++;};});if(cnt>0){var idrow=$("<tr class='FormData' style='display:none'><td class='CaptionTD'>"+"&nbsp;"+"</td><td class='DataTD'><input class='FormElement' id='id_g' type='text' name='id' value='"+rowid+"'/></td></tr>")
if(tb)$(tb).append(idrow);else $(idrow).insertBefore("#Act_Buttons");}
return retpos;};function fillData(rowid,obj){var nm,hc,cnt=0,tmp;$('#'+rowid+' td',obj.grid.bDiv).each(function(i){nm=obj.p.colModel[i].name;if(obj.p.colModel[i].editrules&&obj.p.colModel[i].editrules.edithidden==true)
hc=false;else
hc=obj.p.colModel[i].hidden==true?true:false;if(nm!=='cb'&&nm!=='subgrid'&&obj.p.colModel[i].editable===true){tmp=$(this).html().replace(/\&nbsp\;/ig,'');switch(obj.p.colModel[i].edittype){case"text":case"textarea":$("#"+nm,"#"+frmtb).val(tmp);break;case"select":$("#"+nm+" option","#"+frmtb).each(function(j){if(tmp==$(this).text())this.selected=true;else this.selected=false;});break;case"checkbox":if(tmp==$("#"+nm,"#"+frmtb).val()){$("#"+nm,"#"+frmtb).attr("checked",true);$("#"+nm,"#"+frmtb).attr("defaultChecked",true);}
else{$("#"+nm,"#"+frmtb).attr("checked",false);$("#"+nm,"#"+frmtb).attr("defaultChecked","");}
break;}
if(hc)$("#"+nm,"#"+frmtb).parents("tr:first").hide();cnt++;}});if(cnt>0)$("#id_g","#"+frmtb).val(rowid);else $("#id_g","#"+frmtb).val("");return cnt;};function createEl(eltype,options,vl){var elem="";switch(eltype)
{case"textarea":elem=document.createElement("textarea");$(elem).attr(options);$(elem).html(vl);break;case"checkbox":elem=document.createElement("input");elem.type="checkbox";$(elem).attr({id:options.id,name:options.name});if(!options.value){if(vl.toLowerCase()=='on'){elem.checked=true;elem.defaultChecked=true;elem.value=vl;}else{elem.value="on";}
$(elem).attr("offval","off");}else{var cbval=options.value.split(":");if(vl==cbval[0]){elem.checked=true;elem.defaultChecked=true;}
elem.value=cbval[0];$(elem).attr("offval",cbval[1]);}
break;case"select":var so=options.value.split(";"),sv,ov;elem=document.createElement("select");$(elem).attr({id:options.id,name:options.name});for(var i=0;i<so.length;i++){sv=so[i].split(":");ov=document.createElement("option");ov.value=sv[0];ov.innerHTML=sv[1];if(sv[1]==vl)ov.selected="selected";elem.appendChild(ov);}
break;case"text":elem=document.createElement("input");elem.type="text";elem.value=vl;$(elem).attr(options);break;}
return elem;};function checkValues(val,valref){if(valref>=0)
var edtrul=$t.p.colModel[valref].editrules;if(edtrul){if(edtrul.required==true){if(val.match(/^s+$/)||val=="")return[false,$t.p.colNames[valref]+": "+$.jgrid.edit.msg.required,""];}
if(edtrul.number==true){if(isNaN(val))return[false,$t.p.colNames[valref]+": "+$.jgrid.edit.msg.number,""];}
if(edtrul.minValue&&!isNaN(edtrul.minValue)){if(parseFloat(val)<parseFloat(edtrul.minValue))return[false,$t.p.colNames[valref]+": "+$.jgrid.edit.msg.minValue+" "+edtrul.minValue,""];}
if(edtrul.maxValue&&!isNaN(edtrul.maxValue)){if(parseFloat(val)>parseFloat(edtrul.maxValue))return[false,$t.p.colNames[valref]+": "+$.jgrid.edit.msg.maxValue+" "+edtrul.maxValue,""];}
if(edtrul.email==true){var filter=/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;if(!filter.test(val)){return[false,$t.p.colNames[valref]+": "+$.jgrid.edit.msg.email,""];}}}
return[true,"",""];};});},delGridRow:function(rowids,p){p=$.extend({top:0,left:0,width:200,height:85,modal:false,drag:true,closeicon:'ico-close.gif',imgpath:'',reloadAfterSubmit:true,beforeShowForm:null,afterShowForm:null,beforeSubmit:null,onclickSubmit:null,afterSubmit:null,onclickSubmit:null,delData:{}},$.jgrid.del,p||{});return this.each(function(){var $t=this;if(!$t.grid)return;if(!rowids)return;if(!p.imgpath)p.imgpath=$t.p.imgpath;var onBeforeShow=typeof p.beforeShowForm==='function'?true:false;var onAfterShow=typeof p.afterShowForm==='function'?true:false;if(isArray(rowids))rowids=rowids.join();var gID=$("table:first",$t.grid.bDiv).attr("id");var IDs={themodal:'delmod'+gID,modalhead:'delhd'+gID,modalcontent:'delcnt'+gID};var dtbl="DelTbl_"+gID;if($("#"+IDs.themodal).html()!=null){$("#DelData>td","#"+dtbl).text(rowids);$("#DelError","#"+dtbl).hide();if(onBeforeShow)p.beforeShowForm($("#"+dtbl));viewModal("#"+IDs.themodal,{modal:p.modal});if(onAfterShow)p.afterShowForm($("#"+dtbl));}else{var tbl=$("<table id='"+dtbl+"' class='DelTable'><tbody></tbody></table>");$(tbl).append("<tr id='DelError' style='display:none'><td >"+"&nbsp;"+"</td></tr>");$(tbl).append("<tr id='DelData' style='display:none'><td >"+rowids+"</td></tr>");$(tbl).append("<tr><td >"+p.msg+"</td></tr>");var bS="<input id='dData' type='button' value='"+p.bSubmit+"'/>";var bC="<input id='eData' type='button' value='"+p.bCancel+"'/>";$(tbl).append("<tr><td class='DelButton'>"+bS+"&nbsp;"+bC+"</td></tr>");createModal(IDs,tbl,p,$t.grid.hDiv,$t.grid.hDiv);if(p.drag)DnRModal("#"+IDs.themodal,"#"+IDs.modalhead+" td.modaltext");$("#dData","#"+dtbl).click(function(e){var ret=[true,""];var postdata=$("#DelData>td","#"+dtbl).text();if(typeof p.onclickSubmit==='function')p.delData=p.onclickSubmit(p)||{};if(typeof p.beforeSubmit==='function')ret=p.beforeSubmit(postdata);var gurl=p.url?p.url:$t.p.editurl;if(!gurl){ret[0]=false;ret[1]+=" No url set!";};if(ret[0]===false){$("#DelError>td","#"+dtbl).text(ret[1]);$("#DelError","#"+dtbl).show();}else{if(!p.processing){p.processing=true;$("div.loading","#"+IDs.themodal).fadeIn("fast");$(this).attr("disabled",true);var postd=$.extend({oper:"del",id:postdata},p.delData);$.ajax({url:gurl,type:p.mtype,data:postd,complete:function(data,Status){if(Status!="success"){ret[0]=false;ret[1]=Status+" Status: "+data.statusText+" Error code: "+data.status}else{if(typeof p.afterSubmit==='function'){ret=p.afterSubmit(data,postdata);};}
if(ret[0]==false){$("#DelError>td","#"+dtbl).text(ret[1]);$("#DelError","#"+dtbl).show();}else{if(p.reloadAfterSubmit)$($t).trigger("reloadGrid");else{var toarr=[];toarr=postdata.split(",");for(var i=0;i<toarr.length;i++){$($t).delRowData(toarr[i]);};$t.p.selrow=null;$t.p.selarrrow=[];}}
p.processing=false;$("#dData","#"+dtbl).attr("disabled",false);$("div.loading","#"+IDs.themodal).fadeOut("fast");if(ret[0])$("#"+IDs.themodal).jqmHide();}});}}
return false;});$("#eData","#"+dtbl).click(function(e){$("#"+IDs.themodal).jqmHide();return false;});if(onBeforeShow)p.beforeShowForm($("#"+dtbl));viewModal("#"+IDs.themodal,{modal:p.modal});if(onAfterShow)p.afterShowForm($("#"+dtbl));}});},navGrid:function(elem,o,pEdit,pAdd,pDel,pSearch){o=$.extend({edit:true,editicon:"row_edit.gif",add:true,addicon:"row_add.gif",del:true,delicon:"row_delete.gif",search:true,searchicon:"find.gif",refresh:true,refreshicon:"refresh.gif",position:"left",closeicon:"ico-close.gif"},$.jgrid.nav,o||{});return this.each(function(){var alertIDs={themodal:'alertmod',modalhead:'alerthd',modalcontent:'alertcnt'};var $t=this;if(!$t.grid)return;if($("#"+alertIDs.themodal).html()==null){var vwidth;var vheight;if(typeof window.innerWidth!='undefined'){vwidth=window.innerWidth,vheight=window.innerHeight}else if(typeof document.documentElement!='undefined'&&typeof document.documentElement.clientWidth!='undefined'&&document.documentElement.clientWidth!=0){vwidth=document.documentElement.clientWidth,vheight=document.documentElement.clientHeight}else{vwidth=1024;vheight=768}
createModal(alertIDs,"<div>"+o.alerttext+"</div>",{imgpath:$t.p.imgpath,closeicon:o.closeicon,caption:o.alertcap,top:vheight/2-25,left:vwidth/2-100,width:200,height:50},$t.grid.hDiv,$t.grid.hDiv,true);DnRModal("#"+alertIDs.themodal,"#"+alertIDs.modalhead);}
var navTbl=$("<table cellspacing='0' cellpadding='0' border='0' class='navtable'><tbody></tbody></table>").height(20);var trd=document.createElement("tr");$(trd).addClass("nav-row");var imp=$t.p.imgpath;var tbd;if(o.edit){tbd=document.createElement("td");$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});trd.appendChild(tbd);tbd=document.createElement("td");tbd.title=o.edittitle||"";$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.editicon+"'/></td><td valign='center'>"+o.edittext+"&nbsp;</td></tr></table>")
$(tbd).css("cursor","pointer").addClass("nav-button").click(function(){var sr=$($t).getGridParam('selrow');if(sr)$($t).editGridRow(sr,pEdit||{});else viewModal("#"+alertIDs.themodal);return false;}).hover(function(){$(this).addClass("nav-hover");},function(){$(this).removeClass("nav-hover");});trd.appendChild(tbd);tbd=null;}
if(o.add){tbd=document.createElement("td");$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});trd.appendChild(tbd);tbd=document.createElement("td");tbd.title=o.addtitle||"";$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.addicon+"'/></td><td>"+o.addtext+"&nbsp;</td></tr></table>").css("cursor","pointer").addClass("nav-button").click(function(){if(typeof o.addfunc=='function'){o.addfunc();}else{$($t).editGridRow("new",pAdd||{});}
return false;}).hover(function(){$(this).addClass("nav-hover");},function(){$(this).removeClass("nav-hover");});trd.appendChild(tbd);tbd=null;}
if(o.del){tbd=document.createElement("td");$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});trd.appendChild(tbd);tbd=document.createElement("td");tbd.title=o.deltitle||"";$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.delicon+"'/></td><td>"+o.deltext+"&nbsp;</td></tr></table>").css("cursor","pointer").addClass("nav-button").click(function(){var dr;if($t.p.multiselect){dr=$($t).getGridParam('selarrrow');if(dr.length==0)dr=null;}
else dr=$($t).getGridParam('selrow');if(dr)$($t).delGridRow(dr,pDel||{});else viewModal("#"+alertIDs.themodal);return false;}).hover(function(){$(this).addClass("nav-hover");},function(){$(this).removeClass("nav-hover");});trd.appendChild(tbd);tbd=null;}
if(o.search){tbd=document.createElement("td");$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});trd.appendChild(tbd);tbd=document.createElement("td");if($(elem)[0]==$t.p.pager[0])pSearch=$.extend(pSearch,{dirty:true});tbd.title=o.searchtitle||"";$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td class='no-dirty-cell'><img src='"+imp+o.searchicon+"'/></td><td>"+o.searchtext+"&nbsp;</td></tr></table>").css({cursor:"pointer"}).addClass("nav-button").click(function(){$($t).searchGrid(pSearch||{});return false;}).hover(function(){$(this).addClass("nav-hover");},function(){$(this).removeClass("nav-hover");});trd.appendChild(tbd);tbd=null;}
if(o.refresh){tbd=document.createElement("td");$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});trd.appendChild(tbd);tbd=document.createElement("td");tbd.title=o.refreshtitle||"";var dirtycell=($(elem)[0]==$t.p.pager[0])?true:false;$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.refreshicon+"'/></td><td>"+o.refreshtext+"&nbsp;</td></tr></table>").css("cursor","pointer").addClass("nav-button").click(function(){$t.p.search=false;$t.p.page=1;$($t).trigger("reloadGrid");if(dirtycell)$(".no-dirty-cell",$t.p.pager).removeClass("dirty-cell");if(o.search){var gID=$("table:first",$t.grid.bDiv).attr("id");$("#sval",'#srchcnt'+gID).val("");}
return false;}).hover(function(){$(this).addClass("nav-hover");},function(){$(this).removeClass("nav-hover");});trd.appendChild(tbd);tbd=null;}
if(o.position=="left"){$(navTbl).append(trd).addClass("nav-table-left");}else{$(navTbl).append(trd).addClass("nav-table-right");}
$(elem).prepend(navTbl);});},navButtonAdd:function(elem,p){p=$.extend({caption:"newButton",buttonimg:'',onClickButton:null,position:"last"},p||{});return this.each(function(){if(!this.grid)return;if(elem.indexOf("#")!=0)elem="#"+elem;var findnav=$(".navtable",elem)[0];if(findnav){var tdb,tbd1;var tbd1=document.createElement("td");$(tbd1).append("&nbsp;").css({border:"none",padding:"0px"});var trd=$("tr:eq(0)",findnav)[0];if(p.position!='first'){trd.appendChild(tbd1);}
tbd=document.createElement("td");tbd.title=p.caption||"";var im=(p.buttonimg)?"<img src='"+p.buttonimg+"'/>":"&nbsp;";$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td>"+im+"</td><td>"+p.caption+"&nbsp;</td></tr></table>").css("cursor","pointer").addClass("nav-button").click(function(e){if(typeof p.onClickButton=='function')p.onClickButton();e.stopPropagation();return false;}).hover(function(){$(this).addClass("nav-hover");},function(){$(this).removeClass("nav-hover");});if(p.position!='first'){trd.appendChild(tbd);}else{$(trd).prepend(tbd);$(trd).prepend(tbd1);}
tbd=null;tbd1=null;}});},GridToForm:function(rowid,formid){return this.each(function(){var $t=this;if(!$t.grid)return;var rowdata=$($t).getRowData(rowid);if(rowdata){for(var i in rowdata){$("[name="+i+"]",formid).val(rowdata[i]);}}});},FormToGrid:function(rowid,formid){return this.each(function(){var $t=this;if(!$t.grid)return;var fields=$(formid).serializeArray();var griddata={};$.each(fields,function(i,field){griddata[field.name]=field.value;});$($t).setRowData(rowid,griddata);});}});})(jQuery);var showModal=function(h){h.w.show();};var closeModal=function(h){h.w.hide();if(h.o)h.o.remove();};function createModal(aIDs,content,p,insertSelector,posSelector,appendsel){var clicon=p.imgpath?p.imgpath+'/'+p.closeicon:p.closeicon;var mw=document.createElement('div');jQuery(mw).addClass("modalwin").attr("id",aIDs.themodal);var mh=jQuery('<div id="'+aIDs.modalhead+'"><table width="100%"><tbody><tr><td class="modaltext">'+p.caption+'</td> <td align="right"><a href="javascript:void(0);" class="jqmClose">'+(clicon!=''?'<img src="'+clicon+'" border="0"/>':'X')+'</a></td></tr></tbody></table> </div>').addClass("modalhead");var mc=document.createElement('div');jQuery(mc).addClass("modalcontent").attr("id",aIDs.modalcontent);jQuery(mc).append(content);mw.appendChild(mc);var loading=document.createElement("div");jQuery(loading).addClass("loading").html(p.processData||"");jQuery(mw).prepend(loading);jQuery(mw).prepend(mh);jQuery(mw).addClass("jqmWindow");if(p.drag){mf=document.createElement("div");mf=jQuery("<img  class='jqResize' src='"+p.imgpath+"resize.gif'/>");jQuery(mw).append(mf);}
if(appendsel==true)jQuery('body').append(mw);else jQuery(mw).insertBefore(insertSelector);if(p.left==0&&p.top==0){var pos=[];pos=findPos(posSelector);p.left=pos[0]+4;p.top=pos[1]+4;}
if(p.width==0||!p.width)p.width=300;if(p.height==0||!p.width)p.height=200;if(!p.zIndex)p.zIndex=950;jQuery(mw).css({top:p.top+"px",left:p.left+"px",width:p.width+"px",height:p.height+"px",zIndex:p.zIndex});return false;};function viewModal(selector,o){o=jQuery.extend({toTop:true,overlay:10,modal:false,drag:true,onShow:showModal,onHide:closeModal},o||{});jQuery(selector).jqm(o).jqmShow();return false;};function DnRModal(modwin,handler){jQuery(handler).css('cursor','move');jQuery(modwin).jqDrag(handler).jqResize(".jqResize");return false;};function findPos(obj){var curleft=curtop=0;if(obj.offsetParent){do{curleft+=obj.offsetLeft;curtop+=obj.offsetTop;}while(obj=obj.offsetParent);}
return[curleft,curtop];};function isArray(obj){if(obj.constructor.toString().indexOf("Array")==-1)
return false;else
return true;};