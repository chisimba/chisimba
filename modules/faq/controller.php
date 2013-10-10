<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for FAQ module
 * @author Jeremy O'Connor , remade by Stelio Macumbe
 * @copyright 2004 University of the Western Cape
 * $Id: controller.php 24777 2012-11-30 16:08:44Z dkeats $
 */
class faq extends controller {

    public $objUser;
    protected $objGroup;
    protected $objSysConfig;
    public $objLanguage;
    public $objFaqCategories;
    public $objFaqEntries;
    public $contextId;
    public $contextTitle;
    public $categoryId;

    /**
     * The Init function
     */
    public function init() {
        $this->objUser = $this->getObject('user', 'security');
        $this->objGroup = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objFaqCategories = $this->getObject('dbfaqcategories');
        $this->objFaqEntries = $this->getObject('dbfaqentries');
        $this->objTags = $this->getObject('dbfaqtags');
        // Get the activity logger class
        $this->objLog = $this->newObject('logactivity', 'logger');
        $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
        // Log this module call
        $this->objLog->log();
    }

    /**
     * The dispatch funtion
     * @param string $action The action
     */
    public function dispatch($action=NULL) {
        // Set the layout template for faq - includes the context menu
        $this->setLayoutTemplate("context_layout_tpl.php");

        // Check to ensure the user is allowed to execute this action.
        if ($this->isRestricted($action) && !$this->userHasModifyAccess()) {
            return 'access_denied_tpl.php';
        }
        // Set the error string
        $error = "";
        $this->setVarByRef("error", $error);
        // Get the context
        $this->objDbContext = &$this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objDbContext->getContextCode();
        // If we are not in a context...
        if ($this->contextCode == null) {
            $this->contextId = "root";
            $this->setVarByRef('contextId', $this->contextId);
            $this->contextTitle = "Default";
            $this->setVarByRef('contextTitle', $this->contextTitle);
        }
        // ... we are in a context
        else {
            $this->contextId = $this->contextCode;
            $this->setVarByRef('contextId', $this->contextId);
            $contextRecord = $this->objDbContext->getContextDetails($this->contextCode);
            $this->contextTitle = $contextRecord['title'];
            $this->setVarByRef('contextTitle', $this->contextTitle);
        }

        $numCategories = $this->objFaqCategories->getNumContextCategories($this->contextId);

        // Get category from URL
        $this->categoryId = $this->getParam('category');
        $this->setVarByRef('categoryId', $this->categoryId);

        // return the name of the template to use  because it is a page content template
        // the file must live in the templates/content subdir of the module directory
        switch ($action) {
            // Change the category
            case 'changecategory':
                return $this->view();
            // Add an entry
            case "add":
                return $this->add();
            //Add confirm
            case "addconfirm":
                return $this->addConfirm();
            // Edit an entry
            case "edit":
                return $this->edit();
            case "tag":
                $tag = $this->getParam('tag');
                return $this->viewByTag($tag);
            //Edit confirm
            case "editconfirm":
                return $this->editConfirm();
            // Delete an entry
            case "deleteconfirm":
                return $this->deleteConfirm();
            // Default : view entries

            case "search":
                $query = $this->getParam('q');
                $this->setVarByRef('query', $query);
                return 'search_tpl.php';
            // Add an entry
            case "addcategory":
                return "add_category_tpl.php";
            // Add Confirm
            case "addcategoryconfirm":
                $this->addCategoryConfirm();
                break;
            // Edit an entry
            case "editcategory":
                $this->editCategory();
                return "edit_category_tpl.php";
            // Edit Confirm
            case "editcategoryconfirm":
                $this->editCategoryConfirm();
                break;
            // Delete an entry
            case "deletecategoryconfirm":
                $this->deleteCategoryConfirm();
                break;
            case "managecategories":
                $categories = $this->objFaqCategories->getContextCategories($this->contextId);
                $this->setVarByRef('categories', $categories);
                return "main_tpl.php";
                break;
            //************************************************************************************************/
            case "view":
                return $this->view();
            default:
                return $this->listCategories();
        } // switch
    }

