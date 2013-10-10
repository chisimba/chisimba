<?php

$this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugin_detector.js'));
$desc='<h2>'.$this->objLanguage->languageText("mod_webpresent_whatisjava", "webpresent").'</h2><br>';
$title=$this->objLanguage->languageText("mod_webpresent_minjava", "webpresent");
$content="<h4>".$this->objLanguage->languageText("mod_webpresent_minjava", "webpresent")."</h4>";
$content.="<br>".$this->objLanguage->languageText("mod_webpresent_javacontenterror", "webpresent");

$desc.="<br>".$this->objLanguage->languageText("mod_webpresent_javadesc", "webpresent");
$jsContent='

<script type="text/javascript">
    <!--
    /* PluginDetect v0.4.9 ( Java ) by Eric Gerds www.pinlady.net/PluginDetect */

    if(!PluginDetect){
        var PluginDetect={
            getNum:function(A,_2){
                if(!this.num(A)){
                    return null
                }var m;if(typeof _2=="undefined"){
                    m=/[\d][\d\.\_,-]*/.exec(A)
                }else{
                    m=(new RegExp(_2)).exec(A)
                }return m?m[0].replace(/[\.\_-]/g,","):null
            },
            hasMimeType:function(_4){
                var s,t,z,M=_4.constructor==String?[_4]:_4;for(z=0;z<M.length;z++){
                    s=navigator.mimeTypes[M[z]];if(s&&s.enabledPlugin){
                        t=s.enabledPlugin;if(t.name||t.description){
                            return s
                        }
                    }
                }return null
            },
            findNavPlugin:function(N,_7){
                var _8=N.constructor==String?N:N.join(".*"),numS=_7===false?"":"\\d";var i,re=new RegExp(_8+".*"+numS+"|"+numS+".*"+_8,"i");var _a=navigator.plugins;for(i=0;i<_a.length;i++){
                    if(re.test(_a[i].description)||re.test(_a[i].name)){
                        return _a[i]
                    }
                }return null
            },
            getAXO:function(_b){
                var _c,e;try{
                    _c=new ActiveXObject(_b);return _c
                }catch(e){}return null
            },
            num:function(A){
                return (typeof A!="string"?false:(/\d/).test(A))
            },
            compareNums:function(_e,_f){
                if(!this.num(_e)||!this.num(_f)){
                    return 0
                }if(this.plugin&&this.plugin.compareNums){
                    return this.plugin.compareNums(_e,_f)
                }var m1=_e.split(","),m2=_f.split(","),x,p=parseInt;for(x=0;x<Math.min(m1.length,m2.length);x++){
                    if(p(m1[x],10)>p(m2[x],10)){
                        return 1
                    }if(p(m1[x],10)<p(m2[x],10)){
                    return -1
                }
            }return 0
        },
        formatNum:function(num){
            if(!this.num(num)){
                return null
            }var x,n=num.replace(/\s/g,"").replace(/[\.\_]/g,",").split(",").concat(["0","0","0","0"]);for(x=0;x<4;x++){
                if(/^(0+)(.+)$/.test(n[x])){
                    n[x]=RegExp.$2
                }
            }return n[0]+","+n[1]+","+n[2]+","+n[3]
        },
        initScript:function(){
            var $=this,IE;$.isIE=(/*@cc_on!@*/false);$.IEver=-1;$.ActiveXEnabled=false;if($.isIE){
                IE=(/msie\s*\d\.{0,1}\d*/i).exec(navigator.userAgent);if(IE){
                    $.IEver=parseFloat((/\d.{0,1}\d*/i).exec(IE[0]),10)
                }var _14,x;_14=["ShockwaveFlash.ShockwaveFlash","Msxml2.XMLHTTP","Microsoft.XMLDOM","Msxml2.DOMDocument","TDCCtl.TDCCtl","Shell.UIHelper","Scripting.Dictionary","wmplayer.ocx"];for(x=0;x<_14.length;x++){
                    if($.getAXO(_14[x])){
                        $.ActiveXEnabled=true;break
                    }
                }
            }if($.isIE){
                $.head=typeof document.getElementsByTagName!="undefined"?document.getElementsByTagName("head")[0]:null
            }
        },
        init:function(_15){
            if(typeof _15!="string"){
                return -3
            }_15=_15.toLowerCase().replace(/\s/g,"");var $=this,IE,p;if(typeof $[_15]=="undefined"){
                return -3
            }p=$[_15];$.plugin=p;if(typeof p.installed=="undefined"){
                p.minversion={};p.installed=null;p.version=null;p.getVersionDone=null
            }$.garbage=false;if($.isIE&&!$.ActiveXEnabled){
                return -2
            }return 1
        },
        isMinVersion:function(_17,_18,_19){
            var $=PluginDetect,i=$.init(_17);if(i<0){
                return i
            }if(typeof _18=="undefined"||_18==null){
                _18="0"
            }if(typeof _18=="number"){
                _18=_18.toString()
            }if(!$.num(_18)){
                return -3
            }_18=$.formatNum(_18);if(typeof _19=="undefined"){
                _19=null
            }var p=$.plugin,m=p.minversion;if(typeof m["a"+_18]=="undefined"){
                if(p.getVersionDone==null){
                    var tmp,x;for(x in m){
                        tmp=$.compareNums(_18,x.substring(1,x.length));if(m[x]==1&&tmp<=0){
                            return 1
                        }if(m[x]==-1&&tmp>=0){
                            return -1
                        }
                    };p.getVersion(_18,_19)
                }if(typeof m["a"+_18]!="undefined"){}else{
                    if(p.version!=null||p.installed!=null){
                        p.getVersionDone=1;m["a"+_18]=(p.installed==-1?-1:(p.version==null?0:($.compareNums(p.version,_18)>=0?1:-1)))
                    }else{
                        m["a"+_18]=-1
                    }
                }
            }$.cleanup();return m["a"+_18];return -3
        },
        getVersion:function(_1d,_1e){
            return null
        },
        cleanup:function(){
            var $=this;if($.garbage&&typeof window.CollectGarbage!="undefined"){
                window.CollectGarbage()
            }
        },
        isActiveXObject:function(_22){
            var $=this,result,e,s="<object width=\"1\" height=\"1\" "+"style=\"display:none\" "+$.plugin.getCodeBaseVersion(_22)+">"+$.plugin.HTML+"</object>";if($.head.firstChild){
                $.head.insertBefore(document.createElement("object"),$.head.firstChild)
            }else{
                $.head.appendChild(document.createElement("object"))
            }$.head.firstChild.outerHTML=s;try{
                $.head.firstChild.classid=$.plugin.classID
            }catch(e){}result=false;try{
                if($.head.firstChild.object){
                    result=true
                }
            }catch(e){}try{
                if(result&&$.head.firstChild.readyState<4){
                    $.garbage=true
                }
            }catch(e){}$.head.removeChild($.head.firstChild);return result
        },
        codebaseSearch:function(min){
            var $=this;if(typeof min!="undefined"){
                return $.isActiveXObject(min)
            }
        },
        dummy1:0
    }
}

