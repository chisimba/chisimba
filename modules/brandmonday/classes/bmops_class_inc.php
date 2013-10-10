<?php
/**
 *
 * operations class for brandmonday module
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
 * @package   brandmonday
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * operations class for brandmonday module
 *
 * @author Paul Scott
 * @package brandmonday
 *
 */
class bmops extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    public $uImage;
    public $objWashout;
    public $teeny;
    public $objConfig;
    public $objDbBm;

    /**
     *
     * Constructor

     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
        $this->objIcon = $this->getObject ( 'geticon', 'htmlelements' );
        $this->objLink = $this->getObject ( 'link', 'htmlelements' );
        $this->objDbBm = $this->getObject('dbbm');
        
        $this->objUser = $this->getObject ( 'user', 'security' );
        //$this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objWashout = $this->getObject ( 'washout', 'utilities' );
        $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
    }

    public function happyPeepsTagCloud() {
        $this->objTC = $this->newObject('tagcloud', 'utilities');
        $tagarr = $this->objDbBm->getHappyPeeps();
        if (empty($tagarr)) {
            return NULL;
        }
        else {
            foreach($tagarr as $pl) {
                $utags[] = $pl['from_user'];
            }
            asort($utags);
            foreach($utags as $tag) {
                // create the url
                $url = "http://twitter.com/$tag";
                // get the count of the tag (weight)
                $weight = $this->objDbBm->getTagWeight('tbl_bmplus', $tag);
                $weight = $weight*1000;
                $tag4cloud = array(
                    'name' => $tag,
                    'url' => $url,
                    'weight' => $weight,
                    'time' => time()
                );
                $ret[] = $tag4cloud;
            }
            return $this->objTC->buildCloud($ret);
        }
    }

    public function sadPeepsTagCloud() {
        $this->objTC = $this->newObject('tagcloud', 'utilities');
        $tagarr = $this->objDbBm->getSadPeeps();
        if (empty($tagarr)) {
            return NULL;
        }
        else {
            foreach($tagarr as $pl) {
                $utags[] = $pl['from_user'];
            }
            asort($utags);
            foreach($utags as $tag) {
                // create the url
                $url = "http://twitter.com/$tag";
                // get the count of the tag (weight)
                $weight = $this->objDbBm->getTagWeight('tbl_bmminus', $tag);
                $weight = $weight*1000;
                $tag4cloud = array(
                    'name' => $tag,
                    'url' => $url,
                    'weight' => $weight,
                    'time' => time()
                );
                $ret[] = $tag4cloud;
            }
            return $this->objTC->buildCloud($ret);
        }
    }

    public function activePeepsTagCloud() {
        $this->objTC = $this->newObject('tagcloud', 'utilities');
        $tagarr1 = $this->objDbBm->getSadPeeps();
        $tagarr2 = $this->objDbBm->getHappyPeeps();
        $tagarr = array_merge($tagarr1, $tagarr2);
        if (empty($tagarr)) {
            return NULL;
        }
        else {
            foreach($tagarr as $pl) {
                $utags[] = $pl['from_user'];
            }
            $utags = array_unique($utags);
            asort($utags);
            foreach($utags as $tag) {
                // create the url
                $url = "http://twitter.com/$tag";
                // get the count of the tag (weight)
                $weightsad = $this->objDbBm->getTagWeight('tbl_bmminus', $tag);
                $weighthappy = $this->objDbBm->getTagWeight('tbl_bmplus', $tag);
                $weight = $weightsad+$weighthappy;
                $weight = $weight*1000;
                $tag4cloud = array(
                    'name' => $tag,
                    'url' => $url,
                    'weight' => $weight,
                    'time' => time()
                );
                $ret[] = $tag4cloud;
            }
            return $this->objTC->buildCloud($ret);
        }
    }

    public function mentionsTagCloud() {
        $this->objTC = $this->newObject('tagcloud', 'utilities');
        $tagarr = $this->objDbBm->getUserMentions();
        if (empty($tagarr)) {
            return NULL;
        }
        else {
            foreach($tagarr as $pl) {
                $utags[] = $pl['from_user'];
            }
            asort($utags);
            foreach($utags as $tag) {
                // create the url
                $url = "http://twitter.com/$tag";
                // get the count of the tag (weight)
                $weight = $this->objDbBm->getTagWeight('tbl_bmmentions', $tag);
                $weight = $weight*1000;
                $tag4cloud = array(
                    'name' => $tag,
                    'url' => $url,
                    'weight' => $weight,
                    'time' => time()
                );
                $ret[] = $tag4cloud;
            }
            return $this->objTC->buildCloud($ret);
        }
    }

    public function bestServiceTagCloud() {
        $this->objTC = $this->newObject('tagcloud', 'utilities');
        $tagarr = $this->objDbBm->getBmTags('plus');
        if (empty($tagarr)) {
            return NULL;
        }
        else {
            $utags = $tagarr;
            asort($utags);
            foreach($utags as $tag) {
                // create the url
                $url = "http://twitter.com/#search?q=%23$tag";
                // get the count of the tag (weight)
                $weight = $this->objDbBm->getServiceTagWeight('plus', $tag);
                $weight = $weight*1000;
                $tag4cloud = array(
                    'name' => $tag,
                    'url' => $url,
                    'weight' => $weight,
                    'time' => time()
                );
                $ret[] = $tag4cloud;
            }
            return $this->objTC->buildCloud($ret);
        }
    }

    public function bestServicePieChartAll() {
        $objFlashGraph = $this->getObject('flashgraph','utilities');
        $objFlashGraph->dataSource = $this->uri(array('action'=>'allbsdata'));
        return $objFlashGraph->show();
    }

    public function getAllBsData() { // weight, colour, label
        $objFlashGraphData = $this->newObject('flashgraphdata', 'utilities');
        $objFlashGraphData->graphType = 'pie';
        $tagarr = $this->objDbBm->getBmTags('plus', NULL);
        $num = count($tagarr);
        $last = $num - 20;
        $tagarr = array_slice($tagarr, 0, 20);
        $colours = $this->getObject('websafecolours', 'utilities');
        if (empty($tagarr)) {
            return NULL;
        }
        else {
            $utags = $tagarr;
            asort($utags);
            foreach($utags as $tag) {
                $weight = $this->objDbBm->getServiceTagWeight('plus', $tag);
                $colour = $colours->getRandomColour();
                $objFlashGraphData->addPieDataSet($weight, $colour, $tag);
            }
            return $objFlashGraphData->show();
        }
    }

    public function bestServicePieChartWeek() {
        $objFlashGraph = $this->getObject('flashgraph','utilities');
        $objFlashGraph->dataSource = $this->uri(array('action'=>'weekbsdata'));
        return $objFlashGraph->show();
    }

    public function getWeekBsData() {
        $objFlashGraphData = $this->newObject('flashgraphdata', 'utilities');
        $objFlashGraphData->graphType = 'pie';
        $tagarr = $this->objDbBm->getBmTagsWeekly('tbl_bmplus');
        foreach($tagarr as $tagarray) {
            if(!empty($tagarray)) {
                $value = strtolower($tagarray[0]['meta_value']);
                $ntagarr[] = $value;
            }
        }
        $colours = $this->getObject('websafecolours', 'utilities');
        if (empty($ntagarr)) {
            return NULL;
        }
        else {
            $utags = $ntagarr;
            $weights = array_count_values($utags);
            $utags = array_unique($utags);
            foreach($utags as $tag) {
                $weight =  $weights[$tag];
                $colour = $colours->getRandomColour();
                $objFlashGraphData->addPieDataSet($weight, $colour, $tag);
            }
            return $objFlashGraphData->show();
        }
    }

    public function worstServiceTagCloud() {
        $this->objTC = $this->newObject('tagcloud', 'utilities');
        $tagarr = $this->objDbBm->getBmTags('minus');
        if (empty($tagarr)) {
            return NULL;
        }
        else {
            $utags = $tagarr;
            asort($utags);
            foreach($utags as $tag) {
                // create the url
                $url = "http://twitter.com/#search?q=%23$tag";
                // get the count of the tag (weight)
                $weight = $this->objDbBm->getServiceTagWeight('minus', $tag);
                $weight = $weight*1000;
                $tag4cloud = array(
                    'name' => $tag,
                    'url' => $url,
                    'weight' => $weight,
                    'time' => time()
                );
                $ret[] = $tag4cloud;
            }
            return $this->objTC->buildCloud($ret);
        }
    }

    public function worstServicePieChartAll() {
        $objFlashGraph = $this->getObject('flashgraph','utilities');
        $objFlashGraph->dataSource = $this->uri(array('action'=>'allwsdata'));
        return $objFlashGraph->show();
    }

    public function getAllWsData() { // weight, colour, label
        $objFlashGraphData = $this->newObject('flashgraphdata', 'utilities');
        $objFlashGraphData->graphType = 'pie';
        $tagarr = $this->objDbBm->getBmTags('minus', NULL);
        $num = count($tagarr);
        $last = $num - 20;
        $tagarr = array_slice($tagarr, 0, 20);
        $colours = $this->getObject('websafecolours', 'utilities');
        if (empty($tagarr)) {
            return NULL;
        }
        else {
            $utags = $tagarr;
            asort($utags);
            foreach($utags as $tag) {
                $weight = $this->objDbBm->getServiceTagWeight('minus', $tag);
                $colour = $colours->getRandomColour();
                $objFlashGraphData->addPieDataSet($weight, $colour, $tag);
            }
            return $objFlashGraphData->show();
        }
    }

    public function worstServicePieChartWeek() {
        $objFlashGraph = $this->getObject('flashgraph','utilities');
        $objFlashGraph->dataSource = $this->uri(array('action'=>'weekwsdata'));
        return $objFlashGraph->show();
    }

    public function getWeekWsData() {
        $objFlashGraphData = $this->newObject('flashgraphdata', 'utilities');
        $objFlashGraphData->graphType = 'pie';
        $tagarr = $this->objDbBm->getBmTagsWeekly('tbl_bmminus');
        foreach($tagarr as $tagarray) {
            if(!empty($tagarray)) {
                $value = strtolower($tagarray[0]['meta_value']);
                $ntagarr[] = $value;
            }
        }
        $colours = $this->getObject('websafecolours', 'utilities');
        if (empty($ntagarr)) {
            return NULL;
        }
        else {
            $utags = $ntagarr;
            $weights = array_count_values($utags);
            $utags = array_unique($utags);
            foreach($utags as $tag) {
                $weight =  $weights[$tag];
                $colour = $colours->getRandomColour();
                $objFlashGraphData->addPieDataSet($weight, $colour, $tag);
            }
            return $objFlashGraphData->show();
        }
    }

    public function getLastPosts($num, $mood) {
        return $this->objDbBm->getLast($num, $mood);
    }
}
?>