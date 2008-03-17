

    
    function getContexts(letter)
    {
        
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=context&action=ajaxgetcontexts&letter="+letter,
            success: function(msg){
                jQuery('#browsecontextcontent').html(msg);
                adjustLayout();
            }
        });
        
        
        //alert(letter);
        
    }
    
