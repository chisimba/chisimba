<?php

/**
 * Utilities
 *
 * Context Utilities
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* ----------- data class extends dbTable for tbl_context_usernotes------------ */
// security check - must be included in all scripts
if (!/**
         * Description for $GLOBALS
         * @global string $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS ['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Utilities
 *
 * Context Utilities
 *
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class utilities extends object {

    /**
     * @var object $objDBContext
     */
    public $objDBContext;
    /**
     * @var object $objConfig
     */
    public $objConfig;
    /**
     * @var object $objDBContext
     */
    public $contextCode;

    /**
     * Constructor method to define the table
     */
    public function init() {
        $this->objDBContext = $this->getObject('dbcontext', 'context');
        $this->objLink = $this->getObject('link', 'htmlelements');
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->objConfig = $this->getObject('config', 'config');
        $this->objDBContextModules = $this->getObject('dbcontextmodules', 'context');
        $this->objDBContextParams = $this->getObject('dbcontextparams', 'context');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->contextCode = $this->objDBContext->getContextCode();
        $this->objUser = $this->getObject('user', 'security');
        $this->_objContextModules = $this->getObject('dbcontextmodules', 'context');
        $this->dbSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->showStudentCount = $this->dbSysConfig->getValue('SHOW_STUDENT_COUNT', 'context');
        $this->objUserContext = $this->getObject('usercontext');
    }

    /**
     * Method to get the sliding context menu
     *
     * @return string
     */
    public function getHiddenContextMenu($selectedModule, $showOrHide = 'none', $showOrHideContent = 'none') {
        $str = '';
        $icon = $this->newObject('geticon', 'htmlelements');
        $icon->setModuleIcon('toolbar');
        $toolsIcon = $icon->show();
        $icon->setModuleIcon('context');
        $contentIcon = $icon->show();

        $str .= "<a href=\"#\" onclick=\"Effect.toggle('contextmenu','slide');\">" . $toolsIcon . " Tools</a>";
        $str .= '<div id="contextmenu"  style="width:150px;overflow: hidden;display:' . $showOrHide . ';"> ';
        $str .= $this->getPluginNavigation($selectedModule);
        $str .= '</div>';

        $content = $this->getContextContentNavigation();
        if ($content != '') {
            $str .= "<br/><a href=\"#\" onclick=\"Effect.toggle('contextmenucontent','slide');\">" . $contentIcon . " Content</a>";
            $str .= '<div id="contextmenucontent"  style="width:150px;overflow: hidden;display:' . $showOrHideContent . ';"> ';
            $str .= $content;
            $str .= '</div>';
        }

        $objFeatureBox = $this->getObject('featurebox', 'navigation');

        return $objFeatureBox->show('Toolbox', $str, 'contexttoolbox');
    }

    /**
     * Method to get the left Navigation
     * with the context plugins
     *
     * @param  string $contextCode
     * @access public
     * @return string
     */
    public function getPluginNavigation($selectedModule = NULL) {
        $objSideBar = $this->newObject('sidebar', 'navigation');
        $objModule = $this->newObject('modules', 'modulecatalogue');
        //$objContentLinks = $this->getObject('dbcontextdesigner','contextdesigner');
        $objIcon = $this->getObject('geticon', 'htmlelements');

        $arr = $this->_objContextModules->getContextModules($this->objDBContext->getContextCode());
        $isregistered = '';

        //create the nodes array
        $nodes = array();
        $children = array();
        $nodes [] = array('text' => $this->objDBContext->getMenuText() . ' - Home', 'uri' => $this->uri(NULL, 'context'), 'nodeid' => 'context');
        if (is_array($arr)) {
            foreach ($arr as $contextModule) {
                //$modInfo =$objModule->getModuleInfo($plugin['moduleid']);
                if ($contextModule ['moduleid'] == 'cms') {
                    $isregistered = TRUE;
                } else {
                    $modInfo = $objModule->getModuleInfo($contextModule ['moduleid']);
                    $moduleLink = $this->uri(NULL, $contextModule ['moduleid']); //$this->uri(array('action' => 'contenthome', 'moduleid' => $contextModule['moduleid']));
                    $nodes [] = array('text' => ucwords($modInfo ['name']), 'uri' => $moduleLink, 'nodeid' => $contextModule ['moduleid']);
                }
            }

            return $objSideBar->show($nodes, $selectedModule);
        } else {
            return '';
        }
    }

    /**
     * Method to get the navigation menu
     * for the content section of the context
     *
     * @access public
     * @param  string $selectedLink The link that you are currently on
     * @return string
     */
    public function getContextContentNavigation($selectedLink = NULL) {
        $objSideBar = $this->getObject('sidebar', 'navigation');
        $objModule = $this->getObject('dbcontextmodules', 'context');
        //create the nodes array
        $nodes = array();

        return '';
    }

    /**
     * Method to check if a user can join a
     * context
     * @param  string  $contextCode The context Code
     * @return boolean
     * @access public
     * @author Wesley Nitsckie
     */
    public function canJoin($contextCode) {
        // TODO
        //check if the user is logged in to access an open context
        //check if the user is registered to the context and he is logged in
        //if the context is public then the user can access the context , but only limited access


        return TRUE;
    }

    /**
     * Method to create a link to the course home
     *
     * @return string
     */
    function getContextLinks() {
        $this->objIcon->setIcon("home");
        $this->objIcon->alt = $this->objLanguage->languageText("mod_context_coursehome", 'context');
        $this->objIcon->align = "absmiddle";

        $this->objLink->href = $this->URI(NULL, 'context');
        $this->objLink->link = $this->objIcon->show();
        $str = $this->objLink->show();

        return $str;
    }

    /**
     * Method to create links to the contents
     * and to the course
     *
     * @return string
     */
    function getContentLinks() {
        $this->objIcon->setModuleIcon("content");
        $this->objIcon->alt = $this->objLanguage->languageText("mod_context_coursecontent", 'context');
        $this->objIcon->align = "absmiddle";

        $params = array('nodeid' => $this->getParam('nodeid'), 'action' => 'content');
        $this->objLink->href = $this->URI($params, 'context');
        $this->objLink->link = $this->objIcon->show();
        $str = $this->objLink->show();

        return $str;
    }

    /**
     * Method to create links to the course admin
     *
     * @return string
     */
    function getCourseAdminLink() {
        $this->objIcon->setModuleIcon("contextadmin");
        $this->objIcon->alt = $this->objLanguage->languageText("mod_context_courseadmin", 'context');
        $this->objIcon->align = "absmiddle";

        $params = array('action' => 'courseadmin');
        $this->objLink->href = $this->URI($params, 'contextadmin');
        $this->objLink->link = $this->objIcon->show();
        $str = $this->objLink->show();

        return $str;
    }

    /**
     * Method used to get the path to the course folder
     *
     * @param  string $contextCode The context code
     * @return string
     */
    function getContextFolder($contextCode = NULL) {
        if ($contextCode == NULL) {
            $contextCode = $this->contextCode;
        }
        $str = $this->objConfig->siteRootPath() . 'usrfiles/content/' . $contextCode . '/';

        return $str;
    }

    /**
     * Method used to get the path to the images  folder
     * for a given course code
     *
     * @param  string $contextCode The context code
     * @return string
     */
    function getImagesFolder($contextCode = NULL) {
        return $this->getContextFolder($contextCode) . 'images/';
    }

    /**
     * Method used to get the path to the maps  folder
     * for a given course code
     *
     * @param  string $contextCode The context code
     * @return string
     */
    function getMapsFolder($contextCode = NULL) {
        return $this->getContextFolder($contextCode) . 'maps/';
    }

    /**
     * Method to get the context menu
     *
     * @return string
     * @param  void
     * @access public
     */
    public function getContextMenu() {
        try {
            //initiate the objects
            $objSideBar = $this->newObject('sidebar', 'navigation');
            $objModules = $this->newObject('modules', 'modulecatalogue');

            //get the contextCode
            $this->objDBContext->getContextCode();

            //create the nodes array
            $nodes = array();

            //get the section id
            $section = $this->getParam('id');

            //create the home for the context
            $nodes [] = array('text' => $this->objDBContext->getMenuText() . ' -  ' . $this->objLanguage->languageText("word_home", 'system', 'Home'), 'uri' => $this->uri(NULL, "_default"));

            //get the registered modules for this context
            $arrContextModules = $this->objDBContextModules->getContextModules($this->contextCode);

            foreach ($arrContextModules as $contextModule) {
                $modInfo = $objModules->getModuleInfo($contextModule ['moduleid']);

                $nodes [] = array('text' => $modInfo ['name'], 'uri' => $this->uri(array('action' => 'contenthome', 'moduleid' => $contextModule ['moduleid'])), 'sectionid' => $contextModule ['moduleid']);
            }

            return $objSideBar->show($nodes, $this->getParam('id'));
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage();
            exit ();
        }
    }

    /**
     * Block to searh for context
     */
    public function searchBlock_() {
        $script = $this->getJavaScriptFile('jquery/jquery-ui-personalized-1.6rc6/jquery-1.3.1.js', 'jquery');
        $script .= $this->getJavaScriptFile('jquery/jquery-ui-personalized-1.6rc6/jquery-ui-personalized-1.6rc6.js', 'jquery');
        $script .= '<link type="text/css" href="' . $this->getResourceUri('jquery/jquery-ui-personalized-1.6rc6/theme/ui.all.css', 'jquery') . '" rel="Stylesheet" />';
        $script .= $this->getJavaScriptFile('jquery/jquery.autocomplete.js', 'jquery');
        $this->appendArrayVar('headerParams', $script);
        $str = '<link rel="stylesheet" href="' . $this->getResourceUri('jquery/jquery.autocomplete.css', 'jquery') . '" type="text/css" />';
        $this->appendArrayVar('headerParams', $str);

        $str = '<script type="text/javascript">
$().ready(function() {

	function findValueCallback(event, data, formatted) {
		$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
	}

	function formatItem(row) {
		return row[0] + " (<strong>username: " + row[1] + "</strong>)";
	}

	function formatContextItem(row) {
		return row[0] + " (<strong>' . $this->objLanguage->code2Txt('phrase_othercourses', 'system', NULL, '[-context-]') . ' Code: " + row[1] + "</strong>)";
	}

	function formatResult(row) {
		//return row[0].replace(/(<.+?>)/gi, \'\');
		return row[0];
	}

$(":text, textarea").result(findValueCallback).next().click(function() {
		$(this).prev().search();
	});


	$("#usersearch").autocomplete(\'index.php?module=context&action=searchusers\', {
		width: 300,
		minChars: 2,
		multiple: false,
		matchContains: true,
		formatItem: formatItem,
		formatResult: formatResult,

	}).result(function (evt, data, formatted) {
					$("#usersearch_selected").val(data[1]);
					});

$("#contextsearch").autocomplete(\'index.php?module=context&action=searchcontext\', {
		width: 300,
		multiple: false,
		matchContains: true,
		formatItem: formatContextItem,
		formatResult: formatResult,

	}).result(function (evt, data, formatted) {
					$("#contextsearch_selected").val(data[1]);
					});

	$("#clear").click(function() {
		$(":input").unautocomplete();
	});
});

