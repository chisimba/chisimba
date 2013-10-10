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


class dbwebpresenttagviewcounter extends dbtable
{

    /**
    * Method to construct the class.
    */
    public function init()
    {
        parent::init('tbl_webpresent_tagviews');
        $this->loadClass('link', 'htmlelements');
    }

    /**
     * Method to Add counter that a tag has been clicked on
     * @param string $tag Tag
     * @return string Record Insert Id
     */
    public function addView($tag)
    {
        return $this->insert(array(
                'tag' => $tag,
                'dateviewed' => date('Y-m-d'),
                'datetimeviewed' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            ));
    }

    /**
     * Method to the the most viewed presentations for today
     * @return array List of Most Viewed Presentations for Today
     */
    public function getMostViewedToday()
    {
        $sql = 'SELECT count( tbl_webpresent_tagviews.id ) AS viewcount, tbl_webpresent_tagviews . * FROM tbl_webpresent_tagviews WHERE (dateviewed = \''.date('Y-m-d').'\') GROUP BY tbl_webpresent_tagviews.tag ORDER BY viewcount DESC LIMIT 5';

        return $this->getArray($sql);
    }

    /**
     * Method to the the most viewed presentations this week
     * @return array List of Most Viewed Presentations this week
     */
    public function getMostViewedThisWeek()
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
        $sql = 'SELECT count( tbl_webpresent_tagviews.id ) AS viewcount, tbl_webpresent_tagviews . * FROM tbl_webpresent_tagviews WHERE (dateviewed > \''.$startOfWeek .'\') GROUP BY tbl_webpresent_tagviews.tag ORDER BY viewcount DESC LIMIT 5';

        return $this->getArray($sql);
    }

    /**
     * Method to the the most viewed presentations of all time
     * @return array List of Most Viewed Presentations of all time
     */
    public function getMostViewedAllTime()
    {
        $sql = 'SELECT count( tbl_webpresent_tagviews.id ) AS viewcount, tbl_webpresent_tagviews . * FROM tbl_webpresent_tagviews GROUP BY tbl_webpresent_tagviews.tag ORDER BY viewcount DESC LIMIT 5';
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
                $files = $this->getMostViewedAllTime();
                break;
            case 'week':
                $files = $this->getMostViewedThisWeek();
                break;
            default:
                $files = $this->getMostViewedToday();
                break;
        }
        return $this->prepContent($files, $period);
    }

    /**
     * Method to get the Most Viewed Tag as a table
     *
     * It is designed to present showing "No records today"
     *
     * This is displayed on the home page. It first checks if there was
     * any tag views for today, if not try week, if not show all time stats
     *
     * @return string
     */
    public function getMostViewedTagTable()
    {
        // Check Today
        $files = $this->getMostViewedToday();

        if (count($files) > 0) {
            return $this->getDataFormatted($files, 'today');
        }

        $files = $this->getMostViewedThisWeek();

        if (count($files) > 0) {
            return $this->getDataFormatted($files, 'week');
        }

        return $this->getMostViewedAllTimeTable();
    }

    /**
     * Method to get the Most Viewed Today Presentations as a table
     * @return string
     */
    public function getMostViewedTodayTable()
    {
        $files = $this->getMostViewedToday();

        return $this->getDataFormatted($files, 'today');
    }

    /**
     * Method to get the Most Viewed This Week Presentations as a table
     * @return string
     */
    public function getMostViewedThisWeekTable()
    {
        $files = $this->getMostViewedThisWeek();

        return $this->getDataFormatted($files, 'today');
    }

    /**
     * Method to get the Most Viewed All Time Presentations as a table
     * @return string
     */
    public function getMostViewedAllTimeTable()
    {
        $files = $this->getMostViewedAllTime();

        return $this->getDataFormatted($files, 'today');
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

        $content = '<div id="loading_tags" style="display:none;">'.$objIcon->show().'</div><div id="data_tags">'.$this->prepContent($data, $period).'</div>';

        return $objFeatureBox->show('Most Popular Tags', $content);
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
                    $str = 'No presentations have been viewed on this site';
                    break;
                case 'week':
                    $str = 'No presentations have been viewed this week';
                    break;
                default:
                    $str = 'No presentations have been viewed today';
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

                $fileLink = new link ($this->uri(array('action'=>'tag', 'tag'=>$file['tag'])));
                $fileLink->link = $file['tag'];

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
            $link = new link('javascript:getData(\'tags\', \'today\');');
            $link->link = 'Today';

            $allLinks[] = $link->show();
        }

        // This Week
        if ($period == 'week') {
            $allLinks[] = 'This Week';
        } else {
            $link = new link('javascript:getData(\'tags\', \'week\');');
            $link->link = 'This Week';

            $allLinks[] = $link->show();
        }

        // All Time
        if ($period == 'alltime') {
            $allLinks[] = 'All Time';
        } else {
            $link = new link('javascript:getData(\'tags\', \'alltime\');');
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