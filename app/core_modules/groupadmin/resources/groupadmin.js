function loadAll()
{
	getSiteAdmins();
	getSiteAdminsList(); 
	getLecturers(); 
	getLecturerList(); 
	getStudents();
	getStudentList();
}

function tabsselect()
{
	alert('ye baby');
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
            }
        });
		
		jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=groupadmin&action=ajaxgetgroupname&groupid="+groupId,
            success: function(msg){
                jQuery('#groupname').html(msg);
                if ('function' == typeof window.adjustLayout) {
                    adjustLayout();
                }
            }
        });
		
        frm = document.getElementById('searchform');
		frm.action = 'index.php&module=groupadmin&groupid='+groupId;
		//frm.searchbutton.value = "Add users to "+groupId;
        //alert(frm.action);
        //alert(workgroupId);
        
    }

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