function submitSearch(data)
{

	alert(data[0]);
}


function changeOptions(){
	var max = parseInt(window.prompt(\'Please type number of items to display:\', jQuery.Autocompleter.defaults.max));
	if (max > 0) {
		$("#suggest1").setOptions({
			max: max
		});
	}
}

function submitSearchForm(frm)
{
	username = frm.usersearch_selected.value;

	if(username)
	{
		getUserContext(username);
	}

	frm.usersearch_selected.value = "";
	frm.usersearch.value = "";

}

function submitContextSearchForm(frm)
{
	contextCode = frm.contextsearch_selected.value;
	if(contextCode)
	{
		getContext(contextCode);

	}

	frm.contextsearch_selected.value = "";
	frm.contextsearch.value = "";
}

function getContexts()
{
	listContexts();
}

	</script>';
        $this->appendArrayVar('headerParams', $str);
        $input = '<div style="padding:10px;border:0px dashed black;" >
			<form id="searchform" name="searchform" autocomplete="off">
				<p>

					<table>
						<tr>
							<td>Search by user</td>
							<td><input type="text" id="usersearch"><input type="hidden" id="usersearch_selected">&nbsp;
					<input id="searchbutton" type="button" onclick="submitSearchForm(this.form)" value="Search" /></td>
						</tr>
						<tr>
							<td>' . $this->objLanguage->code2Txt('phrase_othercourses', 'system', NULL, 'Search by [-context-]') . '</td>
							<td><input type="text" id="contextsearch"><input type="hidden" name="contextsearch_selected" id="contextsearch_selected">
							&nbsp;
							<input id="searchbutton" type="button" onclick="submitContextSearchForm(this.form)" value="Search" /></td>
						</tr>
						<tr>
							<td><input type="button" value="' . $this->objLanguage->code2Txt('mod_context_viewallcontexts', 'context', NULL, 'View All [-contexts-]') . '" onclick="listContexts()"></td>
					</table>
				</p>
			</form>
		</div>
		<div id="context_results"></div>';

        return $input;
    }

    public function searchBlock() {
        return '<div id="topic-grid"></div>';
    }

    public function searchBlock__() {
        $script = $this->getJavaScriptFile('jquery/1.2.3/jquery-1.2.3.pack.js', 'jquery');
        $script .= $this->getJavaScriptFile('jquery/jquery.tablesorter.js', 'jquery');
        $script .= $this->getJavaScriptFile('jquery/plugins/tablesorter/pager/jquery.tablesorter.pager.js', 'jquery');
        $script .= '<link rel="stylesheet" href="' . $this->getResourceUri('jquery/plugins/themes/blue/style.css', 'jquery') . '" type="text/css" />';
        $script .= '<script type="text/javascript" id="js">
						$(function() {
								$("table")
									.tablesorter({widthFixed: true, widgets: [\'zebra\']})
									.tablesorterPager({container: $("#pager")});
							}); </script>';
        $this->appendArrayVar('headerParams', $script);

        $pagerDiv = '	<div id="pager" class="pager">
			<form>
				<img src="' . $this->getResourceUri('jquery/plugins/tablesorter/pager/icons/first.png', 'jquery') . '" class="first"/>
				<img src="' . $this->getResourceUri('jquery/plugins/tablesorter/pager/icons/prev.png', 'jquery') . '" class="prev"/>
				<input type="text" class="pagedisplay"/>
				<img src="' . $this->getResourceUri('jquery/plugins/tablesorter/pager/icons/next.png', 'jquery') . '" class="next"/>
				<img src="' . $this->getResourceUri('jquery/plugins/tablesorter/pager/icons/last.png', 'jquery') . '" class="last"/>
				<select class="pagesize">
					<option selected="selected"  value="10">10</option>
					<option value="20">20</option>
					<option value="30">30</option>
					<option  value="40">40</option>
				</select>
			</form>
		</div>';

        return $this->listContexts() . $pagerDiv;
    }

    /**
     * Method to get all the context by a filter
     * @param string $filter
     */
    public function getContextList() {
        $contexts = $this->objDBContext->getAll();

        $arr = array();
        foreach ($contexts as $context) {
            $arr[$this->objDBContext->getTitle($context['contextcode'])] = $context['contextcode']; //$user['userid'];
        }

        return $arr;
    }

    /**
     * Method to get all the context by a filter
     * @param string $filter
     */
    public function getUserList() {
        $users = $this->objUser->getAll();

        $arr = array();
        foreach ($users as $user) {
            $arr[$this->objUser->fullname($user['userid'])] = $user['username']; //$user['userid'];
        }

        return $arr;
    }

    /**
     * Method to format a users context
     * @oaram string username
     */
    public function formatUserContext($username) {
        $this->objUserContext = $this->getObject('usercontext', 'context');
        $contexts = $this->objUserContext->getUserContext($this->objUser->getUserId($username));
        if (count($contexts) > 0) {
            $str = "";
            $objDisplayContext = $this->getObject('displaycontext', 'context');
            foreach ($contexts as $contextCode) {
                $context = $this->objDBContext->getContext($contextCode);
                $str .= $objDisplayContext->formatContextDisplayBlock($context, FALSE, FALSE) . '<br />';
            }

            return $str;
        } else {
            return '<span class="subdued>' . $this->objLanguage->code2Txt('phrase_othercourses', 'system', NULL, 'No [-contexts-] found for this user') . '</span>';
        }
    }

    /**
     * Method to format a context
     * @oaram string username
     */
    public function formatSelectedContext($contextCode) {
        $context = $this->objDBContext->getContext($contextCode);
        $objDisplayContext = $this->getObject('displaycontext', 'context');
        return $objDisplayContext->formatContextDisplayBlock($context, FALSE, FALSE) . '<br />';
    }

    /**
     * Method to show all the context
     */
    public function listContexts() {
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $objLink = $this->getObject('link', 'htmlelements');
        $objTable = $this->getObject('htmltable', 'htmlelements');

        $contexts = $this->objDBContext->getAll("ORDER BY updated DESC");
        if (count($contexts) > 1) {
            $str = '<table><tr class="header"><td>Title</td><td>Code</td><td>Creator</td><<td>Lat Updated</td>/t>&nbsp;</td></tr>';
            $str = '<table cellspacing="1" class="tablesorter">
						<thead>
							<tr>
								<th>Code</th>
								<th>Title</th>
								<th>Creator</th>
								<th>Date Created</th>
								<th>Last Updated</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Title</th>
								<th>Code</th>
								<th>Creator</th>
								<th>Date Created</th>
								<th>Last Updated</th>
								<th>&nbsp;</th>

							</tr>
						</tfoot><tbody>';
            /* $objTable->addHeaderCell('Code');
              $objTable->addHeaderCell('Title', '40%');
              $objTable->addHeaderCell('Creator');
              $objTable->addHeaderCell('Last Updated');
              $objTable->addHeaderCell('&nbsp');
             */
            foreach ($contexts as $context) {
                $arr = array();
                $arr[] = $context['contextcode'];
                $arr[] = $context['title'];

                $arr[] = $this->objUser->fullname($context['userid']);
                $arr[] = $context['updated'];

                $objIcon->setIcon('entercourse');
                $objLink->href = $this->uri(array('action' => 'joincontext', 'contextcode' => $context['contextcode']), 'context');
                $objLink->link = $objIcon->show();
                $enter = $objLink->show();

                $objIcon->setIcon('delete');
                $objLink->href = $this->uri(array('action' => 'delete', 'contextcode' => $context['contextcode']), 'contextadmin');
                $objLink->link = $objIcon->show();
                $delete = $objLink->show();

                $str .='<tr>';
                $str .='<td>' . $context['contextcode'] . '</td>';
                $str .='<td>' . $context['title'] . '</td>';
                $str .='<td>' . $this->objUser->fullname($context['userid']) . '</td>';
                $str .='<td>' . $context['datecreated'] . '</td>';
                $str .='<td>' . $context['updated'] . '</td>';


                $str .='<td>' . $enter . $delete . '</td>';

                $str .= '</tr>';

                //$arr[] = $enter.$delete;
                //$objTable->addRow($arr);
            }
            $str .= '</tbody></table>';

            return $str;
        }
    }

    public function jsonListAllContext() {

        $limit = ($this->getParam('limit') == "") ? "" : $this->getParam('limit');
        $offset = ($this->getParam('offset') == "") ? "" : $this->getParam('offset');
        $filter = ($this->getParam('letter') == "") ? "" : $this->getParam('letter') . '%';

        $sql = "SELECT * FROM tbl_context WHERE title LIKE '" . $filter . "' ORDER BY title ASC limit " . $offset . ", " . $limit;
        $contexts = $this->objDBContext->getArray($sql);
        $nocontexts = count($contexts);
        $courses = array();
        $this->objUserContext = $this->getObject('usercontext', 'context');
        if ($nocontexts > 0) {
            foreach ($contexts as $context) {
                $arr = array();
                $arr['code'] = $context['contextcode'];
                $arr['title'] = htmlentities($context['title']);
                $lectures = $this->objUserContext->getContextLecturers($context['contextcode']);
                $lecturesname = "";
                foreach ($lectures as $lecture) {
                    $lecturesname .= $lecture['firstname'] . " " . $lecture['surname'] . ", ";
                }
                $arr['lecturers'] = htmlentities(substr($lecturesname, 0, -2));
                $arr['access'] = $context['access'];
                $courses[] = $arr;
            }
        }

        return json_encode(array('othercontextcount' => $nocontexts, 'courses' => $courses));
    }

    public function getContext($start = 0, $limit = 25) {
        $params["search"] = ($this->getParam("fields")) ? json_decode(stripslashes($this->getParam("fields"))) : null;
        $params["query"] = ($this->getParam("query")) ? $this->getParam("query") : null;
        $params["sort"] = ($this->getParam("sort")) ? $this->getParam("sort") : null;
        $params["start"] = ($this->getParam("start")) ? $this->getParam("start") : null;
        $params["limit"] = ($this->getParam("limit")) ? $this->getParam("limit") : null;

        trigger_error(var_export($params, true));

        $searchLecturers = FALSE;
        //$searchLecturersString = NULL;
        if (is_array($params['search']) && !empty($params['search'])) {
            foreach ($params['search'] as $field) {
                if ($field == "lecturers") {
                    //$searchLecturers = TRUE;
                    $searchLecturers = TRUE;
                    break;
                }
            }
        }

        $where = "";
        //
        //check if this is a search
        if (is_array($params['search']) && !empty($params['search'])) {
            //$max = count($params['search']);
            //$cnt = 0;
            $or = '';
            foreach ($params['search'] as $field) {
                //$cnt++;
                /*
                if($field == "lecturers")
                    {
                    $sql = "SELECT * FROM tbl_context";
                    $minicontexts = $this->objDBContext->getArray($sql);
                    error_log(var_export($minicontexts, true));
                    foreach($minicontexts as $context){
                    $str = $this->objUserContext->getContextLecturers($context['contextcode']);
                    }
                }
                */
                if ($field == "lecturers") {
                    continue;
                } else {
                    $where .= $or . $field . ' LIKE "' . $params['query'] . '%"';
                    $or = " OR ";
                }
                //if ($cnt < $max) {
                //}
            }
            if ($where != '') {
                $where = ' AND (' . $where . ')';
            }

        }

        if ($searchLecturers === FALSE || $where != '') {
            $sql = "SELECT * FROM tbl_context  WHERE (status != 'Unpublished'){$where} ORDER BY title LIMIT {$start}, 25";
            //Debuging
            error_log(var_export($_REQUEST, true));
            //Debuging
            $contexts = $this->objDBContext->getArray($sql);
        } else {
            $contexts = array();
        }
        if ($searchLecturers !== FALSE) {
            $sql = "SELECT * FROM tbl_context  WHERE (status != 'Unpublished') ORDER BY title LIMIT {$start}, 25";
            $contextsLecturers = $this->objDBContext->getArray($sql);
        } else {
            $contextsLecturers = array();
        }
        $countSQL = "SELECT COUNT(DISTINCT(contextcode)) AS cnt FROM tbl_context";
        $arrCountDistinct = $this->objDBContext->getArray($countSQL);
        $contextCount = $arrCountDistinct[0]['cnt']; //count();
        $this->objUserContext = $this->getObject('usercontext', 'context');
        if ($searchLecturers !== FALSE && !empty($contextsLecturers)) {
            foreach ($contextsLecturers as $context) {
                $lecturers = $this->objUserContext->getContextLecturers($context['contextcode']);
                $lecturersnames = "";
                $separator = '';
                foreach ($lecturers as $lecturer) {
                    $lecturersnames .= $separator . $lecturer['firstname'] . " " . $lecturer['surname'];
                    $separator = "|";
                }
                if ($context['contextcode'] == 'jera01') {
                    trigger_error(($pos = stripos($context['contextcode'], 'jera01'))?$pos:'F');
                }
                if (stripos($context['contextcode'], 'jera01') !== FALSE) {
                    trigger_error($lecturersnames);
                    trigger_error($params['query']);
                    trigger_error(($pos = stripos($lecturersnames, $params['query']))?$pos:'F');
                }
                if ($searchLecturers !== FALSE && (stripos($lecturersnames, $params['query']) !== FALSE || $params['query'] == '')) {
                    $contexts[] = $context;
                }
            }
        }
        if (!empty($contexts)) {
            $tempArr = array();
            foreach ($contexts as $context){
                $tempArr[$context['title']] = $context;
            }
            ksort($tempArr);
            $contexts = array();
            $i = 0;
            foreach ($tempArr as $context){
                $contexts[] = $context;
                ++$i;
                if ($i == 25) {
                    break;
                }
            }
        }
        $courses = array();
        if (!empty($contexts)) {
            //$arr = array();
            foreach ($contexts as $context) {
                if (strtoupper($this->showStudentCount) == 'TRUE') {
                    $studentCount = '&nbsp;(' . count($this->objUserContext->getContextStudents($context['contextcode'])) . ')';
                } else {
                    $studentCount = "";
                }
                $arr = array();
                $arr['contextcode'] = $context['contextcode'];
                $arr['code'] = $context['contextcode'];
                $arr['title'] = htmlentities($context['title']) . $studentCount;
                $lecturers = $this->objUserContext->getContextLecturers($context['contextcode']);
                $lecturersnames = "";
                $comma = '';
                foreach ($lecturers as $lecturer) {
                    $lecturersnames .= $comma . $lecturer['firstname'] . " " . $lecturer['surname'];
                    $comma = ", ";
                }
                $arr['lecturers'] = htmlentities($lecturersnames);
                $arr['access'] = $context['access'];
                $courses[] = $arr;
            }
            //echo "<script>alert('Information updated')</script>";
            return json_encode(array('othercontextcount' => $contextCount, 'courses' => $courses));
            return json_encode($arr); //?
        } else {
            //var_dump($arr);
            return json_encode(array('othercontextcount'=>"0", 'courses'=>array()));
        }
    }

    /**
     * Method to get a paginated
     * list of courses
     *
     * @param unknown_type $start
     * @param unknown_type $limit
     * @return unknown
     */
    public function jsonListContext($start=0, $limit=25) {

        $start = (empty($start)) ? 0 : $start;
        $limit = (empty($limit)) ? 25 : $limit;

        $contexts = $this->objDBContext->getAll("ORDER BY title limit " . $start . ", " . $limit);
        $all = $this->objDBContext->getArray("SELECT count( id ) as cnt FROM tbl_context ORDER BY title");

        $allCount = $all[0]['cnt'];

        $contextCount = count($contexts);
        $courses = array();

        if ($contextCount > 0) {
            foreach ($contexts as $context) {
                $studentCount = "";
                if (strtoupper($this->showStudentCount) == 'TRUE') {
                    $studentCount = '&nbsp;(' . count($this->objUserContext->getContextStudents($context ['contextcode'])) . ')';
                }
                $arr = array();
                $arr['contextcode'] = $context['contextcode'];
                $arr['title'] = htmlentities($context['title']) . $studentCount;
                $arr['author'] = htmlentities($this->objUser->fullname($context['userid']));
                $arr['datecreated'] = $context['datecreated'];
                $arr['lastupdated'] = $context['updated'];
                $arr['status'] = $context['status'];
                $arr['expert'] = "";

                $courses[] = $arr;
            }
        }

        return json_encode(array('totalCount' => $allCount, 'courses' => $courses));
    }

    /**
     * This copies students from one course into another
     * @param <type> $contextCode1
     * @param <type> $contextCode2
     */
    function copyStudentsFromOneCourseToNext($contextCode1, $contextCode2) {
        $groupOps = $this->getObject('groupops', 'groupadmin');
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $contextGroupId1 = $objGroups->getId($contextCode1 . '^Students');
        $contextGroupId2 = $objGroups->getId($contextCode2 . '^Students');

        $usersInContext1 = $groupOps->getUsersInGroup($contextGroupId1);
        foreach ($usersInContext1 as $user) {
            $puid = $this->getUserPuid($user['auth_user_id']);

            $objGroups->addGroupUser($contextGroupId2, $puid);
        }

        return true;
    }

    /**
     * we get puid because we need it to add a user to a group
     * @param <type> $userid
     * @return <type>
     */
    function getUserPuid($userid) {

        $sql =
                "select puid  from tbl_users where userid = '$userid'";
        $rows = $this->objDBContext->getArray($sql);
        $row = $rows[0];
        return $row['puid'];
    }

}

?>
