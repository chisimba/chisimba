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


class dbwebpresentuploadscounter extends dbTable
{

    /**
    * Method to construct the class.
    */
    public function init()
    {
        parent::init('tbl_webpresent_files');
        $this->loadClass('link', 'htmlelements');
    }

    /**
     * Method to the the most viewed presentations for today
     * @return array List of Most Viewed Presentations for Today
     */
    public function getMostUploadedToday()
    {

        $sql = 'SELECT count( tbl_webpresent_files.id ) AS viewcount, tbl_webpresent_files . *, firstname, surname FROM tbl_webpresent_files, tbl_users WHERE (tbl_webpresent_files.creatorid = tbl_users.userid AND dateuploaded LIKE \''.date('Y-m-d').'%\') GROUP BY tbl_webpresent_files.creatorid ORDER BY viewcount DESC LIMIT 5';

        return $this->getArray($sql);
    }

    /**
     * Method to the the most viewed presentations this week
     * @return array List of Most Viewed Presentations this week
     */
    public function getMostUploadedThisWeek()
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
        $sql = 'SELECT count( tbl_webpresent_files.id ) AS viewcount, tbl_webpresent_files . *, firstname, surname FROM tbl_webpresent_files, tbl_users WHERE (tbl_webpresent_files.creatorid = tbl_users.userid AND dateuploaded > \''.$startOfWeek .'\') GROUP BY tbl_webpresent_files.creatorid ORDER BY viewcount DESC LIMIT 5';

        return $this->getArray($sql);
    }

    /**
     * Method to the the most viewed presentations of all time
     * @return array List of Most Viewed Presentations of all time
     */
    public function getMostUploadedAllTime()
    {
        $sql = 'SELECT count( tbl_webpresent_files.id ) AS viewcount, tbl_webpresent_files . *, firstname, surname FROM tbl_webpresent_files, tbl_users WHERE (tbl_webpresent_files.creatorid = tbl_users.userid) GROUP BY tbl_webpresent_files.creatorid ORDER BY viewcount DESC LIMIT 5';
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
                $files = $this->getMostUploadedAllTime();
                break;
            case 'week':
                $files = $this->getMostUploadedThisWeek();
                break;
            default:
                $files = $this->getMostUploadedToday();
                break;
        }
        return $this->prepContent($files, $period);
    }
    /**
     * Method to get the Most Uploaded as a table
     *
     * It is designed to present showing "No records today"
     *
     * This is displayed on the home page. It first checks if there was
     * any uploads for today, if not try week, if not show all time stats
     *
     * @return string
     */
    public function getMostUploadedTable()
    {
        // Check Today
        $files = $this->getMostUploadedToday();

        if (count($files) > 0) {
            return $this->getDataFormatted($files, 'today');
        }

        $files = $this->getMostUploadedThisWeek();

        if (count($files) > 0) {
            return $this->getDataFormatted($files, 'week');
        }

        return $this->getMostUploadedAllTimeTable();
    }
    /**
     * Method to get the Most Uploaded as a list
     *
     * It is designed to present showing "No records today"
     *
     * This is displayed on the home page. It first checks if there was
     * any uploads for today, if not try week, if not show all time stats
     *
     * @return string
     */
    public function getMostUploadedList()
    {
        // Check Today
        $files = $this->getMostUploadedToday();

        if (count($files) > 0) {
            return $this->prepContent2($files, 'today');
        }

        $files = $this->getMostUploadedThisWeek();

        if (count($files) > 0) {
            return $this->prepContent2($files, 'week');
        }

        return $this->getMostUploadedAllTimeList();
    }

    /**
     * Method to get the Most Viewed Today Presentations as a table
     * @return string
     */
    public function getMostUploadedTodayTable()
    {
        $files = $this->getMostUploadedToday();

        return $this->getDataFormatted($files, 'today');
    }

    /**
     * Method to get the Most Viewed This Week Presentations as a table
     * @return string
     */
    public function getMostUploadedThisWeekTable()
    {
        $files = $this->getMostUploadedThisWeek();

        return $this->getDataFormatted($files, 'week');
    }


    /**
     * Method to get the Most Viewed All Time Presentations as a list
     * @return string
     */
    public function getMostUploadedAllTimeList()
    {
        $files = $this->getMostUploadedAllTime();

        return $this->prepContent2($files, 'alltime');
    }

    /**
     * Method to get the Most Viewed All Time Presentations as a table
     * @return string
     */
    public function getMostUploadedAllTimeTable()
    {
        $files = $this->getMostUploadedAllTime();

        return $this->getDataFormatted($files, 'alltime');
    }

    /**
     * Method to get to take data and a period and convert them into a featurebox for display
     * @param array $data List of presentations
     * @param string $period Period Data is for
     * @return string
     */
    private function getDataFormatted($data, $period)
    {
        $objFeatureBox = $this->newObject('featurebox', 'navigation');

        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('loading_circles');

        $content = '<div id="loading_uploads" style="display:none;">'.$objIcon->show().'</div><div id="data_uploads">'.$this->prepContent($data, $period).'</div>';

        return $objFeatureBox->show('Most Uploads', $content);
    }

    /**
     * Method to get to take data and a period and convert them into a table for display, as well as links to other periods
     * @param array $data List of presentations
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
                    $str = 'No presentations have been uploaded on this site';
                    break;
                case 'week':
                    $str = 'No presentations have been uploaded this week';
                    break;
                default:
                    $str = 'No presentations have been uploaded today';
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

                $fileLink = new link ($this->uri(array('action'=>'byuser', 'userid'=>$file['creatorid'])));
                $fileLink->link = $file['firstname'].' '.$file['surname'];

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
            $link = new link('javascript:getData(\'uploads\', \'today\');');
            $link->link = 'Today';

            $allLinks[] = $link->show();
        }

        // This Week
        if ($period == 'week') {
            $allLinks[] = 'This Week';
        } else {
            $link = new link('javascript:getData(\'uploads\', \'week\');');
            $link->link = 'This Week';

            $allLinks[] = $link->show();
        }

        // All Time
        if ($period == 'alltime') {
            $allLinks[] = 'All Time';
        } else {
            $link = new link('javascript:getData(\'uploads\', \'alltime\');');
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
     * @param array $data List of presentations
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
                $str = 'No presentations have been uploaded on this site';
                break;
                case 'week':
                $str = 'No presentations have been uploaded this week';
                break;
                default:
                $str = 'No presentations have been uploaded today';
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
                $fileLink->link = $file['firstname'].' '.$file['surname'];

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
        return $linksContent.$content;
    }



}
?>