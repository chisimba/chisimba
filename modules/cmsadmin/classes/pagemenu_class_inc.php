<?php

/* -------------------- cmstree class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

// end security check
/**
* This object is a wrapper class for building a CUSTOM MENU using Editable Pages stored in tbl_cms_page_menu
* @package cms
* @category cmsadmin
* @copyright 2008, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version
* @author Charl Mert
*/

class pagemenu extends object
{

        /**
        * The Content object
        *
        * @access private
        * @var object
        */
        protected $_objPage;

        /**
        * The User object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

        /**
         * Constructor
         */
        public function init()
        {
            try {
                //TODO: Create initial page menu off imported content
				#$this->_objPortalImporterLog = $this->getObject('dblog', 'portalimporter'); 
				$this->sConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->_objPage = & $this->newObject('dbpagemenu', 'cmsadmin');
                $this->_objUser = & $this->newObject('user', 'security');
                $this->objLanguage = & $this->newObject('language', 'language');

            } catch (Exception $e) {
                throw customException($e->getMessage());
                exit();
            }

        }

		
		/**
		* Returns the Page Menu (Flat style custom menu generated via admin interface)
		* Will display the Default OR menu at the given key (Sub Menu)
		*
		* @author Charl Mert
		* @param sectionid the id of the parent section to get child items for
		* @return html menu
		*/
		public function show($state = '', $sectionid = '', $contentid = ''){

			//TODO: the menustate GET var needs to be changeable
		
			$menuKey = $this->getParam('menustate','');
			
			$menu = $this->_objPage->getMenuText($menuKey);

			return $menu;
		}

	
		/*
		* Return the home link
		* @author: Charl Mert
		*/
		public function getHomeLink(){
			//Determining Links Based on Import Log	
	
			//About Link		
			$link_arr['home'] = '?module=cms';

	
			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['home'].'"><h3>Home</h3></a>
        </li>
</ul>
</div>
';

			return $menu;
		}	
	
