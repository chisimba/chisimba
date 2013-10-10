<?php



// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}


class dbpodcasterdownloadcounter extends dbtable
{

    /**
    * Method to construct the class.
    */
    public function init()
    {
        parent::init('tbl_podcaster_downloads');
        $this->loadClass('link', 'htmlelements');
    }

    public function addDownload($id, $type)
    {
        return $this->insert(array(
                'fileid' => $id,
                'filetype' => $type,
                'datedownloaded' => date('Y-m-d'),
                'datetimedownloaded' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            ));
    }

    /**
     * Method to the the most downloaded podcasts for today
     * @return array List of Most downloaded Presentations for Today
     */
    public function getMostDownloadedToday()
    {
        $sql = 'SELECT count(tbl_podcaster_downloads.id) as viewcount, tbl_podcaster_metadata_media.* FROM tbl_podcaster_downloads, tbl_podcaster_metadata_media WHERE (tbl_podcaster_metadata_media.id = tbl_podcaster_downloads.fileid AND datedownloaded=\''.date('Y-m-d').'\' ) GROUP BY tbl_podcaster_downloads.fileid Order by viewcount desc limit 5';

        return $this->getArray($sql);
    }

    /**
     * Method to the the most downloaded podcasts this week
     * @return array List of Most downloaded Presentations this week
     */
    public function getMostDownloadedThisWeek()
    {
        // Get Start Of Week
        $startOfWeek = date('Y-m-d');

        // Load Date Time Class
        $objDateTime = $this->getObject('dateandtime', 'utilities');

        // Get Previous Day 7 times
        for ($i=1; $i <= 7; $i++)
        {
            $startOfWeek = $objDateTime->previousDay($startOfWeek );
        }

        // SQL
        $sql = 'SELECT count(tbl_podcaster_downloads.id) as viewcount, tbl_podcaster_metadata_media.* FROM tbl_podcaster_downloads, tbl_podcaster_metadata_media WHERE (tbl_podcaster_metadata_media.id = tbl_podcaster_downloads.fileid AND datedownloaded > \''.$startOfWeek .'\' ) GROUP BY tbl_podcaster_downloads.fileid Order by viewcount desc limit 5';

        return $this->getArray($sql);
    }

    /**
     * Method to the the most downloaded podcasts of all time
     * @return array List of Most downloaded Presentations of all time
     */
    public function getMostDownloadedAllTime()
    {
        $sql = 'SELECT count(tbl_podcaster_downloads.id) as viewcount, tbl_podcaster_metadata_media.* FROM tbl_podcaster_downloads, tbl_podcaster_metadata_media WHERE (tbl_podcaster_metadata_media.id = tbl_podcaster_downloads.fileid ) GROUP BY tbl_podcaster_downloads.fileid Order by viewcount DESC LIMIT 5';
        return $this->getArray($sql);
    }

    /**
     * Method to return stats requested via an Ajax Call
     * @param string $period Period of Data Requested
     * @return string Data in Formatted Table
     */
    public function getAjaxData($period)
    {
        switch ($period)
        {
            case 'alltime':
                $files = $this->getMostDownloadedAllTime();
                break;
            case 'week':
                $files = $this->getMostDownloadedThisWeek();
                break;
            default:
                $files = $this->getMostDownloadedToday();
                break;
        }
        return $this->prepContent($files, $period);
    }

    /**
     * Method to get the Most Downloaded Presentations in non formated way
     *
     * It is designed to present showing "No records today"
     *
     * This is displayed on the home page. It first checks if there was
     * any downloads for today, if not try week, if not show all time stats
     *
     * @return string
     */
    public function getMostDownloadedList()
    {
        // Check Today
        $files = $this->getMostDownloadedToday();

        if (count($files) > 0) {
            return $this->prepContent2($files, 'today');
        }

        $files = $this->getMostDownloadedThisWeek();

        if (count($files) > 0) {
            return $this->prepContent2($files, 'week');
        }

        return $this->getMostDownloadedAllTimeList();
    }

