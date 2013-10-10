<?php
/**
*
* Wrapper class for SimplePie. This wrapper was generated
* using the generate module of the Chisimba framework as
* developed by Derek Keats on his birthday in 2006. For
* further information about the class being wrapped, see
* the SimplePie documentation.
* @version $Id: spie_class_inc.php 13614 2009-06-05 19:20:42Z dkeats $
*
*/
class spie extends object
{

    public $objSimplePieWrapper;
    public $objConfig;
    public $useProxy=FALSE;

    /**
    *
    * Standard init method to initialize the class
    * (SimplePie) being wrapped.
    *
    */
    public function init()
    {
        // Get the config object.
        $this->objConfig = $this->getObject('altconfig', 'config');
        //Include the class file to wrap
        require_once($this->getResourcePath('simplepie.inc', "feed"));
        //Instantiate the class
        $this->objSimplePieWrapper = new SimplePie();
        // Set the cache location to usrfiles/feed/cache/
        $cacheLocation = $this->objConfig->getsiteRootPath() . "usrfiles/feed/cache/";
        $this->objSimplePieWrapper->set_cache_location($cacheLocation);
        //Check the proxy settings
        $this->checkProxy();
    }

    /**
    *
    * Set the limit for the number of feeds to display
    *
    * @param int $limit The number of feeds to display
    * @return VOID
    * @access public
    *
    */
    public function setLimit($limit=5)
    {
        $this->limit = $limit;
    }

