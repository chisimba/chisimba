<?php
/**
 * This class contains util methods for displaying full original product details
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

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     davidwaf davidwaf@gmail.com
 */
/**
 * This class provides access to  rating table in the db
 */
class dbproductrating extends dbtable {

    private $productRatingTableName = 'tbl_oer_productrating';

    function init() {
        parent::init($this->productRatingTableName);
    }
    
    /**
     * Stores the rating value
     * @param type $data
     * @return type 
     */
    function  addRating($data){
        return $this->insert($data);
    }
    
    /**
     * Rating the total rating for a given product
     * @param type $productId
     * @return type 
     */
    function getTotalRating($productId){
        $sql=
        "select sum(rate) as rating from $this->productRatingTableName where productid = '$productId'";
        $data=$this->getArray($sql);
        return $data[0]['rating'];
    }
    
    
    /**
     * gets the most rated product
     * @return type 
     */
    function  getMostRatedProducts(){
        $sql=
        "select distinct rt.productid from $this->productRatingTableName as rt,tbl_oer_products as pr where  pr.id=rt.productid and pr.parent_id is null order by rt.totalrating DESC limit 3";
        $data=$this->getArray($sql);
        return $data;  
    }

}

?>
