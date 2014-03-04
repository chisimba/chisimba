<?php
/**
 * Class to generate a Podcast Rss Feed based on the Itunes.com DTD
 * @author Tohir Solomons
 */
class itunesrssgenerator extends object
{
    /**
     * Title of RSS Feed
     * @var string
     */
    public $title;
    
    /**
     * Link to this RSS Feed
     * @var string
     */
    public $rssfeedlink;
    
    /**
     * Description/Summary of RSS Feed
     * @var string
     */
    public $description;
    
    /**
     * Language of RSS Feed
     * @var string
     */
    public $language = 'en';
    
    /**
     * Copyright Information of RSS Feed
     * @var string
     */
    public $copyright;
    
    /**
     * Author of RSS Feed
     * @var string
     */
    public $author;
    
    /**
     * Email Address of Author of RSS Feed
     * @var string
     */
    public $email;
    
    /**
     * Categories the Podcast RSS Feed falls under
     * @var array
     */
    public $categories;
    
    /**
     * List of Podcast Items in the RSS Feed
     * @var array
     */
    private $items;
    
    /**
     * Last Build Date - Internally detected to be the date of the latest podcast
     * @var datetime
     */
    private $lastBuildDate;
    
    
    /**
     * Constructor
     */
    public function init()
    {
        
    }
    
    /**
     * Method to add a Podcast Entry to the Feed
     *
     * @param string $title          Title of the Podcast
     * @param string $link           Link to the Audio File
     * @param string $description    Description of the Podcast
     * @param datetime $date         Date Podcast was Created
     * @param string $author         Author of Podcast
     * @param string $mime           Mime Type of the Podcast Audio File
     * @param int $filesize          File Size of the Audio File
     * @param string $duration       Play Time of the Audio File
     * @param array $category        List of Categories Podcast falls under
     * @param string $keywords       Keywords for the Podcast
     */
    public function addItem($title, $link, $description, $date, $author, $mime='audio/mpeg', $filesize='', $duration='', $category=NULL, $keywords='')
    {
        $this->items[] = array('title'=>$title, 'link'=>$link, 'description'=>$description, 'date'=>$date, 'author'=>$author, 'mime'=>$mime, 'filesize'=>$filesize, 'duration'=>$duration, 'category'=>$category, 'keywords'=>$keywords);
        
        if ($date > $this->lastBuildDate) {
            $this->lastBuildDate = $date;
        }
    }
    
    /**
     * Method to generate the RSS Feed
     *
     * @return string
     */
    public function show()
    {
        $str = '<?xml version="1.0"?>
<rss xmlns:atom="http://www.w3.org/2005/Atom" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
<channel>';
        
        $str .= $this->getFeedInfo();
        
        $str .= $this->getPodcastInfo();
        
        $str .= "\r\n".'</channel>
</rss>';
        
        return $str;
    }
    
    /**
     * Method to generate the Rss Feed Podcast Info
     *
     * @return string
     */
    private function getFeedInfo()
    {
        $str = '';
        
        // Create Array for Storing Items
        $rssInfo = array();
        $itunesInfo = array();
        
        if (isset($this->title)) {
            $rssInfo[] = "\r\n".'<title>'.$this->title.'</title>';
        }
        
        if (isset($this->rssfeedlink)) {
            $rssInfo[] = "\r\n".'<link>'.$this->rssfeedlink.'</link>';
            $rssInfo[] = "\r\n".'<atom:link href="'.$this->rssfeedlink.'" rel="self" type="application/rss+xml"/>';
        }
        
        if (isset($this->author)) {
            $itunesInfo[] = "\r\n".'<itunes:author>'.$this->author.'</itunes:author>';
        }
        
        if (isset($this->description)) {
            $rssInfo[] = "\r\n".'<description>'.$this->description.'</description>';
            $itunesInfo[] = "\r\n".'<itunes:summary>'.$this->description.'</itunes:summary>';
        }
        
        if (isset($this->language)) {
            $rssInfo[] = "\r\n".'<language>'.$this->language.'</language>';
        }
        
        if (isset($this->copyright)) {
            $rssInfo[] = "\r\n".'<copyright>'.$this->copyright.'</copyright>';
        }
        
        if ($this->lastBuildDate != '') {
            // Date of Last Build - automatically generated
            $rssInfo[] = "\r\n".'<lastBuildDate>'.date('r', strtotime($this->lastBuildDate)).'</lastBuildDate>';
        }
        
        if (isset($this->author) || isset($this->email)) {
            $itunesInfo[] = "\r\n".'<itunes:owner>';
            
            if (isset($this->author)) {
                $itunesInfo[] = "\r\n".'<itunes:name>'.$this->author.'</itunes:name>';
            }
            
            if (isset($this->email)) {
                $itunesInfo[] = "\r\n".'<itunes:email>'.$this->email.'</itunes:email>';
            }
            
            $itunesInfo[] = "\r\n".'</itunes:owner>';
        }
        
        if (isset($this->categories) && is_array($this->categories)) {
            foreach ($this->categories as $category) {
                $rssInfo[] = "\r\n".'<category>'.$category.'</category>';
                $itunesInfo[] = "\r\n".'<itunes:category text="'.$category.'" />';	
            }
            
        }
        
        
        // Take RSS Feed Info, and add it to the output
        foreach ($rssInfo as $info)
        {
            $str .= $info;
        }
        
        // Take RSS Feed Info, and add it to the output
        foreach ($itunesInfo as $info)
        {
            $str .= $info;
        }
        
        return $str;
    }
    
    /**
     * Method to generate the Feed for the podcast items
     *
     * @return string
     */
    private function getPodcastInfo()
    {
        $str = '';
        
        foreach ($this->items as $podcast)
        {
            $str .= "\r\n".'<item>';
            
            
            $rssInfo = array();
            $itunesInfo = array();
            
            $rssInfo[] = "\r\n".'<title>'.$podcast['title'].'</title>';
            $rssInfo[] = "\r\n".'<link>'.$podcast['link'].'</link>';
            $rssInfo[] = "\r\n".'<description>'.$podcast['description'].'</description>';
            $rssInfo[] = "\r\n".'<guid>'.$podcast['link'].'</guid>';
            $rssInfo[] = "\r\n".'<enclosure url="'.$podcast['link'].'" length="'.$podcast['filesize'].'" type="'.$podcast['mime'].'" />';
            
            if (isset($this->rssfeedlink)) {
                $rssInfo[] = "\r\n".'<source url="'.$this->rssfeedlink.'"  />';
            }
            
            $rssInfo[] = "\r\n".'<pubDate>'.date('r', strtotime($podcast['date'])).'</pubDate>';
            
            
            $itunesInfo[] = "\r\n".'<itunes:author>'.$podcast['author'].'</itunes:author>';
            $itunesInfo[] = "\r\n".'<itunes:subtitle>'.$podcast['description'].'</itunes:subtitle>';
            
            if (isset($podcast['duration']) && $podcast['duration'] != '') {
                $itunesInfo[] = "\r\n".'<itunes:duration>'.$podcast['duration'].'</itunes:duration>';
            }
            
            foreach ($rssInfo as $info)
            {
                $str .= $info;
            }
            
            foreach ($itunesInfo as $info)
            {
                $str .= $info;
            }
            
            $str .= "\r\n".'</item>';
        }
        
        return $str;
    }
    
}

?>
