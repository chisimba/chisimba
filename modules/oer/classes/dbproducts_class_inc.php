<?php

/**
 * This is a DB layer that manages original products
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
 * @version    0.001
 * @package    oer
 * @author     JCSE
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 *
 * @author davidwaf
 */
class dbproducts extends dbtable {

    private $productsTableName = 'tbl_oer_products';

    function init() {
        parent::init($this->productsTableName);
    }

    /**
     * this selects original products
     */
    function getOriginalProducts($filter='', $start=null, $pageSize=null) {
        $limit = "";
        if ($start != null && $pageSize != null) {
            $limit = " limit $start, $pageSize";
        }
        $sql = "select * from $this->productsTableName where parent_id is null $filter $limit";
        return $this->getArray($sql);
    }

    /**
     * get a total count of original products
     * @return type 
     */
    function getOriginalProductCount($filter=null) {
        $sql = "select count(id) as productcount from $this->productsTableName where parent_id is null";
        if ($filter != null) {
            $sql.= ' ' . $filter;
        }

        $data = $this->getArray($sql);

        $result = $data[0]['productcount'];

        return $result;
    }

    /**
     * get a total count of original products
     * @return type 
     */
    function getAdaptationCount($filter=null) {
        $sql = "select count(id) as productcount from $this->productsTableName where parent_id is not null ";
        if ($filter != null) {
            $sql.= ' ' . $filter;
        }

        $data = $this->getArray($sql);
        return $data[0]['productcount'];
    }

    /**
     * this selects original products
     */
    function getAdaptedProducts($xfilter, $start=null, $pageSize=null) {
        $limit = "";
        if ($start != null && $pageSize != null) {
            $limit = " limit $start, $pageSize";
        }
        $filter = "";
        if ($xfilter != null) {
            $filter = $xfilter;
        }

        $sql = "select * from $this->productsTableName where parent_id is not null $filter $limit";
        return $this->getArray($sql);
    }

    /**
     * returns the most adapted product ids
     * @return type 
     */
    function getMostAdaptedProducts() {
        $sql =
                "select count(parent_id) as mostadapted, parent_id as productid from tbl_oer_products where parent_id is not null group by parent_id order by mostadapted desc limit 5";
        return $this->getArray($sql);
    }

    /**
     * returns the most commented products, limited to 5
     * @return type 
     */
    function getMostCommentedProducts() {
        $sql =
                " select count(identifier) as mostcommented, identifier as productid from tbl_wall_posts where identifier is not null and identifier like 'gen%' group by identifier order by mostcommented desc limit 5";
        return $this->getArray($sql);
    }

    /**
     * returns current authors who have created/adapted ay products
     */
    function getProductAuthors() {
        $sql =
                "select distinct author from $this->productsTableName";
        return $this->getArray($sql);
    }

    /**
     * returns distinct countries that are available for all products
     */
    function getProductCountries() {
        $sql =
                "select distinct country from tbl_oer_products where country is not null";
        return $this->getArray($sql);
    }

