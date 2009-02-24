function loadAll()
{
	getSiteAdmins();
	getSiteAdminsList(); 
	getLecturers(); 
	getLecturerList(); 
	getStudents();
	getStudentList();
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