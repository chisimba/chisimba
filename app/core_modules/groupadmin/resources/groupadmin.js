function loadAll()
{
	getSiteAdmins();
	getSiteAdminsList(); 
	getLecturers(); 
	getLecturerList(); 
	getStudents();
	getStudentList();
}


	
function loadGroupTab(groupId)
    {
           
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetgroupcontent&groupid="+groupId,
            success: function(msg){              
				jQuery('#'+groupId+'_list').html(msg);				
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            },
			beforeSend: function(msg){    				
                jQuery('#'+groupId+'_list').html(showLoading());
            },
			complete: function(msg){              
                getGroupName(groupId)
            }
        });	
        
    }
	
function addUser(groupId, username)
{
	 jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxadduser&groupid="+groupId+"&username="+username,
            success: function(msg){              
				loadGroupTab(groupId);
				jQuery('#result').html(msg);
            }
        });
}

function removeUser(groupId, userid)
{
	//alert(userid);
	 jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxremoveuser&groupid="+groupId+"&userid="+userid,
            success: function(msg){              
				loadGroupTab(groupId);
				//jQuery('#result').html(msg);
            }
        });
}

function showLoading()
{		
	return '<img src="skins/_common/icons/loader.gif">';
}

function getGroupName(groupId)
{
	//load the 
		jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetgroupname&groupid="+groupId,
            success: function(msg){
                jQuery('#groupname').html(msg);				
				jQuery('#groupid').val(groupId);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
		
       
	
}






























/*
function getSiteAdmins()
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetsiteadmins",
            success: function(msg){
                jQuery('#siteadminscontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }
	
	
function getLecturers()
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetlecturers",
            success: function(msg){
                jQuery('#lecturerscontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }
	
	
function getStudents()
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetstudents",
            success: function(msg){
                jQuery('#studentscontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }
	
function getSiteAdminsList()
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetsiteadminslist",
            success: function(msg){
                jQuery('#siteadminscontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }
	
	function getLecturerList()
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetlecturerlist",
            success: function(msg){
                jQuery('#lecturerscontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }
	
	function getStudentList()
    {
         
        jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetstudentist",
            success: function(msg){
                jQuery('#studentscontent').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
        
        
        //alert(workgroupId);
        
    }
	
	*/