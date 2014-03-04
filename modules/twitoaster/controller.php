<?php
/**
 * twitoaster controller class
 *
 * Class to control the twitoaster module
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
 * @package   twitoaster
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

class twitoaster extends controller {

    public $teeny;
    public $objModules;
    public $objTwitterLib;
    public $objCurl;
    public $objOps;

    /**
     *
     * Standard constructor method to retrieve the action from the
     * querystring, and instantiate the user and lanaguage objects
     *
     */
    public function init() {
        try {
            $this->teeny = $this->getObject ( 'tiny', 'tinyurl' );
            $this->objCurl = $this->getObject('curlwrapper', 'utilities');
            $this->objOps = $this->getObject('twitoasterops');
            $this->objUser = $this->getObject ( 'user', 'security' );
            $this->objUserParams = $this->getObject ( 'dbuserparamsadmin', 'userparamsadmin' );
            //Create an instance of the language object
            $this->objLanguage = $this->getObject ( 'language', 'language' );
            $this->objModules = $this->getObject ( 'modules', 'modulecatalogue' );

            if ($this->objModules->checkIfRegistered ( 'twitter' )) {
                // Get other places to upstream content to
                $this->objTwitterLib = $this->getObject ( 'twitterlib', 'twitter' );
            }

        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method to handle adding and saving
     * of comments
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL :
                //var_dump(json_decode($this->objOps->showUser('paulscott56')));
                //var_dump(json_decode($this->objOps->verifyKey('json')));
                
                //var_dump(json_decode($this->objOps->showConvo(4467277193)));
                //var_dump(json_decode($this->objOps->convoUser(NULL, 'paulscott56')));
                //var_dump(json_decode($this->objOps->convoSearch('chisimba')));

                //$returnobj = json_decode($this->objOps->userUpdate('Please do me a favour and reply to this... Thanks!'));
                //$thread = $returnobj->thread;
                //$threadid = $thread->id;
                $threadid = 4465619943;
                $data = json_decode($this->objOps->showConvo($threadid));
                $stats = $data->thread->stats;
                $totalreplies = $stats->total_replies;
                $replydata = $data->replies;
                foreach($replydata as $replies) {
                    $content = $replies->content;
                    $dt = $replies->created_at->datetime_gmt;
                    $name = $replies->user->screen_name;
                    $image = $replies->user->profile_image_url;
echo "<img src='$image' /> $name says: $content at $dt <br />";
                }
                break;

            case 'viewall' :
                break;

        }
    }
}
?> 