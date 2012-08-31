if (document.all)
 {navigator.family = "ie4" }
if (window.navigator.userAgent.toLowerCase().match(/gecko/)) 
{navigator.family = "gecko"}
if (window.navigator.userAgent.toLowerCase().indexOf('opera') != -1) 
{navigator.family = 'opera';}

function MM_showHideLayers() { //v3.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function showmenu (thelayer)
{
    var nextlayer
    for(i=1; i<10; i++)
    {
    
        nextlayer = 'box' + i      
        if (nextlayer != thelayer)
        {
            MM_showHideLayers(nextlayer,'','hide')
			NormalTab(i);
        }
    
    }
    for(i=1; i<10; i++)
    {
	    nextlayer = 'box' + i      
        if (nextlayer == thelayer)
		{
			MM_showHideLayers(nextlayer,'','show');
			SelectedTab(i);
		}
	}
}


function SelectedTab(num)
{
 
    if (navigator.family == "ie4")
    {
		var mytab="label" + num;
        document.all(mytab).className = "multitabselected";
    }

    if (navigator.family == "opera" || navigator.family == "gecko")
    {
     	var mytab="label" + num;
        document.getElementById(mytab).className = "multitabselected"; //tablabel
    }
}

function NormalTab(num)
{
 
    if (navigator.family == "ie4")
    {
		var mytab="label" + num;
        if(document.all(mytab)){document.all(mytab).className = "multitablabel";}
    }

    if (navigator.family == "opera" || navigator.family == "gecko")
    {
     	var mytab="label" + num;
        if(document.getElementById(mytab)){document.getElementById(mytab).className = "multitablabel";}
    }
}