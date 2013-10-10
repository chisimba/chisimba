<?php
/**
* etdmap class extends object
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for creating a site map for reading by search engines
*
* @author Megan Watson
* @copyright (c) 2007 University of the Western Cape
* @version 0.1
*/

class etdmap extends object
{
    /**
    * @var string strXml The xml file as a string
    * @access private
    */
    private $strXml;
    
    /**
    * @var string openTag The opening tags for the xml file if it requires creation
    * @access private
    */
    private $openTag = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    
    /**
    * Constructor for the class
    *
    * @access public
    */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->baseDir = $this->objConfig->getsiteRootPath();
        $this->file = 'sitemap.xml';
        $this->strXml = '';
    }
    
    /**
    * Method to add a url to the site map
    *
    * @access public
    * @return void
    */
    public function addUrl($url, $date)
    {
        $urlXml = $this->createUrl($url, $date);
        $this->insertUrlToMap($urlXml);
    }
    
    /**
    * Method to remove a url from the site map
    *
    * @access public
    * @return void
    */
    public function removeUrl()
    {
    }
    
    /**
    * Method to create / recreate the site map 
    *
    * @access public
    * @return void
    */
    public function createMap()
    {
        // Get all records from the database
        $dbSubmit = $this->getObject('dbsubmissions', 'etd');
        $urls = $dbSubmit->getResourceUrls();

        // Add the opening tags for the xml file
        $this->strXml = $this->openTag;

        // Create the url xml
        if(!empty($urls)){
            foreach($urls as $item){
                $url = $item['dc_identifier'];
                $date = $item['datemodified'];
                if(empty($data)){
                    $date = $item['datecreated'];
                }
                $urlXml = $this->createUrl($url, $date);
                $this->insertUrlToMap($urlXml);
            }
        }
        
        // Write the map
        $this->writeMap();
    }
    
    /**
    * Method to create a new url
    *
    * @access private
    * @return array The url data
    */
    public function createUrl($url, $date)
    {
        // <changefreq>yearly <priority>0.5
        
        $strXml = "<url> \n<loc>{$url}</loc> \n<lastmod>{$date}</lastmod> \n</url>\n";
        return $strXml;
    }
    
    /**
    * Method to add a new url to the site map xml
    *
    * @access private
    * @param array $urlArr The url array
    * @return void
    */
    private function insertUrlToMap($strUrl)
    {
        $this->strXml .= $strUrl;
    }
    
    /**
    * Method to open the sitemap file
    *
    * @access public
    * @return void
    */
    public function readMap()
    {
        // Open file for reading
        if(!file_exists($this->baseDir.$this->file)){
            $this->strXml = '';
            return '';
        }
        
        try{
            $size = filesize($this->baseDir.$this->file);
            $fp = fopen($this->baseDir.$this->file, 'rb');
            $fileStr = fread($fp, filesize($this->baseDir.$this->file));
            fclose($fp);
            
            // Remove the closing xml tag
            $pos = strpos($fileStr, '</urlset>');
            $this->strXml = substr($fileStr, 0, $pos);
            
            return TRUE;
            
        }catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
        
        return FALSE;
    }
    
    /**
    * Method to write the file
    *
    * @access public
    * @return void
    */
    public function writeMap()
    {
        try{   
            // Add the closing xml tag
            $xmlStr = $this->strXml;
            $xmlStr .= '</urlset>';
            
            if(!file_exists($this->baseDir.$this->file)){
                touch($this->baseDir.$this->file);
                chmod($this->baseDir.$this->file, 0644);
                $xmlStr = $this->openTag.$xmlStr;
            }
            
            // Open file for writing
            $fp = fopen($this->baseDir.$this->file, 'wb');
            
            if(fwrite($fp, $xmlStr) === FALSE){
                chmod($this->baseDir.$this->file, 0644);
                fwrite($fp, $xmlStr);
            }
            fclose($fp);
            return TRUE;
        
        }catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
}
?>