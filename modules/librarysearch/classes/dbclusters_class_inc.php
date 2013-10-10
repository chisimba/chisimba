<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Data access class for the library search module.
*
* @package librarysearch
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert <charl.mert@gmail.com>
*/

    class dbclusters extends dbTable
    {

        /**
        * The language object
        *
        * @access private
        * @var object
        */
        protected $_objLanguage;

        /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                parent::init('tbl_librarysearch_clusters');
                $this->table = 'tbl_librarysearch_clusters';
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->objConf = & $this->newObject('altconfig', 'config');
                $this->objSource = & $this->getObject('dbsources', 'librarysearch');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
         * Method to retrieve a cluster
         *
         * @param string clusterTitle OR string $cluster_id The id of the source entry to retrieve
         * @return array Returns the record or FALSE
         * @access public
         */
        public function getCluster($clusterKey)
        {
            $source = $this->getAll("WHERE id = '$clusterKey'");
            $source = $source[0];
            if (empty($source)) {
                //None found attempting to search against text title
                $source = $this->getAll("WHERE title = '$clusterKey'");
                $source = $source[0];
                if (empty($source)) {
                    return FALSE;
                }
            }
            return $source;
        }


        /**
         * Method to retrieve the default clusters
         *
         * @return array Returns the record or FALSE
         * @access public
         */
        public function getDefaultClusters()
        {
            $cluster = $this->getAll("WHERE id = 'init_1' OR id = 'init_2' or id = 'init_3'");
            if (empty($cluster)) {
                return FALSE;
            }
            return $cluster;
        }


        /**
         * Method to retrieve all the clusters
         *
         * @return array Returns the records or FALSE
         * @access public
         */
        public function getClusters()
        {
            $source = $this->getAll();
            if (empty($source)) {
                return FALSE;
            }
            return $source;
        }


        /**
         * Method to retrieve the target datasource URI
         *
         * @param string $source_id The id of the source entry to retrieve
         * @return array Returns the record or FALSE
         * @access public
         */
        public function getClusterSources($clusterId)
        {
            $sources = $this->objSource->getAll("WHERE cluster_id = '$clusterId'");
            
            return $sources;
        }



    }

?>
