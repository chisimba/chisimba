<?php
/**
 * Class to handle blog elements (extras).
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    $Id: blogopsextras_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @package    blog
 * @subpackage blogops
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to handle blog elements (extras)
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: blogopsextras_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blogopsextras extends object
{
    /**
     * Description for public
     *
     * @var    mixed
     * @access public
     */
    public $objConfig;
    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objDbBlog = $this->getObject('dbblog');
            $this->loadClass('href', 'htmlelements');
            $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->showfullname = $this->sysConfig->getValue('show_fullname', 'blog');
            $this->objUser = $this->getObject('user', 'security');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
        if (!extension_loaded("imap")) {
            $this->mail2blog = FALSE;
        }
    }
    /**
     * Show as KML
     *
     * Create a valid KML file for use in Google maps/Google Earth
     *
     * @return string $doc kml document
     * @access public
     */
    public function showKML()
    {
        $kml = $this->getObject('kmlgen', 'simplemap');
        $doc = $kml->overlay('my map', 'a test map');
        $doc.= $kml->generateSimplePlacemarker('place1', 'a place', '18.629057', '-33.932922', 0);
        $doc.= $kml->generateSimplePlacemarker('place2', 'another place', '32.56667', '0.33333', 0);
        $doc.= $kml->simplePlaceSuffix();
        return $doc;
    }
    /**
     * Method to look up geonames database for lat lon cords of a certain place as a string
     *
     * @param  array   $params
     * @param  integer $limit
     * @return string
     */
    public function findGeoTag($params = array() , $limit = '10')
    {
        // do a sanity check on the array of params...
        if (!isset($params['place']) || !isset($params['countrycode'])) {
            return FALSE;
            break;
        }
        $wsurl = "http:// ws.geonames.org/search?";
        $searchparams = "q=" . $params['place'] . "&country=" . $params['countrycode'] . "&maxRows=" . $limit;
        $url = $wsurl . $searchparams;
        // set a client to get the request
        // get the proxy info if set
        $this->objProxy = $this->getObject('proxyparser', 'utilities');
        $proxyArr = $this->objProxy->getProxy();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'] . ":" . $proxyArr['proxy_port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'] . ":" . $proxyArr['proxy_pass']);
        }
        $code = curl_exec($ch);
        curl_close($ch);
        return $code;
    }
    /**
     * Method to return a timeline object of a specific users blog posts
     *
     * @param  array   $posts
     * @param  integer $userid
     * @return array
     */
    public function myBlogTimeline($posts, $userid)
    {
        $this->objUser = $this->getObject('user', 'security');
        // parse the hell outta the posts and build up the timeline XML
        $str = '<data date-time-format="iso8601">';
        foreach($posts as $post) {
            // start the event tag
            $str.= "<event ";
            // add in the event details
            $date = date('Y-m-d', $post['post_ts']);
            $title = $post['post_title'];
            $plink = new href($this->uri(array(
                'action' => 'viewsingle',
                'postid' => $post['id'],
                'userid' => $post['userid']
            ) , 'blog') , $this->objLanguage->languageText("mod_blog_viewpost", "blog") , 'target=_top');
            $image = $this->objUser->getUserImageNoTags($userid);
            $str.= 'start="' . $date . '" title="' . $title . '" image="' . $image . '">';
            $str.= htmlentities($post['post_excerpt'] . "<br />" . $plink->show());
            $str.= "</event>";
        }
        $startdate = date('Y', $posts[0]['post_ts']);
        $str.= "</data>";
        return array(
            $str,
            $startdate
        );
    }
    /**
     * Method to parse the timeline URI data
     *
     * @param  integer $int
     * @param  integer $fdate
     * @param  string  $timeline
     * @return mixed
     */
    public function parseTimeline($int, $fdate, $timeline)
    {
        $objIframe = $this->getObject('iframe', 'htmlelements');
        $objIframe->width = "100%";
        $objIframe->height = "700";
        $ret = $this->uri(array(
            "mode" => "plain",
            "action" => "viewtimeline",
            "timeLine" => $timeline,
            "intervalUnit" => $int,
            "focusDate" => $fdate,
            "tlHeight" => '700'
        ) , "timeline");
        $objIframe->src = $ret;
        return $objIframe->show();
    }
    /**
     * Method not yet implemented due to processor usage and db hit rate
     *
     */
    public function siteBlogTimeline()
    {
        // grab all of the posts

    }
    /**
     * Method to create the form used in the geoTag block
     *
     * @param  void
     * @return string
     */
    public function geoTagForm()
    {
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->objUser = $this->getObject('user', 'security');
        $geoform = new form('checkgeo', $this->uri(array(
            'action' => 'checkgeo'
        )));
        // add rules
        $geoform->addRule('geoplace', $this->objLanguage->languageText("mod_blog_phrase_geoplacereq", "blog") , 'required');
        $geoform->addRule('geocountrycode', $this->objLanguage->languageText("mod_blog_phrase_geocountrycodereq", "blog") , 'required');
        // start a fieldset
        $geofieldset = $this->getObject('fieldset', 'htmlelements');
        $geoadd = $this->newObject('htmltable', 'htmlelements');
        $geoadd->cellpadding = 3;
        // place textfield
        $geoadd->startRow();
        $geoplacelabel = new label($this->objLanguage->languageText('mod_blog_geoplace', 'blog') . ':', 'input_geoplace');
        $geoplace = new textinput('geoplace');
        $geoplace->size = '45%';
        $geoadd->addCell($geoplacelabel->show());
        $geoadd->addCell($geoplace->show());
        $geoadd->endRow();
        // Country code
        $geoadd->startRow();
        // get the codes and countries from the languagecode class
        $langcode = $this->getObject('languagecode', 'language');
        $list = $langcode->countryAlpha();
        $geocountrycodelabel = new label($this->objLanguage->languageText('mod_blog_geocountrycode', 'blog') . ':', 'input_geocountrycode');
        $geocountrycode = $list;
        // new textinput('geocountrycode');
        $geoadd->addCell($geocountrycodelabel->show());
        $geoadd->addCell($geocountrycode);
        // ->show());
        $geoadd->endRow();
        // end off the form and add the buttons
        $this->objGeoButton = &new button($this->objLanguage->languageText('word_lookup', 'blog'));
        $this->objGeoButton->setValue($this->objLanguage->languageText('word_lookup', 'blog'));
        $this->objGeoButton->setToSubmit();
        $geofieldset->addContent($geoadd->show());
        $geoform->addToForm($geofieldset->show());
        $geoform->addToForm($this->objGeoButton->show());
        $geoform = $geoform->show();
        // featurebox it...
        $objGeoFeaturebox = $this->getObject('featurebox', 'navigation');
        return $objGeoFeaturebox->show($this->objLanguage->languageText("mod_blog_geolookup", "blog") , $geoform);
    }
    /**
     * Method to show the latest images posted to the blog as a slideshow
     *
     * @return string
     */
    public function showDiaporama()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        // build up the array of images...
        $path = $this->objConfig->getContentBasePath() . 'blog/';
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        chdir($path);
        $counter = 0;
        $entry = NULL;
        $filearr = glob("{*.jpg,*.JPG,*.png,*.PNG,*.gif,*.GIF}", GLOB_BRACE);
        if (empty($filearr)) {
            return NULL;
        }
        foreach($filearr as $images) {
            $entry.= 't_img[' . $counter . '] = "' . $this->objConfig->getSiteRoot() . "usrfiles/blog/" . $images . '";';
            $counter++;
        }
        $head = '<script type="text/javascript">
                var id_current = 0;

                function majDiaporama ()
                {
                     var t_img = new Array();';
        $head.= $entry;
        $head.= "var img = $('imageDiaporama');

                       Element.hide('imageDiaporama');
                       img.src = '';
                       if (id_current < (t_img.length-1)) id_current++;
                       else id_current = 0;
                       img.src = t_img[id_current];
                       new Effect.Appear('imageDiaporama');";
        $head.= 'window.setTimeout("majDiaporama()",5000);
                }
            </script>';
        $this->appendArrayVar('headerParams', $head);
        $content = '<body onLoad="majDiaporama ();">
                    <div class="warning" id="photoLog">
                    <img src=" " style="width : 120px; height : 80px;" alt="random selection of pictures" id="imageDiaporama"/>
                    </div>  ';
        $this->objFeaturebox = $this->getObject('featurebox', 'navigation');
        $ret = $this->objFeaturebox->showContent($this->objLanguage->languageText("mod_blog_recentpics", "blog") , $content);
        return $ret;
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param string    $o_file Parameter description (if any) ...
     * @param integer   $t_ht   Parameter description (if any).
     *
     * @return string   Return description (if any) .
     * @access public
     */
    public function makeThumbnail($o_file, $t_ht = 150)
    {
        $image_info = getImageSize($o_file);
        // see EXIF for faster way
        switch ($image_info['mime']) {
            case 'image/gif':
                if (imagetypes() &IMG_GIF) {
                    // not the same as IMAGETYPE
                    $o_im = imageCreateFromGIF($o_file);
                } else {
                    $ermsg = 'GIF images are not supported<br />';
                }
                break;

            case 'image/jpeg':
                if (imagetypes() &IMG_JPG) {
                    $o_im = imageCreateFromJPEG($o_file);
                } else {
                    $ermsg = 'JPEG images are not supported<br />';
                }
                break;

            case 'image/png':
                if (imagetypes() &IMG_PNG) {
                    $o_im = imageCreateFromPNG($o_file);
                } else {
                    $ermsg = 'PNG images are not supported<br />';
                }
                break;

            case 'image/wbmp':
                if (imagetypes() &IMG_WBMP) {
                    $o_im = imageCreateFromWBMP($o_file);
                } else {
                    $ermsg = 'WBMP images are not supported<br />';
                }
                break;

            default:
                $ermsg = $image_info['mime'] . ' images are not supported<br />';
                break;
        }
        if (!isset($ermsg)) {
            $o_wd = imagesx($o_im);
            $o_ht = imagesy($o_im);
            // thumbnail width = target * original width / original height
            $t_wd = round($o_wd*$t_ht/$o_ht);
            $t_im = imageCreateTrueColor($t_wd, $t_ht);
            imageCopyResampled($t_im, $o_im, 0, 0, 0, 0, $t_wd, $t_ht, $o_wd, $o_ht);
            $ext = strrchr($o_file, '.');
            if ($ext !== false) {
                $newfile = substr($o_file, 0, -strlen($ext));
            }
            $newfile = basename($newfile);
            $newfile = $newfile . "_" . time() . ".jpg";
            $newfile = $this->objConfig->getSiteRootPath() . $newfile;
            // touch($newfile);
            $ret = imageJPEG($t_im, $newfile);
            imageDestroy($o_im);
            imageDestroy($t_im);
            return $newfile;
        }
        return $ermsg;
    }
    /**
     * Method to parse the DSN
     *
     * @access public
     * @param  string $dsn
     * @return void
     */
    public function parseDSN($dsn)
    {
        $parsed = NULL;
        // $this->imapdsn;
        $arr = NULL;
        if (is_array($dsn)) {
            $dsn = array_merge($parsed, $dsn);
            return $dsn;
        }
        // find the protocol
        if (($pos = strpos($dsn, ':// ')) !== false) {
            $str = substr($dsn, 0, $pos);
            $dsn = substr($dsn, $pos+3);
        } else {
            $str = $dsn;
            $dsn = null;
        }
        if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
            $parsed['protocol'] = $arr[1];
            $parsed['protocol'] = !$arr[2] ? $arr[1] : $arr[2];
        } else {
            $parsed['protocol'] = $str;
            $parsed['protocol'] = $str;
        }
        if (!count($dsn)) {
            return $parsed;
        }
        // Get (if found): username and password
        if (($at = strrpos($dsn, '@')) !== false) {
            $str = substr($dsn, 0, $at);
            $dsn = substr($dsn, $at+1);
            if (($pos = strpos($str, ':')) !== false) {
                $parsed['user'] = rawurldecode(substr($str, 0, $pos));
                $parsed['pass'] = rawurldecode(substr($str, $pos+1));
            } else {
                $parsed['user'] = rawurldecode($str);
            }
        }
        // server
        if (($col = strrpos($dsn, ':')) !== false) {
            $strcol = substr($dsn, 0, $col);
            $dsn = substr($dsn, $col+1);
            if (($pos = strpos($strcol, '/')) !== false) {
                $parsed['server'] = rawurldecode(substr($strcol, 0, $pos));
            } else {
                $parsed['server'] = rawurldecode($strcol);
            }
        }
        // now we are left with the port and mailbox so we can just explode the string and clobber the arrays together
        $pm = explode("/", $dsn);
        $parsed['port'] = $pm[0];
        $parsed['mailbox'] = $pm[1];
        $dsn = NULL;
        return $parsed;
    }
    /**
     * Method to append config settings to the config.xml file
     *
     * @param array $newsettings
     */
    public function setupConfig($newsettings)
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objConfig->appendToConfig($newsettings);
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function pingGoogle($userid)
    {
        $objBk = $this->getObject('background', 'utilities');
        $status = $objBk->isUserConn();
        $callback = $objBk->keepAlive();
        $this->objProxy = $this->getObject('proxy', 'utilities');
        // set up for Google Blog API
        $changesURL = $this->uri(array(
            'module' => 'blog',
            'action' => 'feed',
            'userid' => $userid
        ));
        $name = $this->objUser->fullname($userid) . " Chisimba blog";
        $blogURL = $this->uri(array(
            'module' => 'blog',
            'action' => 'randblog',
            'userid' => $userid
        ));
        // OK lets put it together...
        $gurl = "http:// blogsearch.google.com/ping";
        // do the http request
        // echo $gurl;
        $gurl = str_replace('%26amp%3B', "&", $gurl);
        $gurl = str_replace('&amp;', "&", $gurl);
        $gurl = $gurl . "?name=" . urlencode($name) . "&url=" . urlencode($blogURL) . "&changesUrl=" . urlencode($changesURL);
        // get the proxy info if set
        $proxyArr = $this->objProxy->getProxy(NULL);
        // print_r($proxyArr); die();
        if (!empty($proxyArr)) {
            $parr = array(
                'proxy_host' => $proxyArr['proxyserver'],
                'proxy_port' => $proxyArr['proxyport'],
                'proxy_user' => $proxyArr['proxyusername'],
                'proxy_pass' => $proxyArr['proxypassword']
            );
        }
        // echo $gurl; die();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gurl);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($proxyArr)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxyserver'] . ":" . $proxyArr['proxyport']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxyusername'] . ":" . $proxyArr['proxypassword']);
        }
        $code = curl_exec($ch);
        curl_close($ch);
        switch ($code) {
            case "Thanks for the ping.":
                log_debug("Google blogs API Success! Google said: " . $code);
                break;

            default:
                log_debug("Google blogs API Failure! Google said: " . $code);
                break;
        }
    }
}
?>