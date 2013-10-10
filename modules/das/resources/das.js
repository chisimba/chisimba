


function loadConversations(id)
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=das&action=getconversations&id="+id,
            success: function(msg){
                jQuery('#conversations').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }
	
function showLoading(divId)
{
	
	div = document.getElementById(divId);
	div.innerHTML = "<span class=\"subdued\"><i><h3>Loading ...<img src=\"skins/_common/icons/loader.gif\"></i></span></h3>";
	
}

function loadConversationsText(personId)
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=das&action=conversationtext&personid="+personId,
            success: function(msg){
                jQuery('#conversations').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }