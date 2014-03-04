/**
* Bismillaahirrohmaanirrohiim
* Dhtml MenuBar DOM
*
* @package doiMenu
* @version 1.5.3
* @author  Donna Iwan Setiawan
* @Copyright (C) 2003 - 2004 Donna Iwan Setiawan
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
*/
var _browser=new TBrowser();var _arRegisterMenu=new Array();var _arRegisterMenuIndex=-1;var _arTriggerMenu=new Array();var _arRegisterPopID=new Array();var _arRegisterPopIndex=new Array();var _arRegisterTriggerPopID=new Array();var _arRegisterTriggerPopIndex=new Array();var _arMMClick=new Array();function Initialize()
{ 
var byk=_arRegisterMenuIndex;for(var i=0;i<=byk;i++)
_arRegisterMenu[i].Init();}
function InitResize()
{
var byk=_arRegisterMenuIndex;for(var i=0;i<=byk;i++)
_arRegisterMenu[i].Resize();	
}
function TBrowser()
{
this._name='';this._version='';this._os='';}
var detect=navigator.userAgent.toLowerCase();var total,thestring;if(checkIt('konqueror'))
{
_browser._name="Konqueror";_browser._os="Linux";}
else if(checkIt('safari')) _browser._name="Safari";else if(checkIt('omniweb')) _browser._name="OmniWeb";else if(checkIt('opera')) _browser._name="Opera";else if(checkIt('webtv')) _browser._name="WebTV";else if(checkIt('icab')) _browser._name="iCab";else if(checkIt('msie')) _browser._name="IE";else if(!checkIt('compatible'))
{
_browser._name="Netscape";_browser._version=detect.charAt(8);}
else _browser._name="none";if(_browser._version=='') _browser._version=detect.charAt(place+thestring.length);if(_browser._os=='')
{
if(checkIt('linux')) _browser._os="Linux";else if(checkIt('x11')) _browser._os="Unix";else if(checkIt('mac')) _browser._os="Mac";else if(checkIt('win')) _browser._os="Windows";else _browser._os="none";}
function checkIt(string)
{
place=detect.indexOf(string)+1;thestring=string;return place;}
function TMainMenu(name,direction)
{
_arRegisterMenuIndex++;_arRegisterMenu[_arRegisterMenuIndex]=this;_arRegisterPopID[_arRegisterMenuIndex]=new Array();_arRegisterPopIndex[_arRegisterMenuIndex]=-1;_arRegisterTriggerPopID[_arRegisterMenuIndex]=new Array();_arRegisterTriggerPopIndex[_arRegisterMenuIndex]=-1;_arMMClick[_arRegisterMenuIndex]=true;this._popOnClick=false;this._expandIcon=new TExpandIcon();this._expandIcon._create=false;this._registerMenuIndex=_arRegisterMenuIndex;this._uniqueID=0;this._name=name;this._id='_'+name+'ID';this._index=_arRegisterMenuIndex; 
this._correction=new TCorrection();this._parent=null;this._width='auto';this._direction=direction;if(this._direction=='horizontal')
{
this._expandIcon._symbol='&#9660;';}
else
{
this._expandIcon._symbol='&#9658;';}
this._position='relative';this._top=0;this._left=0;this._cellSpacing=0;this._itemHeight='auto';this._itemWidth='auto';this._background=new TBackground();this._background._color='buttonface';this._pop=new TPopParameter();this._pop._mmName=this._name;this._pop._index=this._index;this._shadow=new TShadow();this._font=new TFont();this._font._family='tahoma,verdana,sans-serif,arial';this._font._size='8pt';this._itemIndex=-1;this._items=new Array();this._itemText=new TText();this._itemText._color='black';this._itemBack=new TBackground();this._itemTextHL=new TText();this._itemTextHL._color='white';                                
this._itemBackHL=new TBackground();this._itemBackHL._color='#B6BDD2';this._border=new TBorder();this._itemBorder=new TBorder();this._itemBorderHL=new TBorder();this._itemBorderHL._top='1px navy solid';this._itemBorderHL._right='1px navy solid';this._itemBorderHL._bottom='1px navy solid';this._itemBorderHL._left='1px navy solid';this._itemTextClick=new TText();this._itemTextClick._color='white';this._itemBackClick=new TBackground();this._itemBackClick._color='#B6BDD2';this._itemBorderClick=new TBorder();this._itemBorderClick._top='1px navy solid';this._itemBorderClick._right='1px navy solid';this._itemBorderClick._bottom='1px navy solid';this._itemBorderClick._left='1px navy solid';this._hideObject=new THiddenObject();this._pop._header=new TPopHeader();this._header=new TPopHeader();this._header._font._size='6pt';this._type='';this._initialTop=0;this._initialLeft=0;this._headerClickState=false;this._headerText='Click Here';this.SetHeaderText=SetMMHeaderText;this.Add=AddItem;this.Build=BuildMenu;this.BuildStyle=BuildStyle;this.Draw=DrawMenu;this.Init=InitMenu;this.SetExpandIcon=SetExpandIcon;this.SetParent=SetParent;this.SetType=SetMenuType;this.SetPopOnClick=SetPopOnClick;this.SetWidth=SetWidth;this.SetBorder=SetBorder;this.SetBorderTop=SetBorderTop;this.SetBorderRight=SetBorderRight;this.SetBorderBottom=SetBorderBottom;this.SetBorderLeft=SetBorderLeft;this.SetItemDimension=SetItemDimension;this.SetItemBorder=SetItemBorder;this.SetItemBorderTop=SetItemBorderTop;this.SetItemBorderRight=SetItemBorderRight;this.SetItemBorderBottom=SetItemBorderBottom;this.SetItemBorderLeft=SetItemBorderLeft;this.SetItemBorderHL=SetItemBorderHL;this.SetItemBorderTopHL=SetItemBorderTopHL;this.SetItemBorderRightHL=SetItemBorderRightHL;this.SetItemBorderBottomHL=SetItemBorderBottomHL;this.SetItemBorderLeftHL=SetItemBorderLeftHL;this.SetItemBorderClick=SetItemBorderClick;this.SetItemBorderTopClick=SetItemBorderTopClick;this.SetItemBorderRightClick=SetItemBorderRightClick;this.SetItemBorderBottomClick=SetItemBorderBottomClick;this.SetItemBorderLeftClick=SetItemBorderLeftClick;this.SetShadow=SetShadow;this.SetFont=SetFont;this.SetBackground=SetBackground;this.SetDirection=SetDirection;this.SetPosition=SetPosition;this.SetCorrection=SetCorrection;this.SetCellSpacing=SetCellSpacing;this.SetItemText=SetItemText;this.SetItemTextHL=SetItemTextHL;this.SetItemTextClick=SetItemTextClick;this.SetItemBackground=SetItemBackground;this.SetItemBackgroundHL=SetItemBackgroundHL;this.SetItemBackgroundClick=SetItemBackgroundClick;this.Resize=ResizeMenu;}
function ResizeMenu()
{
if(this._shadow._create)
{
if(document.all)
{
var elm=document.all(this._id);var shadowElm=document.all('sh_'+this._id);}
else if(document.getElementById)
{
var elm=document.getElementById(this._id);var shadowElm=document.getElementById('sh_'+this._id);}
var posY=findPosY(elm);var posX=findPosX(elm);if(_browser._name=='IE')
{
shadowElm.style.width=elm.offsetWidth+10;shadowElm.childNodes[0].style.width=elm.offsetWidth;shadowElm.style.top=posY;shadowElm.style.left=posX;}
else
{
shadowElm.style.width=elm.offsetWidth;shadowElm.style.top=posY+this._shadow._distance;shadowElm.style.left=posX+this._shadow._distance;}
}
}
function SetDirection(dDirection)
{
dDirection=dDirection.toLowerCase();switch(dDirection)
{
case 'vertical':
this._direction='vertical';break;default:
this._direction='horizontal';}
}
function SetMMHeaderText(dText)
{
this._headerText=dText;}
function SetMenuType(dType)
{
dType=dType.toLowerCase();switch(dType)
{
case 'float':
this._type='float';break;case 'free':
this._type='free';break;default :
this._type='';}
}
function TPopHeader()
{
this._background=new TBackground();this._background._color='black';this._itemText=new TText();this._itemText._weight='bold';this._itemText._color='white';this._font=new TFont();this._font._family='tahoma,verdana,sans-serif,arial';this._font._size='8pt';this.SetBackground=SetBackground;this.SetFont=SetFont;this.SetText=SetItemText;}
function THiddenObject()
{
this._itemIndex=-1;this._items=new Array();this._isHide=false;this._isShow=true;this.Add=AddItem;this.Hide=HideObject;this.Show=ShowObject;this.Clear=ClearObject;}
function ClearObject()
{
if(this._itemIndex >-1)
{
var iMax=this._itemIndex+1;for(var i=0;i<iMax;i++)
{
delete this._items[i];}
this._isHide=false;this._itemIndex=-1;}
}
function HideObject()
{
if(this._itemIndex >-1)
{
var iMax=this._itemIndex+1;for(var i=0;i<iMax;i++)
{
if(document.all)
document.all(this._items[i]).style.visibility="hidden";else if(document.getElementById)
document.getElementById(this._items[i]).style.visibility="hidden";   
}
this._isHide=true;}
}
function ShowObject()
{
if(this._isHide)
{	
var iMax=this._itemIndex+1;for(var i=0;i<iMax;i++)
{
if(document.all)
document.all(this._items[i]).style.visibility="visible";else if(document.getElementById)
document.getElementById(this._items[i]).style.visibility="visible";   
}
}
}
function SetPopOnClick(dBool)
{
if(dBool)
{
this._popOnClick=true;_arMMClick[this._registerMenuIndex]=false;}else
{
this._popOnClick=false;_arMMClick[this._registerMenuIndex]=true;}
}
function TPopMenu(label,icon,clickType,clickParam,status)
{
this._id='';this._parent=null;this._parentPop=null;this._label='&nbsp;'+label;this._top=0;this._left=0;this._status=status;this._tmpIcon=icon;this._icon="";this._itemIndex=-1;this._items=new Array();switch(clickType)
{
case 'function':
this._eClick=clickParam;break;case 'f':
this._eClick=clickParam;break;case 'address':
this._eClick="_openURL('"+clickParam+"')";break;case 'a':
this._eClick="_openURL('"+clickParam+"')";break;default:
this._eClick='';}
this._type=''; 
this.Add=AddItem;this.Draw=DrawPopMenu;this.Init=InitPopMenu;this.SetType=SetPopType;this.SetParent=SetParent;}
function SetPopType(dType)
{
dType=dType.toLowerCase();switch(dType)
{
case 'header':
this._type='h';break;case 'h':
this._type='h';break;default:
this._type='';}
}
function TPopParameter()
{
this._index=-1;this._mmName='';this._padding='1px 1px 1px 1px';this._separator=new TSeparator();this._expandIcon=new TExpandIcon();this._correction=new TCorrection();this._font=new TFont();this._font._family='tahoma,verdana,sans-serif,arial';this._font._size='8pt';this._alpha=100;this._itemWidth=200; 
this._itemHeight='auto';this._itemBorder=new TBorder();this._itemBorder._top='0px none solid';this._itemBorder._right='0px none solid';this._itemBorder._bottom='0px none solid';this._itemBorder._left='0px none solid';this._itemPadding='1px 1px 1px 1px'	;this._itemText=new TText();this._itemBack=new TBackground();this._itemBorderHL=new TBorder();this._itemBorderHL._top='1px navy solid';this._itemBorderHL._right='1px navy solid';this._itemBorderHL._bottom='1px navy solid';this._itemBorderHL._left='1px navy solid';this._itemPaddingHL='0px 0px 0px 0px';this._itemTextHL=new TText();this._itemTextHL._color='white';this._itemBackHL=new TBackground();this._itemBackHL._color='#B6BDD2';this._background=new TBackground();this._background._color='whitesmoke';this._border=new TBorder();this._border._top='1px black solid';this._border._right='1px black solid';this._border._bottom='1px black solid';this._border._left='1px black solid';this._shadow=new TShadow();this._header=new TPopHeader();this._timeOut=750; 
this.SetAlpha=SetAlpha;this.SetBorder=SetBorder;this.SetPadding=SetPadding;this.SetPaddings=SetPaddings;this.SetBorderTop=SetBorderTop;this.SetBorderRight=SetBorderRight;this.SetBorderBottom=SetBorderBottom;this.SetBorderLeft=SetBorderLeft;this.SetItemDimension=SetItemDimension;this.SetItemBorder=SetItemBorder;this.SetItemPadding=SetItemPadding;this.SetItemPaddingHL=SetItemPaddingHL;this.SetItemPaddings=SetItemPaddings;this.SetItemPaddingsHL=SetItemPaddingsHL;this.SetItemBorderTop=SetItemBorderTop;this.SetItemBorderRight=SetItemBorderRight;this.SetItemBorderBottom=SetItemBorderBottom;this.SetItemBorderLeft=SetItemBorderLeft;this.SetItemBorderHL=SetItemBorderHL;this.SetItemBorderTopHL=SetItemBorderTopHL;this.SetItemBorderRightHL=SetItemBorderRightHL;this.SetItemBorderBottomHL=SetItemBorderBottomHL;this.SetItemBorderLeftHL=SetItemBorderLeftHL;this.SetShadow=SetShadow;this.SetFont=SetFont;this.SetBackground=SetBackground;this.SetCorrection=SetCorrection;this.SetExpandIcon=SetExpandIcon;this.SetSeparator=SetSeparator;this.SetDelay=SetDelay;this.SetItemText=SetItemText;this.SetItemTextHL=SetItemTextHL;this.SetItemBackground=SetItemBackground;this.SetItemBackgroundHL=SetItemBackgroundHL;}
function SetAlpha(dAlpha)
{
dAlpha=parseInt(dAlpha);this._alpha=dAlpha;}
function TCorrection()
{
this._top=0;this._left=0;}
function TText()
{
this._color='black';this._align='left';this._decoration='none';this._whiteSpace='normal';this._weight='normal';}
function TShadow()
{
this._create=false;this._color='black';this._distance=3; 
}
function TSeparator()
{
this._align='center' 
this._width=200;this._margin="0px 0px 0px 0px";this._border=new TBorder(); 
this._border._top='1px black solid';this._border._bottom='1px white solid';}
function TFont()
{
this._family='arial,times,sans-serif';this._size='8pt';}
function TBackground()
{
this._image='none'; 
this._repeat='no-repeat';this._color='';this._position='top left';}
function TExpandIcon()
{
this._create=true;this._symbol='&#9658;';this._font=new TFont();this._font._size='6pt';}
function TBorder()
{
this._top='1px gray solid';this._right='1px gray solid';this._bottom='1px gray solid';this._left='1px gray solid';}
function BuildMenu()
{
var result="";var level=-1;this.SetParent(this);result+=this.BuildStyle();result+=this.Draw(level++);level++;for(var i=0;i<=this._itemIndex;i++)
{
result+=this._items[i].Draw(level);result+=BuildPopUpMenu(this._items[i],level);}
document.write(result);}
function InitMenu()
{
if(document.all)
var el_menu=document.all(this._id);else if(document.getElementById)
var el_menu=document.getElementById(this._id);this._position=this._position.toLowerCase();if(this._position=='absolute')
{
el_menu.style.top=this._top;el_menu.style.left=this._left;}else
{
this._top=findPosY(el_menu);this._left=findPosX(el_menu);}
el_menu.style.zIndex=100;if((this._type=='free') && (this._position=='absolute'))
{
if(document.all)
var fr_el=document.all('fr_'+this._id);else if(document.getElementById)
var fr_el=document.getElementById('fr_'+this._id);fr_el.style.top=this._top+'px';fr_el.style.left=this._left+'px';fr_el.style.width=el_menu.offsetWidth;fr_el.style.height=el_menu.offsetHeight;fr_el.style.zIndex=100;}
if(this._shadow._create)
{
if(document.all)
var sh_el=document.all('sh_'+this._id);else if(document.getElementById)
var sh_el=document.getElementById('sh_'+this._id);if(_browser._name=='IE')
{
sh_el.style.top=this._top+'px';sh_el.style.left=this._left+'px';sh_el.style.width=el_menu.offsetWidth+10+'px';sh_el.style.height=el_menu.offsetHeight+10+'px';sh_el.childNodes[0].style.width=el_menu.offsetWidth+'px';sh_el.childNodes[0].style.height=el_menu.offsetHeight+'px';sh_el.childNodes[0].style.backgroundColor=this._shadow._color;sh_el.style.visibility='visible';}
else
{
if(_browser._name=='Konqueror' && this._position=='relative')
sh_el.style.visibility='hidden';else
{
sh_el.style.top=this._top+this._shadow._distance+'px';sh_el.style.left=this._left+this._shadow._distance+'px';sh_el.style.width=el_menu.offsetWidth+'px';sh_el.style.height=el_menu.offsetHeight+'px';sh_el.style.backgroundColor=this._shadow._color;sh_el.style.visibility='visible';}
}
sh_el.style.zIndex=0;}
for(var i=0;i<=this._itemIndex;i++)
{
if(document.all)
var el_menuitem=document.all('pr_'+this._items[i]._id);else if(document.getElementById)
var el_menuitem=document.getElementById('pr_'+this._items[i]._id);if(this._items[i]._itemIndex >-1)
{			
if(document.all)
var el_pop=document.all(this._items[i]._id);else if(document.getElementById)
var el_pop=document.getElementById(this._items[i]._id);el_pop.style.zIndex=102;if(this._items[i]._parent._pop._shadow._create)
{
if(document.all)
var sh_el_pop=document.all('sh_'+this._items[i]._id);else if(document.getElementById)
var sh_el_pop=document.getElementById('sh_'+this._items[i]._id);if(_browser._name=='IE')
{ 
sh_el_pop.style.width=el_pop.offsetWidth+10+'px';sh_el_pop.style.height=el_pop.offsetHeight+10+'px';sh_el_pop.childNodes[0].style.width=el_pop.offsetWidth+'px';sh_el_pop.childNodes[0].style.height=el_pop.offsetHeight+'px';sh_el_pop.childNodes[0].style.backgroundColor=this._items[i]._parent._pop._shadow._color;}
else
{ 
sh_el_pop.style.width=el_pop.offsetWidth+'px';sh_el_pop.style.height=el_pop.offsetHeight+'px';sh_el_pop.style.backgroundColor=this._items[i]._parent._pop._shadow._color;}
sh_el_pop.style.zIndex=101;}
this._items[i].Init(102);}
}
el_menu.style.visibility="visible";if((this._type=='float') && (this._position=='absolute'))
{
this._initialTop=this._top;this._initialLeft=this._left;_floatingMMEffect(this);}
}
function SetParent(parent)
{
for(var i=0;i<=this._itemIndex;i++)
{
this._items[i]._parent=parent;this._items[i]._parentPop=this;this._items[i]._id='_'+parent._name+'-'+parent._uniqueID+"ID";var iIcon=parseInt(this._items[i]._tmpIcon);if((iIcon > 0))
{
this._items[i]._icon='<td style="padding-left:'+iIcon+'px;">';}
else
{
switch(this._items[i]._tmpIcon)
{
case "":
this._items[i]._icon='<td style="padding-left:24px;">';break;case '0':
this._items[i]._icon='<td>';break;default:
this._items[i]._icon='<td class="TIcon'+this._items[i]._parent._index+'" style="background: #C0C0C0; padding: 3px; padding-right: 0px;">'+this._items[i]._tmpIcon+
//<img class="TIcon'+this._items[i]._parent._index+'" src="'+this._items[i]._tmpIcon+'" width="16px" />
'</td><td>';}
}
parent._uniqueID++;this._items[i].SetParent(parent);}
}
function DrawMenu(level)
{
var result="";if(this._shadow._create)
{
if(_browser._name=='IE')
result+='<div style="position:absolute;visibility:hidden;filter: blur( direction=135, strength='+this._shadow._distance+', add=1);" id="sh_'+this._id+'" align="left"><div></div></div>';else
result+='<div style="position:absolute;visibility:hidden;" id="sh_'+this._id+'"></div>';}
if((this._type=='free') && (this._position=='absolute'))
{
result+='<table class="TMenu'+this._index+'" id="fr_'+this._id+'"';result+=' cellspacing="'+this._cellSpacing+'" style="position:absolute;visibility:hidden;">';result+='<tr><td class="TMMHeader'+this._index+'"';result+=' onclick="onMMHeaderClick(event,'+this._name+')"';result+='>'+this._headerText+'</td></tr>';result+='<tr><td> </td></tr>';result+='</table>';}
result+='<table class="TMenu'+this._index+'" id="'+this._id+'"';result+=' cellspacing="'+this._cellSpacing+'">';if(this._direction=='horizontal')
{
if(this._itemIndex >-1)
{
if((this._type=='free') && (this._position=='absolute'))
{
var colSpan=this._itemIndex+1;result+='<tr><td colspan="'+colSpan+'" class="TMMHeader'+this._index+'"';result+=' onclick="onMMHeaderClick(event,'+this._name+')">'+this._headerText+'</td></tr>';}			
}	
result+='<tr>';}
else
{
if((this._type=='free') && (this._position=='absolute'))
{
result+='<td class="TMMHeader'+this._index+'"';result+=' onclick="onMMHeaderClick(event,'+this._name+')">'+this._headerText+'</td>';}
}
if(this._itemIndex >-1)
{
var isExpandIcon=false;for(var i=0;i<=this._itemIndex;i++)
{
var result1='';var expandHTML='';result1+='<td nowrap class="TMenuItem'+this._index+'" id="pr_'+this._items[i]._id+'"';if(this._items[i]._itemIndex >-1)
{
result1+=' onmouseover="onMainMOver(event,this,\''+this._items[i]._id+'\','+level+','+this._name+',\''+escape(this._items[i]._status)+'\')"';if(this._popOnClick)
result1+='onclick="onMainClick(event,this,\''+this._items[i]._id+'\','+this._name+')"';result1+=' onmouseout="onMainMOut(event,this,\''+this._items[i]._id+'\','+this._name+')"';if(this._items[i]._parent._expandIcon._create)
{
expandHTML='<td class="TMMExpand'+this._items[i]._parent._index+'">'+this._items[i]._parent._expandIcon._symbol+'</td>';isExpandIcon=true;}
}
else
{
result1+=' onmouseover="onMainMOver(event,this,\'\','+level+','+this._name+',\''+escape(this._items[i]._status)+'\')"';result1+=' onmouseout="onMainMOut(event,this,\'\','+this._name+')"';result1+=' onclick="'+this._items[i]._eClick+'"';}
var iIcon=parseInt(this._items[i]._tmpIcon);if((iIcon > 0))
{
this._items[i]._icon='<td style="padding-left:'+iIcon+'px;">';}
else
{
switch(this._items[i]._tmpIcon)
{
case "":
if(isExpandIcon && expandHTML=='')
this._items[i]._icon='<td style="padding-left:30px;padding-right:16px">';else
this._items[i]._icon='<td style="padding-left:30px">';break;case '0':
if(isExpandIcon && expandHTML=='')
this._items[i]._icon='<td  style="padding-right:16px">';else
this._items[i]._icon='<td>';break;default:
if(isExpandIcon && expandHTML=='')
this._items[i]._icon='<td class="TIcon'+this._items[i]._parent._index+'" style="background: #C0C0C0; padding: 3px; padding-right: 0px;">'+this._items[i]._tmpIcon+
//<img class="TIcon'+this._items[i]._parent._index+'" src="'+this._items[i]._tmpIcon+'" width="16px" />
'</td><td  style="padding-right:16px">';else
this._items[i]._icon='<td class="TIcon'+this._items[i]._parent._index+'" style="background: #C0C0C0; padding: 3px; padding-right: 0px;">'+this._items[i]._tmpIcon+
//<img class="TIcon'+this._items[i]._parent._index+'" src="'+this._items[i]._tmpIcon+'" width="16px" />
'</td><td>';}
}		
result1+='><table class="TMenuItemChild'+this._index+'"><tr>'+this._items[i]._icon+this._items[i]._label+'</td>';result1+=expandHTML+'</tr></table></td>';if(this._direction=='horizontal')
result+=result1;else
result+='<tr>'+result1+'</tr>';}
}
else
{
var result1='';result1+='<td>&nbsp;</td>';if(this._direction=='horizontal')
result+=result1;else
result+='<tr>'+result1+'</tr>';}
if(this._direction=='horizontal')
result+='</tr>';result+='</table>';return result;}
function DrawPopMenu(level)
{
var result="";if(this._itemIndex >-1)
{
if(this._parent._pop._shadow._create)
{
if(_browser._name=='IE')
result+='<div style="position:absolute;visibility:hidden;filter: blur( direction=135, strength='+this._parent._pop._shadow._distance+', add=1);" id="sh_'+this._id+'" align="left"><div></div></div>';else
result+='<div style="position:absolute;visibility:hidden;" id="sh_'+this._id+'" align="left"></div>';}	
result+='<div class="TPopUp'+this._parent._index+'" id="'+this._id+'" style="filter:alpha(opacity='+this._parent._pop._alpha+');">';for(var i=0;i<=this._itemIndex;i++)
{
if(this._items[i]._label !='-')
{
if(this._items[i]._itemIndex >-1)
{
result+='<div class="TPopUpItem'+this._items[i]._parent._index+'" id="di_'+this._items[i]._id+'">';result+='<table class="TPopUpItem'+this._items[i]._parent._index+'" cellspacing="0" cellpadding="0"';result+=' onmouseover="onPopItemMOver(event,this,\''+this._items[i]._id+'\','+level+','+this._items[i]._parent._name+',\''+this._items[i]._status+'\')"';result+=' onmouseout="onPopItemMOut(event,this,\''+this._items[i]._id+'\','+this._items[i]._parent._name+')"';result+=' id="pr_'+this._items[i]._id+'"><tr>'+this._items[i]._icon+this._items[i]._label+'</td>';if(this._items[i]._parent._pop._expandIcon._create)
result+='<td class="TExpand'+this._items[i]._parent._index+'">'+this._items[i]._parent._pop._expandIcon._symbol+'</td>';result+='</tr></table>';result+='</div>';}
else
{
if(this._items[i]._type=='h')
{
result+='<div class="TPopUpHeader'+this._items[i]._parent._index+'">';result+='<table class="TPopUpHeader'+this._items[i]._parent._index+'" cellspacing="0" cellpadding="2"';result+=' onmouseover="onStaticPopItemMOver(event,'+this._items[i]._parent._name+',\''+this._items[i]._status+'\')"';result+=' onmouseout="onStaticPopItemMOut(event,'+this._items[i]._parent._name+',\''+this._items[i]._status+'\')"';result+='><tr><td>'+this._items[i]._label;result+='</td></tr></table></div>';}else
{
result+='<div class="TPopUpItem'+this._items[i]._parent._index+'">';result+='<table class="TPopUpItem'+this._items[i]._parent._index+'" cellspacing="0" cellpadding="0"';result+=' onmouseover="onPopItemMOver(event,this,\'\','+level+','+this._items[i]._parent._name+',\''+this._items[i]._status+'\')"';result+=' onmouseout="onPopItemMOut(event,this,\'\','+this._items[i]._parent._name+')"';result+=' onclick="hideAll('+this._items[i]._parent._name+');'+this._items[i]._eClick+'"';result+='><tr>'+this._items[i]._icon+this._items[i]._label+'</td>';result+='</tr></table>';result+='</div>';}
}
}
else
{		
if(_browser._name=='IE')
result+='<div style="margin-right:-2px;padding:4px 0px 4px 0px;background-color:'+this._parent._pop._itemBack._color+';" ';else
result+='<div style="margin-right:0px;padding:4px 0px 4px 0px;background-color:'+this._parent._pop._itemBack._color+';" ';  
result+=' onmouseover="onStaticPopItemMOver(event,'+this._parent._name+',\'\')"';result+=' onmouseout="onStaticPopItemMOut(event,'+this._parent._name+',\'\')"';	 		
result+=' ><div class="TSeparator'+this._parent._index+'" ';result+='></div></div>';}
}
result+='</div>';}
return result;}
function AddItem(popMenu)
{
this._itemIndex++;this._items[this._itemIndex]=popMenu;}
function InitPopMenu(zIndex)
{
if(this._itemIndex >-1)
{
for(var i=0;i<=this._itemIndex;i++)
{
if(this._items[i]._itemIndex >-1)
{
if(document.all)
{
var pr_el=document.all('pr_'+this._items[i]._id);var el=document.all(this._items[i]._id);}
else if(document.getElementById)
{
var pr_el=document.getElementById('pr_'+this._items[i]._id);var el=document.getElementById(this._items[i]._id);}
zIndex++;el.style.zIndex=zIndex+1;if(this._items[i]._parent._pop._shadow._create)
{
if(document.all)
var sh_el=document.all('sh_'+this._items[i]._id);else if(document.getElementById)
var sh_el=document.getElementById('sh_'+this._items[i]._id);if(_browser._name=='IE')
{
sh_el.style.width=el.offsetWidth+10+'px';sh_el.style.height=el.offsetHeight+10+'px';sh_el.childNodes[0].style.width=el.offsetWidth+'px';sh_el.childNodes[0].style.height=el.offsetHeight+'px';sh_el.childNodes[0].style.backgroundColor=this._items[i]._parent._pop._shadow._color;}
else
{
sh_el.style.width=el.offsetWidth ;sh_el.style.height=el.offsetHeight;sh_el.style.backgroundColor=this._items[i]._parent._pop._shadow._color;}
sh_el.style.zIndex=zIndex;}
this._items[i].Init(zIndex+1);}
}
}
}
function BuildPopUpMenu(popMenu,level)
{
var result="";level++;for(var i=0;i<=popMenu._itemIndex;i++)
{
result+=popMenu._items[i].Draw(level);result+=BuildPopUpMenu(popMenu._items[i],level);}
return result;}
function BuildStyle()
{
var result='';var tyH;var tyW;result+='<style type="text/css">';result+='table.TMenu'+this._index+'{';result+='cursor:default';result+=';visibility:hidden';result+=';position:'+this._position;tyW=typeof(this._width);if(tyW=='string')
{
this._width=this._width.toLowerCase();if(this._width !='auto' || this._width !='')
result+=';width:'+this._width+'px';}
else
result+=';width:'+this._width+'px'; 
result+=';border-top:'+this._border._top;result+=';border-right:'+this._border._right;result+=';border-bottom:'+this._border._bottom;result+=';border-left:'+this._border._left;result+=';background-color:'+this._background._color;result+=';background-image:'+this._background._image;result+=';background-position:'+this._background._position;result+=';background-repeat:'+this._background._repeat;result+=';}';result+='table.TMenuItemChild'+this._index+'{';result+='width:100%';result+=';font-family:'+this._font._family;result+=';font-size:'+this._font._size;result+=';font-weight:'+this._itemText._weight;result+=';text-align:'+this._itemText._align;result+=';color:'+this._itemText._color;result+=';text-decoration:'+this._itemText._decoration;result+=';white-space:'+this._itemText._whiteSpace;result+=';}'
result+='td.TMenuItem'+this._index+'{';result+='padding: 0px 0px 0px 0px';tyH=typeof(this._itemHeight);tyW=typeof(this._itemWidth);if(tyH=='string')
{
if(this._itemHeight.toLowerCase() !='auto' || this._itemHeight !='')
result+=';height:'+this._itemHeight+'px';}
else
result+=';height:'+this._itemHeight+'px';if(tyW=='string')
{ 
if(this._itemWidth.toLowerCase() !='auto' || this._itemWidth !='')
result+=';width:'+this._itemWidth+'px';}
else
result+=';width:'+this._itemWidth+'px';	  
result+=';border-top:'+this._itemBorder._top;result+=';border-right:'+this._itemBorder._right;result+=';border-bottom:'+this._itemBorder._bottom;result+=';border-left:'+this._itemBorder._left;result+=';background-color:'+this._itemBack._color;result+=';background-repeat:'+this._itemBack._repeat;result+=';background-image:'+this._itemBack._image;result+=';background-position:'+this._itemBack._position;result+=';}';result+='td.TMMExpand'+this._index+'{';result+='width:10px';result+=';text-align:right';result+=';padding-right:2px';result+=';font-family:'+this._expandIcon._font._family;result+=';font-size:'+this._expandIcon._font._size;result+=';font-weight:normal';result+=';text-decoration:none !important';result+=';white-space:nowrap !important';result+=';}';result+='td.TMMHeader'+this._index+'{';result+='height:1px';result+=';background-color:'+this._header._background._color;result+=';background-image:'+this._header._background._image;result+=';background-position:'+this._header._background._position;result+=';background-repeat:'+this._header._background._repeat;result+=';text-align:'+this._header._itemText._align;result+=';text-decoration:'+this._header._itemText._decoration;result+=';white-space:'+this._header._itemText._whiteSpace;result+=';font-weight:'+this._header._itemText._weight;result+=';font-family:'+this._header._font._family;result+=';font-size:'+this._header._font._size;result+=';color:'+this._header._itemText._color;result+=';}';result+='div.TPopUp'+this._index+'{';result+='position:absolute';result+=';padding:'+this._pop._padding;result+=';visibility:hidden';result+=';width:'+this._pop._itemWidth+'px';result+=';border-top:'+this._pop._border._top;result+=';border-right:'+this._pop._border._right;result+=';border-bottom:'+this._pop._border._bottom;result+=';border-left:'+this._pop._border._left;result+=';background-color:'+this._pop._background._color;result+=';background-image:'+this._pop._background._image;result+=';background-position:'+this._pop._background._position;result+=';background-repeat:'+this._pop._background._repeat;result+=';display:block';result+=';}';result+='table.TPopUpHeader'+this._index+'{';result+='width:100%';result+=';cursor:default';result+=';height:'+this._pop._itemHeight+'px';result+=';text-align:'+this._pop._header._itemText._align;result+=';text-decoration:'+this._pop._header._itemText._decoration;result+=';white-space:'+this._pop._header._itemText._whiteSpace;result+=';font-weight:'+this._pop._header._itemText._weight;result+=';font-family:'+this._pop._header._font._family;result+=';font-size:'+this._pop._header._font._size;result+=';color:'+this._pop._header._itemText._color;result+=';}';result+='div.TPopUpHeader'+this._index+'{';result+=';background-color:'+this._pop._header._background._color;result+=';background-image:'+this._pop._header._background._image;result+=';background-position:'+this._pop._header._background._position;result+=';background-repeat:'+this._pop._header._background._repeat;if(_browser._name=='IE')
result+=';margin-right:-2px';result+=';}';result+='table.TPopUpItem'+this._index+'{';result+='width:100%';result+=';height:'+this._pop._itemHeight+'px';result+=';cursor:default';result+=';font-family:'+this._pop._font._family;result+=';font-size:'+this._pop._font._size;result+=';color:'+this._pop._itemText._color;result+=';text-align:'+this._pop._itemText._align;result+=';text-decoration:'+this._pop._itemText._decoration;result+=';white-space:'+this._pop._itemText._whiteSpace;result+=';font-weight:'+this._pop._itemText._weight;result+=';}';result+='div.TPopUpItem'+this._index+'{';result+='cursor:default';result+=';background-color:'+this._pop._itemBack._color;result+=';background-image:'+this._pop._itemBack._image;result+=';background-position:'+this._pop._itemBack._position;result+=';background-repeat:'+this._pop._itemBack._repeat;result+=';border-top:'+this._pop._itemBorder._top;result+=';border-right:'+this._pop._itemBorder._right;result+=';border-bottom:'+this._pop._itemBorder._bottom;result+=';border-left:'+this._pop._itemBorder._left;result+=';padding:'+this._pop._itemPadding;result+=';}';result+='div.TPopUpItem'+this._index+'_1{';result+='cursor:default';result+=';background-color :'+this._pop._itemBackHL._color;result+=';background-image:'+this._pop._itemBackHL._image;result+=';background-position:'+this._pop._itemBackHL._position;result+=';background-repeat:'+this._pop._itemBackHL._repeat;result+=';border-top:'+this._pop._itemBorderHL._top;result+=';border-right:'+this._pop._itemBorderHL._right;result+=';border-bottom:'+this._pop._itemBorderHL._bottom;result+=';border-left:'+this._pop._itemBorderHL._left;result+=';padding:'+this._pop._itemPaddingHL;result+=';}';this._pop._separator._width=((this._pop._itemWidth-this._pop._separator._width) < 0)?this._pop._itemWidth:this._pop._separator._width;var _div=Math.floor((this._pop._itemWidth-this._pop._separator._width)/2);switch(this._pop._separator._align)
{
case 'left':
this._pop._separator._margin='0px '+(_div*2)+'px 0px 0px';break;case 'right':
this._pop._separator._margin='0px 0px 0px '+(_div*2)+'px';break;default:
this._pop._separator._margin='0px '+_div+'px 0px '+_div+'px';}
result+='div.TSeparator'+this._index+'{';result+='margin:'+this._pop._separator._margin;result+=';border-top:'+this._pop._separator._border._top;result+=';border-bottom:'+this._pop._separator._border._bottom;result+=';}';result+='td.TExpand'+this._index+'{';result+='width:10px';result+=';text-align:right';result+=';padding-right:2px';result+=';font-family:'+this._pop._expandIcon._font._family;result+=';font-size:'+this._pop._expandIcon._font._size;result+=';font-weight:normal';result+=';text-decoration:none !important';result+=';white-space:nowrap !important';result+=';}';result+='td.TIcon'+this._index+'{';result+='width:24px';result+=';text-align:left';result+=';text-decoration:normal';result+=';white-space:nowrap';result+=';font-weight:normal';result+=';}';result+='img.TIcon'+this._index+'{';result+='vertical-align:middle';result+=';}';result+='</style>';return result;}
function SetCorrection(dLeft,dTop)
{
dLeft=parseInt(dLeft);if(!dLeft)
this._correction._left=0;else
this._correction._left=dLeft;dTop=parseInt(dTop);if(!dTop)
this._correction._top=0;else
this._correction._top=dTop;}
function SetPosition(dPosition,dLeft,dTop)
{
switch(dPosition)
{
case 'absolute':
this._position=dPosition;break;default:
this._position='relative';}
dLeft=parseInt(dLeft);if(!dLeft)
this._left=0;else
this._left=dLeft;dTop=parseInt(dTop);if(!dTop)
this._top=0;else
this._top=dTop;}
function SetCellSpacing(dSpace)
{
dSpace=parseInt(dSpace);if(!dSpace)
this._cellSpacing=0;else
this._cellSpacing=dSpace;}
function SetWidth(dWidth)
{
dWidth=parseInt(dWidth);if(!dWidth)
this._width='auto';else
this._width=dWidth;}
function SetItemDimension(dWidth,dHeight)
{
dWidth=parseInt(dWidth);dHeight=parseInt(dHeight);if(!dWidth)
this._itemWidth='auto';else
this._itemWidth=dWidth;if(!dHeight)
this._itemHeight='auto';else
this._itemHeight=dHeight;}
function SetBackground(dColor,dImage,dRepeat,dPos)
{
(dColor=='')?this._background._color='transparent':this._background._color=dColor;(dImage=='')?this._background._image='none':this._background._image="url('"+dImage+"')";(dRepeat=='')?this._background._repeat='no-repeat':this._background._repeat=dRepeat;(dPos=='')?this._background._position='top left':this._background._position=dPos;}
function SetItemBackground(dColor,dImage,dRepeat,dPos)
{
(dColor=='')?this._itemBack._color='transparent':this._itemBack._color=dColor;(dImage=='')?this._itemBack._image='none':this._itemBack._image="url('"+dImage+"')";(dRepeat=='')?this._itemBack._repeat='no-repeat':this._itemBack._repeat=dRepeat;(dPos=='')?this._itemBack._position='top left':this._itemBack._position=dPos;}
function SetItemBackgroundHL(dColor,dImage,dRepeat,dPos)
{
(dColor=='')?this._itemBackHL._color='transparent':this._itemBackHL._color=dColor;(dImage=='')?this._itemBackHL._image='none':this._itemBackHL._image="url('"+dImage+"')";(dRepeat=='')?this._itemBackHL._repeat='no-repeat':this._itemBackHL._repeat=dRepeat;(dPos=='')?this._itemBackHL._position='top left':this._itemBackHL._position=dPos;}
function SetItemBackgroundClick(dColor,dImage,dRepeat,dPos)
{
(dColor=='')?this._itemBackClick._color='transparent':this._itemBackClick._color=dColor;(dImage=='')?this._itemBackClick._image='none':this._itemBackClick._image="url('"+dImage+"')";(dRepeat=='')?this._itemBackClick._repeat='no-repeat':this._itemBackClick._repeat=dRepeat;(dPos=='')?this._itemBackClick._position='top left':this._itemBackClick._position=dPos;}
function SetShadow(dCreate,dColor,dDistance)
{
if(dCreate)
{
this._shadow._create=dCreate;this._shadow._color=dColor;this._shadow._distance=dDistance;}
}
function SetFont(dFamily,dSize)
{
this._font._family=dFamily;this._font._size=dSize;}
function SetBorder(dSize,dColor,dType)
{
var dBorder=dSize+'px '+dColor+' '+dType;this._border._top=dBorder;this._border._right=dBorder;this._border._bottom=dBorder;this._border._left=dBorder;}
function SetItemBorder(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorder._top=dBorder;this._itemBorder._right=dBorder;this._itemBorder._bottom=dBorder;this._itemBorder._left=dBorder;}
function SetItemBorderHL(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderHL._top=dBorder;this._itemBorderHL._right=dBorder;this._itemBorderHL._bottom=dBorder;this._itemBorderHL._left=dBorder;}
function SetItemBorderClick(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderClick._top=dBorder;this._itemBorderClick._right=dBorder;this._itemBorderClick._bottom=dBorder;this._itemBorderClick._left=dBorder;}
function SetBorderTop(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._border._top=dBorder;}
function SetItemBorderTop(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorder._top=dBorder;}
function SetItemBorderTopHL(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderHL._top=dBorder;}
function SetItemBorderTopClick(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderClick._top=dBorder;}
function SetBorderRight(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._border._right=dBorder;}
function SetItemBorderRight(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorder._right=dBorder;}
function SetItemBorderRightHL(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderHL._right=dBorder;}
function SetItemBorderRightClick(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderClick._right=dBorder;}
function SetBorderBottom(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._border._bottom=dBorder;}
function SetItemBorderBottom(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorder._bottom=dBorder;}
function SetItemBorderBottomHL(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderHL._bottom=dBorder;}
function SetItemBorderBottomClick(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderClick._bottom=dBorder;}
function SetBorderLeft(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._border._left=dBorder;}
function SetItemBorderLeft(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorder._left=dBorder;}
function SetItemBorderLeftHL(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderHL._left=dBorder;}
function SetItemBorderLeftClick(dSize,dColor,dType)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dBorder=dSize+'px '+dColor+' '+dType;this._itemBorderClick._left=dBorder;}
function SetItemText(dColor,dAlign,dWeight,dDecoration,dWSpace)
{
this._itemText._color=dColor;(dAlign=='')?this._itemText._align='left':this._itemText._align=dAlign;(dWeight=='')?this._itemText._weight='normal':this._itemText._weight=dWeight;(dDecoration=='')?this._itemText._decoration='none':this._itemText._decoration=dDecoration;(dWSpace=='')?this._itemText._whiteSpace='normal':this._itemText._whiteSpace=dWSpace;}
function SetItemTextHL(dColor,dAlign,dWeight,dDecoration,dWSpace)
{
this._itemTextHL._color=dColor;(dAlign=='')?this._itemTextHL._align='left':this._itemTextHL._align=dAlign;(dWeight=='')?this._itemTextHL._weight='normal':this._itemTextHL._weight=dWeight;(dDecoration=='')?this._itemTextHL._decoration='none':this._itemTextHL._decoration=dDecoration;(dWSpace=='')?this._itemTextHL._whiteSpace='normal':this._itemTextHL._whiteSpace=dWSpace;}
function SetItemTextClick(dColor,dAlign,dWeight,dDecoration,dWSpace)
{
this._itemTextClick._color=dColor;(dAlign=='')?this._itemTextClick._align='left':this._itemTextClick._align=dAlign;(dWeight=='')?this._itemTextClick._weight='normal':this._itemTextClick._weight=dWeight;(dDecoration=='')?this._itemTextClick._decoration='none':this._itemTextClick._decoration=dDecoration;(dWSpace=='')?this._itemTextClick._whiteSpace='normal':this._itemTextClick._whiteSpace=dWSpace;}
function SetPaddings(dSize)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dPad=dSize+'px '+dSize+'px '+dSize+'px '+dSize+'px';this._padding=dPad;}
function SetItemPaddingsHL(dSize)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dPad=dSize+'px '+dSize+'px '+dSize+'px '+dSize+'px';this._itemPaddingHL=dPad;}
function SetItemPaddings(dSize)
{
dSize=parseInt(dSize);if(!dSize)
dSize=0;var dPad=dSize+'px '+dSize+'px '+dSize+'px '+dSize+'px';this._itemPadding=dPad;}
function SetPadding(dTop,dRight,dBottom,dLeft)
{
dTop=parseInt(dTop);dRight=parseInt(dRight);dBottom=parseInt(dBottom);dLeft=parseInt(dLeft);if(!dTop) dTop=0;if(!dRight) dRight=0;if(!dBottom) dBottom=0;if(!dLeft) dLeft=0;var dPad=dTop+'px '+dRight+'px '+dBottom+'px '+dLeft+'px';this._padding=dPad;}
function SetItemPaddingHL(dTop,dRight,dBottom,dLeft)
{
dTop=parseInt(dTop);dRight=parseInt(dRight);dBottom=parseInt(dBottom);dLeft=parseInt(dLeft);if(!dTop) dTop=0;if(!dRight) dRight=0;if(!dBottom) dBottom=0;if(!dLeft) dLeft=0;var dPad=dTop+'px '+dRight+'px '+dBottom+'px '+dLeft+'px';this._itemPaddingHL=dPad;}
function SetItemPadding(dTop,dRight,dBottom,dLeft)
{
dTop=parseInt(dTop);dRight=parseInt(dRight);dBottom=parseInt(dBottom);dLeft=parseInt(dLeft);if(!dTop) dTop=0;if(!dRight) dRight=0;if(!dBottom) dBottom=0;if(!dLeft) dLeft=0;var dPad=dTop+'px '+dRight+'px '+dBottom+'px '+dLeft+'px';this._itemPadding=dPad;}
function SetSeparator(dWidth,dAlign,dColor1,dColor2)
{
dWidth=parseInt(dWidth);if(!dWidth)
dWidth='auto';this._separator._width=dWidth;this._separator._align=dAlign;this._separator._border._top='1px '+dColor1+' solid';(dColor2=='')?this._separator._border._bottom='0px none solid':this._separator._border._bottom='1px '+dColor2+' solid';}
function SetExpandIcon(dCreate,dSymbol,dSize)
{
if(dCreate)
{
this._expandIcon._create=true;switch(dSymbol)
{
case '' :
break;default:
this._expandIcon._symbol=dSymbol;}
dSize=parseInt(dSize);if(!dSize)
dSize=6;this._expandIcon._font._size=dSize+'pt';}
else
this._expandIcon._create=false;}
function SetDelay(dTimeOut)
{
dTimeOut=parseInt(dTimeOut);if(!dTimeOut)
dTimeOut=0;this._timeOut=dTimeOut;}
function findPosX(obj)
{
if(_browser._name=="Konqueror")
var curleft=0;else
var curleft=0;if(obj.offsetParent)
{
while (obj.offsetParent)
{
curleft+=obj.offsetLeft;obj=obj.offsetParent;}
}
else if(obj.x)
curleft+=obj.x;return curleft;}
function findPosY(obj)
{
if(_browser._name=="Konqueror")
var curtop=0;else
var curtop=0;if(obj.offsetParent)
{
while (obj.offsetParent)
{
curtop+=obj.offsetTop;obj=obj.offsetParent;}
}
else if(obj.y)
curtop+=obj.y;return curtop;}
function findTriggerPopID(elmID,mmObj)
{
var result=-1;for(var i=0;i<=_arRegisterTriggerPopIndex[mmObj._index];i++)
{
if(_arRegisterTriggerPopID[mmObj._index][i]==elmID)
{
result=i;break;}
}
return result;}
function saveTriggerPopID(elmID,mmObj)
{
_arRegisterTriggerPopIndex[mmObj._index]++;var j=_arRegisterTriggerPopIndex[mmObj._index];_arRegisterTriggerPopID[mmObj._index][j]=elmID;if(document.all)
var el=document.all(elmID);else if(document.getElementById)
var el=document.getElementById(elmID);if(el.className.indexOf("TMenuItem") !=-1)
{
el.style.backgroundColor=mmObj._itemBackClick._color;el.style.backgroundImage=mmObj._itemBackClick._image;el.style.backgroundRepeat=mmObj._itemBackClick._repeat;el.style.backgroundPosition=mmObj._itemBackClick._position;el.childNodes[0].style.color=mmObj._itemTextClick._color;el.childNodes[0].style.textAlign=mmObj._itemTextClick._align;el.childNodes[0].style.textDecoration=mmObj._itemTextClick._decoration;el.childNodes[0].style.whiteSpace=mmObj._itemTextClick._whiteSpace;el.childNodes[0].style.fontWeight=mmObj._itemTextClick._weight;	
el.style.borderTop=mmObj._itemBorderClick._top;el.style.borderRight=mmObj._itemBorderClick._right;el.style.borderBottom=mmObj._itemBorderClick._bottom;el.style.borderLeft=mmObj._itemBorderClick._left;	
}
else
{
el.className="TPopUpItem"+mmObj._index+"_1";}
}
function removeTriggerPopID(elmID,mmObj)
{
var index=findTriggerPopID(elmID,mmObj)
if(index >-1)
{
for(var i=_arRegisterTriggerPopIndex[mmObj._index];i>=index;i--)
{
var ID=_arRegisterTriggerPopID[mmObj._index][i];if(document.all)
var el=document.all(ID);else if(document.getElementById)
var el=document.getElementById(ID);if(el.className.indexOf("TMenuItem") !=-1)
{
el.style.backgroundColor=mmObj._itemBack._color;el.style.backgroundImage=mmObj._itemBack._image;el.style.backgroundRepeat=mmObj._itemBack._repeat;el.style.backgroundPosition=mmObj._itemBack._position;			
el.childNodes[0].style.color=mmObj._itemText._color;el.childNodes[0].style.textAlign=mmObj._itemText._align;el.childNodes[0].style.textDecoration=mmObj._itemText._decoration;el.childNodes[0].style.whiteSpace=mmObj._itemText._whiteSpace;el.childNodes[0].style.fontWeight=mmObj._itemText._weight;el.style.borderTop=mmObj._itemBorder._top;el.style.borderRight=mmObj._itemBorder._right;el.style.borderBottom=mmObj._itemBorder._bottom;el.style.borderLeft=mmObj._itemBorder._left;}
else
{
var IDLen=ID.length;var tableID='pr_'+ID.substr(3,IDLen);if(document.all)
var elTable=document.all(tableID);else if(document.getElementById)
var elTable=document.getElementById(tableID);el.className="TPopUpItem"+mmObj._index;elTable.style.color=mmObj._pop._itemText._color;elTable.style.textAlign=mmObj._pop._itemText._align;elTable.style.textDecoration=mmObj._pop._itemText._decoration;elTable.style.whiteSpace=mmObj._pop._itemText._whiteSpace;elTable.style.fontWeight=mmObj._pop._itemText._weight;}
_arRegisterTriggerPopID[mmObj._index][i]=null;}
_arRegisterTriggerPopIndex[mmObj._index]=index-1;}
}
function removeTriggerPopIDByIndex(index,mmObj)
{
if(_arRegisterTriggerPopIndex[mmObj._index] >-1 && index >-1)
{
for(var i=_arRegisterTriggerPopIndex[mmObj._index];i>=index;i--)
{
var ID=_arRegisterTriggerPopID[mmObj._index][i];if(document.all)
var el=document.all(ID);else if(document.getElementById)
var el=document.getElementById(ID);if(el.className.indexOf("TMenuItem") !=-1)
{
el.style.backgroundColor=mmObj._itemBack._color;el.style.backgroundImage=mmObj._itemBack._image;el.style.backgroundRepeat=mmObj._itemBack._repeat;el.style.backgroundPosition=mmObj._itemBack._position;			
el.childNodes[0].style.color=mmObj._itemText._color;el.childNodes[0].style.textAlign=mmObj._itemText._align;el.childNodes[0].style.textDecoration=mmObj._itemText._decoration;el.childNodes[0].style.whiteSpace=mmObj._itemText._whiteSpace;el.childNodes[0].style.fontWeight=mmObj._itemText._weight;		   
el.style.borderTop=mmObj._itemBorder._top;el.style.borderRight=mmObj._itemBorder._right;el.style.borderBottom=mmObj._itemBorder._bottom;el.style.borderLeft=mmObj._itemBorder._left;}
else
{
var IDLen=ID.length;var tableID='pr_'+ID.substr(3,IDLen);if(document.all)
var elTable=document.all(tableID);else if(document.getElementById)
var elTable=document.getElementById(tableID);				 
el.className="TPopUpItem"+mmObj._index;elTable.style.color=mmObj._pop._itemText._color;elTable.style.textAlign=mmObj._pop._itemText._align;elTable.style.textDecoration=mmObj._pop._itemText._decoration;elTable.style.whiteSpace=mmObj._pop._itemText._whiteSpace;elTable.style.fontWeight=mmObj._pop._itemText._weight;			
}
_arRegisterTriggerPopID[mmObj._index][i]=null;}
_arRegisterTriggerPopIndex[mmObj._index]=index-1;}
}
function removeAllTriggerPopID(mmObj)
{
if(_arRegisterTriggerPopIndex[mmObj._index] >-1)
{
for(var i=_arRegisterTriggerPopIndex[mmObj._index];i>=0;i--)
{
var ID=_arRegisterTriggerPopID[mmObj._index][i];if(document.all)
var el=document.getElementById(ID);else if(document.getElementById)
var el=document.getElementById(ID);		
if(el.className.indexOf("TMenuItem") !=-1)
{
if(_arMMClick[mmObj._index])
{
el.style.backgroundColor=mmObj._itemBack._color;el.style.backgroundImage=mmObj._itemBack._image;el.style.backgroundRepeat=mmObj._itemBack._repeat;el.style.backgroundPosition=mmObj._itemBack._position;				 
el.childNodes[0].style.color=mmObj._itemText._color;el.childNodes[0].style.textAlign=mmObj._itemText._align;el.childNodes[0].style.textDecoration=mmObj._itemText._decoration;el.childNodes[0].style.whiteSpace=mmObj._itemText._whiteSpace;el.childNodes[0].style.fontWeight=mmObj._itemText._weight;		     
el.style.borderTop=mmObj._itemBorder._top;el.style.borderRight=mmObj._itemBorder._right;el.style.borderBottom=mmObj._itemBorder._bottom;el.style.borderLeft=mmObj._itemBorder._left;}
}
else
{
var IDLen=ID.length;var tableID='pr_'+ID.substr(3,IDLen);if(document.all)
var elTable=document.all(tableID);else if(document.getElementById)
var elTable=document.getElementById(tableID);el.className="TPopUpItem"+mmObj._index;elTable.style.color=mmObj._pop._itemText._color;elTable.style.textAlign=mmObj._pop._itemText._align;elTable.style.textDecoration=mmObj._pop._itemText._decoration;elTable.style.whiteSpace=mmObj._pop._itemText._whiteSpace;elTable.style.fontWeight=mmObj._pop._itemText._weight;}		
_arRegisterTriggerPopID[mmObj._index][i]=null;}
_arRegisterTriggerPopIndex[mmObj._index]=-1;}
}
function findRegisteredPopUpMenuID(elmID,mmObj)
{
var result=-1;for(var i=0;i<=_arRegisterPopIndex[mmObj._index];i++)
{
if(_arRegisterPopID[mmObj._index][i]==elmID)
{
result=i;break;}
}
return result;}
function showPopUpMenu(parentElmID,elmID,mmObj,menuType)
{
_arRegisterPopIndex[mmObj._index]++;var j=_arRegisterPopIndex[mmObj._index];_arRegisterPopID[mmObj._index][j]=elmID;var leftPos=0;var parentLeftPos=0;var topPos=0;var parentTopPos=0;if(document.all)
{
var parentElm=document.all(parentElmID);var elm=document.all(elmID);}
else if(document.getElementById)
{
var parentElm=document.getElementById(parentElmID);var elm=document.getElementById(elmID);}
parentLeftPos=findPosX(parentElm);parentTopPos=findPosY(parentElm);var screenPosY=0;var screenPosX=0;if(window.innerHeight)
{
screenPosY=window.pageYOffset
screenPosX=window.pageXOffset
}
else if(document.documentElement && document.documentElement.scrollTop)
{
screenPosY=document.documentElement.scrollTop
screenPosX=document.documentElement.scrollLeft
}
else if(document.body)
{
screenPosY=document.body.scrollTop
screenPosX=document.body.scrollLeft
}
if(menuType=='mm')
{
if(mmObj._direction=='horizontal')
{
if((parentLeftPos-elm.offsetWidth-mmObj._correction._left) < 0)
{
if(parentLeftPos < screenPosX)
{
leftPos=parentLeftPos+parentElm.offsetWidth+mmObj._correction._left-1;}
else
leftPos=parentLeftPos+mmObj._correction._left-1;}
else
{
if(parentLeftPos-screenPosX+elm.offsetWidth+mmObj._correction._left > document.body.clientWidth)
{
if(parentLeftPos+parentElm.offsetWidth-screenPosX > document.body.clientWidth)
leftPos=parentLeftPos-elm.offsetWidth-mmObj._correction._left;else
leftPos=parentLeftPos+parentElm.offsetWidth-elm.offsetWidth-mmObj._correction._left-1;}
else
{
if(parentLeftPos < screenPosX)
{
leftPos=parentLeftPos+parentElm.offsetWidth+mmObj._correction._left-1;}
else
leftPos=parentLeftPos+mmObj._correction._left-1;}
}	
if((parentTopPos-screenPosY-elm.offsetHeight-mmObj._correction._top) <0)
topPos=parentTopPos+parentElm.offsetHeight+1+mmObj._correction._top;else
{
if(parentTopPos+parentElm.offsetHeight+elm.offsetHeight-screenPosY+mmObj._correction._top < document.body.clientHeight)
topPos=parentTopPos+parentElm.offsetHeight+1+mmObj._correction._top;else
topPos=parentTopPos-elm.offsetHeight-mmObj._correction._top;}			 
}
else 
{
if(parentLeftPos-screenPosX-elm.offsetWidth-mmObj._correction._left<0)
leftPos=parentLeftPos+parentElm.offsetWidth+mmObj._correction._left+1;else
{
if((parentLeftPos-screenPosX+parentElm.offsetWidth+elm.offsetWidth+mmObj._correction._left) < document.body.clientWidth)
leftPos=parentLeftPos+parentElm.offsetWidth+mmObj._correction._left+1;else
leftPos=parentLeftPos-elm.offsetWidth-mmObj._correction._left-1;}
if(parentTopPos-screenPosY-elm.offsetHeight-mmObj._correction._top < 0)
{
if(parentTopPos < screenPosY)
topPos=parentTopPos+parentElm.offsetHeight+mmObj._correction._top-1;else			 
topPos=parentTopPos+mmObj._correction._top-1;		
}
else
{
if(parentTopPos-screenPosY+elm.offsetHeight+mmObj._correction._top < document.body.clientHeight)
{
topPos=parentTopPos+mmObj._correction._top-1;}
else
{
if(parentTopPos-screenPosY+parentElm.offsetHeight < document.body.clientHeight)
topPos=parentTopPos+parentElm.offsetHeight-elm.offsetHeight-mmObj._correction._top+1;else
topPos=parentTopPos-elm.offsetHeight-mmObj._correction._top+1;}		  
}
}
elm.style.left=leftPos+'px';elm.style.top=topPos+'px';if(mmObj._pop._shadow._create)
{
if(document.all)
var shadowElm=document.all('sh_'+elmID);else if(document.getElementById)
var shadowElm=document.getElementById('sh_'+elmID);if(_browser._name=='IE')
{			
shadowElm.style.left=leftPos+'px';shadowElm.style.top=topPos+'px';}
else
{
shadowElm.style.left=leftPos+mmObj._pop._shadow._distance+'px';shadowElm.style.top=topPos+mmObj._pop._shadow._distance+'px';}
shadowElm.style.visibility='visible';}	
}
else
{
topPos=parentTopPos;leftPos=parentLeftPos+parentElm.offsetWidth;if(topPos-elm.offsetHeight < 0)
topPos=topPos+mmObj._pop._correction._top;else
{
if(topPos-screenPosY+elm.offsetHeight < document.body.clientHeight)
topPos=topPos+mmObj._pop._correction._top-2;else
topPos=topPos-elm.offsetHeight+parentElm.offsetHeight+mmObj._pop._correction._top+2;}
if((leftPos-((elm.offsetWidth+mmObj._pop._correction._left+1) * 2)) < 0)
leftPos=leftPos+mmObj._pop._correction._left-3;else
{
if((leftPos-screenPosX+elm.offsetWidth+mmObj._pop._correction._left) > document.body.clientWidth)
leftPos=leftPos-parentElm.offsetWidth-elm.offsetWidth-mmObj._pop._correction._left+5;else
leftPos=leftPos+mmObj._pop._correction._left-3;}
elm.style.top=topPos+'px';elm.style.left=leftPos+'px';if(mmObj._pop._shadow._create)
{
if(document.all)
var shadowElm=document.all('sh_'+elmID);else if(document.getElementById)
var shadowElm=document.getElementById('sh_'+elmID);if(_browser._name=='IE')
{
shadowElm.style.left=leftPos+'px';shadowElm.style.top=topPos+'px';}
else
{
shadowElm.style.left=leftPos+mmObj._pop._shadow._distance+'px';shadowElm.style.top=topPos+mmObj._pop._shadow._distance+'px';}			
shadowElm.style.visibility='visible';}					
}	
elm.style.visibility="visible";}
function hidePopUpMenu(elmID,mmObj)
{
var index=findRegisteredPopUpMenuID(elmID,mmObj);if(index >-1)
{
for(var i=_arRegisterPopIndex[mmObj._index];i>=index;i--)
{
var ID=_arRegisterPopID[mmObj._index][i];if(document.all)
document.all(ID).style.visibility="hidden";else if(document.getElementById)
document.getElementById(ID).style.visibility="hidden";if(mmObj._pop._shadow._create)
{
if(document.all)
document.all('sh_'+ID).style.visibility="hidden";else if(document.getElementById)
document.getElementById('sh_'+ID).style.visibility="hidden";}
}
_arRegisterPopIndex[mmObj._index]=index-1
}
}
function hidePopUpMenuByIndex(index,mmObj)
{
if(_arRegisterPopIndex[mmObj._index] >=index)
{
for(var i=_arRegisterPopIndex[mmObj._index];i>=index;i--)
{
var ID=_arRegisterPopID[mmObj._index][i];if(document.all)
document.all(ID).style.visibility="hidden";else if(document.getElementById)
document.getElementById(ID).style.visibility="hidden";if(mmObj._pop._shadow._create)
{
if(document.all)
document.all('sh_'+ID).style.visibility="hidden";else if(document.getElementById)
document.getElementById('sh_'+ID).style.visibility="hidden";}
}
_arRegisterPopIndex[mmObj._index]=index-1;}
}
function hideAllPopUpMenu(mmObj)
{
var index=_arRegisterPopIndex[mmObj._index];if(index >-1)
{
for(i=index;i>=0;i--)
{
var ID=_arRegisterPopID[mmObj._index][i];if(document.all)
document.all(ID).style.visibility="hidden";else if(document.getElementById)
document.getElementById(ID).style.visibility="hidden";if(mmObj._pop._shadow._create)
document.getElementById('sh_'+ID).style.visibility="hidden";}
}
_arRegisterPopIndex[mmObj._index]=-1;}
function triggerHideAll(mmObj)
{
_arTriggerMenu[mmObj._index]=window.setTimeout('hideAll('+mmObj._name+')',mmObj._pop._timeOut);}
function clearTriggerHideAll(mmObj)
{
window.clearTimeout(_arTriggerMenu[mmObj._index]);}
function hideAll(mmObj)
{
hideAllPopUpMenu(mmObj);removeAllTriggerPopID(mmObj);if(mmObj._popOnClick)
_arMMClick[mmObj._index]=false;else
_arMMClick[mmObj._index]=true;window.status='';mmObj._hideObject.Show();}
function onMainClick(event,elm,popID,mmObj)
{
if(!_arMMClick[mmObj._index])
{
_arMMClick[mmObj._index]=true;if(popID !='')
{
showPopUpMenu(elm.id,popID,mmObj,'mm');saveTriggerPopID(elm.id,mmObj);}
}
else
{
_arMMClick[mmObj._index]=false;hideAllPopUpMenu(mmObj);removeAllTriggerPopID(mmObj);elm.style.backgroundColor=mmObj._itemBackHL._color;elm.style.backgroundImage=mmObj._itemBackHL._image;elm.style.backgroundRepeat=mmObj._itemBackHL._repeat;elm.style.backgroundPosition=mmObj._itemBackHL._position;	
elm.childNodes[0].style.color=mmObj._itemTextHL._color;elm.childNodes[0].style.textAlign=mmObj._itemTextHL._align;elm.childNodes[0].style.textDecoration=mmObj._itemTextHL._decoration;elm.childNodes[0].style.whiteSpace=mmObj._itemTextHL._whiteSpace;elm.childNodes[0].style.fontWeight=mmObj._itemTextHL._weight;	
elm.style.borderTop=mmObj._itemBorderHL._top;elm.style.borderRight=mmObj._itemBorderHL._right;elm.style.borderBottom=mmObj._itemBorderHL._bottom;elm.style.borderLeft=mmObj._itemBorderHL._left;}
mmObj._hideObject.Hide();onBubble(event);}
function onMainMOver(event,elm,popID,level,mmObj,status)
{
window.status=unescape(status);clearTriggerHideAll(mmObj);if(_arRegisterTriggerPopID[mmObj._index][0] !=elm.id)
{
if(_arRegisterTriggerPopID[mmObj._index][0] !=null)
{
removeAllTriggerPopID(mmObj);}
if(_arMMClick[mmObj._index])
{
hideAllPopUpMenu(mmObj);removeAllTriggerPopID(mmObj);if(popID !='')
{
showPopUpMenu(elm.id,popID,mmObj,'mm');saveTriggerPopID(elm.id,mmObj);}
if(mmObj._popOnClick)
{
elm.style.backgroundColor=mmObj._itemBackClick._color;elm.style.backgroundImage=mmObj._itemBackClick._image;elm.style.backgroundRepeat=mmObj._itemBackClick._repeat;elm.style.backgroundPosition=mmObj._itemBackClick._position;		 
elm.childNodes[0].style.color=mmObj._itemTextClick._color;elm.childNodes[0].style.textAlign=mmObj._itemTextClick._align;elm.childNodes[0].style.textDecoration=mmObj._itemTextClick._decoration;elm.childNodes[0].style.whiteSpace=mmObj._itemTextClick._whiteSpace;elm.childNodes[0].style.fontWeight=mmObj._itemTextClick._weight;		   
elm.style.borderTop=mmObj._itemBorderClick._top;elm.style.borderRight=mmObj._itemBorderClick._right;elm.style.borderBottom=mmObj._itemBorderClick._bottom;elm.style.borderLeft=mmObj._itemBorderClick._left;	
}else
{
elm.style.backgroundColor=mmObj._itemBackHL._color;elm.style.backgroundImage=mmObj._itemBackHL._image;elm.style.backgroundRepeat=mmObj._itemBackHL._repeat;elm.style.backgroundPosition=mmObj._itemBackHL._position;		 
elm.childNodes[0].style.color=mmObj._itemTextHL._color;elm.childNodes[0].style.textAlign=mmObj._itemTextHL._align;elm.childNodes[0].style.textDecoration=mmObj._itemTextHL._decoration;elm.childNodes[0].style.whiteSpace=mmObj._itemTextHL._whiteSpace;elm.childNodes[0].style.fontWeight=mmObj._itemTextHL._weight;		   
elm.style.borderTop=mmObj._itemBorderHL._top;elm.style.borderRight=mmObj._itemBorderHL._right;elm.style.borderBottom=mmObj._itemBorderHL._bottom;elm.style.borderLeft=mmObj._itemBorderHL._left;		   
mmObj._hideObject.Hide();}
}
else
{
elm.style.backgroundColor=mmObj._itemBackHL._color;elm.style.backgroundImage=mmObj._itemBackHL._image;elm.style.backgroundRepeat=mmObj._itemBackHL._repeat;elm.style.backgroundPosition=mmObj._itemBackHL._position;		 
elm.childNodes[0].style.color=mmObj._itemTextHL._color;elm.childNodes[0].style.textAlign=mmObj._itemTextHL._align;elm.childNodes[0].style.textDecoration=mmObj._itemTextHL._decoration;elm.childNodes[0].style.whiteSpace=mmObj._itemTextHL._whiteSpace;elm.childNodes[0].style.fontWeight=mmObj._itemTextHL._weight;elm.style.borderTop=mmObj._itemBorderHL._top;elm.style.borderRight=mmObj._itemBorderHL._right;elm.style.borderBottom=mmObj._itemBorderHL._bottom;elm.style.borderLeft=mmObj._itemBorderHL._left;	
}
}
onBubble(event);}
function onBubble(event)
{
if(!event)
var event=window.event;event.cancelBubble=true;if(event.stopPropagation)
event.stopPropagation();}
function onMainMOut(event,elm,popID,mmObj)
{
if(!_arMMClick[mmObj._index] || popID=='')
{
elm.style.backgroundColor=mmObj._itemBack._color;elm.style.backgroundImage=mmObj._itemBack._image;elm.style.backgroundRepeat=mmObj._itemBack._repeat;elm.style.backgroundPosition=mmObj._itemBack._position;	
elm.childNodes[0].style.color=mmObj._itemText._color;elm.childNodes[0].style.textAlign=mmObj._itemText._align;elm.childNodes[0].style.textDecoration=mmObj._itemText._decoration;elm.childNodes[0].style.whiteSpace=mmObj._itemText._whiteSpace;elm.childNodes[0].style.fontWeight=mmObj._itemText._weight;elm.style.borderTop=mmObj._itemBorder._top;elm.style.borderRight=mmObj._itemBorder._right;elm.style.borderBottom=mmObj._itemBorder._bottom;elm.style.borderLeft=mmObj._itemBorder._left;}
triggerHideAll(mmObj)
onBubble(event);	
}
function onStaticPopItemMOver(event,mmObj,status)
{
window.status=status;clearTriggerHideAll(mmObj);onBubble(event);}
function onStaticPopItemMOut(event,mmObj)
{
triggerHideAll(mmObj);onBubble(event);}
function onPopItemMOver(event,elm,popID,level,mmObj,status)
{
var index=-1;window.status=status;clearTriggerHideAll(mmObj);elm.parentNode.className='TPopUpItem'+mmObj._index+'_1';elm.style.color=mmObj._pop._itemTextHL._color;elm.style.textAlign=mmObj._pop._itemTextHL._align;elm.style.textDecoration=mmObj._pop._itemTextHL._decoration;elm.style.whiteSpace=mmObj._pop._itemTextHL._whiteSpace;elm.style.fontWeight=mmObj._pop._itemTextHL._weight; 
if(popID !='')
{
index=findRegisteredPopUpMenuID(popID,mmObj);if(index==-1)
{
hidePopUpMenuByIndex(level,mmObj);removeTriggerPopIDByIndex(level,mmObj);	 
showPopUpMenu(elm.id,popID,mmObj,'pm');saveTriggerPopID('di_'+popID,mmObj);}
}
else
{
hidePopUpMenuByIndex(level,mmObj);removeTriggerPopIDByIndex(level,mmObj);}
onBubble(event);}
function onPopItemMOut(event,elm,popID,mmObj)
{ 
if(popID=='')
{
elm.parentNode.className='TPopUpItem'+mmObj._index;elm.style.color=mmObj._pop._itemText._color;elm.style.textAlign=mmObj._pop._itemText._align;elm.style.textDecoration=mmObj._pop._itemText._decoration;elm.style.whiteSpace=mmObj._pop._itemText._whiteSpace;elm.style.fontWeight=mmObj._pop._itemText._weight;	 
}
triggerHideAll(mmObj);onBubble(event);}
window.onload=Initialize;window.onresize=InitResize;if(_browser._name=='Netscape' && _browser._version==4)
window.captureEvents(event.RESIZE);	
var _mmHeaderMoveObj;var _mmHeaderMoveObjCorrectionX=0;var _mmHeaderMoveObjCorrectionY=0;function onMMHeaderMove(event)
{
if(!event)
event=window.event;var xPos=(event.clientX) ? event.clientX : event.pageX
var yPos=(event.clientY) ? event.clientY : event.pageY
xPos=parseInt(xPos);if(!xPos) xPos=0;yPos=parseInt(yPos);if(!yPos) yPos=0;if(document.all)
var frElm=document.all('fr_'+_mmHeaderMoveObj._id);else if(document.getElementById)
var frElm=document.getElementById('fr_'+_mmHeaderMoveObj._id);xPos=xPos-_mmHeaderMoveObjCorrectionX;yPos=yPos-_mmHeaderMoveObjCorrectionY;if(_mmHeaderMoveObj._shadow._create)
{
if(document.all)
var shElm=document.all('sh_'+_mmHeaderMoveObj._id);else if(document.getElementById)
var shElm=document.getElementById('sh_'+_mmHeaderMoveObj._id);if(_browser._name=='IE')
{
shElm.style.left=xPos;shElm.style.top=yPos;}
else
{
shElm.style.left=xPos+_mmHeaderMoveObj._shadow._distance;shElm.style.top=yPos+_mmHeaderMoveObj._shadow._distance;}
}
frElm.style.left=xPos;frElm.style.top=yPos;}
function onMMDocumentClick(event)
{
onMMHeaderClick(event,_mmHeaderMoveObj);}
function onMMHeaderClick(event,mmObj)
{
if(!event)
event=window.event;if((_browser._name=='IE') || (_browser._name=='Konqueror'))
{
var xPos=event.clientX;var yPos=event.clientY;}
else
{
var xPos=event.pageX;var yPos=event.pageY;}
if(document.all)
{
var elm=document.all(mmObj._id);var frElm=document.all('fr_'+mmObj._id);}
else(document.getElementById)
{
var elm=document.getElementById(mmObj._id);var frElm=document.getElementById('fr_'+mmObj._id);}
if(mmObj._headerClickState)
{
mmObj._headerClickState=false;document.onmousemove=null;_mmHeaderMoveObj=null;if(_browser._name=='Netscape' && _browser._version==4)
window.releaseEvents(event.MOUSEMOVE);elm.style.top=frElm.style.top;elm.style.left=frElm.style.left;mmObj._left=xPos-_mmHeaderMoveObjCorrectionX;mmObj._top=yPos-_mmHeaderMoveObjCorrectionY;frElm.style.visibility='hidden';elm.style.visibility='visible';}
else
{
_mmHeaderMoveObjCorrectionX=xPos-mmObj._left;_mmHeaderMoveObjCorrectionY=yPos-mmObj._top;elm.style.visibility='hidden';frElm.style.visibility='visible';mmObj._headerClickState=true;_mmHeaderMoveObj=mmObj;document.onmousemove=onMMHeaderMove;if(_browser._name=='Netscape' && _browser._version==4)
window.captureEvents(event.MOUSEMOVE);	
clearTriggerHideAll(mmObj)
hideAll(mmObj);}
onBubble(event);}
function _floatingMMEffect(mmObj)
{
var posX=0;var posY=0;if(window.innerHeight)
{
posY=window.pageYOffset
posX=window.pageXOffset
}
else if(document.documentElement && document.documentElement.scrollTop)
{
posX=document.documentElement.scrollLeft
posY=document.documentElement.scrollTop
}
else if(document.body)
{
posX=document.body.scrollLeft
posY=document.body.scrollTop
}
if(document.all)
{
var elm=document.all(mmObj._id);var shadowElm=document.all('sh_'+mmObj._id);}
else if(document.getElementById)
{
var elm=document.getElementById(mmObj._id);var shadowElm=document.getElementById('sh_'+mmObj._id);}
if(posY < mmObj._initialTop)
posY=mmObj._initialTop;else posY+=mmObj._initialTop;if(posY==mmObj._top)
{
elm.style.top=posY;if(mmObj._shadow._create)
{
if(_browser._name=='IE')
shadowElm.style.top=posY;else
shadowElm.style.top=posY+mmObj._shadow._distance;}
}
mmObj._top=posY;if(posX < mmObj._initialLeft)
posX=mmObj._initialLeft;else posX+=mmObj._initialLeft;if(posX==mmObj._left)
{
elm.style.left=posX;if(mmObj._shadow._create)
{
if(_browser._name=='IE')
shadowElm.style.left=posX;else
shadowElm.style.left=posX+mmObj._shadow._distance;}
}
mmObj._left=posX;temp=setTimeout('_floatingMMEffect('+mmObj._name+')',500);}
function _openURL(address)
{
self.location=address;} 
function OutputAllMenuPosition(mmObj)
{
var result='';var dTop=0;var dLeft=0;for(var i=0;i<=mmObj._itemIndex;i++)
{
if(document.all)
{	
var elm=document.all('pr_'+mmObj._items[i]._id);}else if(document.getElementById)
{
var elm=document.getElementById('pr_'+mmObj._items[i]._id);}
dTop=findPosY(elm);dLeft=findPosX(elm);result+='pr_'+mmObj._items[i]._id+' : top='+dTop+',left='+dLeft+'<br>';result+=OutputAllPopMenuPosition('--',mmObj._items[i])
}
return result;}
function OutputAllPopMenuPosition(prefix,pmObj)
{
var result='';var dTop=0;var dLeft=0;prefix=prefix+'--';for(var i=0;i<=pmObj._itemIndex;i++)
{
if((pmObj._items[i]._label=='-') || (pmObj._items[i]._type=='h') || (pmObj._items[i]._itemIndex==-1))
{
dTop=0;dLeft=0;}
else
{
dTop=0;dLeft=0;if(document.all)
{	
var elm=document.all('di_'+pmObj._items[i]._id);}else if(document.getElementById)
{
var elm=document.getElementById('di_'+pmObj._items[i]._id);}
dTop=findPosY(elm);dLeft=findPosX(elm);}
result+=prefix+' label : '+pmObj._items[i]._label+' : top='+dTop+',left='+dLeft+'<br>';result+=OutputAllPopMenuPosition(prefix,pmObj._items[i]);}
return result;}
