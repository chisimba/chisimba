<?php
/**
* Class to Extract parameter from a list
*
* Extract parameters from a list of parameters such as x=y, a=b or x=y&a=b
*
* Useage:
*     $objExpar = $this->getObject("extractparams", "utilities");
*     $str=" a=b,c=d,e= f ";
*     $ar= $objExpar->getArrayParams($str, ",");
*     echo $objExpar->a;
*     echo $objExpar->c;
*     echo $objExpar->e;
*     foreach ($ar as $key=>$value) {
*       echo "Property: $key has a value of $value<br />";
*     }
*
* PHP version 5
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
* @category  Chisimba
* @package   filters
* @author    Derek Keats <dkeats@uwc.ac.za>
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id$
* @link      http://avoir.uwc.ac.za
*/



/**
*
* Class to Extract parameter from a list. It returns an array, as well as
* setting properties corresponding to each parameter.
*
* @author Derek Keats
*
*/
class extractparams extends object
{

    public function init()
    {

    }

    /**
     *
     * Method to take a delimited string of parameter and value pairs
     * and return an array of key=>value as well as set a property of
     * this class equal to each parameter and value.
     *
     * @param string $str A delimited string of parameters
     * @param string $delim A delimiter to separate the pairs
     * @return string array An array of key=>values
     *
     */
    public function getArrayParams($str, $delim=",")
    {
        $ar = explode($delim, $str);
        try {
            foreach ($ar as $item) {
                //Split them up in to a string representing each pair
                $tmpAr = explode("=", trim($item));
                //Make sure that there is a pair
                if (count($tmpAr) == 2) {
                    $pName = trim($tmpAr[0]);
                    $pValue =  trim($tmpAr[1]);
                    $ret[$pName] = $pValue;
                    //Set a property for this class for easy use
                    $this->$pName = $pValue;
                }
            }
        } catch (Exception $e) {
                throw customException($e->getMessage());
                //customException::cleanUp();
                exit;
        }
        return $ret;
    }
}
?>