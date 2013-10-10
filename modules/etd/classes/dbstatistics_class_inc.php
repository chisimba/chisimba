<?php
/**
* dbStatistics class extends dbTable
* @package etd
* @filesource
*/

/**
* Class for calculating and displaying statistics on the resources
* @author Megan Watson
* @copyright (c) 2006 University of the Western Cape
* @version 0.1
*/

class dbStatistics extends dbTable
{
    
    /**
    * Constructor for the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        parent::init('tbl_etd_statistics');
        $this->table = 'tbl_etd_statistics';
                
        $this->etdDbSubmissions = $this->getObject('dbsubmissions', 'etd');
                
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objUserStats = $this->getObject('dbuserstats', 'sitestats');  
        $this->objLoginHistory = $this->getObject('dbloginhistory', 'userstats');
        $this->objIpCountry = $this->getObject('iptocountry', 'utilities');
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('layer', 'htmlelements');
        $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
        
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('tabbedbox', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('tabbedbox', 'htmlelements');

        $this->userId = NULL;
        if($this->objUser->isLoggedIn()){
            $this->userId = $this->objUser->userId();
        }
    }
    
    /**
    * Method to 'patch' the statistics table - update all the new submissions
    *
    public function patchStats()
    {
        // Get all the new submissions - date and submitId
        $sql = "SELECT t.submitid, d.enterdate FROM tbl_etd_metadata_thesis t, tbl_dublincoremetadata d
            WHERE d.id = t.dcmetaid";
        $data = $this->getArray($sql);
        
        // Insert into the stats table
        if(!empty($data)){
            foreach($data as $item){
                $fields = array();
                $fields['submitid'] = $item['submitid'];
                $fields['hittype'] = 'upload';
                $fields['ipaddress'] = '172.16.70.241';
                $fields['countrycode'] = 'ZA';
                $fields['creatorid'] = '8027070305';
                $fields['datecreated'] = $item['enterdate'];
                
                $this->insert($fields);
            }
        }
    }
    */
    
    /**
    * Method to add a new statistic.
    * Type = hit to the site, view on a resource, download of a resource
    *
    * @access private
    * @param string $type The type of hit
    * @param string $submitId The submission if the hit is on a resource
    * @return
    */
    private function addStatistic($type = 'hit', $submitId = NULL)
    {   
        $ip = $_SERVER['REMOTE_ADDR'];
        $code = $this->objIpCountry->getCountryByIP($ip);
                 
        $fields = array();
        $fields['submitid'] = $submitId;
        $fields['hittype'] = $type;
        $fields['ipaddress'] = $ip;
        $fields['countrycode'] = $code;
        $fields['creatorid'] = $this->userId;
        $fields['datecreated'] = $this->now();
        $this->insert($fields);
    }
    