		/*
		*  This method will return the main UWC menu (Will make this dynamic when the project eases a little)
		* @author: Charl Mert
		*/
		public function getMainMenu(){
			//Determining Links Based on Import Log	


			$link_arr['about'] = '';
			$link_arr['governance'] = '';
			$link_arr['history'] = '';
			$link_arr['location'] = '';
			$link_arr['management'] = '';
			$link_arr['mission'] = '';
			$link_arr['values'] = '';
			$link_arr['academic'] = '';
			$link_arr['academicprogrammes'] = '';
			$link_arr['lifelonglearning'] = '';
			$link_arr['faculties'] = '';
			$link_arr['arts'] = '';
			$link_arr['communityhealth'] = '';
			$link_arr['dentistry'] = '';
			$link_arr['economicmanagement'] = '';
			$link_arr['education'] = '';
			$link_arr['law'] = '';
			$link_arr['naturalscience'] = '';
			$link_arr['facultydepartments'] = '';
			$link_arr['schools'] = '';
			$link_arr['generalrules'] = '';
			$link_arr['student'] = '';
			$link_arr['undergraduate1'] = '';
			$link_arr['postgraduate1'] = '';
			$link_arr['parttime1'] = '';
			$link_arr['international'] = '';
			$link_arr['undergraduate2'] = '';
			$link_arr['postgraduate2'] = '';
			$link_arr['parttime2'] = '';
			$link_arr['international2'] = '';
			$link_arr['research'] = '';
			$link_arr['applicationforresearch'] = '';
			$link_arr['researchactivities'] = '';
			$link_arr['researchpublications'] = '';
			$link_arr['communityoutreach'] = '';
			$link_arr['administrationandsupport'] = '';
			$link_arr['academicplanningunit'] = '';
			$link_arr['campusprotectionservices'] = '';
			$link_arr['financialservices'] = '';
			$link_arr['hr'] = '';
			$link_arr['hivaidsprogramme'] = '';
			$link_arr['ics'] = '';
			$link_arr['ics'] = '';
			$link_arr['library'] = '';
			$link_arr['developmentandpublicaffairs'] = '';
			$link_arr['officestaffdevelopment'] = '';
			$link_arr['pet'] = '';
			$link_arr['sports'] = '';
			$link_arr['studentadmin'] = '';
			$link_arr['studentdevelopementandsupportservices'] = '';
			$link_arr['writtingcenter'] = '';
			$link_arr['alumni1'] = '';
			$link_arr['alumni2'] = '';
			$link_arr['convocation'] = '';
			$link_arr['ourcampus'] = '';

	
			//About Link		
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/index.htm');
			if (isset($c_id[0])) { $link_arr['about'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			//Governance		
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/governance.htm');
			if (isset($c_id[0])) { $link_arr['governance'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}


			//History		
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/history.htm');
			if (isset($c_id[0])) { $link_arr['history'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}


			//Location		
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/location.htm');
			if (isset($c_id[0])) { $link_arr['location'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}


			//Management
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management.htm');
			if (isset($c_id[0])) { $link_arr['management'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}


			//Mission		
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/index.htm');
			if (isset($c_id[0])) { $link_arr['mission'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}


			//Values
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/core_values.htm');
			if (isset($c_id[0])) { $link_arr['values'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}


			//Academic
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/index.htm');
			if (isset($c_id[0])) { $link_arr['academic'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}

			//Academic Programmes
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/programmes.htm');
			if (isset($c_id[0])) { $link_arr['academicprogrammes'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			//Lifelong Learning		
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/lifelong_learning.htm');
			if (isset($c_id[0])) { $link_arr['lifelonglearning'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			//Faculties	
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/faculties.htm');
			if (isset($c_id[0])) { $link_arr['faculties'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			//Arts	
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/index.htm');
			if (isset($c_id[0])) { $link_arr['arts'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';}


			//Community Health
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/index.htm');
			if (isset($c_id[0])) { $link_arr['communityhealth'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

			//Dentistry
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/index.htm');
			if (isset($c_id[0])) { $link_arr['dentistry'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';}

			//Economic Management
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/index.htm');
			if (isset($c_id[0])) { $link_arr['economicmanagement'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_ems';}

			//Education
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/index.htm');
			if (isset($c_id[0])) { $link_arr['education'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_edu';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/index.htm');
			if (isset($c_id[0])) { $link_arr['law'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_law';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/index.htm');
			if (isset($c_id[0])) { $link_arr['naturalscience'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_nsc';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/departments.htm');
			if (isset($c_id[0])) { $link_arr['facultydepartments'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/index.htm');
			if (isset($c_id[0])) { $link_arr['schools'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/portal_desktop/download.htm');
			if (isset($c_id[0])) { $link_arr['generalrules'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/index.htm');
			if (isset($c_id[0])) { $link_arr['student'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/new_admission_requirements.htm');
			if (isset($c_id[0])) { $link_arr['undergraduate1'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/index.htm');
			if (isset($c_id[0])) { $link_arr['postgraduate1'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/index.htm');
			if (isset($c_id[0])) { $link_arr['parttime1'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/index.htm');
			if (isset($c_id[0])) { $link_arr['international'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/index.htm');
			if (isset($c_id[0])) { $link_arr['undergraduate2'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/index.htm');
			if (isset($c_id[0])) { $link_arr['postgraduate2'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/index.htm');
			if (isset($c_id[0])) { $link_arr['parttime2'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/index.htm');
			if (isset($c_id[0])) { $link_arr['international2'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/research.htm');
			if (isset($c_id[0])) { $link_arr['research'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=research';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/research.htm');
			if (isset($c_id[0])) { $link_arr['applicationforresearch'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=research';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/research.htm');
			if (isset($c_id[0])) { $link_arr['researchactivities'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=research';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/research.htm');
			if (isset($c_id[0])) { $link_arr['researchpublications'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=research';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/index.htm');
			if (isset($c_id[0])) { $link_arr['communityoutreach'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=communityoutreach';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/administration_support/index.htm');
			if (isset($c_id[0])) { $link_arr['administrationandsupport'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/index.htm');
			if (isset($c_id[0])) { $link_arr['academicplanningunit'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_apu';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_campus/campus_services.htm');
			if (isset($c_id[0])) { $link_arr['campusprotectionservices'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/administration_support/central_administration.htm');
			if (isset($c_id[0])) { $link_arr['financialservices'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/index.htm');
			if (isset($c_id[0])) { $link_arr['hr'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';}

			//
			if (isset($c_id[0])) { $link_arr['hivaidsprogramme'] = 'http://hivaids.uwc.ac.za';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/administration_support/central_administration.htm');
			//ICS LINK TO POINT TO WEBSITE
			//if (isset($c_id[0])) { $link_arr['ics'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}
			if (isset($c_id[0])) { $link_arr['ics'] = 'http://ics.uwc.ac.za/';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_campus/library.htm');
			if (isset($c_id[0])) { $link_arr['library'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/index.htm');
			if (isset($c_id[0])) { $link_arr['developmentandpublicaffairs'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/index.htm');
			if (isset($c_id[0])) { $link_arr['officestaffdevelopment'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/index.htm');
			if (isset($c_id[0])) { $link_arr['pet'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_campus/sports.htm');
			if (isset($c_id[0])) { $link_arr['sports'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/student_administration/index.htm');
			if (isset($c_id[0])) { $link_arr['studentadmin'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_admin';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/index.htm');
			if (isset($c_id[0])) { $link_arr['studentdevelopementandsupportservices'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_campus/library.htm');
			if (isset($c_id[0])) { $link_arr['writtingcenter'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}
	
			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/index.htm');
			if (isset($c_id[0])) { $link_arr['alumni1'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/alumni.htm');
			if (isset($c_id[0])) { $link_arr['alumni2'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/convocation/index.htm');
			if (isset($c_id[0])) { $link_arr['convocation'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';}

			//
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_campus/index.htm');
			if (isset($c_id[0])) { $link_arr['ourcampus'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';}
		
			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['about'].'"><h3>About UWC</h3></a>
        </li>
<li><a href="'.$link_arr['governance'].'">Governance</a></li>
<li><a href="'.$link_arr['history'].'">History</a></li>
<li><a href="'.$link_arr['location'].'">Location</a></li>
<li><a href="'.$link_arr['management'].'">Management</a></li>
<li><a href="'.$link_arr['mission'].'">Mission</a></li>
<li><a href="'.$link_arr['values'].'">Values</a></li>
</ul>
</div>
<p/>
<div class="menublock">
<ul>
<li><a href="'.$link_arr['academic'].'"><h3>Academic</h3></a></li>
<li><a href="'.$link_arr['academicprogrammes'].'">Academic Programmes</a></li>
<li><a href="'.$link_arr['lifelonglearning'].'">Division of Lifelong Learning</a></li>
<li>
  <ul><a href="'.$link_arr['faculties'].'">Faculties:</a>
   <li><a href="'.$link_arr['arts'].'"><img src="skins/uwcportal2/images/bgcolor_arts.png" border="0">&nbsp;&nbsp; <span id="facultytext">Arts</span></a></li>
   <li><a href="'.$link_arr['communityhealth'].'"><img src="skins/uwcportal2/images/bgcolor_community.png" border="0">&nbsp;&nbsp; <span id="facultytext">Community &amp; Health</span></a></li>
   <li><a href="'.$link_arr['dentistry'].'"><img src="skins/uwcportal2/images/bgcolor_dentistry.png" border="0">&nbsp;&nbsp; <span id="facultytext">Dentistry</span></a></li>
   <li><a href="'.$link_arr['economicmanagement'].'"><img src="skins/uwcportal2/images/bgcolor_economic.png" border="0">&nbsp;&nbsp; <span id="facultytext">Economic &amp; Management</span></a></li>
   <li><a href="'.$link_arr['education'].'"><img src="skins/uwcportal2/images/bgcolor_education.png" border="0">&nbsp;&nbsp; <span id="facultytext">Education</span></a></li>
   <li><a href="'.$link_arr['law'].'"><img src="skins/uwcportal2/images/bgcolor_law.png" border="0">&nbsp;&nbsp; <span id="facultytext">Law</span></a></li>
   <li><a href="'.$link_arr['naturalscience'].'"><img src="skins/uwcportal2/images/bgcolor_science.png" border="0">&nbsp;&nbsp; <span id="facultytext">Natural Science</span></a></li>
 </ul>
</li>
<li><a href="'.$link_arr['facultydepartments'].'">Faculty Departments</a></li>
<li><a href="'.$link_arr['schools'].'">Schools, Institutes, Centres &amp; Units</a></li>
<li><a href="'.$link_arr['generalrules'].'">General Rules &amp; Procedures</a></li>
</ul>
</div>
<p/>

<div class="menublock">
<ul>
		<li>
			<a href="'.$link_arr['student'].'"><h3>Student</h3></a>
		</li>
<li>Prospective
  <ul>
   <li><a href="'.$link_arr['undergraduate1'].'">Undergraduate</a></li>
   <li><a href="'.$link_arr['postgraduate1'].'">Postgraduate</a></li>
   <li><a href="'.$link_arr['parttime1'].'">Part-time</a></li>
   <li><a href="'.$link_arr['international'].'">International</a></li>
  </ul>
</li>
<li>Current
<ul>
  <li><a href="'.$link_arr['undergraduate2'].'">Undergraduate</a></li>
  <li><a href="'.$link_arr['postgraduate2'].'">Postgraduate</a></li>
  <li><a href="'.$link_arr['parttime2'].'">Part-time</a></li>
  <li><a href="'.$link_arr['international2'].'">International</a></li>
</ul>
</li>
</ul>
</div>
<p/>
<div class="menublock">
<ul>
<li><a href="'.$link_arr['research'].'"><h3>Research</h3></a></li>
<li><a href="'.$link_arr['applicationforresearch'].'">Application for Research</a></li>
<li><a href="'.$link_arr['researchactivities'].'">Research Activities</a></li>
<li><a href="'.$link_arr['researchpublications'].'">Research Publications</a></li>
</ul>
</div>
<p/>
<div class="menublock">
<ul>
<li><a href="'.$link_arr['communityoutreach'].'"><h3>Community Outreach</h3></a></li>
</ul>
</div>
<p/>
<div class="menublock">
<ul>
	<li>
		<a href="'.$link_arr['administrationandsupport'].'"><h3>Administration &amp; Support</h3></a>
	</li>
<li>
<ul>
<li><a href="'.$link_arr['academicplanningunit'].'">Academic Planning Unit</a></li>
<li><a href="'.$link_arr['campusprotectionservices'].'">Campus Protection Services</a></li>
<li>Committee Secretariat</li>
<li><a href="'.$link_arr['financialservices'].'">Financial Services</a></li>
<li><a href="'.$link_arr['hr'].'">Human Resources</a></li>
<li><a href="'.$link_arr['hivaidsprogramme'].'">HIV / AIDS Programme</a></li>
<li><a href="'.$link_arr['ics'].'">Information and Communication Services</a></li>
<li>International Relations</li>
<li><a href="'.$link_arr['library'].'">Library</li>
<li><a href="'.$link_arr['developmentandpublicaffairs'].'">Office of Development and Public Affairs</a></li>
<li><a href="'.$link_arr['officestaffdevelopment'].'">Office of Staff Development</a></li>
<li><a href="'.$link_arr['pet'].'">PET Project</a></li>
<li><a href="'.$link_arr['sports'].'">Sports</a></li>
<li><a href="'.$link_arr['studentadmin'].'">Student Administration</a></li>
<li><a href="'.$link_arr['studentdevelopementandsupportservices'].'">Student Development and Support Services</a></li>
<li><a href="'.$link_arr['writtingcenter'].'">Writing Centre</a></li>
</ul>
</li>
</ul>

</div>
<p/>
<div class="menublock">

<ul>
<li><a href="'.$link_arr['alumni1'].'"><h3>Alumni &amp; Convocation</h3></a></li>
<li><a href="'.$link_arr['alumni2'].'">Alumni</a></li>
<li><a href="'.$link_arr['convocation'].'">Convocation</a></li>
</ul>

</div>
<p/>
<div class="menublock">

<ul>
<li><a href="'.$link_arr['ourcampus'].'"><h3>Our Campus<h3></a></li>
</ul>
</div>
';
			return $this->getHomeLink().$menu;
		}
		









		
		
/*
		*  This method will return the ABOUT menu
		* @author: Charl Mert
		*/
		public function getAboutMenu(){


			$link_arr['missionstatement'] = '';
            $link_arr['aproudhistory'] = '';
            $link_arr['location'] = '';
            $link_arr['corevalues'] = '';
            $link_arr['strategicareas'] = '';
            $link_arr['teachinglearning'] = '';
            $link_arr['researchdev'] = '';
            $link_arr['managementgov'] = '';
            $link_arr['humancapital'] = '';
            $link_arr['studentdev'] = '';
            $link_arr['financeincome'] = '';
            $link_arr['comm'] = '';
            $link_arr['governmanage'] = '';
            $link_arr['gov'] = '';
            $link_arr['govstruct'] = '';
            $link_arr['inst'] = '';
            $link_arr['codeofconduct'] = '';
            $link_arr['policylib'] = '';
            $link_arr['management'] = '';
            $link_arr['academic'] = '';
            $link_arr['studentdevsupport'] = '';
            $link_arr['finance'] = '';
            $link_arr['hr'] = '';
            $link_arr['it'] = '';
            $link_arr['registrar'] = '';
            $link_arr['rectorsoffice'] = '';
            $link_arr['instplanning'] = '';



			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/index.htm');
			if (isset($c_id[0])) { $link_arr ['missionstatement'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/history.htm');
			if (isset($c_id[0])) { $link_arr ['aproudhistory'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/location.htm');
			if (isset($c_id[0])) { $link_arr ['location'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/core_values.htm');
			if (isset($c_id[0])) { $link_arr ['corevalues'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/strategy.htm');
			if (isset($c_id[0])) { $link_arr ['strategicareas'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/strategy_01.htm');
			if (isset($c_id[0])) { $link_arr ['teachinglearning'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/strategy_02.htm');
			if (isset($c_id[0])) { $link_arr ['researchdev'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}
	
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/strategy_03.htm');
			if (isset($c_id[0])) { $link_arr ['managementgov'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/strategy_04.htm');
			if (isset($c_id[0])) { $link_arr ['humancapital'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/strategy_05.htm');
			if (isset($c_id[0])) { $link_arr ['studentdev'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/strategy_06.htm');
			if (isset($c_id[0])) { $link_arr ['financeincome'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/strategy_07.htm');
			if (isset($c_id[0])) { $link_arr ['comm'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/governance_management.htm');
			if (isset($c_id[0])) { $link_arr ['governmanage'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/governance.htm');
			if (isset($c_id[0])) { $link_arr ['gov'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/governance_structure.htm');
			if (isset($c_id[0])) { $link_arr ['govstruct'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/statute.htm');
			if (isset($c_id[0])) { $link_arr ['inst'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}
	
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/council_code_of_conduct.htm');
			if (isset($c_id[0])) { $link_arr ['codeofconduct'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/policy_library.htm');
			if (isset($c_id[0])) { $link_arr ['policylib'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management.htm');
			if (isset($c_id[0])) { $link_arr ['management'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management_01.htm');
			if (isset($c_id[0])) { $link_arr ['academic'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management_02.htm');
			if (isset($c_id[0])) { $link_arr ['studentdevsupport'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management_03.htm');
			if (isset($c_id[0])) { $link_arr ['finance'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management_04.htm');
			if (isset($c_id[0])) { $link_arr ['hr'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management_05.htm');
			if (isset($c_id[0])) { $link_arr ['it'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management_06.htm');
			if (isset($c_id[0])) { $link_arr ['registrar'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management_07.htm');
			if (isset($c_id[0])) { $link_arr ['rectorsoffice'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/about_uwc/management_08.htm');
			if (isset($c_id[0])) { $link_arr ['instplanning'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=about';}

	
			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['missionstatement'].'"><h3>Mission Statement</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['aproudhistory'].'">A Proud History</a></li>
<li><a href="'.$link_arr['location'].'">Location</a></li>
<li><a href="'.$link_arr['corevalues'].'">Our Core Values</a></li>
	</ul>
</ul>
</div>
<p/>
<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['strategicareas'].'"><h3>Strategic Areas</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['teachinglearning'].'">Teaching &amp; Learning</a></li>
<li><a href="'.$link_arr['researchdev'].'">Research &amp; Development</a></li>
<li><a href="'.$link_arr['managementgov'].'">Management &amp; Govern.</a></li>
<li><a href="'.$link_arr['humancapital'].'">Human Capital</a></li>
<li><a href="'.$link_arr['studentdev'].'">Student Development</a></li>
<li><a href="'.$link_arr['financeincome'].'">Finance &amp; Income</a></li>
<li><a href="'.$link_arr['comm'].'">Community &amp; Marketing</a></li>
	</ul>
	
</ul>
</div>
<p/>
<div class="menublock">
<ul>

     	<li>
                <a href="'.$link_arr['governmanage'].'"><h3>Govern &amp; Manage</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['gov'].'">Governance</a></li>
<li><a href="'.$link_arr['govstruct'].'">Governance Structure</a></li>
<li><a href="'.$link_arr['inst'].'">Institutional Statute</a></li>
<li><a href="'.$link_arr['codeofconduct'].'">Council Code of Conduct</a></li>
<li><a href="'.$link_arr['policylib'].'">Policy Library</a></li>
	</ul>

</ul>
</div>
<p/>
<div class="menublock">
<ul>
	
     	<li>
                <a href="'.$link_arr['management'].'"><h3>Management</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['academic'].'">Academic</a></li>
<li><a href="'.$link_arr['studentdevsupport'].'">Student Dev. &amp; Support</a></li>
<li><a href="'.$link_arr['finance'].'">Finance</a></li>
<li><a href="'.$link_arr['hr'].'">Human Resources</a></li>
<li><a href="'.$link_arr['it'].'">Information Technology</a></li>
<li><a href="'.$link_arr['registrar'].'">Registrar</a></li>
<li><a href="'.$link_arr['rectorsoffice'].'">Rector\'s Office</a></li>
<li><a href="'.$link_arr['instplanning'].'">Institutional Planning</a></li>
	</ul>
		
</ul>
</div>
';
			
			return $this->getHomeLink().$menu;
		}		






		/*
		*  This method will return the STUDENT menu
		* @author: Charl Mert
		*/
		public function getAcademicMenu() {


			$link_arr['academic'] = '';
            $link_arr['faculties'] = '';
            $link_arr['departments'] = '';
            $link_arr['schools'] = '';
            $link_arr['inst'] = '';
            $link_arr['centers'] = '';
            $link_arr['units'] = '';
            $link_arr['academicprogrammes'] = '';
            $link_arr['undergraduate'] = '';
            $link_arr['postgraduate'] = '';
            $link_arr['parttime'] = '';
            $link_arr['research'] = '';
            $link_arr['dean'] = '';
            $link_arr['researchfocus'] = '';
            $link_arr['researchunits'] = '';
            $link_arr['lifelonglearning'] = '';


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/index.htm');
			if (isset($c_id[0])) { $link_arr ['academic'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/faculties.htm');
			if (isset($c_id[0])) { $link_arr ['faculties'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/departments.htm');
			if (isset($c_id[0])) { $link_arr ['departments'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/schools.htm');
			if (isset($c_id[0])) { $link_arr ['schools'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/institutes.htm');
			if (isset($c_id[0])) { $link_arr ['inst'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/centres.htm');
			if (isset($c_id[0])) { $link_arr ['centers'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/units.htm');
			if (isset($c_id[0])) { $link_arr ['units'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/programmes.htm');
			if (isset($c_id[0])) { $link_arr ['academicprogrammes'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/programmes_undergraduate.htm');
			if (isset($c_id[0])) { $link_arr ['undergraduate'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/programmes_postgraduate.htm');
			if (isset($c_id[0])) { $link_arr ['postgraduate'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/programmes_parttime.htm');
			if (isset($c_id[0])) { $link_arr ['parttime'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/research.htm');
			if (isset($c_id[0])) { $link_arr ['research'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/research_dean.htm');
			if (isset($c_id[0])) { $link_arr ['dean'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/research_focus.htm');
			if (isset($c_id[0])) { $link_arr ['researchfocus'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/research_units.htm');
			if (isset($c_id[0])) { $link_arr ['researchunits'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/academic/lifelong_learning.htm');
			if (isset($c_id[0])) { $link_arr ['lifelonglearning'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=academic';}




			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['academic'].'"><h3>Academic</h3></a>
        </li>

        <li>
                <a href="'.$link_arr['faculties'].'"><h3>Faculties</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr['departments'].'">Departments</a></li>
<li><a href="'.$link_arr['schools'].'">Schools</a></li>
<li><a href="'.$link_arr['inst'].'">Institutes</a></li>
<li><a href="'.$link_arr['centers'].'">Centers</a></li>
<li><a href="'.$link_arr['units'].'">Units</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['academicprogrammes'].'"><h3>Academic Programmes</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr['undergraduate'].'">Undergraduate</a></li>
<li><a href="'.$link_arr['postgraduate'].'">Post Graduate</a></li>
<li><a href="'.$link_arr['parttime'].'">Part-time</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['research'].'"><h3>Research</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr['dean'].'">Dean</a></li>
<li><a href="'.$link_arr['researchfocus'].'">Research Focus</a></li>
<li><a href="'.$link_arr['researchunits'].'">Research Units</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['lifelonglearning'].'"><h3>Life Long Learning</h3></a>
        </li>

</ul>
</div>



';
			
			return $this->getHomeLink().$menu;
		}				










		/*
		*  This method will return the STUDENT menu
		* @author: Charl Mert
		*/
		public function getStudentMenu(){
	
			$link_arr['faq'] = '';
            $link_arr['admission'] = '';
            $link_arr['finance'] = '';
            $link_arr['accomodation'] = '';
            $link_arr['orientation'] = '';
            $link_arr['registration'] = '';
            $link_arr['studentcard'] = '';
            $link_arr['special'] = '';
            $link_arr['transport'] = '';
            $link_arr['changestudy'] = '';
            $link_arr['termination'] = '';
            $link_arr['_undergraduate'] = '';
            $link_arr['_postgraduate'] = '';
            $link_arr['_parttime'] = '';
            $link_arr['_international'] = '';


			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq.htm');
			if (isset($c_id[0])) { $link_arr ['faq'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_admission.htm');
			if (isset($c_id[0])) { $link_arr ['admission'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_finance.htm');
			if (isset($c_id[0])) { $link_arr ['finance'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_accommodation.htm');
			if (isset($c_id[0])) { $link_arr ['accomodation'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_orientation.htm');
			if (isset($c_id[0])) { $link_arr ['orientation'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_registration.htm');
			if (isset($c_id[0])) { $link_arr ['registration'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_studentcard.htm');
			if (isset($c_id[0])) { $link_arr ['studentcard'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_specialneeds.htm');
			if (isset($c_id[0])) { $link_arr ['special'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_transport.htm');
			if (isset($c_id[0])) { $link_arr ['transport'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_changestudy.htm');
			if (isset($c_id[0])) { $link_arr ['changestudy'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/student/faq_termination.htm');
			if (isset($c_id[0])) { $link_arr ['termination'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student';}



			//Missed these items somehow hehe oops
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/index.htm');
                        if (isset($c_id[0])) { $link_arr ['_undergraduate'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/index.htm');
                        if (isset($c_id[0])) { $link_arr ['_postgraduate'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/index.htm');
                        if (isset($c_id[0])) { $link_arr ['_parttime'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/index.htm');
                        if (isset($c_id[0])) { $link_arr ['_international'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';}


			$menu = '


<div class="menublock">
<ul>
        <li><a href="'.$link_arr['_undergraduate'].'"><h3>Undergraduate</h3></a></li>
        <li><a href="'.$link_arr['_postgraduate'].'"><h3>Postgraduate</h3></a></li>
        <li><a href="'.$link_arr['_parttime'].'"><h3>Part-Time</h3></a></li>
        <li><a href="'.$link_arr['_international'].'"><h3>International</h3></a></li>
</ul>
</div>




<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['faq'].'"><h3>FAQs</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['admission'].'">Admission</a></li>
<li><a href="'.$link_arr['finance'].'">Fincance</a></li>
<li><a href="'.$link_arr['accomodation'].'">Accomodation</a></li>
<li><a href="'.$link_arr['orientation'].'">Orientation</a></li>
<li><a href="'.$link_arr['registration'].'">Registration</a></li>
<li><a href="'.$link_arr['studentcard'].'">Student Card</a></li>
<li><a href="'.$link_arr['special'].'">Special Needs</a></li>
<li><a href="'.$link_arr['transport'].'">Transport/Parking</a></li>
<li><a href="'.$link_arr['changestudy'].'">Change Study</a></li>
<li><a href="'.$link_arr['termination'].'">Terminate Study</a></li>
	</ul>
		
</ul>
</div>




';
			
			return $this->getHomeLink().$menu;
		}				





		/*
		*  This method will return the STUDENT INTERNATIONAL MENU menu
		* @author: Charl Mert
		*/
		public function getStudentInternationalMenu(){


		

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/admission.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/admission_general.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/admission_requirements.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/admission_discretionary.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/admission_procedures.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/admission_application.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/admission_readmission.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/admission_response.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/financial.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/financial_fees.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/financial_credit.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/financial_aid.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/residence.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/residence_houses.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/registration.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/registration_adjustments.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/registration_late.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/registration_secondsemester.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/registration_concurrent.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/registration_dualjoint.htm');
$link_arr[20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/registration_otheruniversity.htm');
$link_arr[21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/advisory.htm');
$link_arr[22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/section02_01.htm');
$link_arr[23] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/advisory_programmes.htm');
$link_arr[24] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/studentrecord.htm');
$link_arr[25] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/studentrecord_studentcard.htm');
$link_arr[26] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/studentrecord_transcripts.htm');
$link_arr[27] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/studentrecord_terminationofstudy.htm');
$link_arr[28] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq.htm');
$link_arr[29] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_admission.htm');
$link_arr[30] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_finance.htm');
$link_arr[31] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_accommodation.htm');
$link_arr[32] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_orientation.htm');
$link_arr[33] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_registration.htm');
$link_arr[34] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_studentcard.htm');
$link_arr[35] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_specialneeds.htm');
$link_arr[36] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_transport.htm');
$link_arr[37] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_changestudy.htm');
$link_arr[38] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/international/faq_termination.htm');
$link_arr[39] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_int';



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Part Time</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[1].'"><h3>Admission</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[2].'">General Information</a></li>
<li><a href="'.$link_arr[3].'">Requirements</a></li>
<li><a href="'.$link_arr[4].'">Descretionary</a></li>
<li><a href="'.$link_arr[5].'">Procedures</a></li>
<li><a href="'.$link_arr[6].'">Application</a></li>
<li><a href="'.$link_arr[7].'">Re-Admission</a></li>
<li><a href="'.$link_arr[8].'">Response to Application</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[9].'"><h3>Financial Matters</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[10].'">Fees</a></li>
<li><a href="'.$link_arr[11].'">Credit Management</a></li>
<li><a href="'.$link_arr[12].'">Financial Aid</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[13].'"><h3>Residence</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[14].'">Residence Houses</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[15].'"><h3>Registration</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[16].'">Adjustments</a></li>
<li><a href="'.$link_arr[17].'">Late Registration</a></li>
<li><a href="'.$link_arr[18].'">Second Semester</a></li>
<li><a href="'.$link_arr[19].'">Concurrent</a></li>
<li><a href="'.$link_arr[20].'">Dual/Joint</a></li>
<li><a href="'.$link_arr[21].'">Other Universities</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[22].'"><h3>Student Advisory</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[23].'">Policies &amp; Rules</a></li>
<li><a href="'.$link_arr[24].'">Programme &amp; Modules</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[25].'"><h3>Student Record</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[26].'">Student Card</a></li>
<li><a href="'.$link_arr[27].'">Transcripts</a></li>
<li><a href="'.$link_arr[28].'">Termination of Study</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[29].'"><h3>FAQ\'s</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[30].'">Admission</a></li>
<li><a href="'.$link_arr[31].'">Finance</a></li>
<li><a href="'.$link_arr[32].'">Accomodation</a></li>
<li><a href="'.$link_arr[33].'">Orientation</a></li>
<li><a href="'.$link_arr[34].'">Registration</a></li>
<li><a href="'.$link_arr[35].'">Student Card</a></li>
<li><a href="'.$link_arr[36].'">Special Needs</a></li>
<li><a href="'.$link_arr[37].'">Transport/Parking</a></li>
<li><a href="'.$link_arr[38].'">Change Study</a></li>
<li><a href="'.$link_arr[39].'">Terminate Study</a></li>



	</ul>
		
</ul>
</div>



';


			
			return $this->getHomeLink().$menu;
		}				








		/*
		*  This method will return the STUDENT PARTTIME MENU menu
		* @author: Charl Mert
		*/
		public function getStudentPartTimeMenu(){


$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/admission.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/admission_general.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/admission_requirements.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/admission_discretionary.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/admission_procedures.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/admission_application.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/admission_readmission.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/admission_response.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/financial.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/financial_fees.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/financial_credit.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/financial_aid.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/residence.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/residence_houses.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/registration.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/registration_adjustments.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/registration_late.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/registration_secondsemester.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/registration_concurrent.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/registration_dualjoint.htm');
$link_arr[20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/registration_otheruniversity.htm');
$link_arr[21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/advisory.htm');
$link_arr[22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/section02_01.htm');
$link_arr[23] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/advisory_programmes.htm');
$link_arr[24] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/studentrecord.htm');
$link_arr[25] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/studentrecord_studentcard.htm');
$link_arr[26] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/studentrecord_transcripts.htm');
$link_arr[27] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/studentrecord_terminationofstudy.htm');
$link_arr[28] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq.htm');
$link_arr[29] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_admission.htm');
$link_arr[30] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_finance.htm');
$link_arr[31] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_accommodation.htm');
$link_arr[32] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_orientation.htm');
$link_arr[33] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_registration.htm');
$link_arr[34] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_studentcard.htm');
$link_arr[35] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_specialneeds.htm');
$link_arr[36] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_transport.htm');
$link_arr[37] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_changestudy.htm');
$link_arr[38] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/parttime/faq_termination.htm');
$link_arr[39] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_part';


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Part Time</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[1].'"><h3>Admission</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[2].'">General Information</a></li>
<li><a href="'.$link_arr[3].'">Requirements</a></li>
<li><a href="'.$link_arr[4].'">Descretionary</a></li>
<li><a href="'.$link_arr[5].'">Procedures</a></li>
<li><a href="'.$link_arr[6].'">Application</a></li>
<li><a href="'.$link_arr[7].'">Re-Admission</a></li>
<li><a href="'.$link_arr[8].'">Response to Application</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[9].'"><h3>Financial Matters</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[10].'">Fees</a></li>
<li><a href="'.$link_arr[11].'">Credit Management</a></li>
<li><a href="'.$link_arr[12].'">Financial Aid</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[13].'"><h3>Residence</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[14].'">Residence Houses</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[15].'"><h3>Registration</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[16].'">Adjustments</a></li>
<li><a href="'.$link_arr[17].'">Late Registration</a></li>
<li><a href="'.$link_arr[18].'">Second Semester</a></li>
<li><a href="'.$link_arr[19].'">Concurrent</a></li>
<li><a href="'.$link_arr[20].'">Dual/Joint</a></li>
<li><a href="'.$link_arr[21].'">Other Universities</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[22].'"><h3>Student Advisory</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[23].'">Policies &amp; Rules</a></li>
<li><a href="'.$link_arr[24].'">Programme &amp; Modules</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[25].'"><h3>Student Record</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[26].'">Student Card</a></li>
<li><a href="'.$link_arr[27].'">Transcripts</a></li>
<li><a href="'.$link_arr[28].'">Termination of Study</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[29].'"><h3>FAQ\'s</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[30].'">Admission</a></li>
<li><a href="'.$link_arr[31].'">Finance</a></li>
<li><a href="'.$link_arr[32].'">Accomodation</a></li>
<li><a href="'.$link_arr[33].'">Orientation</a></li>
<li><a href="'.$link_arr[34].'">Registration</a></li>
<li><a href="'.$link_arr[35].'">Student Card</a></li>
<li><a href="'.$link_arr[36].'">Special Needs</a></li>
<li><a href="'.$link_arr[37].'">Transport/Parking</a></li>
<li><a href="'.$link_arr[38].'">Change Study</a></li>
<li><a href="'.$link_arr[39].'">Terminate Study</a></li>



	</ul>
		
</ul>
</div>



';


			
			return $this->getHomeLink().$menu;
		}				






		/*
		*  This method will return the STUDENT POSTGRAD MENU menu
		* @author: Charl Mert
		*/
		public function getStudentPostgraduateMenu(){


$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/admission.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/admission_general.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/admission_requirements.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/admission_discretionary.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/admission_procedures.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/admission_application.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/admission_readmission.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/admission_response.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/financial.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/financial_fees.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/financial_credit.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/financial_aid.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/residence.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/residence_houses.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/registration.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/registration_adjustments.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/registration_late.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/registration_secondsemester.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/registration_concurrent.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/registration_dualjoint.htm');
$link_arr[20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/registration_otheruniversity.htm');
$link_arr[21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/advisory.htm');
$link_arr[22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/section02_01.htm');
$link_arr[23] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/advisory_programmes.htm');
$link_arr[24] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/studentrecord.htm');
$link_arr[25] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/studentrecord_studentcard.htm');
$link_arr[26] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/studentrecord_transcripts.htm');
$link_arr[27] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/studentrecord_terminationofstudy.htm');
$link_arr[28] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq.htm');
$link_arr[29] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_admission.htm');
$link_arr[30] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_finance.htm');
$link_arr[31] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_accommodation.htm');
$link_arr[32] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_orientation.htm');
$link_arr[33] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_registration.htm');
$link_arr[34] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_studentcard.htm');
$link_arr[35] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_specialneeds.htm');
$link_arr[36] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_transport.htm');
$link_arr[37] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_changestudy.htm');
$link_arr[38] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/postgraduate/faq_termination.htm');
$link_arr[39] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_post';



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Postgraduate</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[1].'"><h3>Admission</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[2].'">General Information</a></li>
<li><a href="'.$link_arr[3].'">Requirements</a></li>
<li><a href="'.$link_arr[4].'">Descretionary</a></li>
<li><a href="'.$link_arr[5].'">Procedures</a></li>
<li><a href="'.$link_arr[6].'">Application</a></li>
<li><a href="'.$link_arr[7].'">Re-Admission</a></li>
<li><a href="'.$link_arr[8].'">Response to Application</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[9].'"><h3>Financial Matters</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[10].'">Fees</a></li>
<li><a href="'.$link_arr[11].'">Credit Management</a></li>
<li><a href="'.$link_arr[12].'">Financial Aid</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[13].'"><h3>Residence</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[14].'">Residence Houses</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[15].'"><h3>Registration</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[16].'">Adjustments</a></li>
<li><a href="'.$link_arr[17].'">Late Registration</a></li>
<li><a href="'.$link_arr[18].'">Second Semester</a></li>
<li><a href="'.$link_arr[19].'">Concurrent</a></li>
<li><a href="'.$link_arr[20].'">Dual/Joint</a></li>
<li><a href="'.$link_arr[21].'">Other Universities</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[22].'"><h3>Student Advisory</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[23].'">Policies &amp; Rules</a></li>
<li><a href="'.$link_arr[24].'">Programme &amp; Modules</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[25].'"><h3>Student Record</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[26].'">Student Card</a></li>
<li><a href="'.$link_arr[27].'">Transcripts</a></li>
<li><a href="'.$link_arr[28].'">Termination of Study</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[29].'"><h3>FAQ\'s</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[30].'">Admission</a></li>
<li><a href="'.$link_arr[31].'">Finance</a></li>
<li><a href="'.$link_arr[32].'">Accomodation</a></li>
<li><a href="'.$link_arr[33].'">Orientation</a></li>
<li><a href="'.$link_arr[34].'">Registration</a></li>
<li><a href="'.$link_arr[35].'">Student Card</a></li>
<li><a href="'.$link_arr[36].'">Special Needs</a></li>
<li><a href="'.$link_arr[37].'">Transport/Parking</a></li>
<li><a href="'.$link_arr[38].'">Change Study</a></li>
<li><a href="'.$link_arr[39].'">Terminate Study</a></li>



	</ul>
		
</ul>
</div>



';


			
			return $this->getHomeLink().$menu;
		}				




		
		/*
		*  This method will return the STUDENT UNDERGRAD MENU menu
		* @author: Charl Mert
		*/
		public function getStudentUndergraduateMenu(){

		for ($i = 0; $i < 40; $i++){
			$link_arr[$i] = '';
		}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/index.htm');
if (isset($c_id[0])) { $link_arr [0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/admission.htm');
if (isset($c_id[0])) { $link_arr [1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/admission_general.htm');
if (isset($c_id[0])) { $link_arr [2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/new_admission_requirements.htm');
if (isset($c_id[0])) { $link_arr [3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/admission_requirements.htm');
if (isset($c_id[0])) { $link_arr [4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/admission_discretionary.htm');
if (isset($c_id[0])) { $link_arr [5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/admission_procedures.htm');
if (isset($c_id[0])) { $link_arr [6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/admission_application.htm');
if (isset($c_id[0])) { $link_arr [7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/admission_readmission.htm');
if (isset($c_id[0])) { $link_arr [8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/admission_response.htm');
if (isset($c_id[0])) { $link_arr [9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/financial.htm');
if (isset($c_id[0])) { $link_arr [10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/financial_fees.htm');
if (isset($c_id[0])) { $link_arr [11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/financial_credit.htm');
if (isset($c_id[0])) { $link_arr [12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/financial_aid.htm');
if (isset($c_id[0])) { $link_arr [13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/residence.htm');
if (isset($c_id[0])) { $link_arr [14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/residence_houses.htm');
if (isset($c_id[0])) { $link_arr [15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/registration.htm');
if (isset($c_id[0])) { $link_arr [16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/registration_adjustments.htm');
if (isset($c_id[0])) { $link_arr [17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/registration_late.htm');
if (isset($c_id[0])) { $link_arr [18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/registration_secondsemester.htm');
if (isset($c_id[0])) { $link_arr [19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/registration_concurrent.htm');
if (isset($c_id[0])) { $link_arr [20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/registration_dualjoint.htm');
if (isset($c_id[0])) { $link_arr [21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/registration_otheruniversity.htm');
if (isset($c_id[0])) { $link_arr [22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/advisory.htm');
if (isset($c_id[0])) { $link_arr [23] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/section02_01.htm');
if (isset($c_id[0])) { $link_arr [24] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/advisory_programmes.htm');
if (isset($c_id[0])) { $link_arr [25] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/studentrecord.htm');
if (isset($c_id[0])) { $link_arr [26] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/studentrecord_studentcard.htm');
if (isset($c_id[0])) { $link_arr [27] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/studentrecord_transcripts.htm');
if (isset($c_id[0])) { $link_arr [28] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/studentrecord_terminationofstudy.htm');
if (isset($c_id[0])) { $link_arr [29] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq.htm');
if (isset($c_id[0])) { $link_arr [30] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_admission.htm');
if (isset($c_id[0])) { $link_arr [31] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_finance.htm');
if (isset($c_id[0])) { $link_arr [32] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_accommodation.htm');
if (isset($c_id[0])) { $link_arr [33] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_orientation.htm');
if (isset($c_id[0])) { $link_arr [34] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_registration.htm');
if (isset($c_id[0])) { $link_arr [35] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_studentcard.htm');
if (isset($c_id[0])) { $link_arr [36] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_specialneeds.htm');
if (isset($c_id[0])) { $link_arr [37] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_transport.htm');
if (isset($c_id[0])) { $link_arr [38] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_changestudy.htm');
if (isset($c_id[0])) { $link_arr [39] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/undergraduate/faq_termination.htm');
if (isset($c_id[0])) { $link_arr [40] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=student_under';}



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Undergraduate</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[1].'"><h3>Admission</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[2].'">General Information</a></li>
<li><a href="'.$link_arr[3].'">Requirements for 2009</a></li>
<li><a href="'.$link_arr[4].'">Requirements</a></li>
<li><a href="'.$link_arr[5].'">Descretionary</a></li>
<li><a href="'.$link_arr[6].'">Procedures</a></li>
<li><a href="'.$link_arr[7].'">Application</a></li>
<li><a href="'.$link_arr[8].'">Re-Admission</a></li>
<li><a href="'.$link_arr[9].'">Response to Application</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[10].'"><h3>Financial Matters</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[11].'">Fees</a></li>
<li><a href="'.$link_arr[12].'">Credit Management</a></li>
<li><a href="'.$link_arr[13].'">Financial Aid</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[14].'"><h3>Residence</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[15].'">Residence Houses</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[16].'"><h3>Registration</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[17].'">Adjustments</a></li>
<li><a href="'.$link_arr[18].'">Late Registration</a></li>
<li><a href="'.$link_arr[19].'">Second Semester</a></li>
<li><a href="'.$link_arr[20].'">Concurrent</a></li>
<li><a href="'.$link_arr[21].'">Dual Joint</a></li>
<li><a href="'.$link_arr[22].'">Other Universities</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[23].'"><h3>Student Advisory</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[24].'">Policies &amp; Rules</a></li>
<li><a href="'.$link_arr[25].'">Programme &amp; Modules</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[26].'"><h3>Student Record</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[27].'">Student Card</a></li>
<li><a href="'.$link_arr[28].'">Transcripts</a></li>
<li><a href="'.$link_arr[29].'">Termination of Study</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[30].'"><h3>FAQ\'s</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[31].'">Admission</a></li>
<li><a href="'.$link_arr[32].'">Finance</a></li>
<li><a href="'.$link_arr[33].'">Accomodation</a></li>
<li><a href="'.$link_arr[34].'">Orientation</a></li>
<li><a href="'.$link_arr[35].'">Registration</a></li>
<li><a href="'.$link_arr[36].'">Student Card</a></li>
<li><a href="'.$link_arr[37].'">Special Needs</a></li>
<li><a href="'.$link_arr[38].'">Transport/Parking</a></li>
<li><a href="'.$link_arr[39].'">Change Study</a></li>
<li><a href="'.$link_arr[40].'">Terminate Study</a></li>



	</ul>
		
</ul>
</div>



';


			
			return $this->getHomeLink().$menu;
		}				



			
		
		
		
		/*
		*  This method will return the RESEARCH  menu
		* @author: Charl Mert
		*/
		public function getResearchMenu(){
			//This links to the Academic menu on the live site
			return $this->getAcademicMenu();
		}				

		










		/*
		*  This method will return the COMMUNITY menu
		* @author: Charl Mert
		*/
		public function getCommunityMenu(){

			$link_arr['ourcommunity'] = '';
            $link_arr['alumni'] = '';
            $link_arr['convocation'] = '';
            $link_arr['outreach'] = '';
            $link_arr['partnership'] = '';
            $link_arr['mathsandscience'] = '';
            $link_arr['learning'] = '';
            $link_arr['economic'] = '';
            $link_arr['growing'] = '';
            $link_arr['multi'] = '';

	

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/index.htm');
			if (isset($c_id[0])) { $link_arr ['ourcommunity'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/alumni.htm');
			if (isset($c_id[0])) { $link_arr ['alumni'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/convocation.htm');
			if (isset($c_id[0])) { $link_arr ['convocation'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/community_outreach.htm');
			if (isset($c_id[0])) { $link_arr ['outreach'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/community_outreach_01.htm');
			if (isset($c_id[0])) { $link_arr ['partnership'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/community_outreach_02.htm');
			if (isset($c_id[0])) { $link_arr ['mathsandscience'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/community_outreach_03.htm');
			if (isset($c_id[0])) { $link_arr ['learning'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/community_outreach_04.htm');
			if (isset($c_id[0])) { $link_arr ['economic'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/community_outreach_05.htm');
			if (isset($c_id[0])) { $link_arr ['growing'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}

			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/community_outreach_06.htm');
			if (isset($c_id[0])) { $link_arr ['multi'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=community';}


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['ourcommunity'].'"><h3>Our Community</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['alumni'].'">Alumni</a></li>
<li><a href="'.$link_arr['convocation'].'">Convocation</a></li>
	</ul>

</ul>
</div>
<p/>
<div class="menublock">
<ul>

	
        <li>
                <a href="'.$link_arr['outreach'].'"><h3>Community Outreach</h3></a>
        </li>
<li><a href="'.$link_arr['partnership'].'">Community Partnership</a></li>
<li><a href="'.$link_arr['mathsandscience'].'">Maths and Science</a></li>
<li><a href="'.$link_arr['learning'].'">A Learning Nation</a></li>
<li><a href="'.$link_arr['economic'].'">Economic Literacy</a></li>
<li><a href="'.$link_arr['growing'].'">Growing Scientists</a></li>
<li><a href="'.$link_arr['multi'].'">Multilingualism</a></li>
	</ul>
	
</ul>
</div>
';
			
			return $this->getHomeLink().$menu;
		}				




		
		
		/*
		*  This method will return the ADMINISTRATION AND SUPPORT menu
		* @author: Charl Mert
		*/
		public function getAdminSupportMenu(){
	
			$link_arr['adminsupport'] = '';
            $link_arr['central'] = '';
            $link_arr['facultyadmin'] = '';
            $link_arr['studentadmin'] = '';


			//Administration & Support
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/administration_support/index.htm');
			if (isset($c_id[0])) { $link_arr ['adminsupport'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}

			//Central Administration
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/administration_support/central_administration.htm');
			if (isset($c_id[0])) { $link_arr ['central'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}
		
			//Faculty Administration
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/administration_support/faculty_administration.htm');
			if (isset($c_id[0])) { $link_arr ['facultyadmin'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}

			//Student Administration
			$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/student_administration/index.htm');
			if (isset($c_id[0])) { $link_arr ['studentadmin'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=adminsupport';}
		
		
			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['adminsupport'].'"><h3>Administration &amp; Support</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['central'].'">Central Administration</a></li>
<li><a href="'.$link_arr['facultyadmin'].'">Faculty Administration</a></li>
<li><a href="'.$link_arr['studentadmin'].'">Student Administration</a></li>
	</ul>
	
</ul>
</div>
';
			
			return $this->getHomeLink().$menu;
		}				

		
		
		
		
		


		/*
		*  This method will return the FACULTY DEPT XHOSA MENU
		* @author: Charl Mert
		*/
		public function getStudentFacultyDeptXhosaMenu(){

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_prof_neethling.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_prof_gilles_maurice_de_schryver.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_thokozile_v_mabeqa.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_alet_van_huyssteen.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_loyiso_k_mletshe.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_nosisi_lynette_mpolweni.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_thenjiswa_ntwana_mgijima.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_nkosinathi_skade.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/staff_shirley_dlamini.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/courses.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/research.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/products.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/links.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/contact.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';



			$menu = '


<div class="menublock">
<ul>
<li><a href="'.$link_arr[0].'"><h3>The Department</h3></a></li>
<li><h3>Staff</h3></li>

	<ul>
<li><a href="'.$link_arr[1].'">Mission Statement</a></li>

<li><a href="'.$link_arr[2].'">Prof. S.J. Neethling</a></li>
<li><a href="'.$link_arr[3].'">Prof. G-M de Schryver</a></li>
<li><a href="'.$link_arr[4].'">Ms. T.V. Mabeqa</a></li>
<li><a href="'.$link_arr[5].'">Mrs. A. van Huyssteen</a></li>
<li><a href="'.$link_arr[6].'">Mr. L.K. Mletshe</a></li>
<li><a href="'.$link_arr[7].'">Ms. N.L. Mpolweni</a></li>
<li><a href="'.$link_arr[8].'">Ms. T.N. Mgijima</a></li>
<li><a href="'.$link_arr[9].'">Mr. N. Skade</a></li>
<li><a href="'.$link_arr[10].'">Mrs. S. Dlamini</a></li>

	</ul>
</ul>
</div>

<div class="menublock">
<ul>
<li><a href="'.$link_arr[11].'"><h3>Modules</h3></a></li>
<li><a href="'.$link_arr[12].'"><h3>Research</h3></a></li>
<li><a href="'.$link_arr[13].'"><h3>Products</h3></a></li>
<li><a href="'.$link_arr[14].'"><h3>Links</h3></a></li>
<li><a href="'.$link_arr[15].'"><h3>Contact Us</h3></a></li>
</ul>
</div>

';


			
			return $this->getHomeLink().$menu;
		}				












		/*
		*  This method will return the FACULTY DEPT Philosiphy MENU
		* @author: Charl Mert
		*/
		public function getStudentFacultyDeptPhilosiphyMenu(){



$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/philosophy/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_philosiphy';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/philosophy/about.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_philosiphy';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/philosophy/staff.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_philosiphy';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/philosophy/modules.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_philosiphy';



			$menu = '


<div class="menublock">
<ul>
<li><a href="'.$link_arr[0].'"><h3>The Department</h3></a></li>
<li><a href="'.$link_arr[1].'"><h3>About The Department</h3></a></li>
<li><a href="'.$link_arr[2].'"><h3>Staff</h3></a></li>
<li><a href="'.$link_arr[3].'"><h3>Modules</h3></a></li>
</ul>
</div>

';


			
			return $this->getHomeLink().$menu;
		}				






		/*
		*  This method will return the FACULTY DEPT RELIGION MENU
		* @author: Charl Mert
		*/
		public function getStudentFacultyDeptWomanMenu(){


$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/about.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/mission.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/undergraduate.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/ba_undergraduate.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/postgraduate.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/career.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/women_gender_staff.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/resource.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/contact_us.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/research.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/gender_visuality.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';



			$menu = '


<div class="menublock">
<ul>
<li><a href="'.$link_arr[0].'"><h3>The Department</h3></a></li>
<li><a href="'.$link_arr[1].'"><h3>About The Department</h3></a></li>

	<ul>
<li><a href="'.$link_arr[2].'">Mission Statement</a></li>
	</ul>
</ul>
</div>

<div class="menublock">
<ul>
<li><a href="'.$link_arr[3].'"><h3>Courses &amp; Programs</h3></a></li>
 <ul>
    <li><a href="'.$link_arr[4].'"> Undergraduate </li>
    <li><a href="'.$link_arr[5].'"> Postgraduate </li>
 </ul>
</ul>
</div>

<div class="menublock">
<ul>
<li><a href="'.$link_arr[6].'"><h3>Career Opportunities</h3></a></li>
<li><a href="'.$link_arr[7].'"><h3>Staff</h3></a></li>
<li><a href="'.$link_arr[8].'"><h3>Resource Center</h3></a></li>
<li><a href="'.$link_arr[9].'"><h3>Contact Us</h3></a></li>
<li><a href="'.$link_arr[10].'"><h3>Research</h3></a></li>
<li><a href="'.$link_arr[11].'"><h3>GVCP</h3></a></li>
</ul>
</div>

';


			
			return $this->getHomeLink().$menu;
		}				







		/*
		*  This method will return the FACULTY DEPT RELIGION MENU
		* @author: Charl Mert
		*/
		public function getStudentFacultyDeptReligionMenu(){


$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/about.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/new.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/features.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/staff.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/courseprograms.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/part_time.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/bursaries.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/contact.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>The Department</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[1].'"><h3>About the Department</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[2].'"><h3>Whats New?</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[3].'"><h3>Features</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[4].'"><h3>Staff</h3></a>
        </li>
	
	<li>
                <a href="'.$link_arr[5].'"><h3>Courses &amp; Programs</h3></a>
        </li>
	
	<li>
                <a href="'.$link_arr[6].'"><h3>Part-Time Study</h3></a>
        </li>
	
	<li>
                <a href="'.$link_arr[7].'"><h3>Bursaries</h3></a>
        </li>
	
	<li>
                <a href="'.$link_arr[8].'"><h3>Contact</h3></a>
        </li>
	

</ul>
</div>



';


			
			return $this->getHomeLink().$menu;
		}				







		/*
		*  This method will return the FACULTY DEPT LINGUISTICS MENU
		* @author: Charl Mert
		*/
		public function getStudentFacultyDeptLinguisticsMenu(){

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/index.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/about_department.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/modules.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/undergraduate_programmes.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/postgraduate_programmes.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/staff.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_christopher_stroud.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_felix_banda.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_charlyn_dyers.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_ntombizodwa_cynthia_gxowa-dlayedwa.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_david_foster.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_zannie_bock.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_paul_duncan.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_marcelyn_oostendorp.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_monica_kirsten.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_waseem_matthews.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/profile_nobuhle_luphondo.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/workshop.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/workshop_200702.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/workshop_200702_program.htm');
$link_arr[20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/workshop_200702_abstracts.htm');
$link_arr[21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/seminars.htm');
$link_arr[22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/pictures.htm');
$link_arr[23] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Arts Faculty</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[1].'"><h3>The Department</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[2].'"><h3>About the Department</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[3].'"><h3>Modules</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[4].'">Undergraduate</a></li>
<li><a href="'.$link_arr[5].'">Postgraduate</a></li>
	</ul>

	<li>
                <a href="'.$link_arr[6].'"><h3>Staff</h3></a>
        </li>

	<ul>

<li><a href="'.$link_arr[7].'"> Prof Christopher Stroud</a></li>
<li><a href="'.$link_arr[8].'"> Prof Felix Banda</a></li>
<li><a href="'.$link_arr[9].'"> Prof Charlyn Dyers</a></li>
<li><a href="'.$link_arr[10].'"> Dr N.C. Gxowa-Dlayedwa</a></li>
<li><a href="'.$link_arr[11].'"> Mr David Foster</a></li>
<li><a href="'.$link_arr[12].'"> Ms Zannie Bock</a></li>
<li><a href="'.$link_arr[13].'"> Mr Paul Duncan</a></li>
<li><a href="'.$link_arr[14].'"> Ms Marcelyn Oostendorp</a></li>
<li><a href="'.$link_arr[15].'"> Ms Monica Kirsten</a></li>
<li><a href="'.$link_arr[16].'"> Mr Waseem Matthews</a></li>
<li><a href="'.$link_arr[17].'"> Ms Nobuhle Luphondo</a></li>

	</ul>

		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[18].'"><h3>Workshops</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[19].'">Feb 2007</a></li>
<li><a href="'.$link_arr[20].'">Program</a></li>
<li><a href="'.$link_arr[21].'">Abstracts</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[22].'"><h3>Seminars</h3></a>
        </li>

	<li>
                <a href="'.$link_arr[23].'"><h3>Pictures</h3></a>
        </li>
		
</ul>
</div>



';


			
			return $this->getHomeLink().$menu;
		}				










		/*
		*  This method will return the FACULTY DEPT LIB INFO SCIENCE MENU
		* @author: Charl Mert
		*/
		public function getStudentFacultyDeptLibMenu(){


$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/index.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/about_department.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/undergraduate_programmes.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/postgraduate_programmes.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Advanced_Certificate_School_Librarianship.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Short_Courses.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Year1_Course_Descriptors.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Year2_Course_Descriptors.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Year3_Course_Descriptors.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Year4_Course_Descriptors.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/master_programmes.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Staff_Gavin_Davis.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Staff_Genevieve_HartPhD.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Staff_Lizette_King.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Staff_Sarah_Loretha_Witbooi.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Staff_Sandy_Zinn.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Staf_Tafseer_Abbas.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/seminar.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Certificate_course_ceremony.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Georges_Farewell.htm');
$link_arr[20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/Photographs.htm');
$link_arr[21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/contact_us.htm');
$link_arr[22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Arts Faculty</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[1].'"><h3>Home</h3></a>
        </li>
	<li>
                <a href="'.$link_arr[2].'"><h3>About the Department</h3></a>
        </li>
	<li>
                <h3>Courses</h3>
        </li>

	<ul>
<li><a href="'.$link_arr[3].'">Undergraduate</a></li>
<li><a href="'.$link_arr[4].'">Postgraduate</a></li>
<li><a href="'.$link_arr[5].'">ACE: School Librarianship</a></li>
<li><a href="'.$link_arr[6].'">Short Courses</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <h3>Course Descriptors</h3>
        </li>

	<ul>
<li><a href="'.$link_arr[7].'">Year 1 Course Descriptor</a></li>
<li><a href="'.$link_arr[8].'">Year 2 Course Descriptor</a></li>
<li><a href="'.$link_arr[9].'">Year 3 Course Descriptor</a></li>
<li><a href="'.$link_arr[10].'">Year 4 Course Descriptor</a></li>
<li><a href="'.$link_arr[11].'">Master\'s Course Descriptor</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <h3>Staff</h3>
        </li>

	<ul>
<li><a href="'.$link_arr[12].'">Gavin Davis</a></li>
<li><a href="'.$link_arr[13].'">Genevieve Hart</a></li>
<li><a href="'.$link_arr[14].'">Lizette King</a></li>
<li><a href="'.$link_arr[15].'">Sally Witbooi</a></li>
<li><a href="'.$link_arr[16].'">Sandy Zinn</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <h3>Admin &amp; Support</h3>
        </li>

	<ul>
<li><a href="'.$link_arr[17].'">Tafseer Abbas</a></li>
	</ul>
		
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <h3>Events</h3>
        </li>

	<ul>
<li><a href="'.$link_arr[18].'">Seminars</a></li>
<li><a href="'.$link_arr[19].'">Short Course Ceremonies</a></li>
<li><a href="'.$link_arr[20].'">George\'s Farewell</a></li>
	</ul>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[21].'"><h3>Photograph</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[22].'"><h3>Contact Us</h3></a>
        </li>
		
</ul>
</div>



';


			
			return $this->getHomeLink().$menu;
		}				



		







		/*
		*  This method will return the FACULTY DEPT LIL menu
		* @author: Charl Mert
		*/
		public function getFacultyDeptLilMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/about.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/language_services.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/service_profile.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/short_courses.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/english_asian.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/people.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/contact.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>The Center</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[1].'"><h3>About Us</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[2].'"><h3>Language Services</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[3].'"><h3>Service Profile</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[4].'"><h3>Shourt Courses</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[5].'"><h3>English for Asian Students</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[6].'"><h3>People</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[7].'"><h3>Contact Us</h3></a>
        </li>


</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				





		/*
		*  This method will return the FACULTY DEPT SOC DEV menu
		* @author: Charl Mert
		*/
		public function getFacultyDeptSocDevMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/about.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/course.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/course_honours_degrees.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/course_masters_degrees.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/staff.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/module_descriptors.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/teaching_methods.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/timetable_2007.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/gallery.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/downloads.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/links.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>The Institute</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[1].'"><h3>About the Institute</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[2].'"><h3>Course Programmes</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[3].'">Honours</a></li>
<li><a href="'.$link_arr[4].'">Masters</a></li>
	</ul>

</ul>
</div>

<div class="menublock">

<ul>
<li><a href="'.$link_arr[5].'">Staff</a></li>
<li><a href="'.$link_arr[6].'">Module Descriptors</a></li>
	<ul>
<li><a href="'.$link_arr[7].'">Teaching Methods</a></li>
<li><a href="'.$link_arr[8].'">Timetable 2007</a></li>
<li><a href="'.$link_arr[9].'">Gallery</a></li>
	</ul>
</ul>
</div>


<div class="menublock">

<ul>
<li><a href="'.$link_arr[10].'">Downloads</a></li>
<li><a href="'.$link_arr[11].'">Links</a></li>
</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				













		/*
		*  This method will return the FACULTY DEPT HISTORY menu
		* @author: Charl Mert
		*/
		public function getFacultyDeptHistoryMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/index.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/about.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/staff.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/undergraduate.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/undergraduate_1st_year.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/undergraduate_2nd_year.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/undergraduate_3rd_year.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/postgraduate_programmes.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/postgraduate_courses.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/postgraduate_diploma.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/masters.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/research_projects.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/castle_company.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/project_public_pasts.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/visual_history_project.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/presidential_project.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/seminars.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/conferences.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/contact.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';




			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Arts Faculty</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[1].'"><h3>The Department</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[2].'"><h3>About The Department</h3></a>
        </li>

</ul>
</div>

<div class="menublock">

<ul>
<li><a href="'.$link_arr[3].'">Staff</a></li>
<li><a href="'.$link_arr[4].'">Undergraduate</a></li>
	<ul>
<li><a href="'.$link_arr[5].'">First Year</a></li>
<li><a href="'.$link_arr[6].'">Second Year</a></li>
<li><a href="'.$link_arr[7].'">Third Year</a></li>
	</ul>
</ul>
</div>


<div class="menublock">

<ul>
<li><a href="'.$link_arr[8].'">Postgraduate</a></li>
	<ul>
<li><a href="'.$link_arr[9].'">Postgraduate Courses</a></li>
<li><a href="'.$link_arr[10].'">Postgraduate Diploma</a></li>
	</ul>
</ul>
</div>



<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[11].'"><h3>Masters</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[12].'"><h3>Research Projects</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[13].'">Castle Company and Control</a></li>
<li><a href="'.$link_arr[14].'">Project on Public Pasts</a></li>
<li><a href="'.$link_arr[15].'">Visual History Project</a></li>
<li><a href="'.$link_arr[16].'">SADET (Western Cape)</a></li>
	</ul>

</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[17].'"><h3>Seminars</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[18].'"><h3>Conferences</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[19].'"><h3>Contact Us</h3></a>
        </li>

</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				









		/*
		*  This method will return the FACULTY DEPT GEO menu
		* @author: Charl Mert
		*/
		public function getFacultyDeptGeoMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/index.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/about.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_list.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_pirie.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_mcpherson.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_dyssel.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_moodaley.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_boekstein.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_carolissen.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_de_wet.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/staff_frenchman.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/programmes.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/undergraduate.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/postgraduate.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/research.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/seminars.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/seminar_2005.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/seminar_2004.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/links.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/field_trips.htm');
$link_arr[20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/news.htm');
$link_arr[21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/job_advertisements.htm');
$link_arr[22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/contact.htm');
$link_arr[23] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Arts Faculty</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[1].'"><h3>The Department</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[2].'"><h3>About The Department</h3></a>
        </li>


	<ul>
<li><a href="'.$link_arr[3].'">Staff</a></li>
<li>Staff</li>
<li><a href="'.$link_arr[4].'">  Prof. Gordon Pirie</a></li>
<li><a href="'.$link_arr[5].'">  Mr. Elsworth McPherson</a></li>
<li><a href="'.$link_arr[6].'">  Mr. Michael Dyssel</a></li>
<li><a href="'.$link_arr[7].'">  Mr. Colin Moodaley</a></li>
<li><a href="'.$link_arr[8].'">  Mr. Mark Boekstein</a></li>
<li><a href="'.$link_arr[9].'">  Ms. Mandy Carolissen</a></li>
<li><a href="'.$link_arr[10].'">  Ms. Felicity de Wet</a></li>
<li><a href="'.$link_arr[11].'">  Mr. David Frenchman</a></li>

	</ul>
</ul>
</div>
<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[12].'"><h3>Programmes</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[13].'">Undergraduate</a></li>
<li><a href="'.$link_arr[14].'">Postgraduate</a></li>
	</ul>

</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[15].'"><h3>Research</h3></a>
        </li>
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[16].'"><h3>Seminars</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[17].'">2005 Seminars</a></li>
<li><a href="'.$link_arr[18].'">2004 Seminars</a></li>
	</ul>

</ul>
</div>	

<div class="menublock">
<ul>
    <li> <a href="'.$link_arr[19].'"><h3>Links</h3></a> </li>
    <li> <a href="'.$link_arr[20].'"><h3>Field Trips</h3></a> </li>
    <li> <a href="'.$link_arr[21].'"><h3>News</h3></a> </li>
    <li> <a href="'.$link_arr[22].'"><h3>Job Advertisements</h3></a> </li>
    <li> <a href="'.$link_arr[23].'"><h3>Contact Us</h3></a> </li>
</ul>
</div>
';
			
			return $this->getHomeLink().$menu;
		}				






		/*
		*  This method will return the FACULTY DEPT ENGLISH menu
		* @author: Charl Mert
		*/
		public function getFacultyDeptEnglishMenu(){
	
$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/index.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/about.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/courses.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/1st_year_courses.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/2nd & 3rdyr_courses.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/staff.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/postgraduate.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/postgraduate_courses.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/postgraduate_structure.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/research_projects.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/contact.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Arts Faculty</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[1].'">The Department</a></li>
<li><a href="'.$link_arr[2].'">About The Department</a></li>
<li>Staff</li>
	</ul>
</ul>
</div>
<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[3].'"><h3>Undergraduate</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[4].'">First Year</a></li>
<li><a href="'.$link_arr[5].'">Second &amp; Third Year</a></li>
	</ul>

</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[6].'"><h3>Staff</h3></a>
        </li>
</ul>
</div>	


<div class="menublock">
<ul>

 
        <li>
                <a href="'.$link_arr[7].'"><h3>Postgraduate</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[8].'">2007 Courses</a></li>
<li><a href="'.$link_arr[9].'">Course Structure</a></li>
	</ul>

</ul>
</div>	

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[10].'"><h3>Research</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[11].'"><h3>Contact Us</h3></a>
        </li>
		
	
</ul>
</div>
';
			
			return $this->getHomeLink().$menu;
		}				

			




		/*
		*  This method will return the FACULTY DEPT LANGUAGE menu
		* @author: Charl Mert
		*/
		public function getFacultyDeptLanguageMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/index.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/staff_cloarec_myriam.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/staff_hermans_mark.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/staff_huet_haupt_ludivine.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/staff_mentzner_martina.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/staff_mohamed_yasien.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/staff_mustapha_saidi.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/staff_vanryneveld_hannelore.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/undergraduate.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/arabic_undergraduate.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/french_undergraduate.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/german_undergraduate.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/latin_undergraduate.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/postgraduate.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/arabic_postgraduate.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/french_postgraduate.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/german_postgraduate.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/latin_postgraduate.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/contact_us.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Arts Faculty</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[1].'">The Department</a></li>
<li>Staff</li>
<li><a href="'.$link_arr[2].'"> Cloarec, Myriam</a></li>
<li><a href="'.$link_arr[3].'"> Hermans, Mark</a></li>
<li><a href="'.$link_arr[4].'"> Huet-Haupt, Ludivine</a></li>
<li><a href="'.$link_arr[5].'"> Mentzner, Martina</a></li>
<li><a href="'.$link_arr[6].'"> Mohamed, Yasien</a></li>
<li><a href="'.$link_arr[7].'"> Mustapha, Saidi</a></li>
<li><a href="'.$link_arr[8].'"> van Ryneveld, Hannelore</a></li>
	</ul>
</ul>
</div>
<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[9].'"><h3>Undergraduate</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[10].'">Arabic</a></li>
<li><a href="'.$link_arr[11].'">French</a></li>
<li><a href="'.$link_arr[12].'">German</a></li>
<li><a href="'.$link_arr[13].'">Latin</a></li>
	</ul>

</ul>
</div>

<div class="menublock">
<ul>

 
        <li>
                <a href="'.$link_arr[14].'"><h3>Postgraduate</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[15].'">Arabic</a></li>
<li><a href="'.$link_arr[16].'">French</a></li>
<li><a href="'.$link_arr[17].'">German</a></li>
<li><a href="'.$link_arr[18].'">Latin</a></li>
	</ul>

</ul>
</div>	

<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[19].'"><h3>Contact Us</h3></a>
        </li>
		
	
</ul>
</div>
';
			
			return $this->getHomeLink().$menu;
		}				

			




		/*
		*  This method will return the FACULTY DEPARTMENT ANTHRO SOCIAL MENU 
		* @author: Charl Mert
		*/
		public function getFacultyDeptAnthroMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/about.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/staff.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/undergraduate_courses.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/postgraduate_courses.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/research.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/international_relations.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/pictures.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>The Department</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[1].'">About The Department</a></li>
<li><a href="'.$link_arr[2].'">Staff</a></li>
<li><a href="'.$link_arr[3].'">Undergraduate Courses</a></li>
<li><a href="'.$link_arr[4].'">Postgraduate Courses</a></li>
<li><a href="'.$link_arr[5].'">Research</a></li>
<li><a href="'.$link_arr[6].'">International Relations</a></li>
<li><a href="'.$link_arr[7].'">Pictures</a></li>
	<ul>
</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				

			









		/*
		*  This method will return the FACULTY DEPARTMENT AFRIKAANS MENU 
		* @author: Charl Mert
		*/
		public function getFacultyDeptAfrikaansMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/beroepskanse.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/personeel.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/kontak.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/program.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/nagraads.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/publikasies.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/beurse&_pryse.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Department</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[1].'">Beroepskanse</a></li>
	<ul>
</ul>
</div>

<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[2].'"><h3>Personeel</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[3].'">Kontak</a></li>
	</ul>

</ul>
</div>

<div class="menublock">
<ul>

 
        <li>
                <a href="'.$link_arr[4].'"><h3>Program</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[5].'">Nagraad</a></li>
	</ul>

</ul>
</div>	

<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[6].'"><h3>Publikasies</h3></a>
        </li>
		
        <li>
                <a href="'.$link_arr[7].'"><h3>Beurse &amp; Pryse</h3></a>
        </li>
       	
</ul>
</div>
';
			
			return $this->getHomeLink().$menu;
		}				

			



		
		/*
		*  This method will return the FACULTY MENU menu
		* @author: Charl Mert
		*/
		public function getFacultyArtsMenu(){
	
$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/dean.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/faculty_info.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/career_opps.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/academic_units.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/afrikaans/index.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_afrikaans';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/anthropology_sociology/index.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_anthro';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/foreign_languages/index.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_language';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/english/index.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_english';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/geography/index.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_geo';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/history/index.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_history';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_institute/social_development/index.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_soc';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_centre/iilwimi_sentrum/index.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_lil';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/library_info/index.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_library';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/linguistics/index.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_linguistics';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/religion_theology/index.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_religion';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/women_gender/index.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_woman';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/philosophy/index.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_philosiphy';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_department/xhosa/index.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=dept_xhosa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/programmes.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/programmes_undergraduate.htm');
$link_arr[20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/ba_explained.htm');
$link_arr[21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/first_year.htm');
$link_arr[22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/major_subjects.htm');
$link_arr[23] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/postgraduate.htm');
$link_arr[24] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/post_humanity_fellowships.htm');
$link_arr[25] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/academic_units.htm');
$link_arr[26] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/faculty_staff.htm');
$link_arr[27] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/research.htm');
$link_arr[28] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/students_say.htm');
$link_arr[29] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/funding.htm');
$link_arr[30] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/admission.htm');
$link_arr[31] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/arts/contact.htm');
$link_arr[32] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_arts';


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Faculty</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[1].'">Dean\'s Message</a></li>
<li><a href="'.$link_arr[2].'">Faculty Information</a></li>
<li><a href="'.$link_arr[3].'">Career Opportunities</a></li>
	</ul>
</ul>
</div>
<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[4].'"><h3>Departments</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[5].'">Afrikaans</a></li>
<li><a href="'.$link_arr[6].'">Anthro-Soc</a></li>
<li><a href="'.$link_arr[7].'">Foreign Languages</a></li>
<li><a href="'.$link_arr[8].'">English</a></li>
<li><a href="'.$link_arr[9].'">Geography</a></li>
<li><a href="'.$link_arr[10].'">History</a></li>
<li><a href="'.$link_arr[11].'">Inst. for Social Dev.</a></li>
<li><a href="'.$link_arr[12].'">IiLwimi Sentrum</a></li>
<li><a href="'.$link_arr[13].'">Library &amp; Info. Sci</a></li>
<li><a href="'.$link_arr[14].'">Linguistics</a></li>
<li><a href="'.$link_arr[15].'">Religion &amp; Theory</a></li>
<li><a href="'.$link_arr[16].'">Woman &amp; Gender</a></li>
<li><a href="'.$link_arr[17].'">Philosophy</a></li>
<li><a href="'.$link_arr[18].'">Xhosa</a></li>
	</ul>

</ul>
</div>

<div class="menublock">
<ul>

 
        <li>
                <a href="'.$link_arr[19].'"><h3>Academic Programmes</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[20].'">Undergraduate</a></li>
<li><a href="'.$link_arr[21].'">BA Explained</a></li>
<li><a href="'.$link_arr[22].'">First Year Subjects</a></li>
<li><a href="'.$link_arr[23].'">BA Major Subjects</a></li>
<li><a href="'.$link_arr[24].'">Postgraduate</a></li>
<li><a href="'.$link_arr[25].'">Postgraduate humanity</a></li>
	</ul>

</ul>
</div>	

<div class="menublock">
<ul>

        <li>
                <a href="'.$link_arr[26].'"><h3>Academic Units</h3></a>
        </li>
		
        <li>
                <a href="'.$link_arr[27].'"><h3>Faculty Staff</h3></a>
        </li>
		
        <li>
                <a href="'.$link_arr[28].'"><h3>Faculty Research</h3></a>
        </li>
		
        <li>
                <a href="'.$link_arr[29].'"><h3>What our students say</h3></a>
        </li>
		
        <li>
                <a href="'.$link_arr[30].'"><h3>Funding &amp; Busaries</h3></a>
        </li>
		
        <li>
                <a href="'.$link_arr[31].'"><h3>Admission</h3></a>
        </li>
		
        <li>
                <a href="'.$link_arr[32].'"><h3>Contact Us</h3></a>
        </li>
	
</ul>
</div>
';
			
			return $this->getHomeLink().$menu;
		}				

				

		/*
		*  This method will return the FACULTY HEALTH menu
		* @author: Charl Mert
		*/
		public function getFacultyHealthMenu(){

$link_arr['faculty'] = '';
$link_arr['aboutfaculty'] = '';
$link_arr['facultyinfo'] = '';
$link_arr['mission'] = '';
$link_arr['facultystaff'] = '';
$link_arr['directions'] = '';
$link_arr['publicrelations'] = '';
$link_arr['news'] = '';
$link_arr['departmentsandschools'] = '';
$link_arr['academicprogrammes'] = '';
$link_arr['undergraduate'] = '';
$link_arr['postgraduate'] = '';
$link_arr['admission'] = '';
$link_arr['researchprojects'] = '';
$link_arr['projects'] = '';
$link_arr['courses'] = '';
$link_arr['career'] = '';
$link_arr['funding'] = '';
$link_arr['health'] = '';
$link_arr['contact'] = '';
$link_arr['faculty'] = '';
$link_arr['message'] = '';
$link_arr['about'] = '';
$link_arr['facultyinfo'] = '';
$link_arr['lib'] = '';
$link_arr['academicprogrammes'] = '';
$link_arr['en'] = '';
$link_arr['af'] = '';
$link_arr['staff'] = '';
$link_arr['timetable'] = '';
$link_arr['contact'] = '';
	


$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/index.htm');
if (isset($c_id[0])) { $link_arr ['faculty'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/about.htm');
if (isset($c_id[0])) { $link_arr ['aboutfaculty'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/faculty_info.htm');
if (isset($c_id[0])) { $link_arr ['facultyinfo'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/mission.htm');
if (isset($c_id[0])) { $link_arr ['mission'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/faculty_staff.htm');
if (isset($c_id[0])) { $link_arr ['facultystaff'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/direction.htm');
if (isset($c_id[0])) { $link_arr ['directions'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/public.htm');
if (isset($c_id[0])) { $link_arr ['publicrelations'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/news.htm');
if (isset($c_id[0])) { $link_arr ['news'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/departments_institutes.htm');
if (isset($c_id[0])) { $link_arr ['departmentsandschools'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/programmes.htm');
if (isset($c_id[0])) { $link_arr ['academicprogrammes'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/programmes_undergraduate.htm');
if (isset($c_id[0])) { $link_arr ['undergraduate'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/programmes_postgraduate.htm');
if (isset($c_id[0])) { $link_arr ['postgraduate'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/admission.htm');
if (isset($c_id[0])) { $link_arr ['admission'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/research.htm');
if (isset($c_id[0])) { $link_arr ['researchprojects'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/projects.htm');
if (isset($c_id[0])) { $link_arr ['projects'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

//$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/downloads/Interdisciplinary%20Core%20Courses.doc');
//if (isset($c_id[0])) { $link_arr ['courses'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}
if (isset($c_id[0])) { $link_arr ['courses'] = 'usrfiles/importcms/gen11Srv7Nme54_4262_1210050547/Interdisciplinary%20Core%20Courses.doc';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/careers.htm');
if (isset($c_id[0])) { $link_arr ['career'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/funding.htm');
if (isset($c_id[0])) { $link_arr ['funding'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}

//$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/http://www.health.uct.ac.za/wchdf/');
//if (isset($c_id[0])) { $link_arr ['health'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}
if (isset($c_id[0])) { $link_arr ['health'] = 'http://www.health.uct.ac.za/wchdf/';}

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/community_health/contact.htm');
if (isset($c_id[0])) { $link_arr ['contact'] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_health';}



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['faculty'].'"><h3>Faculty</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['aboutfaculty'].'">About the Faculty</a></li>
<li><a href="'.$link_arr['facultyinfo'].'">Faculty Information</a></li>
<li><a href="'.$link_arr['mission'].'">Mission Statement</a></li>
<li><a href="'.$link_arr['facultystaff'].'">Faculty Staff</a></li>
<li><a href="'.$link_arr['directions'].'">Directions</a></li>
<li><a href="'.$link_arr['publicrelations'].'">Public Relations</a></li>
<li><a href="'.$link_arr['news'].'">News</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['departmentsandschools'].'"><h3>Departments &amp; Schools</h3></a>
        </li>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['academicprogrammes'].'"><h3>Academic Programmes</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr['undergraduate'].'">Undergraduate</a></li>
<li><a href="'.$link_arr['postgraduate'].'">Postgraduate</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr['admission'].'"><h3>Admission</h3></a>
        </li>

	    <li>
                <a href="'.$link_arr['researchprojects'].'"><h3>Research Projects</h3></a>
        </li>

	    <li>
                <a href="'.$link_arr['projects'].'"><h3>Projects</h3></a>
        </li>

	    <li>
                <a href="'.$link_arr['courses'].'"><h3>Interdisciplinary Core Courses</h3>(Download)</a>
        </li>
	  	
	    <li>
                <a href="'.$link_arr['career'].'"><h3>Career Opportunities</h3></a>
        </li>

	    <li>
                <a href="'.$link_arr['funding'].'"><h3>Funding your Studies</h3></a>
        </li>


	    <li>
                <a href="'.$link_arr['health'].'"><h3>Dean\'s Health Forum</h3></a>
        </li>

	    <li>
                <a href="'.$link_arr['contact'].'"><h3>Contact Us</h3></a>
        </li>


</ul>
</div>


';
			
			return $this->getHomeLink().$menu;
		}				

	


		/*
		*  This method will return the FACULTY DENTISTRY MENU menu
		* @author: Charl Mert
		*/
		public function getFacultyDentistryMenu(){
	


$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/index.htm');
$link_arr[faculty] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/dean.htm');
$link_arr[message] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/about_us.htm');
$link_arr[about] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/faculty_info.htm');
$link_arr[facultyinfo] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/library.htm');
$link_arr[lib] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/courses.htm');
$link_arr[academicprogrammes] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/english_version.htm');
$link_arr[en] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/afrikaans.htm');
$link_arr[af] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/faculty_staff.htm');
$link_arr[staff] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/timetable.htm');
$link_arr[timetable] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/dentistry/contact.htm');
$link_arr[contact] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=faculty_dentistry';

			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[faculty].'"><h3>faculty</h3></a>
        </li>
	<ul>
<li><a href="'.$link_arr[message].'">deans message</a></li>
<li><a href="'.$link_arr[about].'">about us</a></li>
<li><a href="'.$link_arr[facultyinfo].'">faculty information</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[lib].'"><h3>Dentistry Library</h3></a>
        </li>
		
</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[academicprogrammes].'"><h3>Academic Programmes</h3></a>
        </li>
</ul>
</div>

<div class="menublock">
<ul>
        <li>
                <h3>Student Information Sheet</h3>
        </li>
	<ul>
<li><a href="'.$link_arr[en].'">English Version</a></li>
<li><a href="'.$link_arr[af].'">Afrikaans Version</a></li>
	</ul>
</div>




<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[staff].'"><h3>Faculty Staff</h3></a>
        </li>

	    <li>
                <a href="'.$link_arr[timetable].'"><h3>Timetable</h3></a>
        </li>

	    <li>
                <a href="'.$link_arr[contact].'"><h3>Contact Us</h3></a>
        </li>

	    <li>
                <a href="'.$link_arr[yearbook].'"><h3>Year Book</h3></a>
        </li>
	  	
</ul>
</div>


';
			
			return $this->getHomeLink().$menu;
		}				

	




		/*
		*  This method will return the FACULTY EMS MENU menu
		* @author: Charl Mert
		*/
		public function getFacultyEMSMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/about_faculty.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/dean.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/faculty_info.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/careers.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/programmes.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/programmes_undergraduate.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/programmes_postgraduate.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/departments_schools.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/academic_units.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/faculty_admin.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/faculty_info.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/faculty_staff.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/research.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/admission.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/find.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/economic_management/contact.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_ems";


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Faculty</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[1].'"><h3>About Faculty</h3></a>
        </li>


	<ul>
<li><a href="'.$link_arr[2].'">Dean\'s Message</a></li>
<li><a href="'.$link_arr[3].'">Faculty Information</a></li>
<li><a href="'.$link_arr[4].'">Career Opportunities</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[5].'"><h3>Academic Programmes</h3></a>
        </li>
<ul>
<li><a href="'.$link_arr[6].'">Undergraduate</a></li>
<li><a href="'.$link_arr[7].'">Postgraduate</a></li>
</ul>	

</ul>
</div>


<div class="menublock">
<ul>
        <li>
				<a href="'.$link_arr[8].'"><h3>Department &amp; Schools</h3></a>
        </li>

        <li>
				<a href="'.$link_arr[9].'"><h3>Academic Institutes Centers &amp; Units</h3></a>
        </li>

<ul>
<li>Administration</li>
<li><a href="'.$link_arr[10].'">Faculty Office</a></li>
<li><a href="'.$link_arr[11].'">Faculty Information</a></li>
</ul>	


</ul>	
</div>


<div class="menublock">
<ul>
        <li>
				<a href="'.$link_arr[12].'"><h3>Faculty Staff</h3></a>
        </li>

        <li>
				<a href="'.$link_arr[13].'"><h3>Faculty Research</h3></a>
        </li>

        <li>
				<a href="'.$link_arr[14].'"><h3>Admission</h3></a>
        </li>

        <li>
				<a href="'.$link_arr[15].'"><h3>Find Us</h3></a>
        </li>


        <li>
				<a href="'.$link_arr[16].'"><h3>Contact Us</h3></a>
        </li>

</ul>	
</div>


';
			
			return $this->getHomeLink().$menu;
		}				







	

		/*
		*  This method will return the FACULTY EMS MENU menu
		* @author: Charl Mert
		*/
		public function getFacultyEducationMenu(){
	


$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/dean.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/deans_report.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/faculty_info.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/about_us.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/advanced_programmes.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/professional_programmes.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/centres_&_projects.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/b_ed.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/pgce_computer_course.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

//$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/downloads/Pie_Vol1_Dec01.pdf');
//$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";
$link_arr[10] = 'usrfiles/importcms/gen11Srv7Nme54_8768_1210050546/Pie_Vol1_Dec01.pdf';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/deans_report.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/staff.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/research.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/contact.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/education/links.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_edu";


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Faculty</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[1].'">Dean\'s Message</a></li>
<li><a href="'.$link_arr[2].'">Dean\'s Report</a></li>
<li><a href="'.$link_arr[3].'">Faculty Information</a></li>
<li><a href="'.$link_arr[4].'">More About Us</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
      <li>  <a href="'.$link_arr[5].'"><h3>Advanced Programmes</h3></a>   </li>
      <li>  <a href="'.$link_arr[6].'"><h3>Professional Programmes</h3></a>   </li>
      <li>  <a href="'.$link_arr[7].'"><h3>Projects &amp Centers</h3></a>   </li>
      <li>  <a href="'.$link_arr[8].'"><h3>B Ed Computers in <br/>Education</h3></a>   </li>
      <li>  <a href="'.$link_arr[9].'"><h3>PGCE</h3></a>   </li>
      <li>  <a href="'.$link_arr[10].'"><h3>UWC Papers in Education</h3></a>   </li>
      <li>  <a href="'.$link_arr[11].'"><h3>Students</h3></a>   </li>
      <li>  <a href="'.$link_arr[12].'"><h3>Staff</h3></a>   </li>
      <li>  <a href="'.$link_arr[13].'"><h3>Research</h3></a>   </li>
      <li>  <a href="'.$link_arr[14].'"><h3>Contact Details</h3></a>   </li>
      <li>  <a href="'.$link_arr[15].'"><h3>Links</h3></a>   </li>
</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				

	





		/*
		*  This method will return the FACULTY LAW MENU menu
		* @author: Charl Mert
		*/
		public function getFacultyLawMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/about.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/faculty_info.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/careers.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/international_partners.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/whats_new.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/find_out.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/links.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/alumni.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/courses.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/course_material.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/staff.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/courses.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/departments.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/faculty_journal.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/llm_mphil_brochure.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/students.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/students_faq\'s.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/subject_01.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/academic_units.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/faculty_staff.htm');
$link_arr[20] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/research.htm');
$link_arr[21] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/admission.htm');
$link_arr[22] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/annual_african_moot.htm');
$link_arr[23] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/law/contact.htm');
$link_arr[24] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_law";


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Faculty</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[1].'"><h3>About the Faculty</h3></a>
        </li>


	<ul>
<li><a href="'.$link_arr[2].'">Faculty Information</a></li>
<li><a href="'.$link_arr[3].'">Career Opportunities</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
	  <li>  <a href="'.$link_arr[4].'"><h3>International Partners</h3></a></li>
      <li>  <a href="'.$link_arr[5].'"><h3>What\'s New</h3></a>   </li>
      <li>  <a href="'.$link_arr[6].'"><h3>Find Out More</h3></a>   </li>
      <li>  <a href="'.$link_arr[7].'"><h3>Links</h3></a>   </li>
      <li>  <a href="'.$link_arr[8].'"><h3>Alumni</h3></a>   </li>
      <li>  <a href="'.$link_arr[9].'"><h3>Academic Programmes</h3></a>   </li>
	<ul>
<li><a href="'.$link_arr[10].'">Online Course Material</a></li>
	</ul>

      <li>  <a href="'.$link_arr[11].'"><h3>Staff</h3></a>   </li>
      <li>  <a href="'.$link_arr[12].'"><h3>Courses</h3></a>   </li>
      <li>  <a href="'.$link_arr[13].'"><h3>Departments</h3></a>   </li>
      <li>  <a href="'.$link_arr[14].'"><h3>Law Democracy &amp;<br/> Development Journal</h3></a>   </li>
      <li>  <a href="'.$link_arr[15].'"><h3>LLM and Mphil Brochure</h3></a>   </li>

      <li>  <a href="'.$link_arr[16].'"><h3>Students</h3></a>   </li>
	<ul>
<li><a href="'.$link_arr[17].'">FAQ\'s</a></li>
	</ul>


      <li>  <a href="'.$link_arr[18].'"><h3>Subject Groups</h3></a>   </li>
      <li>  <a href="'.$link_arr[19].'"><h3>Academic Units</h3></a>   </li>
      <li>  <a href="'.$link_arr[20].'"><h3>Faculty Staff</h3></a>   </li>
      <li>  <a href="'.$link_arr[21].'"><h3>Faculty Research</h3></a>   </li>
      <li>  <a href="'.$link_arr[22].'"><h3>Admission</h3></a>   </li>
      <li>  <a href="'.$link_arr[23].'"><h3>4th Annual African Trade Moot</h3></a>   </li>
      <li>  <a href="'.$link_arr[24].'"><h3>Contact Us</h3></a>   </li>

</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				

	







		/*
		*  This method will return the FACULTY NATURAL SCIENCES MENU menu
		* @author: Charl Mert
		*/
		public function getFacultyNaturalScienceMenu(){
	


$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/dean.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/faculty_info.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/careers.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/undergraduate.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/postgraduate.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

//$c_id = $this->_objPortalImporterLog->getContentFileMatch('http://www.science.uwc.ac.za/openday/default.htm');
//$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";
$link_arr[6] = 'http://www.science.uwc.ac.za/openday/default.htm';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/fun_with_science.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/departmental_webpages.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/links.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/faculty_staff.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty/natural_science/contact.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id']."&menustate=faculty_nsc";

			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Faculty</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[1].'">Dean\'s Message</a></li>
<li><a href="'.$link_arr[2].'">Faculty Information</a></li>
<li><a href="'.$link_arr[3].'">Career Opportunities</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <h3>Academic Programmes</h3>
        </li>
<ul>
<li><a href="'.$link_arr[4].'">Undergraduate</a></li>
<li><a href="'.$link_arr[5].'">Postgraduate</a></li>
</ul>	

</ul>
</div>


<div class="menublock">
<ul>
        <li>
				<a href="'.$link_arr[6].'"><h3>Research Open Day</h3></a>
        </li>

        <li>
				<a href="'.$link_arr[7].'"><h3>Fun With Science</h3></a>
        </li>

        <li> <a href="'.$link_arr[8].'"><h3>Departmental Pages</h3></a> </li>
        <li> <a href="'.$link_arr[9].'"><h3>Links</h3></a> </li>
        <li> <a href="'.$link_arr[10].'"><h3>Faculty Staff</h3></a> </li>
        <li> <a href="'.$link_arr[11].'"><h3>Contact Us</h3></a> </li>

</ul>	
</div>

';
			
			return $this->getHomeLink().$menu;
		}				







		/*
		*  This method will return the ADMIN SUPPORT APU MENU menu
		* @author: Charl Mert
		*/
		public function getAdminSupportAPUMenu(){
	
$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/about.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/staff.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/documents.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/discussion_papers.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/plans.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/policy.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/reports.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/research.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_unit/apu/contact_us.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'];


			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>The Unit</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[1].'"><h3>About the Unit</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[2].'"><h3>Staff</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[3].'"><h3>Documents</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[4].'">Discussion Papers</a></li>
<li><a href="'.$link_arr[5].'">Plans</a></li>
<li><a href="'.$link_arr[6].'">Policies</a></li>
<li><a href="'.$link_arr[7].'">Reports</a></li>
<li><a href="'.$link_arr[8].'">Research</a></li>
	</ul>
</ul>
</div>

<div class="menublock">

<ul>
        <li>
                <a href="'.$link_arr[9].'"><h3>Documents</h3></a>
        </li>
</ul>
</div>


';
			
			return $this->getHomeLink().$menu;
		}				









		/*
		*  This method will return the OUR CAMPUS  menu
		* @author: Charl Mert
		*/
		public function getOurCampusMenu(){
	
$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/library.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/campus_services.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/sports.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/map.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/wifi.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/registration.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/activated.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/future.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('our_campus/faq.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=ourcampus';

			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Our Campus</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[1].'">Library</a></li>
<li><a href="'.$link_arr[2].'">Campus Services</a></li>
<li><a href="'.$link_arr[3].'">Sports</a></li>
<li><a href="'.$link_arr[4].'">Campus Map</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[5].'"><h3>Wi-Fi</h3></a>
        </li>

	<ul>
<li><a href="'.$link_arr[6].'">Registration Process</a></li>
<li><a href="'.$link_arr[7].'">Activated Sites</a></li>
<li><a href="'.$link_arr[8].'">Future Coverage</a></li>
<li><a href="'.$link_arr[9].'">FAQ\'s</a></li>
	</ul>
</div>



';
			
			return $this->getHomeLink().$menu;
		}				





		/*
		*  This method will return the ADMIN SUPPORT HR  menu
		* @author: Charl Mert
		*/
		public function getAdminSupportHRMenu(){
	
$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/strategy.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/services.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/our_people.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/online_services.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/faq\'s.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/policies_procedures.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/hrpolicy_document.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/code_conduct.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/early_retirement_policy.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/exit_policy.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/contract_policy_guidlines.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/performance_management.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/probation.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/recruitment_policy_admin.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/recruitment_policy_management.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/reward_foundation.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/comments.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/vacancy_list.htm');
$link_arr[18] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/human_resources/contact.htm');
$link_arr[19] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_hr';



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Human Resources</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[1].'"><h3>People Strategy</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[2].'"><h3>Services</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[3].'"><h3>Our People</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[4].'"><h3>Online Services</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[5].'"><h3>FAQ\'s</h3></a>
        </li>

        <li>
                <a href="'.$link_arr[6].'"><h3>Policies &amp; Procedures</h3></a>
        </li>





	<ul>
<li><a href="'.$link_arr[7].'">Admin Leave Policy</a></li>
<li><a href="'.$link_arr[8].'">Code Of Conduct</a></li>
<li><a href="'.$link_arr[9].'">Early Retirement</a></li>
<li><a href="'.$link_arr[10].'">Exit Policy</a></li>
<li><a href="'.$link_arr[11].'">Fulltime Fixed Contract</a></li>
<li><a href="'.$link_arr[12].'">Performance &amp; Management</a></li>
<li><a href="'.$link_arr[13].'">Probation Policy</a></li>
<li><a href="'.$link_arr[14].'">Recruitment Policy</a></li>
<li><a href="'.$link_arr[15].'">Management Recruitment Policy</a></li>
<li><a href="'.$link_arr[16].'">Reward Foundation</a></li>
	</ul>
</div>


<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[17].'"><h3>Comments</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[18].'"><h3>Vacancies @ UWC</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[19].'"><h3>Contact Us</h3></a>
        </li>


</div>



';
			
			return $this->getHomeLink().$menu;
		}				






		/*
		*  This method will return the ADMIN SUPPORT ODPA  menu
		* @author: Charl Mert
		*/
		public function getAdminSupportODPAMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/mission.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/alumni.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/cmpr.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/development.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/staff.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/projects.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/publications.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/news.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/events.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/media_resource_list.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/development_public_affairs/contact_us.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_odpa';



			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>ODPA</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[1].'"><h3>Mission</h3></a>
        </li>
        <li>
                <a href=""><h3>Units</h3></a>
        </li>


	<ul>
<li><a href="'.$link_arr[2].'">Alumni</a></li>
<li><a href="'.$link_arr[3].'">CMPR</a></li>
<li><a href="'.$link_arr[4].'">Development</a></li>
	</ul>
</ul>
</div>

<div class="menublock">
<ul>
 
<li><a href="'.$link_arr[5].'">Staff</a></li>
<li><a href="'.$link_arr[6].'">Projects</a></li>
<li><a href="'.$link_arr[7].'">Publications</a></li>
<li><a href="'.$link_arr[8].'">News</a></li>
<li><a href="'.$link_arr[9].'">Events</a></li>
<li><a href="'.$link_arr[10].'">Media Resource List</a></li>
<li><a href="'.$link_arr[11].'">Contact Us</a></li>
	</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				







		/*
		*  This method will return the ADMIN SUPPORT OSD  menu
		* @author: Charl Mert
		*/
		public function getAdminSupportOSDMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/introduction.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/staff_development.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/training_schedule.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/registration_procedures.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/staff_activities.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/general_orientation.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/colloquium.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/colloquium_day1_06.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/colloquium_day2_06.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/additional_resources.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/development_policy.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/office_staff_development/contact.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_osd';




			$menu = '

<div class="menublock">
<ul>
        <li>
                <a href="'.$link_arr[0].'"><h3>Mission</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[1].'"><h3>Introduction</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[2].'"><h3>Staff Development</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[3].'"><h3>Annual Trainning Schedule</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[4].'"><h3>Registration Procedures</h3></a>
        </li>
        <li>
                <a href="'.$link_arr[5].'"><h3>Staff Developement Activities</h3></a>
        </li>


	<ul>
<li><a href="'.$link_arr[6].'">General Orientation</a></li>
<li><a href="'.$link_arr[7].'">Teaching &amp; Learning</a></li>
<li><a href="'.$link_arr[8].'">Colloquium</a></li>
		<ul>
<li><a href="'.$link_arr[9].'">Day 1</a></li>
<li><a href="'.$link_arr[10].'">Day 2</a></li>
<li><a href="'.$link_arr[11].'">Additional Resources</a></li>
		</ul>

	</ul>
</ul>
</div>

<div class="menublock">
<ul>
 
<li><a href="'.$link_arr[5].'"><h3>Staff Development Policy</h3></a></li>
<li><a href="'.$link_arr[6].'"><h3>Contact Us</h3></a></li>

</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				





		/*
		*  This method will return the ADMIN SUPPORT PET  menu
		* @author: Charl Mert
		*/
		public function getAdminSupportPETMenu(){
	

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/home.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/about.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/services.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/facilities.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/resources.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/contact.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('faculty_project/pet/events.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_pet';


			$menu = '

<div class="menublock">
<ul>
        <li><a href="'.$link_arr[0].'"><h3>The Project</h3></a></li>
        <li><a href="'.$link_arr[1].'"><h3>Home</h3></a></li>
        <li><a href="'.$link_arr[2].'"><h3>About Us</h3></a></li>
        <li><a href="'.$link_arr[3].'"><h3>Services</h3></a></li>
        <li><a href="'.$link_arr[4].'"><h3>Facilities</h3></a></li>
        <li><a href="'.$link_arr[5].'"><h3>Resources</h3></a></li>
        <li><a href="'.$link_arr[6].'"><h3>Contact Us</h3></a></li>
        <li><a href="'.$link_arr[7].'"><h3>Events Calendar</h3></a></li>
</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				



		/*
		*  This method will return the ADMIN SUPPORT STUDENT ADMIN  menu
		* @author: Charl Mert
		*/
		public function getAdminSupportStudentAdminMenu(){
	
$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/student_administration/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_admin';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/student_administration/services.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_admin';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/student_administration/management.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_admin';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('administration/student_administration/contact.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_admin';

			$menu = '

<div class="menublock">
<ul>
        <li><a href="'.$link_arr[0].'"><h3>Student Administration</h3></a></li>
 <ul>
        <li><a href="'.$link_arr[1].'">Services</a></li>
        <li><a href="'.$link_arr[2].'">Management</a></li>
        <li><a href="'.$link_arr[3].'">Contact Us</a></li>
 </ul>
</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				




		/*
		*  This method will return the ADMIN SUPPORT STUDENT DEV SUPPORT  menu
		* @author: Charl Mert
		*/
		public function getAdminSupportStudentDevMenu(){

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/purpose.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/goals_objectives.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/management.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/projects.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/projects_01.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/projects_03.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/projects_02.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/units.htm');
$link_arr[7] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/campus_health_services.htm');
$link_arr[8] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/office_student_disabilities.htm');
$link_arr[9] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/financial_aid_office.htm');
$link_arr[10] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/institute_counselling.htm');
$link_arr[11] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/residence_catering_services.htm');
$link_arr[12] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/sport_administration.htm');
$link_arr[13] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/student_discipline.htm');
$link_arr[14] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/office_student_development.htm');
$link_arr[15] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/contact.htm');
$link_arr[16] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('student/sds/index.htm');
$link_arr[17] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=admin_support_student_dev';


	
			$menu = '

<div class="menublock">
<ul>
        <li><a href="'.$link_arr[17].'"><h3>Student Dev. &amp; Support</h3></a></li>
 <ul>
        <li><a href="'.$link_arr[0].'">About Us</a></li>
        <li><a href="'.$link_arr[1].'">Goals &amp; Objectives</a></li>
        <li><a href="'.$link_arr[2].'">Management</a></li>
 </ul>
</ul>
</div>

<div class="menublock">
<ul>
        <li><a href="'.$link_arr[3].'"><h3>Projects</h3></a></li>
 <ul>
        <li><a href="'.$link_arr[4].'">Brawam Siswam</a></li>
        <li><a href="'.$link_arr[5].'">Maths, Science &amp; Accounting</a></li>
        <li><a href="'.$link_arr[6].'">UWC RAG</a></li>
 </ul>
</ul>
</div>

<div class="menublock">
<ul>
        <li><a href="'.$link_arr[7].'"><h3>Units</h3></a></li>
 <ul>
        <li><a href="'.$link_arr[8].'">Campus Health Services</a></li>
        <li><a href="'.$link_arr[9].'">Disabilities</a></li>
        <li><a href="'.$link_arr[10].'">Financial Aid</a></li>
        <li><a href="'.$link_arr[11].'">Institute for Counselling</a></li>
        <li><a href="'.$link_arr[12].'">Residential &amp; Catering</a></li>
        <li><a href="'.$link_arr[13].'">Sports Administration</a></li>
        <li><a href="'.$link_arr[14].'">Student Discipline</a></li>
        <li><a href="'.$link_arr[15].'">Student Development</a></li>
 </ul>
</ul>
</div>

<div class="menublock">
<ul>
        <li><a href="'.$link_arr[16].'"><h3>Contact Us</h3></a></li>
</ul>
</div>


';
			
			return $this->getHomeLink().$menu;
		}				



		/*
		*  This method will return the ALUMNI AND CONVOCATION  menu
		* @author: Charl Mert
		*/
		public function getAlumniConvocationMenu(){

$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/our_community/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/convocation/index.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/convocation/term_of_reference.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/convocation/elected_members.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/convocation/president_report.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('public/convocation/advertising.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0]['content_id'].'&menustate=alumniconvocation';

	
			$menu = '

<div class="menublock">
<ul>
        <li><a href="'.$link_arr[17].'"><h3>Our Community</h3></a></li>
        <li><a href="'.$link_arr[17].'"><h3>Convocation</h3></a></li>
 <ul>
        <li><a href="'.$link_arr[0].'">About Us</a></li>
        <li><a href="'.$link_arr[1].'">Council Members</a></li>
        <li><a href="'.$link_arr[2].'">Annual Meeting</a></li>
        <li><a href="'.$link_arr[2].'">Advertising</a></li>
 </ul>
</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				





		/*
		*  This method will return the ALUMNI AND CONVOCATION  menu
		* @author: Charl Mert
		*/
		public function getICCBMenu(){

$c_id = $this->_objPortalImporterLog->getContentFileMatch('events/iccb_2008/index.htm');
$link_arr[0] = '?module=cms&action=showfulltext&id='.$c_id[0][content_id].'&menustate=iccb';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('events/iccb_2008/conf_details.htm');
$link_arr[1] = '?module=cms&action=showfulltext&id='.$c_id[0][content_id].'&menustate=iccb';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('events/iccb_2008/call_for_papers.htm');
$link_arr[2] = '?module=cms&action=showfulltext&id='.$c_id[0][content_id].'&menustate=iccb';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('events/iccb_2008/registration_form.htm');
$link_arr[3] = '?module=cms&action=showfulltext&id='.$c_id[0][content_id].'&menustate=iccb';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('events/iccb_2008/venue.htm');
$link_arr[4] = '?module=cms&action=showfulltext&id='.$c_id[0][content_id].'&menustate=iccb';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('events/iccb_2008/accomodation.htm');
$link_arr[5] = '?module=cms&action=showfulltext&id='.$c_id[0][content_id].'&menustate=iccb';

$c_id = $this->_objPortalImporterLog->getContentFileMatch('events/iccb_2008/contact.htm');
$link_arr[6] = '?module=cms&action=showfulltext&id='.$c_id[0][content_id].'&menustate=iccb';

	
			$menu = '

<div class="menublock">
<ul>
        <li><a href="'.$link_arr[0].'"><h3>The ICCB</h3></a></li>
        <li><a href="'.$link_arr[1].'"><h3>Conference Details</h3></a></li>
 <ul>
        <li><a href="'.$link_arr[2].'">Call For Papers</a></li>
        <li><a href="'.$link_arr[3].'">Registration</a></li>
        <li><a href="'.$link_arr[4].'">Conference Venue</a></li>
        <li><a href="'.$link_arr[5].'">Accomodation</a></li>
        <li><a href="'.$link_arr[6].'">Contact Us</a></li>
 </ul>
</ul>
</div>

';
			
			return $this->getHomeLink().$menu;
		}				



}
?>
