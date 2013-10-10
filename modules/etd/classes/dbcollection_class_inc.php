<?php
/**
* dbcollection class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbcollection class for managing the data in the tbl_etd_collections table.
* @author Megan Watson
* @author Jonathan Abrahams
* @copyright (c) 2004 UWC
* @version 0.1
*/

class dbcollection extends dbtable
{
    /**
    * @var string Property to store the first column field used by the browse object
    */
    public $col1Field = '';

    /**
    * @var string Property to store the second column field used by the browse object
    */
    public $col2Field = '';

    /**
    * @var string Property to store the first column table heading used by the browse object
    */
    public $col1Header = '';

    /**
    * @var string Property to store the first column table heading used by the browse object
    */
    public $col2Header = '';

    /**
    * @var string Property to store the search title used by the browse object
    */
    public $type = '';

    /**
    * @var string Property to store the object type used by the browse object
    */
    public $_browseType = '';

    /**
    * @var string $subType The type of submission - etd/other.
    * Used to distiguish the source of the document in the database table.
    */
    public $subType = 'etd';

    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_etd_collections');
        $this->table = 'tbl_etd_collections';
        $this->bridgeTable = 'tbl_etd_collection_submission';

        $this->dbBridge = $this->getObject('dbcollectsubmit', 'etd');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('link', 'htmlelements');

        // Type is a Collection
        $this->type = $this->objLanguage->languageText( 'mod_etd_typeCollection', 'Collections' );