    /**
    * Method to get a statistic
    *
    * @access private
    * @param string $type
    * @param string $submitId
    * @return array
    */
    private function getStatistic($type = 'hit', $submitId = NULL)
    {
        $sql = "SELECT COUNT(*) AS count FROM {$this->table}";
        $sql .= " WHERE hittype = '$type'";
        if(isset($submitId) && !empty($submitId)){
            $sql .= " AND submitid = '$submitId'";
        }
        
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0]['count'];
        }
        return 0;
    }
    
    /**
    * Method to get the most downloaded and most viewed statistics
    *
    * @access private
    * @return array $data
    */
    private function getMostViewed()
    {
        $sql = "SELECT count(s.submitid) AS count, s.submitid, s.hittype, d.dc_title 
            FROM {$this->table} s, tbl_etd_metadata_thesis t, tbl_dublincoremetadata d
            WHERE s.hittype='visit' AND s.submitid = t.id AND t.dcmetaid = d.id GROUP BY s.submitid, s.hittype, d.dc_title ORDER BY count DESC LIMIT 5";
        
        $data = $this->getArray($sql);
        
        $sql2 = "SELECT count(s.submitid) AS count, s.submitid, s.hittype, d.dc_title 
            FROM {$this->table} s, tbl_etd_metadata_thesis t, tbl_dublincoremetadata d
            WHERE s.hittype='download' AND s.submitid = t.id AND t.dcmetaid = d.id GROUP BY s.submitid, s.hittype, d.dc_title ORDER BY count DESC LIMIT 5";
        
        $data2 = $this->getArray($sql2);
        
        return array('visit' => $data, 'download' => $data2);
    }
    
    /**
    * Method to get the statistics by month
    *
    * @access private
    * @param string $type
    * @return array $data
    */    
    private function getStatsByMonth($type)
    {
        $year = date('Y');
        
        $sql = "SELECT count(*) as cnt, EXTRACT(MONTH FROM datecreated) as month FROM {$this->table}";
        $sql .= " WHERE EXTRACT(YEAR FROM datecreated) = '$year' AND hittype = '$type' 
            GROUP BY EXTRACT(MONTH FROM datecreated) ORDER BY EXTRACT(MONTH FROM datecreated)";

        
        $data = $this->getArray($sql);
        
        return $data;
    }
    
    /**
    * Method to get the statistics by month
    *
    * @access private
    * @param string $type
    * @return array $data
    */    
    private function getStatsByMonthCountry($type)
    {
        $year = date('Y');
        
        $sql = "SELECT count(*) as cnt, EXTRACT(MONTH FROM datecreated) as month, countrycode FROM {$this->table}";
        $sql .= " WHERE EXTRACT(YEAR FROM datecreated) = '$year' AND hittype = '$type' 
            GROUP BY EXTRACT(MONTH FROM datecreated), countrycode ORDER BY EXTRACT(MONTH FROM datecreated)";

        
        $data = $this->getArray($sql);
        
        return $data;
    }
    
    /**
    * Method to get the statistics by country
    *
    * @access private
    * @param string $type
    * @return array $data
    */    
    private function getCountryStats($type)
    {
        $sql = "SELECT count(*) as cnt, countrycode FROM {$this->table}";
        $sql .= " WHERE hittype = '$type' GROUP BY countrycode";

        $data = $this->getArray($sql);
        return $data;
    }
    
    /**
    * Method to return all the countries represented by visitors to the site with the number of visits per country.
    *
    * @access private
    * @return array $countries
    */
    private function getCountries()
    {
        $sql = 'SELECT DISTINCT(countrycode), count(*) as cnt FROM '.$this->table.' group by countrycode';
        
        $data = $this->getArray($sql);
        
        return $data;
    }
    
    /**
    * Method to record a hit on the site
    * The method checks session to see if this is a new user or the current one
    *
    * @access public
    * @return
    */
    public function recordHit()
    {
        $check = $this->getSession('newhit');
        
        if($check == 'yes'){
            return TRUE;
        }
        $this->setSession('newhit', 'yes');
        $this->addStatistic('hit');
        return FALSE;
    }
    
    /**
    * Method to record a visit to a resource
    * The method checks session to see if the user is still busy on the same resource
    *
    * @access public
    * @param string $submitId
    * @return
    */
    public function recordVisit($submitId)
    {
        $check = $this->getSession('newvisit');
        
        if($check == $submitId){
            return TRUE;
        }
        $this->setSession('newvisit', $submitId);
        $this->addStatistic('visit', $submitId);
        return FALSE;
    }
    
    /**
    * Method to record the download of a resource
    * The method uses the submitId recorded in session from the hit to the resource
    *
    * @access public
    * @return
    */
    public function recordDownload()
    {
        $submitId = $this->getSession('newvisit');
        
        $this->addStatistic('download', $submitId);
        return FALSE;
    }

    /**
    * Method to record the submission of a new resource
    * The method uses the submitId recorded in session from the hit to the archive
    *
    * @access public
    * @return
    */
    public function recordUpload($submitId)
    {        
        $this->addStatistic('upload', $submitId);
        return FALSE;
    }

    /**
    * Method to show the statistics for a resource
    *
    * @access public
    * @param string $submitId
    * @return string html
    */
    public function showResourceStats($submitId)
    {
        $hits = $this->getStatistic('visit', $submitId);
        $downloads = $this->getStatistic('download', $submitId);
        
        $lbStats = $this->objLanguage->languageText('word_statistics');
        $lbHits = $this->objLanguage->languageText('mod_etd_thisresourcehasbeenvisited', 'etd');
        $lbDownloads = $this->objLanguage->languageText('mod_etd_thisresourcehasbeendownloaded', 'etd');
        $lnViewMonth = $this->objLanguage->languageText('mod_etd_viewbymonth', 'etd');
        $lbTimes = strtolower($this->objLanguage->languageText('word_times'));
        $lbTime = strtolower($this->objLanguage->languageText('word_time'));
        
        $str = '<p>'.$lbHits.'&nbsp;'.$hits.'&nbsp;';
        if($hits == 1){
            $str .= $lbTime;
        }else{
            $str .= $lbTimes;
        }
        $str .= '</p>';
        
        $str .= '<p>'.$lbDownloads.'&nbsp;'.$downloads.'&nbsp;';
        if($downloads == 1){
            $str .= $lbTime;
        }else{
            $str .= $lbTimes;
        }
        $str .= '</p>';
        
        $objTab = new tabbedbox();
        $objTab->extra = 'style="background-color: #FCFAF2; padding: 2px;"';
        $objTab->addTabLabel($lbStats);
        $objTab->addBoxContent($str);
        
        return $objTab->show();
    }
        
    /**
    * Method to generate the user statistics
    *
    * @access public
    * @return string html
    */
    public function getUserStats()
    {
        $userCount = $this->objUserStats->countUsers();
        $countryCount = $this->objUserStats->getTotalCountries();
        $totalLogins = $this->objLoginHistory->getTotalLogins();
        $countries = $this->objUserStats->getFlags();
        
        $hdUser = $this->objLanguage->languageText('phrase_userstats');
        $lbTotalUsers = $this->objLanguage->languageText('phrase_totalusers');
        $lbTotalLogins = $this->objLanguage->languageText('mod_etd_totallogins', 'etd');
        $lbUserCountries = $this->objLanguage->languageText('mod_etd_numcountriesrepresented', 'etd');
        
        $str = '<p>'.$lbTotalUsers.': '.$userCount.'</p>';
        $str .= '<p>'.$lbTotalLogins.': '.$totalLogins.'</p>';
        $str .= '<p>'.$lbUserCountries.': '.$countryCount.'</p>';
        
        $objLayer = new layer();
        $objLayer->str = $countries;
        $objLayer->padding = '0px; padding-left: 10px';
        $str .= $objLayer->show();
        
        $objTab = new tabbedbox();
        $objTab->extra = 'style="background-color: #FCFAF2; padding: 5px;"';
        $objTab->addTabLabel($hdUser);
        $objTab->addBoxContent($str);
        
        return $this->objFeatureBox->showContent($hdUser, $str); //$objTab->show();
    }
    
    /**
    * Method to generate the site statistics
    *
    * @access private
    * @return string html
    */
    private function getSiteStats()
    {
        $siteVisitsCount = $this->getStatistic('hit');
        $countries = $this->getCountries();
        
        $hdSite = $this->objLanguage->languageText('phrase_sitestats');
        $lbVisits = $this->objLanguage->languageText('mod_etd_visitstosite', 'etd');
        $lbCountries = $this->objLanguage->languageText('mod_etd_countriesrepresented', 'etd');
        
        $str = '<p>'.$lbVisits.': '.$siteVisitsCount.'</p>';
        
        $i = 0; $flags = '';
        if(!empty($countries)){
            foreach($countries as $item){
                if(!empty($item['countrycode'])){
                    $i++;
                    $image = $this->objIpCountry->getCountryFlag($item['countrycode']);
                    $country = $this->objIpCountry->getCountryName($item['countrycode']);
                                        
                    $flags .= "<img src = '$image' alt = '$country' title = '$country' />&nbsp;&nbsp;";
                }
            }
        }
        
        $str .= '<p>'.$lbCountries.': '.$i.'</p>';
        
        $objLayer = new layer();
        $objLayer->str = $flags;
        $objLayer->padding = '5px; padding-left: 10px';
        $str .= $objLayer->show();
                
        $objTab = new tabbedbox();
        $objTab->extra = 'style="background-color: #FCFAF2; padding: 5px;"';
        $objTab->addTabLabel($hdSite);
        $objTab->addBoxContent($str);
        
        return $this->objFeatureBox->showContent($hdSite, $str); //$objTab->show();        
    }
    
    /**
    * Method to display the most viewed and most downloaded resources
    *
    * @access private
    * @return string html
    */
    private function showMostViewed()
    {
        $data = $this->getMostViewed();
        
        // Configure the data for display.
        $row = array();
        if(!empty($data)){
            foreach($data as $key => $item){
                $i = 0;
                foreach($item as $val){
                    $row[$i][$key]['id'] = $val['submitid'];
                    $row[$i++][$key]['title'] = $val['dc_title'];
                }
            }
        }
        
        $hdViewed = $this->objLanguage->languageText('mod_etd_mostviewedresources', 'etd');
        $hdDownload = $this->objLanguage->languageText('mod_etd_mostdownloadedresources', 'etd');
        
        $objTable = new htmltable();
        $objTable->width = '70%';
        $objTable->border = '1';
        $objTable->cellpadding = '5';
        
        $hdArr = array();
        $hdArr[] = $hdViewed;
        $hdArr[] = $hdDownload;
        
        $objTable->addHeader($hdArr);
        
        
        // Display the data
        if(!empty($row)){
            $class = 'even';
            foreach($row as $item){
                $class = ($class == 'odd') ? 'even' : 'odd';
                
                $lnVisit = ''; $lnDownload = '';
                // Create link to view the resource
                if(isset($item['visit']['title'])){
                    $objLink = new link($this->uri(array('action' => 'viewtitle', 'id' => $item['visit']['id'])));
                    $objLink->link = $item['visit']['title'];
                    $lnVisit = $objLink->show();
                }
                
                if(isset($item['download']['title'])){
                    $objLink = new link($this->uri(array('action' => 'viewtitle', 'id' => $item['download']['id'])));
                    $objLink->link = $item['download']['title'];
                    $lnDownload = $objLink->show();
                }
                
                $rowArr = array();
                $rowArr[] = $lnVisit;
                $rowArr[] = $lnDownload;
                
                $objTable->addRow($rowArr, $class);
            }
        }
        
        return '<div align="center">'.$objTable->show().'</div>';
    }
    
    /**
    * Method to generate the statistics by resource
    *
    * @access private
    * @param bool $breakdown True if monthly statistics should be broken down by country
    * @return string html
    */
    private function getStatsByResource($breakdown = FALSE)
    {  
        $resVisitsCount = $this->getStatistic('visit');
        $downloadsCount = $this->getStatistic('download');
        $resourceCount = $this->etdDbSubmissions->getCount();
        
        // Get stats by month & country
        $monthVisit = $this->getStatsByMonthCountry('visit');
        $monthDownload = $this->getStatsByMonthCountry('download');
        $monthUpload = $this->getStatsByMonthCountry('upload');
        
        $aveResourceVisits = 0;
        $aveResourceDownloads = 0;
        if($resourceCount > 0){
            $aveResourceVisits = round($resVisitsCount/$resourceCount,0);
            $aveResourceDownloads = round($downloadsCount/$resourceCount,0);
        }
                
        $hdResource = $this->objLanguage->languageText('phrase_resourcestats');
        $lbTotalResource = $this->objLanguage->languageText('mod_etd_totalresourcesavailable', 'etd');
        $lbTotalDownloads = $this->objLanguage->languageText('mod_etd_totalresourcesdownloaded', 'etd');
        $lbTotalHits = $this->objLanguage->languageText('mod_etd_totalvisitstoresources', 'etd');
        $lbAveRes = $this->objLanguage->languageText('mod_etd_avevisitsperresource', 'etd');
        $lbAveDown = $this->objLanguage->languageText('mod_etd_avedownloadsperresource', 'etd');
        $lnView = $this->objLanguage->languageText('mod_etd_viewbycountry', 'etd');
        $lnView2 = $this->objLanguage->languageText('mod_etd_viewbymonth', 'etd');
        $lbMonth = $this->objLanguage->languageText('word_month');
        $lbCountry = $this->objLanguage->languageText('word_country');
        $lbVisits = $this->objLanguage->languageText('word_visits');
        $lbDownloads = $this->objLanguage->languageText('word_downloads');
        $lbUploads = $this->objLanguage->languageText('phrase_newsubmissions');
        $lnBreak = $this->objLanguage->languageText('phrase_breakdownbycountry');
                
        $objTable = new htmltable();
        $objTable->cellpadding = 5;
        
        $objTable->addRow(array($lbTotalResource.': '.$resourceCount));
        
        $objTable->startRow();
        $objTable->addCell($lbTotalHits.': '.$resVisitsCount, '40%');
        $objTable->addCell($lbAveRes.': '.$aveResourceVisits);
        $objTable->endRow();
        
        $objTable->addRow(array($lbTotalDownloads.': '.$downloadsCount, $lbAveDown.': '.$aveResourceDownloads));
        
        $str = $objTable->show();
        $str .= '<br /><br />';
        
        $str .= $this->showMostViewed();
        $str .= '<br /><br />';
        
        // Organise stats into an array for easy display
        $hitArr = array();
        if(!empty($monthVisit)){
            foreach($monthVisit as $item){
                $hitArr[$item['month']]['visit'] = isset($hitArr[$item['month']]['visit']) ? $hitArr[$item['month']]['visit']+$item['cnt'] : $item['cnt'];
                $mntArr[$item['month']][$item['countrycode']]['visit'] = $item['cnt'];
            }
        }
        if(!empty($monthDownload)){
            foreach($monthDownload as $item){
                $hitArr[$item['month']]['download'] = isset($hitArr[$item['month']]['download']) ? $hitArr[$item['month']]['download']+$item['cnt'] : $item['cnt'];
                $mntArr[$item['month']][$item['countrycode']]['download'] = $item['cnt'];
            }
        }
        if(!empty($monthUpload)){
            foreach($monthUpload as $item){
                $hitArr[$item['month']]['upload'] = isset($hitArr[$item['month']]['upload']) ? $hitArr[$item['month']]['upload']+$item['cnt'] : $item['cnt'];
                $mntArr[$item['month']][$item['countrycode']]['upload'] = $item['cnt'];
            }
        }
        
        // links to view by country or breakdown by country
        $objLink = new link($this->uri(array('action' => 'viewstats', 'view' => 'country')));
        $objLink->link = $lnView;
        $str .= '<p>'.$objLink->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        
        if($breakdown){
            $objLink = new link($this->uri(array('action' => 'viewstats')));
            $objLink->link = $lnView2;
        }else{
            $objLink = new link($this->uri(array('action' => 'viewstats', 'break' => TRUE)));
            $objLink->link = $lnBreak;
        }
            $str .= $objLink->show().'</p>';


        // Display stats
        $objTable = new htmltable();
        $objTable->cellpadding = 5;
        $objTable->width = '80%';
        $objTable->border = '1';
        
        // Check if stats should be broken down by country
        if($breakdown){
            // Breakdown by country
            $objTable->addHeader(array($lbMonth, $lbCountry, $lbVisits, $lbDownloads, $lbUploads));
            if(!empty($mntArr)){
                ksort($mntArr);
                foreach($mntArr as $key => $val){
                    $month = $this->objDate->monthFull($key);
                    ksort($val);
                    $rows = count($val) + 1;
                    
                    $objTable->startRow();
                    $objTable->addCell($month, '25%', '', '', '', "rowspan='{$rows}'");
                    $objTable->endRow();
                        
                    foreach($val as $key2 => $item){
                        $country = $this->objIpCountry->getCountryName($key2);
                        $visits = 0; $downloads = 0; $uploads = 0;
                        
                        if(isset($item['visit'])){
                            $visits = $item['visit'];
                        }
                        if(isset($item['download'])){
                            $downloads = $item['download'];
                        }
                        if(isset($item['upload'])){
                            $uploads = $item['upload'];
                        }
                        
                        $objTable->startRow();
                        $objTable->addCell($country, '30%');
                        $objTable->addCell($visits, '15%', '', 'center');
                        $objTable->addCell($downloads, '15%', '', 'center');
                        $objTable->addCell($uploads, '15%', '', 'center');
                        $objTable->endRow();
                    }
                }
            }
        }else{
            $objTable->addHeader(array($lbMonth, $lbVisits, $lbDownloads, $lbUploads));
            if(!empty($hitArr)){
                ksort($hitArr);
                foreach($hitArr as $key => $item){
                    $month = $this->objDate->monthFull($key);
                    $visits = 0; $downloads = 0; $uploads = 0;
                    
                    if(isset($item['visit'])){
                        $visits = $item['visit'];
                    }
                    if(isset($item['download'])){
                        $downloads = $item['download'];
                    }
                    if(isset($item['upload'])){
                        $uploads = $item['upload'];
                    }
                    
                    $objTable->startRow();
                    $objTable->addCell($month, '50%');
                    $objTable->addCell($visits, '25%', '', 'center');
                    $objTable->addCell($downloads, '25%', '', 'center');
                    $objTable->addCell($uploads, '25%', '', 'center');
                    $objTable->endRow();
                }
            }
        }
        
        $objLayer = new layer();
        $objLayer->width = '100%';
        $objLayer->align = 'center';
        $objLayer->str = $objTable->show();
        $str .= $objLayer->show();
        $str .= '<br />';
        
        $objTab = new tabbedbox();
        $objTab->extra = 'style="background-color: #FCFAF2; padding: 5px;"';
        $objTab->addTabLabel($hdResource);
        $objTab->addBoxContent($str);
        
        return $this->objFeatureBox->showContent($hdResource, $str); //$objTab->show();
    }
    
    /**
    * Method to generate the statistics by country for the resources
    *
    * @access private
    * @param bool $breakdown True if monthly statistics should be broken down by country
    * @return string html
    */
    private function getStatsByCountry($breakdown)
    {
        $resVisitsCount = $this->getStatistic('visit');
        $downloadsCount = $this->getStatistic('download');
        $resourceCount = $this->etdDbSubmissions->getCount();
        
        /*$countryVisit = $this->getCountryStats('visit');
        $countryDownload = $this->getCountryStats('download');
        $countryUpload = $this->getCountryStats('upload');
        */
        $countryVisit = $this->getStatsByMonthCountry('visit');
        $countryDownload = $this->getStatsByMonthCountry('download');
        $countryUpload = $this->getStatsByMonthCountry('upload');
        
        $aveResourceVisits = 0;
        $aveResourceDownloads = 0;
        if($resourceCount > 0){
            $aveResourceVisits = round($resVisitsCount/$resourceCount,0);
            $aveResourceDownloads = round($downloadsCount/$resourceCount,0);
        }
                
        $hdResource = $this->objLanguage->languageText('phrase_resourcestats');
        $lbTotalResource = $this->objLanguage->languageText('mod_etd_totalresourcesavailable', 'etd');
        $lbTotalDownloads = $this->objLanguage->languageText('mod_etd_totalresourcesdownloaded', 'etd');
        $lbTotalHits = $this->objLanguage->languageText('mod_etd_totalvisitstoresources', 'etd');
        $lbAveRes = $this->objLanguage->languageText('mod_etd_avevisitsperresource', 'etd');
        $lbAveDown = $this->objLanguage->languageText('mod_etd_avedownloadsperresource', 'etd');
        $lnView = $this->objLanguage->languageText('mod_etd_viewbymonth', 'etd');
        $lnView2 = $this->objLanguage->languageText('mod_etd_viewbycountry', 'etd');
        $lbMonth = $this->objLanguage->languageText('word_month');
        $lbCountry = $this->objLanguage->languageText('word_country');
        $lbVisits = $this->objLanguage->languageText('word_visits');
        $lbDownloads = $this->objLanguage->languageText('word_downloads');
        $lbUploads = $this->objLanguage->languageText('phrase_newsubmissions');
        $lnBreak = $this->objLanguage->languageText('phrase_breakdownbymonth');
        
        $objTable = new htmltable();
        $objTable->cellpadding = 5;
        
        $objTable->addRow(array($lbTotalResource.': '.$resourceCount));
        
        $objTable->startRow();
        $objTable->addCell($lbTotalHits.': '.$resVisitsCount, '40%');
        $objTable->addCell($lbAveRes.': '.$aveResourceVisits);
        $objTable->endRow();
        
        $objTable->addRow(array($lbTotalDownloads.': '.$downloadsCount, $lbAveDown.': '.$aveResourceDownloads));
        
        $str = $objTable->show();
        $str .= '<br /><br />';
        
        $hitArr = array();
        $mntArr = array();
        if(!empty($countryVisit)){
            foreach($countryVisit as $item){
                $hitArr[$item['countrycode']]['visit'] = isset($hitArr[$item['countrycode']]['visit']) ? $hitArr[$item['countrycode']]['visit']+$item['cnt'] : $item['cnt'];
                $cntArr[$item['countrycode']][$item['month']]['visit'] = $item['cnt'];
            }
        }
        if(!empty($countryDownload)){
            foreach($countryDownload as $item){
                $hitArr[$item['countrycode']]['download'] = isset($hitArr[$item['countrycode']]['download']) ? $hitArr[$item['countrycode']]['download']+$item['cnt'] : $item['cnt'];
                $cntArr[$item['countrycode']][$item['month']]['download'] = $item['cnt'];
            }
        }
        if(!empty($countryUpload)){
            foreach($countryUpload as $item){
                $hitArr[$item['countrycode']]['upload'] = isset($hitArr[$item['countrycode']]['upload']) ? $hitArr[$item['countrycode']]['upload']+$item['cnt'] : $item['cnt'];
                $cntArr[$item['countrycode']][$item['month']]['upload'] = $item['cnt'];
            }
        }
        
        // Links to view by month / breakdown by month
        $objLink = new link($this->uri(array('action' => 'viewstats')));
        $objLink->link = $lnView;
        $str .= '<p>'.$objLink->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        
        if($breakdown){
            $objLink = new link($this->uri(array('action' => 'viewstats', 'view' => 'country')));
            $objLink->link = $lnView2;
        }else{
            $objLink = new link($this->uri(array('action' => 'viewstats', 'view' => 'country', 'break' => TRUE)));
            $objLink->link = $lnBreak;
        }
        $str .= $objLink->show().'</p>';
        
        $objTable = new htmltable();
        $objTable->cellpadding = 5;
        $objTable->width = '80%';
        $objTable->border = '1';
        
        

        if($breakdown){
            if(!empty($cntArr)){
                $objTable->addHeader(array($lbCountry, $lbMonth, $lbVisits, $lbDownloads, $lbUploads));
                ksort($cntArr);
                foreach($cntArr as $key => $val){
                    $country = $this->objIpCountry->getCountryName($key);
                    ksort($val);
                    $rows = count($val) + 1;
                    
                    $objTable->startRow();
                    $objTable->addCell($country, '25%', '', '', '', "rowspan='{$rows}'");
                    $objTable->endRow();
                        
                    foreach($val as $key2 => $item){
                        $month = $this->objDate->monthFull($key2);
                        $visits = 0; $downloads = 0; $uploads = 0;
                        
                        if(isset($item['visit'])){
                            $visits = $item['visit'];
                        }
                        if(isset($item['download'])){
                            $downloads = $item['download'];
                        }
                        if(isset($item['upload'])){
                            $uploads = $item['upload'];
                        }
                        
                        $objTable->startRow();
                        $objTable->addCell($month, '30%');
                        $objTable->addCell($visits, '15%', '', 'center');
                        $objTable->addCell($downloads, '15%', '', 'center');
                        $objTable->addCell($uploads, '15%', '', 'center');
                        $objTable->endRow();
                        
                        $month = '';
                    }
                }
            }
        }else{
            if(!empty($hitArr)){
                $objTable->addHeader(array($lbCountry, $lbVisits, $lbDownloads, $lbUploads));
                foreach($hitArr as $key => $item){
                    $country = $this->objIpCountry->getCountryName($key);
                    $visits = 0; $downloads = 0; $uploads = 0;
                    
                    if(isset($item['visit'])){
                        $visits = $item['visit'];
                    }
                    if(isset($item['download'])){
                        $downloads = $item['download'];
                    }
                    if(isset($item['upload'])){
                        $uploads = $item['upload'];
                    }
                    
                    $objTable->startRow();
                    $objTable->addCell($country, '50%');
                    $objTable->addCell($visits, '25%', '', 'center');
                    $objTable->addCell($downloads, '25%', '', 'center');
                    $objTable->addCell($uploads, '25%', '', 'center');
                    $objTable->endRow();
                }
            }
        }
                
        $objLayer = new layer();
        $objLayer->width = '100%';
        $objLayer->align = 'center';
        $objLayer->str = $objTable->show();
        $str .= $objLayer->show();
        $str .= '<br />';
        
        $objTab = new tabbedbox();
        $objTab->extra = 'style="background-color: #FCFAF2; padding: 5px;"';
        $objTab->addTabLabel($hdResource);
        $objTab->addBoxContent($str);
        
        return $this->objFeatureBox->showContent($hdResource, $str); // $objTab->show();
    }

    /**
    * Method to display print and email buttons
    *
    * @access private
    * @return string html
    */
    private function getButtons($view, $break = FALSE)
    {
        $lbPrint = $this->objLanguage->languageText('phrase_printfriendly');
        $lbEmail = $this->objLanguage->languageText('word_email');
        
        // Print friendly page
        $url = $this->uri(array('action' => 'printstats', 'view' => $view, 'break' => $break));
            
        $onclick = "javascript:window.open('" .$url."', 'resource', 'left=100, top=100, width=500, height=400, scrollbars=1, fullscreen=no, toolbar=yes, menubar=yes, resizable=yes')";
        $objButton = new button('print', $lbPrint);
        $objButton->setOnClick($onclick);
            
        $formStr = $objButton->show().'&nbsp;&nbsp;&nbsp;';
        
        // Email resource
        $url = $this->uri(array('action' => 'emailstats', 'view' => $view, 'break' => $break));
            
        $objButton = new button('email', $lbEmail);
        $objButton->setToSubmit();
            
        $formStr .= $objButton->show();
            
        $objForm = new form('emailstats', $url);
        $objForm->addToForm($formStr);
            
        $str = $objForm->show();
        
        return $str;
    }

    /**
    * Method to display the statistics for the whole site
    *
    * @access public
    * @param string $view View by country / by month
    * @return string html
    */
    public function showAll($view = '')
    {
        $head = $this->objLanguage->languageText('word_statistics');
        
        $objHead = new htmlheading();
        $objHead->str = $head;
        $objHead->type = 1;
        $str = $objHead->show();
        
        $break = $this->getParam('break');
        if($view == 'country'){
            $layerStr = '<p>'.$this->getStatsByCountry($break).'</p>';
        }else{
            $layerStr = '<p>'.$this->getStatsByResource($break).'</p>';
        }
        $layerStr .= '<p>'.$this->getSiteStats().'</p>';
        $layerStr .= '<p>'.$this->getUserStats().'</p>';
        
        // Print / email
        $layerStr .= '<p>'.$this->getButtons($view, $break).'</p>';
        
        return $str.$layerStr;
    }
}
?>