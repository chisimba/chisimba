<?php

/**
 * Simple class to output start and end tags for form elements
 *
 * 
 * PHP version 3
 * 
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */


class formtags
{
    /**
    * @param   string $action  
    * @param   string $method  
    * @param   string $name    
    * @param   string $onsubmit
    * @returns string $str
    */
    function startform($action,$method='GET',$id=FALSE,$onsubmit=FALSE)
    {
        $str="<form action=\"".$action."\" method=\"".$method."\" ";
        if ($id)
        {
            $str.="ID='".$id."' ";
        }
        if ($onsubmit)
        {
            $str.="onsubmit=\"".$onsubmit."\" ";
        }
        $str.=">\n";
        return $str;
    }

    /**
    * @returns string $str
    */
    function closeform()
    {
        $str="</form>\n";
        return $str;
    }
}
?>