    /**
     * Method to get the Most Downloaded Presentations as a table
     *
     * It is designed to present showing "No records today"
     *
     * This is displayed on the home page. It first checks if there was
     * any downloads for today, if not try week, if not show all time stats
     *
     * @return string
     */
    public function getMostDownloadedTable()
    {
        // Check Today
        $files = $this->getMostDownloadedToday();

        if (count($files) > 0) {
            return $this->getDataFormatted($files, 'today');
        }

        $files = $this->getMostDownloadedThisWeek();

        if (count($files) > 0) {
            return $this->getDataFormatted($files, 'week');
        }

        return $this->getMostDownloadedAllTimeTable();
    }


    /**
     * Method to get the Most Downloaded Today Presentations as a table
     * @return string
     */
    public function getMostDownloadedTodayTable()
    {
        $files = $this->getMostDownloadedToday();

        return $this->getDataFormatted($files, 'today');
    }

    /**
     * Method to get the Most Downloaded This Week Presentations as a table
     * @return string
     */
    public function getMostDownloadedThisWeekTable()
    {
        $files = $this->getMostDownloadedThisWeek();

        return $this->getDataFormatted($files, 'week');
    }

    /**
     * Method to get the Most Viewed All Time Presentations in second format
     * @return string
     */
    public function getMostDownloadedAllTimeList()
    {
        $files = $this->getMostDownloadedAllTime();

        return $this->prepContent2($files, 'alltime');
    }

    /**
     * Method to get the Most Viewed All Time Presentations as a table
     * @return string
     */
    public function getMostDownloadedAllTimeTable()
    {
        $files = $this->getMostDownloadedAllTime();

        return $this->getDataFormatted($files, 'alltime');
    }



    /**
     * Method to get to take data and a period and convert them into a featurebox for display
     * @param array $data List of podcasts
     * @param string $period Period Data is for
     * @return string
     */
    private function getDataFormatted($data, $period)
    {
        $objFeatureBox = $this->newObject('featurebox', 'navigation');

        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('loading_circles');

        $content = '<div id="loading_downloads" style="display:none;">'.$objIcon->show().'</div><div id="data_downloads">'.$this->prepContent($data, $period).'</div>';

        return $objFeatureBox->show('Most Downloaded', $content);
    }

    /**
     * Method to get to take data and a period and convert them into a table for display, as well as links to other periods
     * @param array $data List of podcasts
     * @param string $period Period Data is for
     * @return string
     */
    private function prepContent($data, $period)
    {
        // Create Empty String
        $content = '';

        // Convert to Lowercase, just in case
        $period = strtolower($period);

        // Create Array of Permitted Types
        $permittedTypes = array ('today', 'week', 'alltime');

        // Check that period is valid, if not, show daily result
        if (!in_array($period, $permittedTypes)) {
            $period = 'today';
        }

        // If no results, return notice to user
        if (count($data) == 0)
        {
            switch ($period)
            {
                case 'alltime':
                    $str = 'No podcasts have been downloaded on this site';
                    break;
                case 'week':
                    $str = 'No podcasts have been downloaded this week';
                    break;
                default:
                    $str = 'No podcasts have been downloaded today';
                    break;
            }

            $content = '<div class="noRecordsMessage">'.$str.'</div>';


            // Else start creating a table
        } else {
            $table = $this->newObject('htmltable', 'htmlelements');
            $counter = 0;

            $this->loadClass('link', 'htmlelements');

            foreach ($data as $file)
            {
                $counter++;
                $table->startRow();
                $table->addCell($counter.'.', 20, 'top', 'center');

                $fileLink = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
                $fileLink->link = $file['title'];

                $table->addCell($fileLink->show());
                $table->addCell($file['viewcount'], 20, 'top', 'center');

                $table->endRow();
            }

            $content = $table->show();
        }



        // Start creating links to other periods, current period should not be a link


        // Today
        if ($period == 'today') {
            $allLinks[] = 'Today';
        } else {
            $link = new link('javascript:getData(\'downloads\', \'today\');');
            $link->link = 'Today';

            $allLinks[] = $link->show();
        }

        // This Week
        if ($period == 'week') {
            $allLinks[] = 'This Week';
        } else {
            $link = new link('javascript:getData(\'downloads\', \'week\');');
            $link->link = 'This Week';

            $allLinks[] = $link->show();
        }

        // All Time
        if ($period == 'alltime') {
            $allLinks[] = 'All Time';
        } else {
            $link = new link('javascript:getData(\'downloads\', \'alltime\');');
            $link->link = 'All Time';

            $allLinks[] = $link->show();
        }


        if (count($allLinks) > 0) {
            $linksContent = '<p align="right">';
            $divider = '';
            foreach ($allLinks as $link)
            {
                $linksContent .= $divider.$link;
                $divider = ' | ';
            }
            $linksContent .= '</p>';
        }

        // Return Links + Content
        return $linksContent.$content;
    }

