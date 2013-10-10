<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Controller for Rubric module
* @author Jeremy O'Connor
* @porter Dean Van Niekerk
* @  PORTED from php4 to php5
* @email dvanniekerk@uwc.ac.za
* @copyright 2004 University of the Western Cape
* $Id: controller.php 16870 2010-02-17 13:51:15Z pwando $
*/
class rubric extends controller
{
   public $objUser;
	public $objLanguage;
	public $contextCode;
	public $objDbRubricTables;
	public $objDbRubricPerformances;
	public $objDbRubricObjectives;
	public $objDbRubricCells;
	public $objDbRubricAssessments;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access protected
     * @var    object
     */
    protected $objSysConfig;

    /**
     * Instance of the groupadminmodel class of the groupadmin module.
     *
     * @access protected
     * @var    object
     */
    protected $objGroup;

    /**
    * The Init function
    */
    public function init()
    {
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objGroup = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language','language');
		  $this->objDbRubricTables =& $this->getObject('dbrubrictables');
		  $this->objDbRubricPerformances =& $this->getObject('dbrubricperformances');
		  $this->objDbRubricObjectives =& $this->getObject('dbrubricobjectives');
		  $this->objDbRubricCells =& $this->getObject('dbrubriccells');
		  $this->objDbRubricAssessments =& $this->getObject('dbrubricassessments');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
		$this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
    }

   /**
     * Method to override isValid to enable administrators to perform certain action
     *
     * @param $action Action to be taken
     * @return boolean
     */
    public function isValid($action) {

    	$validActions = array('viewtable', 'assessments', 'viewassessment');

        if ($this->objUser->isAdmin () || $this->objContextGroups->isContextLecturer() || in_array($action, $validActions)) {
            return TRUE;
        } else {
            return FALSE;//parent::isValid ( $action );
        }
    }

