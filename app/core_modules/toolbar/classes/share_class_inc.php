<?php
/**
 *
 * Share class
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
 * @package   toolbar
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
 * Share class
 *
 * @author Paul Scott
 * @package toolbar
 *
 */
class share extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    private $objModules;
    private $objIcon;

    protected $tImage;
    protected $fbImage;
    protected $delImage;
    protected $inImage;

    protected $twitAPI = 'http://twitter.com/home?status=';
    protected $fbAPI = 'http://www.facebook.com/share.php?u=';
    protected $delAPI = 'http://del.icio.us/post?v=&noui&jump=close&url='; // remember we need a &title= also here!
    protected $inAPI = 'http://www.linkedin.com/shareArticle?mini=true&ro=false&summary=&source=&url='; // &title=SOMETHING

    private $teeny = NULL;
    private $useTeeny = FALSE;

    public $shareBar;
    public $myURL;
    public $myTitle;

    /**
     *
     * Constructor

     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objIcon = $this->getObject ( 'geticon', 'htmlelements' );
        $this->objLink = $this->getObject ( 'link', 'htmlelements' );
        $this->objWashout = $this->getObject ( 'washout', 'utilities' );
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        if($this->objModules->checkIfRegistered('tinyurl')) {
            $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
            $this->useTeeny = TRUE;
        }

        $this->myURL = $this->uri('');
        $this->myTitle = '';
    }

    public function setup($url, $title, $text = 'Interesting Post! ') {
        $this->myURL = $url;
        $this->myTitle = $title;
        if($this->useTeeny === TRUE) {
            $this->myURL = $this->teeny->create($url);
        }
        // ok now set the API URL's to use the title and url where appropriate, as well as the text.
        $this->twitAPI = $this->twitAPI.$text.$this->myURL;
        $this->fbAPI = $this->fbAPI.$this->myURL;
        $this->delAPI = $this->delAPI.$this->myURL."&title=".$title;
        $this->inAPI = $this->inAPI.$this->myURL."&title=".$title;

        $this->tImage = $this->objIcon->getLinkedIcon($this->twitAPI, 'sharetwitter', 'png');
        $this->fbImage = $this->objIcon->getLinkedIcon($this->fbAPI, 'sharefacebook', 'png');
        $this->delImage = $this->objIcon->getLinkedIcon($this->delAPI, 'sharedelicious', 'png');
        $this->inImage = $this->objIcon->getLinkedIcon($this->inAPI, 'sharelinkedin', 'png');
    }
    public function show() {
        $this->shareBar = $this->tImage.$this->fbImage.$this->delImage.$this->inImage;

        return $this->shareBar;
    }

}
?>