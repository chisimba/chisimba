<?php
/**
* @package essayadmin
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* The essay admin block class displays a block with an alert if students have handed in.
* @author Megan Watson
*/

class block_essayadmin extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_essayadmin_name', 'essayadmin');

        $this->objSubmit =& $this->getObject('dbessay_book', 'essay');
        $objDbContext = &$this->getObject('dbcontext', 'context');

        $this->contextCode = $objDbContext->getContextCode();

        $this->objTable =& $this->newObject('htmltable', 'htmlelements');
        $this->objIcon =& $this->newObject('geticon', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
    }

    /**
    * Method to check for new hand-ins on essay topics
    */
    function checkSubmits()
    {
        $hdTopics = $this->objLanguage->languageText('mod_essayadmin_topics', 'essayadmin');
        $hdMarked = $this->objLanguage->languageText('mod_essayadmin_marked', 'essayadmin');
        $hdSubmitted = $this->objLanguage->languageText('mod_essayadmin_submitted', 'essayadmin');

        $submits = $this->objSubmit->getContextSubmissions($this->contextCode);
        $topics = array(); $str = ''; $i = 0;

        if(!empty($submits)){
            foreach($submits as $item){
                if(!isset($topics[$item['id']])){
                    $topics[$item['id']]['submitted'] = 0;
                    $topics[$item['id']]['marked'] = 0;
                }
                $topics[$item['id']]['name'] = $item['name'];
                $topics[$item['id']]['closing_date'] = $item['closing_date'];
                if(!is_null($item['submitDate'])){
                    $topics[$item['id']]['submitted']++;
                    if(!empty($item['mark'])){
                        $topics[$item['id']]['marked']++;
                    }
                }
            }

            if(!empty($topics)){
                $hd = array();
                $hd[] = $hdTopics;
                $hd[] = $hdMarked.' / '.$hdSubmitted;
                $this->objTable->cellpadding = 2;
                $this->objTable->cellspacing = 2;
                $this->objTable->addHeader($hd);

                foreach($topics as $item){
                    if($item['marked'] < $item['submitted']){
                        $class = (($i++ % 2) == 0) ? 'odd':'even';
                        $num = $item['marked'].' / '.$item['submitted'];
                        $this->objTable->startRow();
                        $this->objTable->addCell($item['name'], '', '', '', $class);
                        $this->objTable->addCell($num, '', '', 'center', $class);
                        $this->objTable->endRow();
                    }
                }
                return $this->objTable->show();
            }
        }
        return '';
    }

    /**
    * Display link to Essay Admin
    */
    public function getLink()
    {
        $url = $this->uri('', 'essayadmin');
        $this->objIcon->setModuleIcon('essayadmin');
        $objLink = new link($url);
        $objLink->link = $this->objIcon->show();
        $lnStr = '<p>'.$objLink->show();
        $objLink = new link($url);
        $objLink->link = $this->title;
        $lnStr .= '&nbsp;'.$objLink->show().'</p>';

        return $lnStr;
    }

    /**
    * Display function
    */
    public function show()
    {
        if(is_null($this->contextCode)){
            return '';
        }
        return $this->checkSubmits().$this->getLink();
    }
}
?>