    /**
    * The dispatch funtion
    * @param string $action The action
    * @return string The content template file
    */
public function dispatch($action=Null)
    {
        // Set the layout template.
        $this->setLayoutTemplate("layout_tpl.php");

        // Check to ensure the user is allowed to execute this action.
        if ($this->isRestricted($action) && !$this->userHasModifyAccess()) {
            return 'access_denied_tpl.php';
        }

        // 1. ignore action at moment as we only do one thing - say hello
        // 2. load the data object (calls the magical getObject which finds the
        //    appropriate file, includes it, and either instantiates the object,
        //    or returns the existing instance if there is one. In this case we
        //    are not actually getting a data object, just a helper to the
        //    controller.
        // 3. Pass variables to the template
        $this->setVarByRef('objUser', $this->objUser);
		  $this->setVarByRef('objLanguage', $this->objLanguage);
        // return the name of the template to use  because it is a page content template
        // the file must live in the templates/content subdir of the module directory
		  $this->objDbContext = &$this->getObject('dbcontext','context');
		  // Check if called from Megan's module.
		$noBanner = $this->getParam("NoBanner", "no");
		$this->setVarByRef('noBanner', $noBanner);
		if ($noBanner == "yes") {
			//$this->setPageTemplate("NoBanner_page_tpl.php");
            $this->setVar('pageSuppressBanner', TRUE);
            $this->setVar('pageSuppressToolbar', TRUE);
		}
		// Get the context
		$this->contextCode = $this->objDbContext->getContextCode();
		$this->setVarByRef('contextCode', $this->contextCode);
        // Are we in a context?
		if ($this->contextCode == Null) {
			$this->contextCode = "root";
			$contextTitle = "Lobby";
		}	else {
			$contextRecord = $this->objDbContext->getContextDetails($this->contextCode);
			$contextTitle = $contextRecord['title'];
		}
	    $this->setVarByRef('contextTitle', $contextTitle);
		switch($action){
			case "createtable":
				$this->setVarByRef("_type", $this->getParam("type", ""));
		        return "create_tpl.php";
			case "createtableconfirm":
				$this->setLayoutTemplate(NULL);
                $type = $this->getParam("type", "");
                // Insert a record into the database
				$tableId = $this->objDbRubricTables->insertSingle(
					$type == 'predefined' ? 'root' : $this->contextCode,
					$_POST["title"],
					$_POST["description"],
					$_POST["rows"],
					$_POST["cols"],
					$type == 'predefined' ? $this->objUser->userId() : NULL
				);
				$title = $_POST["title"];
				$description = $_POST["description"];
				$rows = $_POST["rows"];
				$cols = $_POST["cols"];
				$this->setVarByRef("tableId", $tableId);
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Build the performances array

		        //return "edit_tpl.php";

                return $this->nextAction('edittable', array('tableId'=>$tableId, 'new'=>'yes'));
			// Rename a rubric
			case "renametable":
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$this->setVarByRef("title", $title);
				$description = $tableInfo[0]['description'];
				$this->setVarByRef("description", $description);
		        return "rename_tpl.php";
            // Rename the rubric
			case "renametableconfirm":
				$tableId = $this->getParam("tableId", "");
				$this->objDbRubricTables->updateSingle(
					$tableId,
					$_POST["title"],
					$_POST["description"]
				);
				break;
			// Clone a rubric
			case 'clonetable':
				$tableId = $this->getParam('tableId', "");
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$contextCode = $tableInfo[0]['contextcode'];
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$userId = $tableInfo[0]['userid'];
                // Insert a record into the database
				$_tableId = $this->objDbRubricTables->insertSingle(
					$contextCode,
					"Copy of $title",
					$description,
					$rows,
					$cols,
					$userId
				);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
				}
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
                // Insert the perofmances into the database
				for ($j=0;$j<$cols;$j++) {
					$this->objDbRubricPerformances->insertSingle(
						$_tableId,
						"{$j}",
						$performances[$j]
					);
				}
                // Insert the objectives into the database
				for ($i=0;$i<$rows;$i++) {
					$this->objDbRubricObjectives->insertSingle(
						$_tableId,
						"{$i}",
						$objectives[$i]
					);
				}
                // Insert the cells into the database
				for ($i=0;$i<$rows;$i++) {
					for ($j=0;$j<$cols;$j++) {
						$this->objDbRubricCells->insertSingle(
							$_tableId,
							"{$i}",
							"{$j}",
							$cells[$i][$j]
						);
					}
				}
				break;
			// Copy a rubric
			case "copytable":
				$tableId = $this->getParam("tableId", "");
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
                // Insert a record into the database
				$_tableId = $this->objDbRubricTables->insertSingle(
					$this->contextCode,
					"Copy of $title",
					$description,
					$rows,
					$cols,
					NULL
				);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
				}
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
                // Insert the perofmances into the database
				for ($j=0;$j<$cols;$j++) {
					$this->objDbRubricPerformances->insertSingle(
						$_tableId,
						"{$j}",
						$performances[$j]
					);
				}
                // Insert the objectives into the database
				for ($i=0;$i<$rows;$i++) {
					$this->objDbRubricObjectives->insertSingle(
						$_tableId,
						"{$i}",
						$objectives[$i]
					);
				}
                // Insert the cells into the database
				for ($i=0;$i<$rows;$i++) {
					for ($j=0;$j<$cols;$j++) {
						$this->objDbRubricCells->insertSingle(
							$_tableId,
							"{$i}",
							"{$j}",
							$cells[$i][$j]
						);
					}
				}
				break;
			// Edit the rubric
			case "edittable":
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);

                // Check if this is a new rubric being created or one being edited
                if ($this->getParam('new', 'no') == 'yes') {
                    $performances = array();
                    for ($j=0;$j<$cols;$j++) {
                        $performances[] = "Performance ".($j+1);
                    }
                    $this->setVarByRef("performances", $performances);
                    // Build the objectives array
                    $objectives = array();
                    for ($i=0;$i<$rows;$i++) {
                        $objectives[] = "Objective ".($i+1);
                    }
                    $this->setVarByRef("objectives", $objectives);
                    // Build the cells matrix
                    $cells = array();
                    for ($i=0;$i<$rows;$i++) {
                        $cells[$i] = array();
                        for ($j=0;$j<$cols;$j++) {
                            $cells[$i][$j] = "";
                        }
                    }
                    $this->setVarByRef("cells", $cells);
                    $this->setVar('suppressModify', true);

                } else {
                    // Build the performances array
                    $performances = array();
                    for ($j=0;$j<$cols;$j++) {
                        $performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
                        $performances[] = $performance[0]['performance'];
                    }
                    $this->setVarByRef("performances", $performances);
                    // Build the objectives array
                    $objectives = array();
                    for ($i=0;$i<$rows;$i++) {
                        $objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
                        $objectives[] = $objective[0]['objective'];
                    }
                    $this->setVarByRef("objectives", $objectives);
                    // Build the cells matrix
                    $cells = array();
                    for ($i=0;$i<$rows;$i++) {
                        $cells[$i] = array();
                        for ($j=0;$j<$cols;$j++) {
                            $cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
                            $cells[$i][$j] = $cell[0]['contents'];
                        }
                    }
                    $this->setVarByRef("cells", $cells);
                }
		        return "edit_tpl.php";
			case "edittableconfirm":
				$tableId = $this->getParam("tableId", "");
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Update the performances
				$this->objDbRubricPerformances->deleteAll($tableId);
				for ($j=0;$j<$cols;$j++) {
					$this->objDbRubricPerformances->insertSingle(
						$tableId,
						"{$j}",
						$_POST["performance{$j}"]
					);
				}
				// Update the objectives
				$this->objDbRubricObjectives->deleteAll($tableId);
				for ($i=0;$i<$rows;$i++) {
					$this->objDbRubricObjectives->insertSingle(
						$tableId,
						"{$i}",
						$_POST["objective{$i}"]
					);
				}
				// Update the cells
				$this->objDbRubricCells->deleteAll($tableId);
				for ($i=0;$i<$rows;$i++) {
					for ($j=0;$j<$cols;$j++) {
						$this->objDbRubricCells->insertSingle(
							$tableId,
							"{$i}",
							"{$j}",
							$_POST["cell{$i}{$j}"]
						);
					}
				}

                return $this->nextAction('viewtable', array('tableId'=>$tableId));

				//return "view_tpl.php";
            case 'addrow':
                //$this->setLayoutTemplate(NULL);
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
                }
				for ($i=$rows;$i<($rows+1);$i++) {
					$objective = "Objective ".($i+1);
					$this->objDbRubricObjectives->insertSingle(
						$tableId,
						"{$i}",
						$objective
					);
                    $objectives[] = $objective;
				}
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				for ($i=$rows;$i<($rows+1);$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cells[$i][$j] = "";
						$this->objDbRubricCells->insertSingle(
							$tableId,
							"{$i}",
							"{$j}",
							""
						);
					}
				}
				$this->setVarByRef("cells", $cells);
                $rows++;
                $this->objDbRubricTables->updateRows($tableId, $rows);
		        return "edit_tpl.php";
            case 'addcol':
                //$this->setLayoutTemplate(NULL);
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
                }
				for ($j=$cols;$j<($cols+1);$j++) {
					$performance = "Performance ".($j+1);
					$this->objDbRubricPerformances->insertSingle(
						$tableId,
						"{$j}",
						$performance
					);
                    $performances[] = $performance;
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
                }
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
					for ($j=$cols;$j<($cols+1);$j++) {
						$cells[$i][$j] = "";
						$this->objDbRubricCells->insertSingle(
							$tableId,
							"{$i}",
							"{$j}",
							""
						);
					}
				}
				$this->setVarByRef("cells", $cells);
                $cols++;
                $this->objDbRubricTables->updateCols($tableId, $cols);
		        return "edit_tpl.php";
            case 'delrow':
                //$this->setLayoutTemplate(NULL);
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<($rows-1);$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
                }
				$this->objDbRubricObjectives->deleteSingle($tableId, $rows-1);
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<($rows-1);$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				for ($i=($rows-1);$i<$rows;$i++) {
					for ($j=0;$j<$cols;$j++) {
				        $this->objDbRubricCells->deleteSingle($tableId, $i, $j);
                    }
                }
				$this->setVarByRef("cells", $cells);
                $rows--;
                $this->objDbRubricTables->updateRows($tableId, $rows);
		        return "edit_tpl.php";
            case 'delcol':
                //$this->setLayoutTemplate(NULL);
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<($cols-1);$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
                }
				$this->objDbRubricPerformances->deleteSingle($tableId,$cols-1);
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
                }
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<($cols-1);$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				for ($i=0;$i<$rows;$i++) {
					for ($j=($cols-1);$j<$cols;$j++) {
				        $this->objDbRubricCells->deleteSingle($tableId, $i, $j);
                    }
                }
				$this->setVarByRef("cells", $cells);
                $cols--;
                $this->objDbRubricTables->updateCols($tableId, $cols);
		        return "edit_tpl.php";
            // Delete the rubric
			case "deletetable":
				$tableId = $this->getParam("tableId", "");				
				$this->objDbRubricTables->deleteSingle($tableId);
				$this->objDbRubricPerformances->deleteAll($tableId);
				$this->objDbRubricObjectives->deleteAll($tableId);
				$this->objDbRubricCells->deleteAll($tableId);
				$this->objDbRubricAssessments->deleteAll($tableId);
				return $this->nextAction(NULL);
			// View a rubric
			case "viewtable":
				$tableId = $this->getParam("tableId", "");
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
				}
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				$this->setVarByRef("cells", $cells);
				return "view_tpl.php";
			// View assessments
			case 'assessments':
				$tableId = $this->getParam('tableId', '');
				$this->setVarByRef('tableId', $tableId);
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
            //foreach($tableInfo1 as $tableInfo)
            //{
				//$title = $tableInfo['title'];
				$description = $tableInfo[0]['description'];
				//$description = $tableInfo['description'];
				$rows = $tableInfo[0]['rows'];
				//$rows = $tableInfo['rows'];
				$cols = $tableInfo[0]['cols'];
				//$cols = $tableInfo['cols'];
				//}

				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVar('maxtotal',$cols*$rows);
				$assessments = $this->objDbRubricAssessments->listAll($tableId);

				$this->setVarByRef("assessments", $assessments);
				// Do we want to show student names ?
				$showStudentNames = $this->getParam("showStudentNames", "yes");
				$this->setVarByRef("showStudentNames", $showStudentNames);
				return "assessments_tpl.php";
			// Add an assessment
			case 'addassessment':
				$tableId = $this->getParam('tableId', "");
				$this->setVarByRef("tableId", $tableId);
				// Get table information
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
				}
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				$this->setVarByRef("cells", $cells);
				// Get student # and student from URL
				$studentNo = $this->getParam("studentNo", "");
				$this->setVarByRef('studentNo', $studentNo);
				//$student = $this->getParam("student", "");
				if ($studentNo == "") {
				    $student = "";
				}
				else {
					$_userId = $this->objUser->getUserId($studentNo);
					if ($_userId == FALSE) {
					    $student = "not found";
					}
					else {
						$student = $this->objUser->fullname($_userId);
					}
				}
				$this->setVarByRef('student', $student);
				$this->setVar('mode','add');
				return "use_tpl.php";
			case "addassessmentconfirm":
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				// Get table information
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Get form information
				$teacher = $this->objUser->fullname();
				$this->setVarByRef("teacher", $teacher);
				$studentNo = $_POST['studentNo'];
				$this->setVarByRef("studentNo", $studentNo);
				//$student = $_POST['student'];
				if ($studentNo == "") {
				    $student = "";
				}
				else {
					$_userId = $this->objUser->getUserId($studentNo);
					if ($_userId == FALSE) {
					    $student = "";
					}
					else {
						$student = $this->objUser->fullname($_userId);
					}
				}
				$this->setVarByRef("student", $student);
				// Calculate score
				$scores = array();
				$total = 0;
				for ($i=0;$i<$rows;$i++) {
					if (isset($_POST["row{$i}"])) {
						$subtotal = $_POST["row{$i}"] + 1;
						$scores[$i] = $subtotal;
						$total += $subtotal;
					}
					else {
						$scores[$i] = 0;
					}

				}
				$this->setVarByRef("scores", $scores);
				$this->setVarByRef("total", $total);
				$this->setVar('maxtotal', $cols*$rows);
				$timestamp = strftime('%Y-%m-%d', mktime());
				// Add the assessment to the database
				$this->objDbRubricAssessments->insertSingle(
					$tableId,
					$teacher,
					$studentNo,
					$student,
					join(",",$scores),
					$timestamp
				);
				$date = $timestamp;
				$this->setVarByRef("date", $date);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
				}
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				$this->setVarByRef("cells", $cells);
				$this->setVar("IsAssessment", 1);
				return "view_tpl.php";
			// Add an assessment
			case "editassessment":
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				// Get table information
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Get ID
				$id = $this->getParam("id", "");
				$this->setVar('id',$id);
				// Get assessment information
				$assessment = $this->objDbRubricAssessments->listSingle($id);
				$teacher = $assessment[0]['teacher'];
				$this->setVarByRef("teacher", $teacher);
				$studentNo = $assessment[0]['studentno'];
				$this->setVarByRef("studentNo", $studentNo);
				$student = $assessment[0]['student'];
				$this->setVarByRef("student", $student);
				$date = $assessment[0]['timestamp'];
				$this->setVarByRef("date", $date);
				// Get the scores
				$scores = explode(",", $assessment[0]['scores']);
				$this->setVarByRef("scores", $scores);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
				}
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				$this->setVarByRef("cells", $cells);
				$this->setVar('mode','edit');
				return "use_tpl.php";
			case "editassessmentconfirm":
				$tableId = $this->getParam("tableId", "");
				$this->setVarByRef("tableId", $tableId);
				// Get table informtion
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Get form information
				$id = $this->getParam("id", "");
				$teacher = $this->objUser->fullname();
				$this->setVarByRef("teacher", $teacher);
				$studentNo = $_POST['studentNo'];

				$this->setVarByRef("studentNo", $studentNo);
				//$student = $_POST['student'];
				if ($studentNo == "") {
				    $student = "";
				}
				else {
					$_userId = $this->objUser->getUserId($studentNo);
					if ($_userId == FALSE) {
					    $student = "";
					}
					else {
						$student = $this->objUser->fullname($_userId);
					}
				}
				$this->setVarByRef("student", $student);
				// Calculate score
				$scores = array();
				$total = 0;
				for ($i=0;$i<$rows;$i++) {
					if (isset($_POST["row{$i}"])) {
						$subtotal = $_POST["row{$i}"] + 1;
						$scores[$i] = $subtotal;
						$total += $subtotal;
					}
					else {
						$scores[$i] = 0;
					}

				}
				$this->setVarByRef("scores", $scores);
				$this->setVarByRef("total", $total);
				$this->setVar('maxtotal', $cols*$rows);
				$timestamp = strftime('%Y-%m-%d %H:%M:%S', mktime());
				// Edit the assessment in the database
				$this->objDbRubricAssessments->updateSingle(
					$id,
					$tableId,
					$teacher,
					$studentNo,
					$student,
					join(",",$scores),
					$timestamp
				);
				$date = $timestamp;
				$this->setVarByRef("date", $date);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
				}
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				$this->setVarByRef("cells", $cells);
				$this->setVar("IsAssessment", 1);
				return "view_tpl.php";
			// Delete an assessment
			case 'deleteassessment':
				$id = $this->getParam("id", "");
				$this->objDbRubricAssessments->deleteSingle($id);
				// Redirect to assessments
				return $this->nextAction('assessments', array('tableId'=>$this->getParam("tableId", "")));
			// View an assessment
			case "viewassessment":
				$id = $this->getParam("id", "");
				$assessment = $this->objDbRubricAssessments->listSingle($id);
				$tableId = $assessment[0]['tableid'];
				$this->setVarByRef("tableId", $tableId);
				// Get table information
				$tableInfo = $this->objDbRubricTables->listSingle($tableId);
				$title = $tableInfo[0]['title'];
				$description = $tableInfo[0]['description'];
				$rows = $tableInfo[0]['rows'];
				$cols = $tableInfo[0]['cols'];
				$this->setVarByRef("title", $title);
				$this->setVarByRef("description", $description);
				$this->setVarByRef("rows", $rows);
				$this->setVarByRef("cols", $cols);
				// Get assessment information
				$teacher = $assessment[0]['teacher'];
				$this->setVarByRef("teacher", $teacher);
				$studentNo = $assessment[0]['studentno'];
				$this->setVarByRef("studentNo", $studentNo);
				// Check to see if user has tried to alter the URL manualy...
				if (!(
					$this->isValid('viewassessment')
					&& (
						$this->objUser->isContextLecturer($this->objUser->userId(), $this->contextCode)
						|| $this->objUser->isContextStudent($this->contextCode)
						&& $this->objUser->userName() == $studentNo
					)
				)) {
					return $this->nextAction('assessments',array('tableId'=>$this->getParam("tableId", "")));
				}
				$student = $assessment[0]['student'];
				$this->setVarByRef("student", $student);
				// Get the scores
				$scores = explode(",", $assessment[0]['scores']);
				$this->setVarByRef("scores", $scores);
				$total = 0;
				foreach ($scores as $score) {
					$total += $score;
				}
				$this->setVarByRef("total", $total);
				$this->setVar('maxtotal', $cols*$rows);
				$date = $assessment[0]['timestamp'];
				$this->setVarByRef("date", $date);
				// Build the performances array
				$performances = array();
				for ($j=0;$j<$cols;$j++) {
					$performance = $this->objDbRubricPerformances->listSingle($tableId, $j);
					$performances[] = $performance[0]['performance'];
				}
				$this->setVarByRef("performances", $performances);
				// Build the objectives array
				$objectives = array();
				for ($i=0;$i<$rows;$i++) {
					$objective = $this->objDbRubricObjectives->listSingle($tableId, $i);
					$objectives[] = $objective[0]['objective'];
				}
				$this->setVarByRef("objectives", $objectives);
				// Build the cells matrix
				$cells = array();
				for ($i=0;$i<$rows;$i++) {
					$cells[$i] = array();
					for ($j=0;$j<$cols;$j++) {
						$cell = $this->objDbRubricCells->listSingle($tableId, $i, $j);
						$cells[$i][$j] = $cell[0]['contents'];
					}
				}
				$this->setVarByRef("cells", $cells);
				$this->setVar("IsAssessment", 1);
				return "view_tpl.php";
			default:
		} // switch
		if ($action == "assign") {
		    $tableId = $this->getParam("tableId");
			$this->setSession('rubric',$tableId);
			//$_SESSION['rubric'] = $tableId;
		}
		$tables = $this->objDbRubricTables->listAll($this->contextCode, $this->contextCode == 'root' ? $this->objUser->userId() : NULL);
		$this->setVarByRef("tables", $tables);
		if ($this->contextCode != 'root') {
			$pdtables = $this->objDbRubricTables->listAll("root", $this->objUser->userId());
			$this->setVarByRef("pdtables", $pdtables);
		}
        return "main_tpl.php";
    }

    /**
     * Checks if the user has access to make modifications to the rubrics.
     *
     * @return boolean True if the user can make modifications, false otherwise.
     */
    protected function userHasModifyAccess()
    {
        $limitedUsers = $this->objSysConfig->getValue('mod_rubric_limited_users', 'rubric');
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
    protected function isRestricted($action)
    {
        $restrictedActions = array('createtable');
        return in_array($action, $restrictedActions);
    }
}
?>
