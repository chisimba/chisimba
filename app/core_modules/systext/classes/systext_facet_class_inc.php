<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}
/**
*
* @copyright (c) 2000-2005, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package systext
* @version 0.1
* @since 20 September 2005
* @author Kevin Cyster
*/

/**
* The systext facet class is responsible for processing and managing the
* text abstraction data and tables.
*
* @author Kevin Cyster
*/
class systext_facet extends dbTable
{
    /**
    * @var config _Config an object reference
    */
    var $_objConfig;

    /**
    * @var string $system_type The system type from the module's config parameter
    */
    var $system_type;

    /**
    * @var array $systemTypeList A list of all system types
    */
    var $systemTypeList;


    /**
    * @var array $abstractList A list of text and abstracts for the current
    * logged in user which is stored in the session variable.
    */
    var $abstractList;

    /**
    * @var systemType _SystemTypeDb an object reference.
    */
    var $_objSystemTypeDb;

    /**
    * @var textItem _TextItemDb an object reference.
    */
    var $_objTextItemDb;

    /**
    * @var abstractText _AbstractTextDb an object reference.
    */
    var $_objAbstractTextDb;

    /**
    * @var user _UserDb an object reference.
    */
    var $_objUserDb;

    /**
    * Method to initialize the systext facet object
    *
    * @access private
    */
    function init()
    {
    	parent::init('tbl_sysconfig_properties');
    	$this -> _objSystemTypeDb = $this -> getObject('dbsystem', 'systext');
        $this -> _objTextItemDb = $this -> getObject('dbtext', 'systext');
        $this -> _objAbstractTextDb = $this -> getObject('dbabstract', 'systext');

        $this -> _objConfig = $this -> getObject('altconfig', 'config');

        // The abstract list is persistent for this session.
        // Initialize the abstract list for this instance.
        if($this -> getSession('systext')){
            // The abstract list is available so fetch it from the session variable.
            $this -> fetchSession();
        }else{
            // This is the first instance of the systext facet ( normally at login after authentication )
            // The abstract list must be generated, ie. get it from the database tables.
            // The session permissions variable is initialized as well.
            $this -> updateSession();
        }
    }

    // -------------- tbl_systext_system methods -------------//
    /**
    * Method for adding a system type to the database.
    *
    * @param string $systemType The name of the system type
    * @param string $creatorId  The id of the user who created the system type
    */
    public function addSystemType($systemType, $creatorId)
    {
        return $this -> _objSystemTypeDb -> addRecord($systemType, $creatorId);
    }

    /**
    * Method for retrieving a system type
    *
    * @param string $id The id of the system type
    * @return array $data  The system type data
    */
    public function getSystemType($id)
    {
        return $this -> _objSystemTypeDb -> getRecord($id);
    }

    /**
    * Method for deleting a system type
    *
    * @param string $id  The system type to be deleted
    */
    public function deleteSystemType($id)
    {
        return $this -> _objSystemTypeDb -> deleteRecord($id);
    }

    /**
    * Method for listing all system types
    *
    * @return array $data  All system type data
    */
    public function listSystemTypes()
    {
        return $this -> _objSystemTypeDb -> listAllRecords();
    }

    /**
    * Method for editing a system type
    *
    * @param string $systemId  The id of the system type being edited
    * @param string $systemType  The system type
    */
    public function editSystemType($systemId, $systemType)
    {
        return $this -> _objSystemTypeDb -> editRecord($systemId, $systemType);
    }

    // -------------- tbl_systext_text methods -------------//
    /**
    * Method for adding a text to the database.
    *
    * @param string $text  The word to be abstracted eg. contex
    * @param string $creatorId  The id of the user who created the text entry
    */
    public function addTextItem($text, $creatorId)
    {
        return $this -> _objTextItemDb -> addRecord($text, $creatorId);
    }

    /**
    * Method for retrieving a text abstract
    *
    * @param string $id The id of the text
    * @return array $data  The text data
    */
    public function getTextItem($id)
    {
        return $this -> _objTextItemDb -> getRecord($id);
    }

    /**
    * Method for deleting text
    *
    * @param string $id  The text to be deleted
    */
    public function deleteTextItem($id)
    {
        return $this -> _objTextItemDb -> deleteRecord($id);
    }

    /**
    * Method for listing all text within a module
    *
    * @return array $data  All text data
    */
    public function listTextItems()
    {
        return $this -> _objTextItemDb -> listAllRecords();
    }

