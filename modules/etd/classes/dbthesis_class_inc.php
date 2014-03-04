<?php
/**
* dbThesis class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbThesis class for managing the data in the tbl_etd_metadata_thesis table.
* Class provides functionality for browsing metadata via the viewbrowse class.
*
* @author Megan Watson
* @author Jonathan Abrahams
* @copyright (c) 2005 UWC
* @version 0.2
* @modified by Megan Watson on 2006 11 04 ported to 5ive/chisimba
*/

class dbThesis extends dbtable
{
    /**
    * @var string Property to store the first column field used by the browse object
    */
    protected $col1Field = '';

    /**
    * @var string Property to store the second column field used by the browse object
    */
    protected $col2Field = '';

    /**
    * @var string Property to store the third column field used by the browse object
    */
    protected $col3Field = '';

    /**
    * @var string Property to store the first column table heading used by the browse object
    */
    protected $col1Header = '';

    /**
    * @var string Property to store the second column table heading used by the browse object
    */
    protected $col2Header = '';

    /**
    * @var string Property to store the third column table heading used by the browse object
    */
    protected $col3Header = '';

    /**
    * @var string Property to store the search title used by the browse object
    */
    public $type = '';

    /**
    * @var string Property to store the object type used by the browse object
    */
    public $_browseType = '';

    /**
    * @var string Property to set the submission type - distinguish between etd's and other documents.
    */
    protected $subType = 'etd';

    /**
    * @var string Property to show the number of records returned in a result set
    */
    public $recordsFound = 0;

    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_etd_metadata_thesis');
        $this->table = 'tbl_etd_metadata_thesis';
        $this->dcTable = 'tbl_dublincoremetadata';
        $this->submitTable = 'tbl_etd_submissions';
        $this->bridgeTable = 'tbl_etd_collection_submission';

        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objUser =& $this->getObject('user', 'security');