    /**
    *
    * Method to extract the proxy settings from the chisimba settings
    * and set the useProxy to TRUE if settings found
    *
    * @access private
    * @return TRUE|FALSE
    *
    */
    private function checkProxy()
    {

        $proxy = $this->objConfig->getProxy();
        if ($proxy && $proxy !=="") {
            $this->useProxy=TRUE;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Check the proxy settings and start SimplePie
    * This just avoids having to replicate the code in every
    * method that uses it.
    *
    * @param string $url The URL of the feed
    * @return TRUE
    * @access public
    *
    */
    public function startPie($url)
    {
        if (!$this->useProxy) {
            $this->setFeedUrl($url);
        } else {
            // We are using a proxy so use the curl wrapper to return the string
            $objCurl = $this->getObject('curl', 'utilities');
            $rss = $objCurl->exec($url);
            $this->objSimplePieWrapper->set_raw_data($rss);
        }
        $this->objSimplePieWrapper->init();
        return TRUE;
    }


    /**
     * This is the URL of the feed you want to parse.
     *
     * This allows you to enter the URL of the feed you want to parse, or the
     * website you want to try to use auto-discovery on. This takes priority
     * over any set raw data.
     *
     * You can set multiple feeds to mash together by passing an array instead
     * of a string for the $url. Remember that with each additional feed comes
     * additional processing and resources.
     *
     * @access public
     * @param mixed $url This is the URL (or array of URLs) that you want to parse.
     * @see SimplePie::set_raw_data()
     */
    public function setFeedUrl($url)
    {
        return $this->objSimplePieWrapper->set_feed_url($url);
    }

    /**
    *
    * Get the feed provided by the given URL and hand off to
    * the display method indicated by $display. Valid methods
    * are:
    *   displayPlain (boring, title and description)
    *   displaySmart (try to figure out what feed it is and display accordingly)
    *
    * @param string $display The method to use to render the output
    * @param string $url The URL of the feed
    * @return string The rendered feed
    * @access public
    *
    */
    public function getFeed($url, $display="displayPlain")
    {
        $this->startPie($url);
        return $this->$display();
    }

    /**
    *
    * Get the title for the whole feed, and insert the logo if
    * there is a logo that exists. If no logo, then just use the
    * title.
    *
    * @return string The rendered title, with or without a logo
    * @access private
    *
    */
    private function getTitleWithLogo()
    {
        unset($logo);
        $title = $this->getTitle();
        if ($logo = $this->getImageUrl()) {
            $logo = '<img src="' . $logo
              . '" width="' . $this->getImageWidth()
              . '" height="' . $this->getImageHeight()
              . '" alt="' . $title . '" />';
            return '<div class="feed_render_title_forcewhite">'
              . '<table><tr><td>' . $logo . '</td><td><h3 style="color:black;">&nbsp;&nbsp;'
              . $title . '</h3></td></tr></table></div>';
        } else {
            return '<h3 class="feed_render_title">' . $title . '</h3><br />';
        }

    }

    /**
    *
    * Method to provice a div tag that goes above each feed.
    * This can be used to provide a tab above each feed
    * for example.
    *
    * @return string The div tag above each item
    * @access private
    *
    */
    private function getFeedTop()
    {
        return '<div class="feed_render_feedtop"></div>';
    }

    /**
    *
    * Method to provice a div tag that goes blow each feed.
    * This can be used to provide a tab above each feed
    * for example.
    *
    * @return string The div tag below each item
    * @access private
    *
    */
    private function getFeedBottom()
    {
        return '<div class="feed_render_feedbottom"></div>';
    }

    /**
    *
    * Method to provice a div tag that goes above each item in
    * a feed. This can be used to provide a tab above each item
    * for example.
    *
    * @return string The div tag above each item
    * @access private
    *
    */
    private function getItemTop()
    {
        return '<div class="feed_render_top"></div>';
    }

    /**
    *
    * Method to provice a div tag that goes bllow each item in
    * a feed. This can be used to provide a tab below each item
    * for example.
    *
    * @return string The div tag above each item
    * @access private
    *
    */
    private function getItemBottom()
    {
        return '<div class="feed_render_bottom"></div>';
    }

    /**
    *
    * Get the feeds and display only a list of fields
    *
    * @param string $url The URL of the feed
    * @param string $fields An array of fields to display
    * @return string The rendered feed
    * @access public
    */
    public function getFields($url, $fields)
    {
        $this->startPie($url);
        $ret = $this->getTitleWithLogo();
        $counter = 0;
        foreach ($this->objSimplePieWrapper->get_items() as $item) {
            $counter++;
            // Now get the fields
            $ret .= $this->getFeedTop()
              . '<div class="feed_render_default">';
            foreach ($fields as $field) {
                if ($field=="pubDate") {
                    $field="date";
                }
                $method = "get_" . $field;
                $$field = $item->$method();
                if ($field == "title") {
                    $ln = $item->get_link();
                    $ret .= '<a href="' . $ln . '">' . $$field . '</a><br />';
                } else {
                    if ($field == "date") {
                        $ret .= '<p class="feed_render_date">'
                          .  $item->get_date('j F Y | g:i a') . '</p>';
                    } else {
                        $ret .= $$field . "<br />";
                    }
                }
            }
            $ret .= '</div>' . $this->getFeedBottom();
            if (isset($this->limit)) {
                if ($counter==$this->limit) {
                    break;
                }
            }
        }
        return $ret;
    }

    /**
    *
    * Render the output as a plain display of Title and description
    *
    * @return string the formatted output
    * @access private
    *
    */
    private function displayPlain()
    {
        $title = $this->getTitle();
        $ret = $this->getTitleWithLogo();
        $counter=0;
        $objHumanizeDate = $this->getObject("translatedatedifference", "utilities");
        foreach ($this->objSimplePieWrapper->get_items() as $item) {
            $counter++;
            $rawDate =  strtotime($item->get_date('j F Y  g:i a'));
            $rawDate = date('Y-m-d H:i:s', $rawDate);
            $humanTime = $objHumanizeDate->getDifference($rawDate);
            $ret .= $this->getItemTop()
              . '<div class="feed_render_default">'
              . '<p class="feed_render_link"><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></p>'
              . '<p class="feed_render_description">' . $item->get_description() . '</p>'
              . '<p class="feed_render_date">' .  $humanTime . '</p>'
              . '</div>' . $this->getItemBottom();
            if (isset($this->limit)) {
                if ($counter==$this->limit) {
                    break;
                }
            }
        }
        return $ret;
    }

    /**
     *
     * Try to figure out what kind of feed we have and be a bit
     * smart about how it is rendered. It uses some criteria to
     * identify known feed sources, and calls the appropriate method
     * to render them. It degrades to displayPlain if it does not
     * recognize the source.
     *
     * @return string the formatted output
     * @access private
     *
     */
    private function displaySmart()
    {
        $title = $this->getTitle();
        if ($this->isTwitterSearch($title)) {
            return $this->twitterSearch();
        }
        $permaLink = $this->getPermalink();
        if ($this->isYouTube($permaLink)) {
            return $this->youTubeFeed();
        }
        if ($this->isSlideShare($permaLink)) {
            return $this->slideShareFeed();
        }
        // Degrade to the plain display so as not to fail when it cannot identify feed
        return $this->displayPlain();
    }

    /**
    *
    * Process the results of a feed from a twitter search. This
    * avoids the title and description (which are the same in the
    * feed) being duplicated, and caters for a bug with links in the
    * title in the current version of SimplePie.
    *
    * @return string The rendered feed
    *
    */
    public function twitterSearch()
    {
        $ret = $this->getTitleWithLogo();
        $counter=0;
        $avatar = "";
        $objHumanizeDate = $this->getObject("translatedatedifference", "utilities");
        foreach ($this->objSimplePieWrapper->get_items() as $item) {
            $counter++;
            $author = $item->get_author();
            $name = $author->get_name();
            //$lns = $item->get_links();
            $ln = $author->get_link();
            if ($avatars = $item->get_links('image') ) {
                $avatar = $avatars[0];
                $avatar =  '<img src="' . $avatar . '">&nbsp;';
            }
            $nickAr = explode(" (", $name);
            $nick = "<a href=\"" . $ln . "\">" . $nickAr[0] . "</a>:&nbsp;&nbsp;";
            $description = $item->get_description();
            $info = '<table><tr><td>' . $avatar
              . '</td><td>' . $nick . " "
              . $description . '</td></tr></table>';
            $rawDate =  strtotime($item->get_date('j F Y  g:i a'));
            $rawDate = date('Y-m-d H:i:s', $rawDate);
            $humanTime = $objHumanizeDate->getDifference($rawDate);
            $ret .= $this->getItemTop()
              . '<div class="feed_render_default">'
              . '<p class="feed_render_description">' . $info
              . '<br /><span class="feed_render_date">'
              .  $humanTime . '</span></p>'
              . '</div>' . $this->getItemBottom();

            if (isset($this->limit)) {
                if ($counter==$this->limit) {
                    break;
                }
            }
        }
        unset($author, $name, $ln, $nickAr, $nick, $description, $info);
        return $ret;
    }

    /**
    *
    * Process the output of a YouTube feed. This avoids the title
    * being repeated since it is already part of the description. It
    * also inserts the YouTube logo
    *
    * @return string The rendered feed
    *
    */
    public function youTubeFeed()
    {
        // YouTube has some really ugly feeds
        $permaLink = $this->getPermalink();
        if (!$this->isYoutTubeStandards($permaLink)) {
            $standardsFeed = FALSE;
        }
        $ret = $this->getTitleWithLogo();
        $counter=0;
        $objHumanizeDate = $this->getObject("translatedatedifference", "utilities");
        foreach ($this->objSimplePieWrapper->get_items() as $item) {
            $counter++;
            $description = $item->get_description();
            if (!$standardsFeed) {
                $title = $item->get_title();
                $ln = $item->get_link();
                $title = '<a href="' . $ln . '">'. $title . '</a>';
                $description = str_replace("align=\"right\"", "style=\"float:left; margin-left: 5px; margin-right: 20px;\"", $description);
                $description = $title . "<br />" . $description;
            }
            $rawDate =  strtotime($item->get_date('j F Y  g:i a'));
            $rawDate = date('Y-m-d H:i:s', $rawDate);
            $humanTime = $objHumanizeDate->getDifference($rawDate);
            $ret .= $this->getItemTop()
              . '<div class="feed_render_default">'
              . '<p class="feed_render_description">' . $description
              . '<br /><span class="feed_render_date">'
              .  $humanTime
              . '</span></p>'
              . '</div>' . $this->getItemBottom();;
            if (isset($this->limit)) {
                if ($counter==$this->limit) {
                    break;
                }
            }
        }
        unset($title, $description, $logo);
        return $ret;

    }

    /**
    * Process the output of a SlideShare feed
    *
    * Slideshare embeds its whole layout in a CDATA tag and does not give
    * any control over layout. This sucks. It floats the thumbnail right, which looks very ugly.
    * Thus we take it and float it left
    *
    * @return string The rendered feed
    *
    */
    public function slideShareFeed()
    {
        $title = $this->getTitle();
        $ret = $this->getTitleWithLogo();
        $counter=0;
        $objHumanizeDate = $this->getObject("translatedatedifference", "utilities");
        foreach ($this->objSimplePieWrapper->get_items() as $item) {
            $counter++;
            $description = $item->get_description();
            $description = str_replace("float:right;", "float:left; margin-left: 5px; margin-right: 20px;", $description);
            $title = $item->get_title();
            $ln = $item->get_link();
            $rawDate =  strtotime($item->get_date('j F Y  g:i a'));
            $rawDate = date('Y-m-d H:i:s', $rawDate);
            $humanTime = $objHumanizeDate->getDifference($rawDate);
            $ret .= $this->getItemTop()
              . '<div class="feed_render_default">'
              . '<p class="feed_render_description"><a href="'
              . $ln . '">' . $title . '</a><br />' . $description
              . '<br /><span class="feed_render_date">'
              .  $humanTime
              . '</span></p>'
              . '</div>' . $this->getItemBottom();
            if (isset($this->limit)) {
                if ($counter==$this->limit) {
                    break;
                }
            }
        }
        return $ret;
    }





    // --------------- Methods for determining the search type
    /**
     *
     * Method to determine if a feed is a twitter search feed
     *
     * @param string Title The title from the feed
     * @return TRUE|FALSE
     * @access private
     *
     */
    private function isTwitterSearch($title)
    {
        // Check for Twitter search results
        $twitterSearch = stripos($title, "Twitter Search");
        if (!$twitterSearch == FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Method to determine if a feed is a Youtube feed
     *
     * @param string $permaLink The permaLink from the feed
     * @return TRUE|FALSE
     * @access private
     *
     */
    private function isYouTube($permaLink)
    {
        $yt = stripos($permaLink, "youtube.com");
        if (!$yt == FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function isYoutTubeStandards($permaLink)
    {
        $yt = stripos($permaLink, "standards");
        if (!$yt == FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Method to determine if a feed is a Slideshare feed
     *
     * @param string $permaLink The permaLink from the feed
     * @return TRUE|FALSE
     * @access private
     *
     */
    public function isSlideShare($permaLink)
    {
        $ss = stripos($permaLink, "slideshare.net");
        if (!$ss == FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }

    }
    // --------------- End methods for determining search type


    /**
    *
    * Wrapper method for get_title in the SimplePie
    * class being wrapped. See that class for details of the
    * get_titlemethod.
    *
    * Gets the title of the channel
    *
    * @return String The feed title
    * @access Public
    *
    */
    public function getTitle()
    {
        return $this->objSimplePieWrapper->get_title();
    }

    /**
    *
    * Wrapper method for get_permalink in the SimplePie
    * class being wrapped. See that class for details of the
    * get_permalinkmethod.
    *
    * Gets the permalink of the channel
    *
    * @return String The feed permalink
    * @access Public
    *
    */
    public function getPermalink()
    {
        return $this->objSimplePieWrapper->get_permalink();
    }

    /**
    *
    * Wrapper method for get_image_title in the SimplePie
    * class being wrapped. See that class for details of the
    * get_image_titlemethod.
    *
    * @return String The logo Title
    * @access Public
    *
    */
    public function getImageTitle()
    {
        return $this->objSimplePieWrapper->get_image_title();
    }

    /**
    *
    * Wrapper method for get_image_url in the SimplePie
    * class being wrapped. See that class for details of the
    * get_image_urlmethod.
    *
    * @return String The Logo image URL
    * @access Public
    *
    */
    public function getImageUrl()
    {
        return $this->objSimplePieWrapper->get_image_url();
    }

    /**
    *
    * Wrapper method for get_image_link in the SimplePie
    * class being wrapped. See that class for details of the
    * get_image_linkmethod.
    *
    * @return String The logo image Link
    * @access Public
    *
    */
    public function getImageLink()
    {
        return $this->objSimplePieWrapper->get_image_link();
    }

    /**
    *
    * Wrapper method for get_image_width in the SimplePie
    * class being wrapped. See that class for details of the
    * get_image_widthmethod.
    *
    * @return String The logo image width
    * @access Public
    *
    */
    public function getImageWidth()
    {
        return $this->objSimplePieWrapper->get_image_width();
    }

    /**
    *
    * Wrapper method for get_image_height in the SimplePie
    * class being wrapped. See that class for details of the
    * get_image_heightmethod.
    *
    * @return String The logo image height
    * @access Public
    *
    */
    public function getImageHeight()
    {
        return $this->objSimplePieWrapper->get_image_height();
    }









    //--------------------- PLEASE NOTE --------------------------//
    /*
     * I am working here. This is incomplete. Documentation will be inserted
     * as I update or add functionality
     *
     * @Todo --
     *  1. Deal with proxy settings (can SimplePie handle it?)
     *
     *
     */



    /**
    *
    * Wrapper method for get_author in the SimplePie
    * class being wrapped. See that class for details of the
    * get_authormethod.
    *
    * This returns the author of a feed identified by key
    *
    * @access public
    *
    */
    public function getAuthor($key=0)
    {
        return $this->objSimplePieWrapper->get_author($key);
    }



    /**
    *
    * Wrapper method for get_description in the SimplePie
    * class being wrapped. See that class for details of the
    * get_descriptionmethod.
    *
    */
    public function get_description()
    {
        return $this->objSimplePieWrapper->get_description();
    }

    /**
    *
    * Wrapper method for get_copyright in the SimplePie
    * class being wrapped. See that class for details of the
    * get_copyrightmethod.
    *
    */
    public function get_copyright()
    {
        return $this->objSimplePieWrapper->get_copyright();
    }

    /**
    *
    * Wrapper method for get_language in the SimplePie
    * class being wrapped. See that class for details of the
    * get_languagemethod.
    *
    */
    public function get_language()
    {
        return $this->objSimplePieWrapper->get_language();
    }

    /**
    *
    * Wrapper method for get_latitude in the SimplePie
    * class being wrapped. See that class for details of the
    * get_latitudemethod.
    *
    */
    public function get_latitude()
    {
        return $this->objSimplePieWrapper->get_latitude();
    }

    /**
    *
    * Wrapper method for get_longitude in the SimplePie
    * class being wrapped. See that class for details of the
    * get_longitudemethod.
    *
    */
    public function get_longitude()
    {
        return $this->objSimplePieWrapper->get_longitude();
    }



    /**
    *
    * Wrapper method for get_item_quantity in the SimplePie
    * class being wrapped. See that class for details of the
    * get_item_quantitymethod.
    *
    */
    public function get_item_quantity($max)
    {
        return $this->objSimplePieWrapper->get_item_quantity($max);
    }

    /**
    *
    * Wrapper method for get_item in the SimplePie
    * class being wrapped. See that class for details of the
    * get_itemmethod.
    *
    */
    public function get_item($key)
    {
        return $this->objSimplePieWrapper->get_item($key);
    }

    /**
    *
    * Wrapper method for get_items in the SimplePie
    * class being wrapped. See that class for details of the
    * get_itemsmethod.
    *
    */
    public function get_items($start=0, $end=0)
    {
        return $this->objSimplePieWrapper->get_items($start,$end);
    }

    /**
    *
    * Wrapper method for sort_items in the SimplePie
    * class being wrapped. See that class for details of the
    * sort_itemsmethod.
    *
    */
    public function sort_items($a,$b)
    {
        return $this->objSimplePieWrapper->sort_items($a,$b);
    }

    /**
    *
    * Wrapper method for merge_items in the SimplePie
    * class being wrapped. See that class for details of the
    * merge_itemsmethod.
    *
    */
    public function merge_items($urls,$start,$end,$limit)
    {
        return $this->objSimplePieWrapper->merge_items($urls,$start,$end,$limit);
    }
    
    /**
    *
    * Wrapper method for force_feed in the SimplePie
    * class being wrapped. See that class for details of the
    * force_feed method.
    *
    * @return Void
    * @access Public
    * @param BOOLEAN TRUE|FALSE
    *
    */
    public function forceFeed($enable)
    {
        return $this->objSimplePieWrapper->force_feed($enable);
    }

}
?>
