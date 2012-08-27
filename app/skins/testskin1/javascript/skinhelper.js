/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//The ajax object to get and set values
var AjaxObj = new Object();
AjaxObj.Navigation_FontSize = 0;
AjaxObj.Border_Radius = 0;
AjaxObj.Menulink_FontSize = 0;
AjaxObj.Body_FontSize = 0;
AjaxObj.Skin_Color = "";
AjaxObj.Border_Size = 0;
AjaxObj.Requester = new XMLHttpRequest();
AjaxObj.Handler = function(){
    if(this.Requester.readyState == 4 ){
        this.responseValue = this.Requester.responseXML;
        this.docElement = this.responseValue.documentElement;
        //setting the color
        this.Skin_Color = this.docElement.getElementsByTagName("background").item(0).firstChild.data;
        //setting the border radius
        this.Border_Radius = this.docElement.getElementsByTagName("border_radius").item(0).firstChild.data;
        //setting the border size
        this.Border_Size = this.docElement.getElementsByTagName("border_size").item(0).firstChild.data;
        //setting the font size
        this.Body_FontSize = this.docElement.getElementsByTagName("font_size").item(0).firstChild.data;
        this.Navigation_FontSize = this.docElement.getElementsByTagName("navigation_fontsize").item(0).firstChild.data;
        this.Menulink_FontSize = 15;
    }
}
AjaxObj._get = function(){
    this.Requester.open("GET","canvases/testskin1/values.xml", false);
    this.Requester.send(null);
    this.Requester.onreadystatechange = this.Handler();
}
AjaxObj._get();
jQuery(function(){
    jQuery(document).ready(function(){
        jQuery(".ChisimbaCanvas").width("100%");
        jQuery(".ChisimbaCanvas").css("font-family","ubuntu light, Calibri, Arial");
        jQuery("html").css("margin-top","2pc");
        
        /**
         *setting the Colors
         */
        //document.bgColor = AjaxObj.Skin_Color;
        jQuery("#footer a").css("color","red");
        jQuery("#footer, html").css("background",AjaxObj.Skin_Color);
        jQuery("td.odd").css("background","#ffffff");
        jQuery(".featurebox, .filemanager_left, #header, #nav-secondary").css("background-image","-webkit-gradient(linear, 0% 0%, 0% 100%, from(#fff), to("+ AjaxObj.Skin_Color+"))");
        jQuery(".featurebox, .filemanager_left, #header, #nav-secondary").css("background-image","-moz-linear-gradient(-10% 90% 90deg,"+ AjaxObj.Skin_Color+", #fff)");
        jQuery("#Canvas_Content_Body_Region1, #Canvas_Content_Body_Region2, #Canvas_Content_Body_Region3").css("background",AjaxObj.Skin_Color);
        /*
         *setting the Font size
         */
        jQuery("#Canvas_Content_Body_Region1, #Canvas_Content_Body_Region2, #Canvas_Content_Body_Region3").css("font-size", AjaxObj.Body_FontSize+"px");
        jQuery("div#menu").css("font-size",AjaxObj.Navigation_FontSize+"px");
        jQuery(".menulinktext").css("font-size", AjaxObj.Menulink_FontSize);
        /**
         * setting the border radius
         */
        jQuery("h5.featureboxheader, div.featurebox, ul#nav-secondary, div.filemanager_left, div#navigation, h1#sitename, ul#menuList li").css("border-radius",AjaxObj.Border_Radius+"px");
        jQuery("input[type=text], input[type=password], input[type=search]").css("border-radius",AjaxObj.Border_Radius+"px");
        /**
         * setting the border size
         */
        jQuery("#header, .featurebox, ul#nav-secondary, div.filemanager_left,  .currentstory").css("border", AjaxObj.Border_Size+"px solid #fff");
        /**
         * other
         */
        jQuery("div.featurebox, ul#nav-secondary, div.filemanager_left,  .currentstory").css("box-shadow", "0 1px 2px #666");
    })
});