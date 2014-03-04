/**
 * 
 */

jQuery(document).ready(function(){ 
    jQuery("#form_curriculumform").validate();
    jQuery("#form_createsectionnode").validate();
   var hoverSuccess=false;
    jQuery("a").hover(
   
   function () {
            if(loggedIn){
    hoverSuccess=false;         
                var link = this.href;
                if(link.indexOf("viewsection") > 0){
                  hoverSuccess=true;
                  var sectionIdIndex=link.indexOf("sectionid=")+10;
                    var productIdIndex=link.indexOf("productid=")+10;
                    var nodeTypeIndex=link.indexOf("nodetype=")+9;
                    var sectionId='-1';
                    var productId='-1';
                    var nodeType='';
                    if(sectionIdIndex > -1){
                        sectionId= link.substring(sectionIdIndex);
                    }
                    if(productIdIndex > -1){
                        productId= link.substring(productIdIndex,sectionIdIndex-11);
                    }
                    if(nodeTypeIndex > -1){
                        nodeType=link.substring(nodeTypeIndex);
                    }
                    var editLink='&nbsp;<a href="?module=oer&action=editsectionnode&id='+sectionId+'&editproductid='+productId+'"><img src="skins/oeru/images/icons/edit.png" /></a>';
                    var deleteLink='&nbsp;<a  class="deletenode" href="?module=oer&action=deletesectionnode&id='+sectionId+'&editproductid='+productId+'"><img src="skins/oeru/images/icons/delete.png" /></a>';
                    var editContentLink='';
                    if(nodeType == 'section'){
                        editContentLink= '&nbsp;<a href="?module=oer&action=editsectioncontent&id='+sectionId+'&editproductid='+productId+'"><img src="skins/oeru/images/application_form_edit.png" /></a>';
                    }
                  
                    jQuery(this).prepend(jQuery('<span class="editsection">'+editLink+editContentLink+deleteLink+"&nbsp;</span>"));     
                }
            }
        }, 
        function () {
            if(loggedIn && hoverSuccess){
                jQuery(this).find("span:first").remove();
            }
        }
        );
}); 


/**
 * this dynamically shows the type of node when creating a curriculum
 */
function displaySelectedNode(){
    var selectedVal = jQuery("#input_nodetype").val(); 
    jQuery("#createin").show();
    
    if(selectedVal == 'calendar'){
        jQuery("#calendardiv").show();
        jQuery("#modulediv").hide();
        jQuery("#yeardiv").hide();
    }else    if(selectedVal == 'module'){
        jQuery("#modulediv").show();
        jQuery("#calendardiv").hide();
        jQuery("#yeardiv").hide();
    }else  if(selectedVal == 'year'){
        jQuery("#yeardiv").show();
        jQuery("#calendardiv").hide();
        jQuery("#modulediv").hide();
    }else{
        jQuery("#calendardiv").hide();
        jQuery("#modulediv").hide();
        jQuery("#yeardiv").hide();
        jQuery("#createin").hide(); 
    }
       
}