        // Set object property
        $this->_browseType = 'collection';
    }

    /**
    * Method to set the type of submission - etd/other.
    * Used to distiguish the source of the document in the database table.
    * @param string $type The of submission.
    */
    public function setSubmitType($type)
    {
        $this->subType = $type;
    }

    /**
    * Method to get a Collection.
    * @param string $id The id of the collection.
    */
    public function getCollection($id)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE id = '$id'";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to get a set of collections based on a given set of id's.
    * @param array $idSet The set of id's.
    */
    public function getCollectionSet($idSet)
    {
        $isSql = '';
        if(!empty($idSet)){
            foreach($idSet as $item){
                if(!empty($isSql)){
                    $isSql .= ' ||';
                }
                $isSql .= " id = '$item'";
            }
            $sql = 'SELECT name FROM '.$this->table;
            $sql .= " WHERE $isSql";

            $data = $this->getArray($sql);
            if(!empty($data)){
                return $data;
            }
        }
        return FALSE;
    }

    /**
    * Method to get a list of Collections.
    */
    public function getAllCollections()
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE submissionType = '{$this->subType}'";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to check if a collection exists and create it if it doesn't.
    *
    * @access public
    * @param string $name The name of the collection
    * @param string $userId The id of the user checking the collection.
    * @return string $id The id of the existing / created collection
    */
    public function checkCollection($name)
    {
        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE name = '$name' && submissionType = '{$this->subType}'";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0]['id'];
        }
        return FALSE;
    }
    
    /**
    * Method to add a new collection.
    *
    * @access private
    * @param string $name The name of the collection
    * @param string $userId The id of the user creating the collection.
    * @return string $id The id of the created collection
    */
    public function addCollection($name, $userId)
    {
        $fields = array();
        $fields['name'] = $name;
        $fields['submissionType'] = $this->subType;
        $fields['creatorId'] = $userId;
        $fields['dateCreated'] = date('Y-m-d H:i:s');

        $id = $this->insert($fields);
        return $id;
    }

    /**
    * Method to save a collection to the table.
    * @param string $userId The id of the user creating the collection.
    * @param string $id The id of the collection if it exists.
    */
    public function saveCollection($userId, $name, $subject, $id = NULL)
    {
        $fields = array();
        $fields['name'] = $name;
        $fields['subject'] = $subject;
        $fields['updated'] = date('Y-m-d H:i:s');

        if($id){
            $this->update('id', $id, $fields);
        }else{
            $fields['submissiontype'] = $this->subType;
            $fields['creatorid'] = $userId;
            $fields['datecreated'] = date('Y-m-d H:i:s');

            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
    * Method to delete a collection if it is empty.
    * @param string $id The id of the collection to delete.
    */
    public function deleteCollection($id)
    {
        if(!$this->dbBridge->isEmpty($id)){
            $this->delete('id', $id);
            return TRUE;
        }
        return FALSE;
    }

    /**
    * Method to pass a dbcollection object to the browse class.
    */
    public function createBrowse()
    {
        $count = 0;
        do {
            $name = 'x'.$count;
            $subject = 'y'.$count;
            $this->insert( array(
                'name' => $name,
                'subject'=> $subject
                ) );
        } while ( $count++ < 100 );

    }

    /**
    * Method to get collections limited to a given number of results.
    * @param string $limit The number of collections to return. Default = 10 results.
    * @param string $start The collection for the result set to start from. Default = Null, return from the start.
    */
    public function getData($limit = NULL, $start = 0)
    {
        $sql = "SELECT count(*) as cnt, col.name as col1, col.subject as col2, col.id FROM {$this->table} AS col ";
        $join = "LEFT JOIN {$this->bridgeTable} AS bridge ON col.id = bridge.collectionId ";
        $filter = " WHERE submissionType = '{$this->subType}' ";
        $orderBy = "GROUP BY bridge.collectionId ORDER by LOWER(col.name)";
        $sqlLimit = $limit ? " LIMIT $limit OFFSET $start" : NULL;
        $results = $this->getArray( $sql.$join.$filter.$orderBy.$sqlLimit );

        // Get bounds
        $sqlFound = "SELECT COUNT(*) as found FROM ".$this->table;
        $row = $this->getArray( $sqlFound.$filter );
        $this->recordsFound = $row[0]['found'];
        return $results;
    }

    /**
    * Method to get collections by letter.
    * @param string $letter The letter to display. Default = a.
    * @param string $limit The number of collections to return. Default = 10 results.
    * @param string $start The collection for the result set to start from. Default = Null, return from the start.
    */
    public function getByLetter($letter = 'a', $limit = NULL, $start = 0)
    {
        $sql = "SELECT count(*) as cnt, col.name as col1, col.subject as col2, col.id FROM {$this->table} AS col ";
        $join = "LEFT JOIN {$this->bridgeTable} AS bridge ON col.id = bridge.collectionId ";
        $filter = " WHERE submissionType = '{$this->subType}' AND (LOWER(name) LIKE '$letter%' ";

        if(strtolower($letter) == 'a'){
            $filter .= "AND NOT (name LIKE 'a %' OR name LIKE 'an %')";
        }
        if(strtolower($letter) == 't'){
            $filter .= "AND NOT (name LIKE 'the %')";
        }
        $filter .= " OR name LIKE 'the $letter%' ";
        $filter .= " OR name LIKE 'a $letter%' ";
        $filter .= " OR name LIKE 'an $letter%' )";

        $orderBy = " GROUP BY bridge.collectionId ORDER by LOWER(col.name)";
        $sqlLimit = $limit ? " LIMIT $limit OFFSET $start" : NULL;
        $results = $this->getArray( $sql.$join.$filter.$orderBy.$sqlLimit );

        // Get bounds
        $sqlFound = "SELECT COUNT(*) as found FROM ".$this->table;
        $row = $this->getArray( $sqlFound.$filter );
        $this->recordsFound = $row[0]['found'];
        return $results;
    }

    /**
    * Method to get collections using search data.
    * @param string $search The search data to use.
    * @param string $limit The number of collections to return. Default = 10 results.
    * @param string $start The collection for the result set to start from. Default = Null, return from the start.
    */
    public function getBySearch($search, $limit = NULL, $start = 0)
    {
        $sql = "SELECT count(*) as cnt, col.name as col1, col.subject as col2, col.id FROM {$this->table} AS col ";
        $join = "LEFT JOIN {$this->bridgeTable} AS bridge ON col.id = bridge.collectionId ";
        $filter = " WHERE submissionType = '{$this->subType}' AND (LOWER(name) LIKE '%$search%' )";
        
        $orderBy = " GROUP BY bridge.collectionId ORDER by LOWER(col.name)";
        $sqlLimit = $limit ? " LIMIT $limit OFFSET $start" : NULL;
        $results = $this->getArray( $sql.$join.$filter.$orderBy.$sqlLimit );

        // Get bounds
        $sqlFound = "SELECT COUNT(*) as found FROM ".$this->table;
        $row = $this->getArray( $sqlFound.$filter );
        $this->recordsFound = $row[0]['found'];
        return $results;
    }

    /**
    * Method to get the headings to use when displaying the search results.
    */
    public function getHeading()
    {
        $col1 = $this->objLanguage->languageText('mod_etd_collection');
        $col2 = $this->objLanguage->languageText('mod_etd_subject');

        return array('col1' => $col1, 'col2' => $col2);
    }

    /**
    * Method to display a collection.
    * @param string $id The id of the collection to display.
    * @param string $backUri The uri for the back link.
    */
    public function displayItem($id, $backUri)
    {
        $back = $this->objLanguage->languageText('word_back');

        $str = '';

        $objLink = new link($backUri);
        $objLink->link = $back;
        $str .= $objLink->show();
        return $str;
    }
}
?>