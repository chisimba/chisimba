<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_mcqtests extends object
{

    public function init()
    {
        $this->loadClass('treenode','tree');
        $this->objTests =& $this->newObject('dbtestadmin', 'mcqtests');
    }

    public function show()
    {
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'mcqtests'), 'text'=>'MCQ Tests', 'preview'=>'sffas'));

        $nodesArray = array();

        $tests = $this->objTests->getAllTests();

        foreach ($tests as $test)
        {
            $node =& new treenode(array('link'=>$this->uri(array('action'=>'view', 'id'=>$test['id'])), 'text'=>$test['name']));

            $nodesArray['mcqtests_'.$test['id']] =& $node;
            $rootNode->addItem($nodesArray['mcqtests_'.$test['id']]);
        }
        return $rootNode;
    }

    /**
     *
     *Method to get a set of links for a context
     *@param string $contextCode
     *@return array
     * @access public
     */
    public function getContextLinks($contextCode)
    {
        $bigArr = array();

        $tests = $this->objTests->getTests($contextCode);

		if($tests)
         {
	        foreach ($tests as $test)
	        {
	
	            $testArray = array();
	            $testArray['menutext'] = $test['name'];
	            $testArray['description'] = $test['description'];
	            $testArray['itemid'] = $test['id'];
	            $testArray['moduleid'] = 'mcqtests';
	            $testArray['params'] = array('action' => 'view','id' => $test['id']);
	            $bigArr[] = $testArray;
	        }
	        return $bigArr;
	    } else {
			return FALSE;
		}
    }
}
?>