    /**
     * returns distinct languages for original products
     */
    function getOriginalProductLanguages() {
        $sql =
                "select distinct language from " . $this->productsTableName . " where language is not null and parent_id is null";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * returns distinct products using specified language
     */
    function getLanguageOriginalProducts($lang) {

        $sql = "select * from $this->productsTableName where parent_id is null and language='" . $lang . "'";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * returns distinct adaptations using specified language
     */
    function getLanguageAdaptations($lang) {
        $sql = "select * from $this->productsTableName where parent_id is not null and language='" . $lang . "'";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * returns distinct languages for adaptations
     */
    function getAdaptationsLanguages() {
        $sql =
                "select distinct language from " . $this->productsTableName . " where language is not null and parent_id is not null";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * returns distinct regions that are available for all products
     */
    function getProductRegions() {
        $sql =
                "select distinct region from tbl_oer_products where region is not null";
        return $this->getArray($sql);
    }

    /**
     * saves original product into db
     */
    function saveOriginalProduct($data) {
        $id = $this->insert($data);
        $objIndexData = $this->getObject('indexdata', 'search');
        $saveDate = date('Y-m-d H:M:S');
        $name = $data['title'];
        $product = $this->getProduct($id);
        $description = $product['keywords'] . ' : ' . $product['description'];
        $url = $this->uri(array('action' => 'vieworiginalproduct', 'id' => $id), 'oer');
        $objTrimStr = $this->getObject('trimstr', 'strings');
        $teaser = $objTrimStr->strTrim(strip_tags($description), 500);
        $objUser = $this->getObject("user", "security");
        $userId = $objUser->userId();
        $module = 'oer';

        $objIndexData->luceneIndex($id, $saveDate, $url, $name, NULL, $teaser, $module, $userId, NULL, NULL, NULL);
        return $id;
    }

    /**
     * Updates original product
     * @param  $data fields containing updated data
     * @param  $id ID of product to be updated
     * @return type 
     */
    function updateOriginalProduct($data, $id) {
        $this->update("id", $id, $data);
        // Prepare to add product to search index
        $objIndexData = $this->getObject('indexdata', 'search');
        $saveDate = date('Y-m-d H:M:S');
        $product = $this->getProduct($id);
        $name = $product['title'];

        $description = $product['keywords'] . ' : ' . $product['description'];
        $url = $this->uri(array('action' => 'vieworiginalproduct', 'id' => $id), 'oer');
        $objTrimStr = $this->getObject('trimstr', 'strings');
        $teaser = $objTrimStr->strTrim(strip_tags($description), 500);
        $objUser = $this->getObject("user", "security");
        $userId = $objUser->userId();
        $module = 'oer';

        $objIndexData->luceneIndex($id, $saveDate, $url, $name, NULL, $teaser, $module, $userId, NULL, NULL, NULL);

        return $id;
    }

    /**
     * Get parent product data
     * @param  $id ID of the product
     * @return array
     */
    function getParentData($id) {
        //Fetch parent id of the adaptation
        $sql = "select * from $this->productsTableName where id = '$id'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            $sql = "select * from $this->productsTableName where id = '" . $data[0]["parent_id"] . "'";
            $data = $this->getArray($sql);
            if (count($data) > 0) {
                return $data[0];
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }

    /**
     * returns product details for a specific id
     * @param  $id the product id 
     * @return NULL if product not found, else an array with product details
     */
    function getProduct($id) {
        $sql = "select * from $this->productsTableName where id = '$id'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            return $data[0];
        } else {
            return NULL;
        }
    }

    /**
     * returns random x number of adaptations
     * @param type $fragment
     * @param type $limit
     * @return type 
     */
    function getRandomAdaptationsByInstitution($fragment, $limit) {
        //$sql='SELECT * FROM tbl_oer_products WHERE RAND()<='.$fragment.' and parent_id is not null limit '.$limit.';';
        $sql = 'SELECT * FROM  tbl_oer_products where  parent_id IS NOT NULL ORDER BY RAND()';

        if ($limit) {
            $sql.=' LIMIT ' . $limit . ';';
        }
        return $this->getArray($sql);
    }

    /**
     *  returns list of adaptations by given institution id
     * @param type $institutionId 
     */
    function getAdaptationsByInstitution($institutionId) {
        $sql = "select * from $this->productsTableName where institutionid = '$institutionId'";

        return $this->getArray($sql);
    }

    /**
     *
     * @param type $institutionId
     * @return type 
     */
    function getProductAdaptationCountByInstitution($institutionId) {
        $sql = "select count(*) as adaptationcount from $this->productsTableName where institutionid = '$institutionId'";
        $data = $this->getArray($sql);
        return $data[0]['adaptationcount'];
    }

    /**
     * Get distinct products institutions
     * @return array
     */
    function getDistinctProductsInstitutions() {
        $sql = "select distinct institutionid, count(*) as institutioncount from " . $this->productsTableName . " where institutionid is not null and parent_id is null";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * Get distinct adaptations institutions
     * @return array
     */
    function getDistinctAdaptationsInstitutions() {
        $sql = "select distinct institutionid, count(*) as institutioncount from " . $this->productsTableName . " where institutionid is not null and parent_id is not null";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * Get distinct products oersource
     * @return array
     */
    function getDistinctProductsOerResource() {
        $sql = "select distinct oerresource, count(*) as oerresourcecount from " . $this->productsTableName . " where oerresource is not null and parent_id is null";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * Get distinct adaptations oersource
     * @return array
     */
    function getDistinctAdaptationsOerResource() {
        $sql = "select distinct oerresource, count(*) as oerresourcecount from " . $this->productsTableName . " where oerresource is not null and parent_id is not null";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * returns count of adaptations for a specific product
     * @param  $id the product id
     * @return NULL if product not found, else a count of adaptations
     */
    function getProductAdaptationCount($parentId) {
        $sql = "select count(*) as adaptationcount from $this->productsTableName where parent_id = '$parentId'";
        $data = $this->getArray($sql);
        return $data[0]['adaptationcount'];
    }

    /**
     * returns array of adaptations for a specific product
     * @param  $id the product id
     * @return NULL if product not found, else an array of product adaptations if any
     */
    function getProductAdaptations($parentId, $filter, $start=null, $pageSize=null) {
        $limit = "";
        if ($start != null && $pageSize != null) {
            $limit = " limit $start, $pageSize";
        }
        $sql = "select * from $this->productsTableName where parent_id = '$parentId' $filter $limit";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * returns array of adaptations for a specific product
     * @param  $id the product id
     * @return NULL if product not found, else an array of product adaptations if any
     */
    function getAllProductAdaptations($parentId, $filter) {
        $sql = "select * from $this->productsTableName where parent_id = '" . $parentId . "'" . $filter;
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * returns count of adaptations for every original product
     * @param  $id the product id
     * @return an array with count of adaptations per product, where parentid is null, gives a count of original products
     */
    function getAllProductAdaptationCount() {
        $sql = "select parent_id, count(*) as count from $this->productsTableName GROUP BY parent_id";
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * returns product title for a specific id
     * @param  $id the product id
     * @return NULL if product not found, else an array with product details
     */
    function getProductTitle($id) {
        $sql = "select * from $this->productsTableName where id = '$id'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            return $data[0]['title'];
        } else {
            return NULL;
        }
    }

    /**
     * returns true if product is original-product
     * @param  $id the product id
     * @return True if product does not have parentid, else false
     */
    function isOriginalProduct($id) {
        $sql = "select * from $this->productsTableName where id = '$id'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            if (!empty($data[0]['parent_id'])) {
                return False;
            } else {
                return True;
            }
        } else {
            return False;
        }
    }

    /**
     * deletes a product
     * @param $id  ID of the product to be deleted
     */
    function deleteOriginalProduct($id) {
        $this->delete("id", $id);
    }

    /**
     * returns an array of countries with adapatations
     */
    function getCountriesWithAdaptations() {
        $sql =
                "select distinct country from tbl_oer_products where parent_id is not null and country is not null";
        return $this->getArray($sql);
    }

}

?>