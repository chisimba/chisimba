<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* The class provides a hello world block to demonstrate
* how to use blockalicious
*
* @author Derek Keats
*
*/
class block_widget1 extends object
{
    var $title;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->title=$this->objLanguage->languageText("mod_dkeatscom_widget1", "dkeatscom");
        $this->blockType = "none";
    }

    /**
    * Method to output a widget block
    */
    public function show()
	{
        return $this->getWidget();
    }

    private function getWidget()
    {
        return  "<script type=\"text/javascript\">
digg_id = 'digg-widget-container'; //make this id unique for each widget you put on a single page.
digg_width = '206px';
digg_height = '300px';
digg_target = 1;
digg_title = 'Linux/Unix';
</script>
<script type=\"text/javascript\" src=\"http://digg.com/tools/widgetjs\"></script>
<script type=\"text/javascript\" src=\"http://digg.com/tools/services?type=javascript&callback=diggwb&endPoint=/stories/topic/linux_unix/popular&count=10\"></script>
";
    }
}
?>