    /**
     *
     * This is a method that overrides the parent class to stipulate whether
     * the current module requires login. Having it set to false gives public
     * access to this module including all its actions.
     *
     * @access public
     * @return bool FALSE
     */
    public function requiresLogin($action) {
        $requiresLogin = array('add', 'addcategory','edit');

        if (in_array($action, $requiresLogin)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to override isValid to enable administrators to perform certain action
     *
     * @param $action Action to be taken
     * @return boolean
     */
    public function isValid($action) {
        if(!$this->objUser->isLoggedIn()){
            return FALSE;
        }
        if ($this->objUser->isAdmin() || $this->objContextGroups->isContextLecturer()) {
            return TRUE;
        } else {
            return FALSE; //parent::isValid ( $action );
        }
    }

    public function listCategories() {
        $tagCloud = $this->objTags->getTagCloud();
        $this->setVarByRef('tagCloud', $tagCloud);

        $categories = $this->objFaqCategories->getContextCategories($this->contextId);
        $this->setVarByRef('categories', $categories);
        return "main_tpl.php";
    }

    /**
     * View all FAQ entries by tag.
     */
    public function viewByTag($tag) {
        $list = $this->objFaqEntries->listAllByTag($tag);
        $this->setVarByRef('list', $list);
        // Get all the categories
        $categories = $this->objFaqCategories->getContextCategories($this->contextId);
        $this->setVarByRef('categories', $categories);
        return "view_tpl.php";
    }

    /**
     * View all FAQ entries.
     */
    public function view() {
        // Get all FAQ entries
        // $list = $this->objFaqEntries->listAll($this->contextId, $this->categoryId);
        // $this->setVarByRef('list', $list);
        // Get all FAQ entries
        $list = $this->objFaqEntries->listAllWithNav($this->contextId, $this->categoryId);
        $this->setVarByRef('list', $list);

        // Get all the categories
        $categories = $this->objFaqCategories->getContextCategories($this->contextId);
        $this->setVarByRef('categories', $categories);
        return "view_tpl.php";
    }

    /**
     * Add a FAQ entry.
     */
    public function add() {
        // Get all the categories
        $categories = $this->objFaqCategories->getContextCategories($this->contextId);
        $this->setVarByRef('categories', $categories);

        return "add_tpl.php";
    }

    /**
     * Confirm add.
     */
    public function addConfirm() {
        $question = $this->getParam("question");
        $answer = $this->getParam("answer");
        $category = $this->getParam("category");
        $tags = $this->getParam("faqtags");
        // Insert a record into the database
        $this->objFaqEntries->insertSingle(
                $this->contextId,
                $category,
                $question,
                $answer,
                $this->objUser->userId(),
                mktime(),
                $tags
        );

        return $this->nextAction('view', array('category' => $category));
    }

    /**
     * Edit a FAQ entry.
     */
    public function edit() {
        $id = $this->getParam('id', null);
        $item = $this->objFaqEntries->listSingle($id);
        $this->setVarByRef('item', $item);
        $objTags = $this->getObject('dbfaqtags');
        $tags = $objTags->getFaqTags($id);
        $this->setVarByRef("tags", $tags);
        // Get all the categories
        $categories = $this->objFaqCategories->getContextCategories($this->contextId);
        $this->setVarByRef('categories', $categories);

        return "edit_tpl.php";
    }

    /**
     * Confirm edit.
     */
    public function editConfirm() {
        $id = $this->getParam('id');
        $question = $this->getParam('question');
        $answer = $this->getParam('answer');
        $category = $this->getParam('category');
        $objTags = $this->getObject('dbfaqtags');
        $tags = $this->getParam("faqtags");
        // Update the record in the database
        $this->objFaqEntries->updateSingle(
                $id,
                $question,
                $answer,
                $category,
                $this->objUser->userId(),
                mktime()
        );


        $objTags->updateFaqTags($id, $tags);

        return $this->nextAction('view', array('category' => $category));
    }

    /**
     * Confirm delete.
     */
    public function deleteConfirm() {
        $id = $this->getParam('id', null);
        // Delete the record from the database
        $this->objFaqEntries->deleteSingle($id);
        $objTags = $this->getObject('dbfaqtags');
        $objTags->clearFaqTags($id);
        return $this->nextAction('view', array('category' => $this->categoryId));
    }

    /**
     * Method to load an HTML element's class.
     * @param string $name The name of the element
     * @return The element object
     */
    public function loadHTMLElement($name) {
        return $this->loadClass($name, 'htmlelements');
    }

    /**
     * Method to get a new HTML element.
     * @param string $name The name of the element
     * @return The element object
     */
    public function &newHTMLElement($name) {
        return $this->newObject($name, 'htmlelements');
    }

    /**
     * Method to get an HTML element.
     * @param string $name The name of the element
     * @return The element object
     */
    public function getHTMLElement($name) {
        return $this->getObject($name, 'htmlelements');
    }

    /**
     * Confirm add.
     */
    public function addCategoryConfirm() {
        $categoryName = $this->getParam("category");


        if (trim($categoryName) == '') {
            return $this->nextAction('addcategory', array('error' => 'nothingentered'));
        } else {
            // Insert the category into the database
            $result = $this->objFaqCategories->insertSingle(
                            $this->contextId,
                            $categoryName,
                            $this->objUser->userId()
            );
            return $this->nextAction(NULL, array('message' => 'categoryadded', 'result' => $result));
        }
    }

    /**
     * Edit a FAQ entry.
     */
    public function editCategory() {
        $id = $this->getParam('id', null);
        $list = $this->objFaqCategories->listSingleId($id);
        $this->setVarByRef('list', $list);
    }

    /**
     * Confirm edit.
     */
    public function editCategoryConfirm() {
        $id = $this->getParam('id', null);
        $categoryId = $_POST["category"];
        // Update the record in the database
        $this->objFaqCategories->updateSingle(
                $id,
                $categoryId,
                $this->objUser->userId(),
                mktime()
        );
        return $this->nextAction(NULL);
    }

    /**
     * Confirm delete.
     */
    public function deleteCategoryConfirm() {
        $id = $this->getParam('id', null);
        // Delete the record from the database
        $this->objFaqCategories->deleteSingle($id);
        $this->objFaqEntries->delete("categoryid", $id);
        return $this->nextAction(NULL);
    }

    /**
     * Checks if the user has access to make modifications to the FAQ.
     *
     * @return boolean True if the user can make modifications, false otherwise.
     */
    protected function userHasModifyAccess() {
        $limitedUsers = $this->objSysConfig->getValue('mod_faq_limited_users', 'faq');
        if ($limitedUsers) {
            $userId = $this->objUser->userId();
            $groups = array('Site Admin', 'Lecturers');
            $isMember = FALSE;
            foreach ($groups as $group) {
                $groupId = $this->objGroup->getId($group);
                if ($this->objGroup->isGroupMember($userId, $groupId)) {
                    $isMember = TRUE;
                    break;
                }
            }
            return $isMember;
        } else {
            return TRUE;
        }
    }

    /**
     * Checks if the given action is only available to certain user groups.
     *
     * @param string $action The name of the action to check.
     * @return boolean True if the action is restricted, false otherwise.
     */
    protected function isRestricted($action) {
        $restrictedActions = array('add', 'addconfirm', 'edit', 'editconfirm', 'deleteconfirm', 'addcategory', 'addcategoryconfirm', 'editcategory', 'editcategoryconfirm', 'deletecategoryconfirm', 'managecategories');
        return in_array($action, $restrictedActions);
    }

}
