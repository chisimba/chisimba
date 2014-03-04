


function getWorkgroupFiles(workgroupId)
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=workgroup&action=ajaxgetfiles&workgroupid="+workgroupId,
            success: function(msg){
                jQuery('#browsefiles').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }