

    
    function getContexts(letter)
    {
        
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=ajaxgetcontexts&letter="+letter,
            success: function(msg){
                jQuery('#browsecontextcontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(letter);
        
    }

	function getUserContexts()
    {
        
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=ajaxgetusercontexts",
            success: function(msg){
                jQuery('#browseusercontextcontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(letter);
        
    }
    
    function getAllContext()
    {
    	
    	
    	jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=ajaxgetallcontexts",
            success: function(msg){
                jQuery('#browseallcontextcontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
    }
    
    function getContext(contextCode)
    {
    	
    	jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=ajaxgetselectedcontext&contextcode="+contextCode,
            success: function(msg){
                jQuery('#context_results').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });	
    }
    
    function getUserContext(username)
    {
    	
    	jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=ajaxgetselectedusercontext&username="+username,
            success: function(msg){
                jQuery('#context_results').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });	
    }
    
    
    function listContexts()
    {
    	
    	jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=ajaxlistcontext",
            success: function(msg){
                jQuery('#context_results').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });	
    }
    
    function ajaxUpdate(uri, divId)
    {
    		jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: uri,
            success: function(msg){
                jQuery('#'+divId).html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });	
    }
    
    
    function contextPrivate()
    {
        alert(contextPrivateMessage);
    }
    
