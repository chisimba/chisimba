<?php
/**
 * message jabberblog dbtable derived class
 *
 * Class to interact with the database for the popularity contest module
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
 * @category  chisimba
 * @package   jabberblog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */
class dbjbim extends dbTable {
    /**
     * Constructor
     *
     */
    public function init() {
        parent::init ( 'tbl_jabberblog' );
        $this->objPresence = $this->getObject ( 'dbjbpresence' );
        $this->objUser = $this->getObject ( 'user', 'security' );
    }

    /**
     * Public method to insert a record to the popularity contest table as a log.
     *
     * This method takes the IP and module_name and inserts the record with a timestamp for temporal analysis.
     *
     * @param array $recarr
     * @return string $id
     */
    public function addRecord($pl) {
        $userSplit = explode ( '/', $pl ['from'] );
        $userSplit2 = explode ( "/", $userSplit [0] );
        $times = $this->now ();
        $recarr ['datesent'] = $times;
        $recarr ['msgtype'] = $pl ['type'];
        $recarr ['msgfrom'] = $userSplit2 [0];
        $recarr ['msgbody'] = $pl ['body'];
        $recarr ['twitthreadid'] = $pl['twitthreadid'];
        // Check for empty messages
        if ($recarr ['msgbody'] == "") {
            return;
        } else {
            $itemid = $this->insert ( $recarr, 'tbl_jabberblog' );
            $objImView = $this->getObject('jbviewer');
            $objImView->parseHashtags($recarr['msgbody'], $itemid);
            $objImView->parseAtTags($recarr['msgbody'], $itemid);
            $this->appendSitemap($itemid);

            return $itemid;
        }
    }

    public function getRange($start, $num) {
        $range = $this->getAll ( "ORDER BY datesent ASC LIMIT {$start}, {$num}" );
        return array_reverse ( $range );
    }

    public function appendSitemap($itemid) {
        // add to the blog sitemap
        $this->objConfig = $this->getObject('altconfig', 'config');
        $maparray = array('url' => $this->uri(array('action' => 'viewsingle', 'postid' => $itemid)), 'lastmod' => $this->now(), 'changefreq' => 'daily', 'priority' => 0.5 );
        $smarr = array($maparray);
        $sitemap = $this->getObject('sitemap', 'utilities');
        if(!file_exists($this->objConfig->getsiteRootPath().'jblogsitemap.xml')) {
            $smxml = $sitemap->createSiteMap($smarr);
            $sitemap->writeSitemap($smxml, 'jblogsitemap');
        }
        else {
            $smxml = $sitemap->updateSiteMap($maparray, 'jblogsitemap');
       	}
    }

    public function getAllPosts() {
        return array_reverse($this->getAll());
    }

    public function getSingle($msgid) {
        return $this->getAll ( "WHERE id = '$msgid'" );
    }

    public function getPostById($pid) {
        return $this->getSingle ( $pid );
    }

    public function getNoMsgs() {
        return $this->getRecordCount ( 'tbl_jabberblog' );
    }

    public function keySearch($keyword) {
        return $this->getAll("WHERE msgbody LIKE '%%$keyword%%'");
    }

    public function addHashTag() {

    }
}
?>