<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
* Class to access the ContextCore Tables
* @package cms
* @category cmsadmin
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version
* @author Wesley  Nitsckie
* @example :
*/

class dbcategories extends dbTable
{

        /**
        * Constructor
        */
        public function init()
        {
            parent::init('tbl_cms_categories');
        }

        /**
         * Methode to get the list of categories
         * @access public
         * @return array
         */
        public function getCategories()
        {

            return $this->getAll();
        }

        /**
         * Method to get the number of categories for a certain section
         * @param string $sectionId The id of the category
         * @access public
         * @return int
         */
        public  function getCatCount($sectionId = NULL)
        {
            if($sectionId == NULL) {
                return $this->getRecordCount();
            } else {
                return $this->getRecordCount('WHERE sectionid = \''. $sectionId.'\'');
            }

        }


        /**
         * Method to add a section to the database
         * @access public
         * @return bool
         */
        public function add
            ()
        {
            //get param from dropdown
            $parentSelected = $this->getParam('parent');
            //get type(section or category) and its id
            $matches = split(':', $parentSelected);
            $type = trim($matches['0']);
            $id = trim($matches['1']);
            if($type == 'category') {
                $sectionId = $this->getSectionIdOfCat($id);
            }
            try {
                $title = $this->getParam('title');
                $menuText = $this->getParam('menutext');
                if($type == 'category') {
                    $section = $sectionId;
                    $parent = $id;
                } else {
                    $section = $id;
                    $parent = 'section';
                }
                $image = $this->getParam('image');
                $imagePostion = $this->getParam('imagepostion');
                $access = $this->getParam('access');
                $desciption = $this->getParam('description');
                $published = $this->getParam('published');
                if($type == 'category') {
                    $count = $this->getCatLevel($parent) + '1';
                } else {
                    $count = '1';
                }

                return $this->insert(array(
                                         'title' => $title,
                                         'menutext' => $menuText,
                                         'sectionid' => $section,
                                         'parent_id' => $parent,
                                         'image' => $image,
                                         'image_position' => $imagePostion,
                                         'access' => $access,
                                         'ordering' => 0,
                                         'count' => $count,
                                         'description' => $desciption,
                                         'published' => $published
                                     ));

            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage();
                exit();
            }
        }

        /**
         * Method to add a section to the database
         * @access public
         * @return bool
         */

        public function edit()
        {
            try {

                $id = $this->getParam('id');
                $section = $this->getParam('section');
                $title = $this->getParam('title');
                $menuText = $this->getParam('menutext');
                $image = $this->getParam('image');
                $imagePostion = $this->getParam('imagepostion');
                $access = $this->getParam('access');
                $desciption = $this->getParam('description');
                $published = $this->getParam('published');
                $ordering = $this->getParam('ordering');
                $arrFields = array(
                                 'title' => $title,
                                 'menutext' => $menuText,
                                 'sectionid' => $section,
                                 'image' => $image,
                                 'image_position' => $imagePostion,
                                 'access' => $access,
                                 'ordering' => $ordering,
                                 'description' => $desciption,
                                 'published' => $published);
                return $this->update('id', $id, $arrFields);

            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage();
                exit();
            }
        }
        /**
         * Method to get the menutext for a section
         * @return string
         * @access public
         * @param string id 
         */

        public function getMenuText($id)
        {
            $line = $this->getCategory($id);

            return $line['menutext'];
        }

        /**
         * Method to get a Section
         * @var string id The section id
         * @return array
         * @access public
         */
        public function getCategory($id)
        {

            return $this->getRow('id', $id);


        }
        /**
         * Method to get all categories in a specific section
         *
         * @param string $sectionId The id(pk) of the section
         * @return array $categoriesInSection An array of associative arrays for all categories in the section
         * @access public
          */
        public function getCategoryInSection($sectionId, $level = NULL)
        {
            if(isset($level)) {
                return $this->getAll('WHERE sectionid = \''.$sectionId.'\' AND count = \''.$level.'\'');
            } else {
                return $this->getAll('WHERE sectionid = \''.$sectionId.'\'');
            }
        }
        /**
         * Method to return the count value of a category
         *
         * @param string $id The id(pk) of the category
         * @return array $categoriesInSection An array of associative arrays for all categories in the section
         * @access public
          */
        public function getCatLevel($id)
        {
            //get entry
            $cat = $this->getRow('id', $id);
            //get and return value of count field
            $count = $cat['count'];
            return $count;
        }
        /**
         * Method to return the sectionId of a category
         *
         * @param string $id The id(pk) of the category
         * @return string $sectionId The id(pk) of the categories section
         * @access public
          */
        public function getSectionIdOfCat($id)
        {
            //get entry
            $cat = $this->getRow('id', $id);
            //get and return sectionId
            $sectionId = $cat['sectionid'];
            return $sectionId;
        }
        /**
         * Method to delete a category
         *
         * @return NULL
         * @access public
          */
        public function deleteCat($id)
        {
            //if cat has nodes delete nodes as well
            if($this->hasNodes($id)) {
                //get cat details
                $category = $this->getCategory($id);
                //get number of levels in section
                $this->objCmsUtils =& $this->newObject('cmsutils', 'cmsadmin');
                $numLevels = $this->objCmsUtils->getNumNodeLevels($category['sectionid']);
                $parentId = $id;
                $nodeIdArray = array();
                $level = $category['count'] + '1';
                //get an array of all the cats nodes
                for($i = $level; $i <= $numLevels; $i++) {
                    $nodes = $this->getAll('WHERE parent_id = \''.$parentId.'" AND count = \''.$i.'"');
                    foreach($nodes as $node) {
                        $nodeIdArray[] = $node['id'];
                    }
                }
                //delete each node in array
                foreach($nodeIdArray as $data) {
                    $this->delete('id', $data);
                }
                //delete original category
                $this->delete('id', $id);
            } else {
                $this->delete('id', $id);
            }
        }
        /**
         * Method to check if a category has child/leaf node(s)
         *
         * @param string $id The id(pk) of the category
         * @return bool True if has nodes else False
         * @access public
          */
        public function hasNodes($id)
        {
            $nodes = $this->getAll('WHERE parent_id = \''.$id.'\'');
            if(count($nodes) > '0') {
                $hasNodes = True;
            } else {
                $hasNodes = False;
            }
            return $hasNodes;
        }
}
?>