        // Default browse type
        $this->setBrowseType( 'author' );
    }

    /**
    * Method to set the type of submission - etd/other.
    * Used to distiguish the source of the document in the database table.
    *
    * @access public
    * @param string $type The submission type
    * @return
    */
    public function setSubmitType($type)
    {
        $this->subType = $type;
    }

    /**
    * Method to insert thesis metadata
    *
    * @access public
    * @param array $fields The fields and values to add / update
    * @param string $id The table row to update if exists
    * @return string $id
    */
    public function insertMetadata($fields, $id = NULL)
    {
        if($id){
            $fields['modifierid'] = $this->objUser->userId();
            $fields['datemodified'] = $this->now();
            $fields['updated'] = $this->now();
            $this->update('id', $id, $fields);
        }else{
            $fields['creatorid'] = $this->objUser->userId();
            $fields['datecreated'] = $this->now();
            $fields['updated'] = $this->now();
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
    * Method to delete thesis metadata
    *
    * @access public
    * @param string $id The table row to delete
    * @return
    */
    public function deleteMetadata($id)
    {
        $this->delete('id', $id);
    }

    /**
    * Method to replace a set of elements where the item has changed
    * eg The name of a degree has been changed and needs to be updated
    *
    * @access public
    * @param string $search The field / column to search in
    * @param string $searchTerm The item to search on (or be replaced)
    * @param array $fields The field name and replacement item
    * @return void
    */
    public function replaceElement($search, $searchTerm, $fields)
    {
        $this->update($search, $searchTerm, $fields);
    }

    /**
    * Method to get resource title
    *
    * @access public
    * @param string $id The table row to fetch
    * @return array The resource
    */
    public function getTitle($submitid)
    {
        $sql = "SELECT dc.id AS dcId, thesis.id AS thesisId, thesis.*, dc.* ";
        $sql .= "FROM {$this->table} AS thesis, {$this->dcTable} AS dc ";
        $sql .= "WHERE dc.id = thesis.dcMetaId AND thesis.id = '$id'";

        $data = $this->getArray($sql);
        return $data[0];
    }

    /**
    * Method to get resource metadata
    *
    * @access public
    * @param string $id The table row to fetch
    * @return array The resource
    */
    public function getMetadata($id)
    {
        $this->embTable = 'tbl_etd_embargos';

        $sql = "SELECT dc.id AS dcId, thesis.id AS thesisId, thesis.*, dc.*, ";

            $sql .= "(SELECT periodend FROM {$this->embTable} em, {$this->table} th
                    WHERE submissionid = th.submitid AND th.id = '$id') AS embargo ";


        $sql .= "FROM {$this->table} AS thesis, {$this->dcTable} AS dc ";
        $sql .= "WHERE dc.id = thesis.dcMetaId AND thesis.id = '$id'";

        $data = $this->getArray($sql);

        return $data[0];
    }

    /**
    * Method to get metadata records limited to a given number of results.
    *
    * @access public
    * @param string $limit The number of metadata records to return. Default = 10 results.
    * @param string $start The metadata record for the result set to start from. Default = Null, return from the start.
    * @return array Metadata result set
    */
    public function getData($limit = 10, $start = NULL, $joinId = NULL)
    {
        $sqlNorm = "SELECT dc.{$this->col1Field} as col1, dc.{$this->col2Field} as col2, dc.{$this->col3Field} as col3, thesis.id as id, ";

        // Remove "A " and "The " and "'n " and " for sorting
        $sqlNorm .= " REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$this->col1Field}), 'a ', ''), 'the ', ''), '\'n ', ''), '\"', '') as sort ";


        $sqlFound = "SELECT COUNT(*) AS count ";

        $sql = "FROM {$this->table} AS thesis, {$this->submitTable} AS submit, {$this->dcTable} AS dc ";

        $sql .= "WHERE submit.id = thesis.submitid AND dc.id = thesis.dcmetaid ";
        $sql .= "AND submit.submissiontype = '{$this->subType}' AND submit.status = 'archived' ";

        $sqlLimit = "ORDER BY sort "; //{$this->col1Field}) ";

        $sqlLimit .= $limit ? "LIMIT $limit " : NULL;
        $sqlLimit .= $start ? "OFFSET $start " : NULL;

        /* End testing */

        // Get result set

        $data = $this->getArray($sqlNorm.$sql.$sqlLimit);

        //echo '<pre>'; print_r($data);

        // Get number of results
        $data2 = $this->getArray($sqlFound.$sql);
        if(!empty($data2)){
            $this->recordsFound = $data2[0]['count'];
        }

        return $data;
    }

    /**
    * Method to get metadata records by letter.
    *
    * @access public
    * @param string $letter The letter to display. Default = a.
    * @param string $limit The number of metadata records to return. Default = 10 results.
    * @param string $start The metadata record for the result set to start from. Default = Null, return from the start.
    * @return array Metadata result set
    */
    public function getByLetter($letter = 'a', $limit = 10, $start = NULL, $joinId = NULL)
    {
        $letter = strtolower($letter);
        $sqlNorm = "SELECT dc.{$this->col1Field} as col1, dc.{$this->col2Field} as col2, dc.{$this->col3Field} as col3, thesis.id as id, ";

        // Remove "A " and "The " and "'n " and " for sorting
        $sqlNorm .= " REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$this->col1Field}), 'a ', ''), 'the ', ''), '\'n ', ''), '\"', '') as sort ";

        $sqlFound = "SELECT COUNT(*) AS count ";

        $sql = "FROM {$this->table} AS thesis, {$this->submitTable} AS submit, {$this->dcTable} AS dc ";

        $sql .= "WHERE submit.id = thesis.submitid AND dc.id = thesis.dcmetaid ";
        $sql .= "AND submit.submissiontype = '{$this->subType}' AND submit.status = 'archived' ";
        $sql .= "AND ( LOWER({$this->col1Field}) LIKE '$letter%' ";

        if(strtolower($letter) == 'a'){
            $sql .= "AND NOT ( LOWER({$this->col1Field}) LIKE 'a %' OR LOWER({$this->col1Field}) LIKE 'an %') ";
        }
        if(strtolower($letter) == 't'){
            $sql .= "AND NOT ( LOWER({$this->col1Field}) LIKE 'the %') ";
        }

        $sql .= " OR LOWER({$this->col1Field}) LIKE 'the $letter%' ";
        $sql .= " OR LOWER({$this->col1Field}) LIKE 'a $letter%' ";
        $sql .= " OR LOWER({$this->col1Field}) LIKE 'an $letter%' ";
        $sql .= " OR LOWER({$this->col1Field}) LIKE '\'n $letter%' ";
        $sql .= " OR LOWER({$this->col1Field}) LIKE '\"$letter%' ) ";

        $sqlLimit = "ORDER BY sort "; //LOWER({$this->col1Field}) ";

        $sqlLimit .= $limit ? "LIMIT $limit " : NULL;
        $sqlLimit .= $start ? "OFFSET $start " : NULL;

        // Get result set
        $data = $this->getArray($sqlNorm.$sql.$sqlLimit);

        // Get number of results
        $data2 = $this->getArray($sqlFound.$sql);
        if(!empty($data2)){
            $this->recordsFound = $data2[0]['count'];
        }

        return $data;
    }

    /**
    * Method to set the type of browse mode, and set the fields to be used for the search
    *
    * @access public
    * @param string $type The type of object to be used by the browse class
    */
    public function setBrowseType( $type = NULL )
    {

        switch ( $type ) {
            case 'author':
                $this->col1Field = 'dc_creator';
                $this->col2Field = 'dc_title';
                $this->col3Field = 'dc_date';
                $this->col1Header = $this->objLanguage->languageText('word_author');
                $this->col2Header = $this->objLanguage->languageText('word_title');
                $this->col3Header = $this->objLanguage->languageText('word_year');
                $this->type = $this->objLanguage->languageText('word_authors');
                break;
            case 'title':
                $this->col1Field = 'dc_title';
                $this->col2Field = 'dc_creator';
                $this->col3Field = 'dc_date';
                $this->col1Header = $this->objLanguage->languageText('word_title');
                $this->col2Header = $this->objLanguage->languageText('word_author');
                $this->col3Header = $this->objLanguage->languageText('word_year');
                $this->type = $this->objLanguage->languageText('word_titles');
                break;
            case 'faculty':
                $this->col1Field = 'thesis_degree_discipline';
                $this->col2Field = 'dc_creator';
                $this->col3Field = 'dc_date';
                $this->col1Header = $this->objLanguage->languageText('word_faculty');
                $this->col2Header = $this->objLanguage->languageText('word_author');
                $this->col3Header = $this->objLanguage->languageText('word_year');
                $this->type = $this->objLanguage->languageText('word_titles');
                break;
            default :
                $type = 'author';
                $this->col1Field = 'dc_creator';
                $this->col2Field = 'dc_title';
                $this->col1Header = $this->objLanguage->languageText('word_author');
                $this->col2Header = $this->objLanguage->languageText('word_title');
                $this->col3Header = '';
                $this->type = $this->objLanguage->languageText('word_authors');
        }
        // Set object property
        $this->_browseType = $type;
    }

    /**
    * Method to get the headings to use when displaying the search results.
    *
    * @access public
    * @return array Column headings
    */
    public function getHeading()
    {
        return array('col1' => $this->col1Header, 'col2' => $this->col2Header, 'col3' => $this->col3Header);
    }

    /**
    * Method to execute a search using a given filter - used by the simple and advanced searches.
    *
    * @access public
    * @param string $filter The search criteria.
    * @param string $limit The limit on the results returned.
    * @return array The result set and count
    */
    function search($filter, $limit = NULL, $start = 0)
    {
        $sqlNorm = 'SELECT thesis.id AS id, dc.dc_creator AS col2, dc.dc_title AS col1, dc.dc_date AS col3 ';

        $sqlCount = 'SELECT COUNT(*) AS count ';

        $sql = "FROM {$this->table} AS thesis, {$this->dcTable} AS dc, {$this->submitTable} AS submit ";
        $sql .= "WHERE thesis.dcmetaid = dc.id AND thesis.submitid = submit.id ";
        $sql .= "AND submit.submissiontype = '{$this->subType}' AND ({$filter}) ";

        $sqlLimit = "ORDER BY dc_date DESC ";
        $sqlLimit .= $limit ? " LIMIT $limit OFFSET $start" : '';

        $data = $this->getArray($sqlNorm.$sql.$sqlLimit);

        $count = 0;
        $data2 = $this->getArray($sqlCount.$sql);
        if(!empty($data2)){
            $count = $data2[0]['count'];
        }

        return array($data, $count);
    }

    /**
    * Method to get a list of submissions.
    *
    * @access public
    * @return array Submissions list
    */
    public function getAllMeta()
    {
        $sql = 'SELECT dc_title, dc_creator, dc_subject, dc_identifier, thesis.id AS metaid ';
        $sql .= "FROM {$this->table} AS thesis, {$this->dcTable} AS dc ";
        $sql .= "WHERE dc.id = thesis.dcmetaid ";
        $sql .= "ORDER BY dc.enterdate DESC LIMIT 20";
        $data = $this->getArray($sql);

        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get the metadata for a given set of resources.
    *
    * @access public
    * @param array $list The list of resource id's
    * @return array The resources metadata
    */
    public function getFromList($list)
    {
        $sql = "SELECT dc_title, dc_creator, dc_subject, dc_identifier, thesis.id AS metaid,
            REPLACE(REPLACE(REPLACE(REPLACE(LOWER(dc_title), 'a ', ''), 'the ', ''), '\'n ', ''), '\"', '') as sort
            FROM {$this->table} AS thesis, {$this->dcTable} AS dc
            WHERE dc.id = thesis.dcmetaid ";

        // Add the list of id's to the sql
        if(!empty($list)){
            $listSql = '';
            foreach($list as $id){
                $listSql .= !empty($listSql) ? 'OR ' : '';
                $listSql .= "thesis.id = '{$id}' ";
            }
            $sql .= "AND ({$listSql}) ";
        }

        $sql .= 'ORDER BY sort';

        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Method to execute a search using a given filter - used by external searches.
    *
    * @access public
    * @param string $filter The search criteria.
    * @param string $limit The limit on the results returned.
    * @return array The result set and count
    */
    public function search2($keyword)
    {
        $sqlNorm = 'SELECT thesis.id AS id, dc.*, thesis.* ';
        $sqlCount = 'SELECT count(*) AS cnt ';

        $term = strtolower($keyword);

        $sql = "FROM {$this->table} AS thesis, {$this->dcTable} AS dc, {$this->submitTable} AS submit
                WHERE thesis.dcmetaid = dc.id AND thesis.submitid = submit.id
                AND submit.submissiontype = '{$this->subType}'
                AND ";
        $filter = "(LOWER(dc.dc_creator) LIKE '%$term%' OR LOWER(dc.dc_title) LIKE '%$term%'
                OR LOWER(dc.dc_subject) LIKE '%$term%') ";

        $sqlOrder = "ORDER BY dc.dc_date ";

//        echo $sqlNorm.$sql.$sqlOrder;

        $data = $this->getArray($sqlNorm.$sql.$filter.$sqlOrder);
        $data2 = $this->getArray($sqlCount.$sql.$filter);
        $count = isset($data2[0]['cnt']) ? $data2[0]['cnt'] : 0;

        $this->setSession('sql', $filter);
        return array($data, $count);
    }
}
?>