<?php
/**
 * feeder class
 * This class is an adapter class to create feeds for syndication
 * @author Paul Scott
 */
include("feedcreator_class_inc.php");

class feeder extends object
{
    public $objFeeder;
    public  $rssImage = null;
    public  $rssItem = null;

    public function init()
    {
        $this->objFeeder = new UniversalFeedCreator();
    }

    public function setupFeed($stylesheet = false, $title, $description, $link, $feedURL)
    {
        $this->objFeeder->useCached($stylesheet, $title, $description, $link, $feedURL);
        //check if the author wants a stylesheet associated...
        if($stylesheet == "true")
        {
            $this->objFeeder->xslStyleSheet = "http://manalang.com/wp-content/rss2.xsl";
        }

        $this->objFeeder->title = $title;
        $this->objFeeder->description = $description;
        $this->objFeeder->link = $link;
        $this->objFeeder->feedURL = $feedURL;

        //add an image if it exists
        if($this->rssImage != null)
        {
            $this->objFeeder->image = $this->rssImage;
        }

        //Add news item
        if($this->rssItem != null)
        {
            $this->objFeeder->addItem($this->rssItem);
        }


    }

    /**
     * Method to add an image to the feed
     */
    public function setrssImage($iTitle, $iURL, $iLink, $iDescription, $iTruncSize = 500, $desHTMLSyn = true)
    {
        $image = new FeedImage();
        $image->title = $iTitle; //"dailyphp.net logo";
        $image->url = $iURL; //"http://www.dailyphp.net/images/logo.gif";
        $image->link = $iLink; //"http://www.dailyphp.net";
        $image->description = $iDescription; //"Feed provided by dailyphp.net. Click to visit.";
        //optional
        $image->descriptionTruncSize = $iTruncSize;
        $image->descriptionHtmlSyndicated = $desHTMLSyn;
        return $this->rssImage = $image;

    }

    /**
     * Method to add an item
     */
    public function addItem($itemTitle, $itemLink, $itemDescription, $itemSource, $itemAuthor, $itemDate=NULL)
    {
        $item = new FeedItem();
        $item->title = $itemTitle; //"This is an the test title of an item";
        $item->link = $itemLink; //"http://localhost/item/";
        $item->description = $itemDescription; //"<b>description in </b><br/>HTML";
        //item->descriptionTruncSize = 500;
        $item->descriptionHtmlSyndicated = true;

        $item->date = is_null($itemDate) ? time() : $itemDate; // Needs to be unix timestamp
        $item->source = $itemSource; //"http://www.dailyphp.net";
        $item->author = $itemAuthor; //"John Doe";
        return $this->objFeeder->addItem($item);

    }

    /**
     * Method to output the feed
     */
    public function output($format = "RSS2.0", $filename="feed.xml")
    { //RSS0.91, RSS1.0, RSS2.0, PIE0.1, MBOX, OPML, ATOM0.3, HTML, JS
        switch ($format)
        {
            default: "RSS2.0";

            case "RSS0.91":
                return $this->objFeeder->saveFeed("RSS0.91", $filename);
                break;

            case "RSS1.0":
                return $this->objFeeder->saveFeed("RSS1.0", $filename);
                break;

            case "RSS2.0":
                return $this->objFeeder->saveFeed("RSS2.0", $filename);
                break;

            case "PIE0.1":
                return $this->objFeeder->saveFeed("PIE0.1", $filename);
                break;

            case "MBOX":
                return $this->objFeeder->saveFeed("MBOX", $filename);
                break;

            case "OPML":
                return $this->objFeeder->saveFeed("OPML", $filename);
                break;

            case "ATOM0.3":
                return $this->objFeeder->saveFeed("ATOM0.3", $filename);
                break;

            case "HTML":
                return $this->objFeeder->saveFeed("HTML", $filename);
                break;

            case "JS":
                return $this->objFeeder->saveFeed("JS", $filename);
                break;
        } //end switch
    } //end function
} //end class
?>