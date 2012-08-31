CKEDITOR.plugins.add('contexttools',{

    init:function(a){
        var cmd = a.addCommand('contexttools', {exec:contexttools_onclick});
        cmd.modes={wysiwyg:1,source:1};
        cmd.canUndo=false;
        a.ui.addButton('contexttools',{ label:'Context tools', command:'contexttools', icon:this.path+'images/contexttools.png' });
    }
})

function contexttools_onclick(e)
{
    //CKEDITOR.instances[instancename].insertHtml("popopo");
    showpopup();
}
function showpopup(){
    

var w = 800, h = 600;

if (document.all || document.layers) {
   w = screen.availWidth;
   h = screen.availHeight;
}
var top=(w-600)/2;
var left=(h-300)/2;

var href=siteRootPath+'?module=contexttools&instancename='+instancename;
window.open(href, 'Tools', 'width=600,height=300,top='+top+',left='+left+',scrollbars=yes');

}