 /**
     * Method to get to take data and a period and convert them into a list for display, as well as links to other periods
     * @param array $data List of podcasts
     * @param string $period Period Data is for
     * @return string
     */
    private function prepContent2($data, $period)
    {
        // Create Empty String
        $content = '';

        // Convert to Lowercase, just in case
        $period = strtolower($period);

        // Create Array of Permitted Types
        $permittedTypes = array ('today', 'week', 'alltime');

        // Check that period is valid, if not, show daily result
        if (!in_array($period, $permittedTypes)) {
            $period = 'today';
        }

        // If no results, return notice to user
        if (count($data) == 0)
        {
            switch ($period)
            {
                case 'alltime':
                $str = 'No podcasts have been downloaded on this site';
                break;
                case 'week':
                $str = 'No podcasts have been downloaded this week';
                break;
                default:
                $str = 'No podcasts have been downloaded today';
                break;
            }

            $content = '<div class="noRecordsMessage">'.$str.'</div>';


            // Else start creating a table
        } else {
            $table = $this->newObject('htmltable', 'htmlelements');
            $counter = 0;

            $this->loadClass('link', 'htmlelements');
            $filetitle='Presentation';
            $result='';
            foreach ($data as $file)
            {

                if ($file['title'] == '') {
                    $filetitle.='-'.$counter;
                } else {
                    $filetitle = $file['title'];
                }

                $counter++;
                $result.="<li>";
                $fileLink = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
                $fileLink->link = $filetitle;

                $result.=$fileLink->show();
                $result.=' - '.$file['viewcount'];

                $result.="</li>";
            }

            $content = $result;
        }



        // Start creating links to other periods, current period should not be a link


        // Today
        if ($period == 'today') {
            $allLinks[] = 'Today';
        } else {
            $link = new link('javascript:getData(\'views\', \'today\');');
            $link->link = 'Today';

            $allLinks[] = $link->show();
        }

        // This Week
        if ($period == 'week') {
            $allLinks[] = 'This Week';
        } else {
            $link = new link('javascript:getData(\'views\', \'week\');');
            $link->link = 'This Week';

            $allLinks[] = $link->show();
        }

        // All Time
        if ($period == 'alltime') {
            $allLinks[] = 'All Time';
        } else {
            $link = new link('javascript:getData(\'views\', \'alltime\');');
            $link->link = 'All Time';

            $allLinks[] = $link->show();
        }


        if (count($allLinks) > 0) {
            $linksContent = '<p align="right">';
            $divider = '';
            foreach ($allLinks as $link)
            {
                $linksContent .= $divider.$link;
                $divider = ' | ';
            }
            $linksContent .= '</p>';
        }

        // Return Links + Content
        return $content;
    }


}
?>