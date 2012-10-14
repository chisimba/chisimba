<?php
/* -------------------- string class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* Class for creating links to social bookmarking sites
*
* @category  Chisimba
* @package   utilities
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
*/
class socialbookmarking extends object {


    /**
     * @var string $url URL of page to be bookmarked
     * @access private
     */
    private $url;

    public $includeTextLink = TRUE;

    public $options = array('stumbleUpon', 'delicious', 'newsvine', 'reddit', 'muti', 'facebook', 'addThis');

    /**
     * Constructor
     */
    public function init()
    {
        // Load Link Class
        $this->loadClass('link', 'htmlelements');

        // Set URL to Current Page
        $this->setUrl('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    }

    /**
     * Method to set the URL. URL has to be encoded, thats why users are forced to use this method.
     * @param string $url URL of Page
     */
    public function setUrl($url)
    {
        $this->url = urlencode($url);
    }

    private function checkURL($url)
    {
        if ($url == '') {
            return $this->url;
        } else {
            return urlencode($url);
        }
    }
    
    /**
     * Method to show social bookmarking for all options besides Digg
     */
    public function show()
    {
        $str = '';
        
        foreach ($this->options as $option)
        {
            $str .= $this->$option().' &nbsp; ';
        }
        
        return $str;
    }

    /**
     * Method to create a link for users to bookmark the page at Stumble Upon
     * @param string $url URL of Page
     * @return string Stumble Upon Link
     */
    public function stumbleUpon($url='')
    {
        // Check URL
        $url = $this->checkURL($url);

        // Create Link
        $stumbleUpon = new link ('http://www.stumbleupon.com/submit?url='.$url);
        $stumbleUpon->link = '<img src="'.$this->getResourceURI('socialbookmarking/stumbleupon.png').'" border="0" alt="Stumble Upon" title="Stumble Upon" />';

        if ($this->includeTextLink) {
            $stumbleUpon->link .= ' Stumble Upon';
            return '<span class="nowrap">'.$stumbleUpon->show().'</span>';
        } else {
            return $stumbleUpon->show();
        }
    }

    /**
     * Method to create a link for users to bookmark the page at del.icio.us
     * @param string $url URL of Page
     * @return string Del.icio.us Link
     */
    public function delicious($url='')
    {
        // Check URL
        $url = $this->checkURL($url);

        // Create Link
        $delicious = new link ('http://del.icio.us/post?url='.$url);
        $delicious->link = '<img src="'.$this->getResourceURI('socialbookmarking/delicious.png').'" border="0" alt="del.icio.us" title="del.icio.us" />';

        if ($this->includeTextLink) {
            $delicious->link .= ' del.icio.us';
            return '<span class="nowrap">'.$delicious->show().'</span>';
        } else {
            return $delicious->show();
        }
    }

    /**
     * Method to create a link for users to bookmark the page at Newsvine
     * @param string $url URL of Page
     * @return string Newsvine Link
     */
    public function newsvine($url='')
    {
        // Check URL
        $url = $this->checkURL($url);

        // Create Link
        $newsvine = new link ('http://www.newsvine.com/_tools/seed&amp;save?u='.$url);
        $newsvine->link = '<img src="'.$this->getResourceURI('socialbookmarking/newsvine.png').'" border="0" alt="Newsvine" title="Newsvine" />';

        if ($this->includeTextLink) {
            $newsvine->link .= ' Newsvine';
            return '<span class="nowrap">'.$newsvine->show().'</span>';
        } else {
            return $newsvine->show();
        }
    }

    /**
     * Method to create a link for users to bookmark the page at reddit
     * @param string $url URL of Page
     * @return string reddit Link
     */
    public function reddit($url='')
    {
        // Check URL
        $url = $this->checkURL($url);

        // Create Link
        $reddit = new link ('http://reddit.com/submit?url='.$url);
        $reddit->link = '<img src="'.$this->getResourceURI('socialbookmarking/reddit.png').'" border="0" alt="Reddit" title="Reddit" />';

        if ($this->includeTextLink) {
            $reddit->link .= ' Reddit';
            return '<span class="nowrap">'.$reddit->show().'</span>';
        } else {
            return $reddit->show();
        }
    }

    /**
     * Method to create a link for users to bookmark the page at muti.co.za
     * @param string $url URL of Page
     * @return string muti.co.za Link
     */
    public function muti($url='')
    {
        // Check URL
        $url = $this->checkURL($url);

        // Create Link
        $muti = new link ("javascript:location.href='http://muti.co.za/submit?url='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title)");
        $muti->link = '<img src="'.$this->getResourceURI('socialbookmarking/muti.png').'" border="0" alt="muti.co.za" title="muti.co.za" />';

        if ($this->includeTextLink) {
            $muti->link .= ' muti';
            return '<span class="nowrap">'.$muti->show().'</span>';
        } else {
            return $muti->show();
        }
    }

    /**
     * Method to create a link for users to bookmark the page at Facebook
     * @param string $url URL of Page
     * @return string Facebook Link
     */
    public function facebook($url='')
    {
        // Check URL
        $url = $this->checkURL($url);

        // Create Link
        $facebook = new link ('http://www.facebook.com/share.php?u='.$url);
        $facebook->link = '<img src="'.$this->getResourceURI('socialbookmarking/facebook_share_icon.gif').'" border="0" alt="Facebook" title="Facebook" />';

        if ($this->includeTextLink) {
            $facebook->link .= ' Facebook';
            return '<span class="nowrap">'.$facebook->show().'</span>';
        } else {
            return $facebook->show();
        }
    }

    /**
     * Method to create a link for users to use the addthis.com service
     * @param string $url URL of Page
     * @return string addthis.com Link
     */
    public function addThis($url='')
    {
        // Check URL
        $url = $this->checkURL($url);

        // Create Link
        $addThis = new link ('http://www.addthis.com/bookmark.php?pub=&amp;url='.$url);
        $addThis->link = '<img src="'.$this->getResourceURI('socialbookmarking/button1-bm.gif').'" border="0" width="125" height="16" border="0" />';

        return $addThis->show();
    }

    /**
     * Method to create a link for users to digg the URL
     * @return string digg script
     */
    public function diggThis()
    {
        return '<script type="text/javascript">
   daigg_skin = "compact";
</script><script src="http://digg.com/tools/diggthis.js" type="text/javascript"></script>';
    }











}

?>