PluginDetect.initScript();PluginDetect.java={
    mimeType:"application/x-java-applet",
    classID:"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93",
    DTKclassID:"clsid:CAFEEFAC-DEC7-0000-0000-ABCDEFFEDCBA",
    DTKmimeType:"application/npruntime-scriptable-plugin;DeploymentToolkit",
    minWebStart:"1,4,2,0",
    JavaVersions:["1,9,1,25","1,8,1,25","1,7,1,25","1,6,1,25","1,5,0,25","1,4,2,25","1,3,1,25"],
    lowestPreApproved:"1,6,0,02",
    lowestSearchable:"1,3,1,0",
    searchAXOJavaPlugin:function(min,_34){
        var e,z,T,$=PluginDetect;var _36,C_DE,C,DE,v;var AXO=ActiveXObject;var _38=(typeof _34!="undefined")?_34:this.minWebStart;var Q=min.split(","),x;for(x=0;x<4;x++){
            Q[x]=parseInt(Q[x],10)
        }for(x=0;x<3;x++){
            if(Q[x]>9){
                Q[x]=9
            }
        }if(Q[3]>99){
            Q[3]=99
        }var _3a="JavaPlugin."+Q[0]+Q[1]+Q[2]+(Q[3]>0?("_"+(Q[3]<10?"0":"")+Q[3]):"");for(z=0;z<this.JavaVersions.length;z++){
        if($.compareNums(min,this.JavaVersions[z])>0){
            return null
        }T=this.JavaVersions[z].split(",");_36="JavaPlugin."+T[0]+T[1];v=T[0]+"."+T[1]+".";for(C=T[2];C>=0;C--){
            if($.compareNums(T[0]+","+T[1]+","+C+",0",_38)>=0){
                try{
                    new AXO("JavaWebStart.isInstalled."+v+C+".0")
                }catch(e){
                    continue
                }
            }if($.compareNums(min,T[0]+","+T[1]+","+C+","+T[3])>0){
                return null
            }for(DE=T[3];DE>=0;DE--){
                C_DE=C+"_"+(DE<10?"0"+DE:DE);try{
                    new AXO(_36+C_DE);return v+C_DE
                }catch(e){}if(_36+C_DE==_3a){
                    return null
                }
            }try{
                new AXO(_36+C);return v+C
            }catch(e){}if(_36+C==_3a){
                return null
            }
        }
    }return null
},
minIEver:7,
HTML:"<param name=\"code\" value=\"A14999.class\" />",
getCodeBaseVersion:function(v){
    var r=v.replace(/[\.\_]/g,",").split(","),$=PluginDetect;if($.compareNums(v,"1,4,1,02")<0){
        v=r[0]+","+r[1]+","+r[2]+","+r[3]
    }else{
        if($.compareNums(v,"1,5,0,02")<0){
            v=r[0]+","+r[1]+","+r[2]+","+r[3]+"0"
        }else{
            v=Math.round((parseFloat(r[0]+"."+r[1],10)-1.5)*10+5)+","+r[2]+","+r[3]+"0"+",0"
        }
    }return "codebase=\"#version="+v+"\""
},
digits:[2,8,8,32],
getFromMimeType:function(_3d){
    var x,t,$=PluginDetect;var re=new RegExp(_3d);var tmp,v="0,0,0,0",digits="";for(x=0;x<navigator.mimeTypes.length;x++){
        t=navigator.mimeTypes[x];if(re.test(t.type)&&t.enabledPlugin){
            t=t.type.substring(t.type.indexOf("=")+1,t.type.length);tmp=$.formatNum(t);if($.compareNums(tmp,v)>0){
                v=tmp;digits=t
            }
        }
    }return digits.replace(/[\.\_]/g,",")
},
hasRun:false,
value:null,
queryJavaHandler:function(){
    var $=PluginDetect.java,j=window.java,e;$.hasRun=true;try{
        if(typeof j.lang!="undefined"&&typeof j.lang.System!="undefined"){
            $.value=j.lang.System.getProperty("java.version")+" "
        }
    }catch(e){}
},
queryJava:function(){
    var $=PluginDetect,t=this,nua=navigator.userAgent,e;if(typeof window.java!="undefined"&&window.navigator.javaEnabled()){
        if(/gecko/i.test(nua)){
            if($.hasMimeType("application/x-java-vm")){
                try{
                    var div=document.createElement("div"),evObj=document.createEvent("HTMLEvents");evObj.initEvent("focus",false,true);div.addEventListener("focus",t.queryJavaHandler,false);div.dispatchEvent(evObj)
                }catch(e){}if(!t.hasRun){
                    t.queryJavaHandler()
                }
            }
        }else{
            if(/opera.9\.(0|1)/i.test(nua)&&/mac/i.test(nua)){
                return null
            }t.queryJavaHandler()
        }
    }return t.value
},
getVersion:function(min,jar){
    if(typeof min=="undefined"){
        min=null
    }if(typeof jar=="undefined"){
        jar=null
    }var _46=null,$=PluginDetect;var dtk=this.queryDeploymentToolKit();if(dtk==-1&&$.isIE){
        this.installed=-1;return
    }if(dtk!=-1&&dtk!=null){
        _46=dtk
    }if(!$.isIE){
        var p1,p2,p,tmp;var _49,mt;mt=$.hasMimeType(this.mimeType);_49=(mt&&navigator.javaEnabled());if(!_46&&_49){
            tmp="Java[^\\d]*Plug-in";p=$.findNavPlugin(tmp);if(p){
                tmp=new RegExp(tmp,"i");p1=tmp.test(p.description)?$.getNum(p.description):null;p2=tmp.test(p.name)?$.getNum(p.name):null;if(p1&&p2){
                    _46=($.compareNums($.formatNum(p1),$.formatNum(p2))>=0)?p1:p2
                }else{
                    _46=p1||p2
                }
            }
        }if(!_46&&(_49||(mt&&/linux/i.test(navigator.userAgent)&&$.findNavPlugin("IcedTea.*Java",false)))){
            tmp=this.getFromMimeType("application/x-java-applet.*jpi-version.*=");if(tmp!=""){
                _46=tmp
            }
        }if(!_46&&_49&&/macintosh.*safari/i.test(navigator.userAgent)){
            p=$.findNavPlugin("Java.*\\d.*Plug-in.*Cocoa",false);if(p){
                p1=$.getNum(p.description);if(p1){
                    _46=p1
                }
            }
        }if(!_46){
            p=this.queryJava();if(p){
                _46=p
            }
        }if(!_46&&mt){
            p=this.queryExternalApplet(jar);if(p[0]){
                _46=p[0]
            }
        }if(!_46&&_49&&!/macintosh.*ppc/i.test(navigator.userAgent)){
            tmp=this.getFromMimeType("application/x-java-applet.*version.*=");if(tmp!=""){
                _46=tmp
            }
        }this.installed=_46?1:-1;if(!_46&&_49){
            if(/safari/i.test(navigator.userAgent)){
                this.installed=0
            }
        }
    }else{
        var Q;if(!_46){
            if($.IEver>=this.minIEver){
                Q=this.findMax(this.lowestPreApproved,min);_46=this.searchAXOJavaPlugin(Q,this.lowestPreApproved)
            }else{
                Q=this.findMax(this.lowestSearchable,min);_46=this.searchAXOJavaPlugin(Q)
            }
        }if(!_46){
            this.JavaFix()
        }if(!_46){
            tmp=this.queryExternalApplet(jar);if(tmp[0]){
                _46=tmp[0]
            }
        }if(!_46&&$.IEver>=this.minIEver){
            if(min==null){
                _46=$.codebaseSearch()
            }else{
                this.minversion["a"+min]=$.codebaseSearch(min)?1:-1;return
            }
        }this.installed=_46?1:-1
    }this.setVersion(_46)
},
findMax:function(_4b,_4c){
    var $=PluginDetect;if(typeof _4c=="undefined"||_4c==null||$.compareNums(_4c,_4b)<0){
        return _4b
    }return _4c
},
setVersion:function(_4e){
    var $=PluginDetect;this.version=$.formatNum($.getNum(_4e));if(typeof this.version=="string"&&this.allVersions.length==0){
        this.allVersions[0]=this.version
    }
},
allVersions:[],
queryDeploymentToolKit:function(){
    if(typeof this.queryDTKresult!="undefined"){
        return this.queryDTKresult
    }this.allVersions=[];var $=PluginDetect,e,x;var _51=[null,null],obj;var len=null;if($.isIE&&$.IEver>=6){
        _51=$.instantiate("object","","")
    }if(!$.isIE&&$.hasMimeType(this.DTKmimeType)){
        _51=$.instantiate("object","type="+this.DTKmimeType,"")
    }if(_51[0]&&_51[1]&&_51[1].parentNode){
        obj=_51[0].firstChild;if($.isIE&&$.IEver>=6){
            try{
                obj.classid=this.DTKclassID
            }catch(e){}try{
                if(obj.object&&obj.readyState<4){
                    $.garbage=true
                }
            }catch(e){}
        }try{
            len=obj.jvms.getLength();if(len!=null&&len>0){
                for(x=0;x<len;x++){
                    this.allVersions[x]=$.formatNum($.getNum(obj.jvms.get(x).version))
                }
            }
        }catch(e){}_51[1].parentNode.removeChild(_51[1])
    }this.queryDTKresult=this.allVersions.length>0?this.allVersions[this.allVersions.length-1]:(len==0?-1:null);return this.queryDTKresult
},
queryExternalApplet:function(jar){
    if(!jar||typeof jar!="string"){
        return [null,null]
    }if(typeof this.queryExternalAppletResult!="undefined"){
        return this.queryExternalAppletResult
    }var $=PluginDetect,e,version=null,vendor=null,obj;var _55;var par="<param name=\"archive\" value=\""+jar+"\" />"+"<param name=\"mayscript\" value=\"true\" />"+"<param name=\"scriptable\" value=\"true\" />";var _57=function(_58){
    var obj,e;if(_58[0]&&_58[1]&&_58[1].parentNode){
        obj=_58[0].firstChild;try{
            if($.isIE&&obj.object&&obj.readyState<4){
                $.garbage=true
            }
        }catch(e){}try{
            version=obj.getVersion()+" ";vendor=obj.getVendor()+" "
        }catch(e){}_58[1].parentNode.removeChild(_58[1])
    }
};if($.isIE){
    _55=$.instantiate("object","archive=\""+jar+"\" code=\"A.class\" type=\""+this.mimeType+"\"","<param name=\"code\" value=\"A.class\" />"+par)
}else{
    _55=$.instantiate("object","archive=\""+jar+"\" classid=\"java:A.class\" type=\""+this.mimeType+"\"",par)
}_57(_55);if(!version){
    _55=$.instantiate("applet","archive=\""+jar+"\" code=\"A.class\" mayscript=\"true\"","<param name=\"mayscript\" value=\"true\">");_57(_55)
}this.queryExternalAppletResult=[version,vendor];return this.queryExternalAppletResult
},
JavaFix:function(){
var $=PluginDetect;if($.isIE&&window.history&&window.history.length==0&&window.location&&(/^file/).test(window.location.href)){
    var _5b=$.instantiate("object","codebase=\"#version=99,99,99,99\" classid=\""+this.classID+"\"",this.HTML);if(_5b[1]&&_5b[1].parentNode){
        _5b[1].parentNode.removeChild(_5b[1])
    }
}
}
};
PluginDetect.instantiate=function(_63,_64,_65){
var e,d=document,tag1="<"+_63+" width=\"1\" height=\"1\" "+_64+">"+_65+"</"+_63+">",body=(d.getElementsByTagName("body")[0]||d.body),div=d.createElement("div");if(body){
body.appendChild(div)
}else{
try{
    d.write("<div>o</div><div>"+tag1+"</div>");body=(d.getElementsByTagName("body")[0]||d.body);body.removeChild(body.firstChild);div=body.firstChild
}catch(e){
    try{
        body=d.createElement("body");d.getElementsByTagName("html")[0].appendChild(body);body.appendChild(div);div.innerHTML=tag1;return [div,body]
    }catch(e){}
}return [div,div]
}if(div&&div.parentNode){
try{
    div.innerHTML=tag1
}catch(e){}
}return [div,div]
};
function openWindow(theURL,winName,features) {
newwindow=window.open(theURL,winName,features);
if (window.focus) {
newwindow.focus()
}
}

var minVer=PluginDetect.isMinVersion("Java", "1.5");

if(minVer == 1){
window.open("';
$jsContent.=$this->objConfig->getSiteRoot().'/index.php?module=realtime&action=classroom&id='.$id.'&agenda='.$agenda.'","_self","fullscreen");
}else{
window.open("'.$this->objConfig->getSiteRoot().'/index.php?module=webpresent&action=showerror&title='.$title.'&content='.$content.'&desc='.$desc.'&id='.$id.'&agenda='.$agenda.'","_self","fullscreen");
}
//--></script>';

$this->appendArrayVar("headerParams", $jsContent);

?>