    /**
    * Method for editing a text item
    *
    * @param string $textId  The id of the text item being edited
    * @param string $text  The text
    */
    public function editTextItem($textId, $text)
    {
        return $this -> _objTextItemDb -> editRecord($textId, $text);
    }

    // -------------- tbl_systext_abstract methods -------------//
    /**
    * Method for adding a abstract element to the database.
    *
    * @param string $systemId The id of the system type the abstract is being added to
    * @param string $textId  The id of the text that is being abstracted
    * @param string $abstract  The abstracted word eg. course
    * @param string $creatorId  The id of the user who created the text entry
    * @param string $canDelete  Indicates whether item can be deleted
    */
    public function addAbstractText($systemId, $textId, $abstract, $creatorId, $canDelete = NULL)
    {
        return $this -> _objAbstractTextDb -> addRecord($systemId, $textId, $abstract, $creatorId, $canDelete = NULL);
    }

    /**
    * Method for retrieving a text abstract by cross reference
    *
    * @param string $syetemId The id of the system
    * @param string $textId The id of the text item
    * @return array $data  The text data
    */
    public function getAbstractText($systemId, $textId)
    {
        return $this -> _objAbstractTextDb -> getRecord($systemId, $textId);
    }

    /**
    * Method for retrieving a text abstract
    *
    * @param string $id The id of the abstract
    * @return array $data  The text data
    */
    public function getAbstractTextById($id)
    {
        return $this -> _objAbstractTextDb -> getRecordById($id);
    }

    /**
    * Method for deleting a text abstract
    *
    * @param string $id  The text to be deleted
    */
    public function deleteAbstractText($id)
    {
        return $this -> _objAbstractTextDb -> deleteRecord($id);
    }

    /**
    * Method for listing all text abstracts
    *
    * @return array $data  All text data
    */
    public function listAbstractText($systemId)
    {
        return $this -> _objAbstractTextDb -> listRecords($systemId);
    }

    /**
    * Method for editing an abstract
    *
    * @param string $id  The id of the abstract being edited
    * @param string $abstract The abstract
    * @param string $canDelete  Indicates whether item can be deleted
    */
    public function editAbstractText($id, $abstract, $canDelete = NULL)
    {
        return $this -> _objAbstractTextDb -> editRecord($id, $abstract, $canDelete = NULL);
    }

    /**
    * Method to access the abstracted text array
    *
    * @return nothing
    * @access public
    */
    public function updateSession()
    {
        // This list will be generated using the private function _getAbstractList
        $this -> abstractList = $this -> _getAbstractList();
                // Set the session variable
        $this -> setSession('systext', $this -> abstractList);
    }

    /**
    * Method to fetch the session variable containing abstracted text array
    *
    * @return nothing
    * @access public
    */
    public function fetchSession()
    {
        // The assumption is the list has already been made persistent( made available in the session variable) at initialization.
        $this -> abstractList = $this -> getSession('systext');
    }
    // --------------------- PRIVATE methods --------------------//
    /**
    * Private method to build the abstract list
    *
    * @return array the abstract list with words to be abstracted as indicies and
    * the abstracts as values
    * @access private
    */
    public function _getAbstractList()
    {
        $rs = $this->getRow('pname','SYSTEM_TYPE');
        $system_type = $rs['pvalue'];

        $systemTypeList = $this -> listSystemTypes();
        $textItemList = $this -> listTextItems();
        $abstractList = array();

        foreach($systemTypeList as $arrSystemType){
            if($arrSystemType['systemtype'] == $system_type){
                $systemTypeId = $arrSystemType['id'];
                $abstractTextList = $this -> listAbstractText($systemTypeId);
            }
        }
        if(isset($abstractTextList) && !empty($abstractTextList)){
            foreach($textItemList as $textItem){
                $notFound = TRUE;
                foreach($abstractTextList as $abstractText){
                    if($textItem['id'] == $abstractText['textid']){
                        $abstractList[$textItem['textinfo']] = $abstractText['abstract'];
                        $notFound = FALSE;
                    }
                }
                if($notFound){
                    $abstractItem = $this -> getAbstractText('init_1', $textItem['id']);
                    if(!empty($abstractItem)){
                        $abstractList[$textItem['textinfo']] = $abstractItem[0]['abstract'];
                    }

                }
            }
        }
        ksort($abstractList);

        return $abstractList;
    }
}

?>