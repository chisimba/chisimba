<?php

class dbnewsmenu extends dbtable
{

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access protected
     * @var object
     */
    protected $objSysConfig;

    public function init()
    {
        parent::init('tbl_news_menu');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('link', 'htmlelements');
        
        $this->objBlocks = $this->getObject('blocks', 'blocks');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }

    public function toolbar($current='storyview')
    {
        $toolbar = $this->objSysConfig->getValue('SHOWTOOLBAR', 'news');

        if ($toolbar != 1) {
            return '';
        } else {
            $options = array('storyview', 'showmap', 'map', 'viewtimeline', 'timeline', 'themecloud');

            if (!in_array($current, $options)) {
                $current = 'storyview';
            }

            switch ($current)
            {
                case 'map':
                    $current = 'showmap';
                    break;
                case 'timeline':
                    $current = 'viewtimeline';
                    break;
            }

            $menuOptions = array('storyview', 'showmap', 'viewtimeline', 'themecloud');

            $toolbar = '<div id="topnewsbar"><div id="modernbricksmenu"><ul>';

            foreach ($menuOptions as $option)
            {
                $link = new link ($this->uri(array('action'=>$option)));
                $link->link = $this->objLanguage->languageText('mod_news_toolbar_'.$option, 'news');

                $isCurrent = ($option == $current) ? ' id="current"' : '';

                $toolbar .= '<li'.$isCurrent.' class="'.$option.'">'.$link->show().'</li>';
            }

            $toolbar .= '</ul></div><div id="modernbricksmenuline">&nbsp;</div></div>';

            return $toolbar;
        }
    }

    public function generateMenu()
    {
        $list = $this->getMenuItems();

        $str = '<h2>'.$this->prepareItem_module(array('itemvalue'=>'news', 'itemname'=>$this->objLanguage->languageText('mod_news_frontpage', 'news', 'Front Page'))).'</h2>';

        $homeLinkText = $this->objSysConfig->getValue('mod_news_homelinktext', 'news');
        $str = '<h2 id="newswelcome">'.$this->prepareItem_module(array('itemvalue'=>'news', 'itemname'=>$homeLinkText)).'</h2>';

        if (count($list) == 0){
            return $str.'<p class="warning">'.$this->objLanguage->languageText('mod_news_nosectionssetupyet', 'news', 'No Sections setup yet').'.</p>';
        } else {

            $str .= '<div id="newsmenu">';

            $inUL = FALSE;

            foreach ($list as $item)
            {
                $text = $this->prepareItem($item);

                if ($item['itemtype'] == 'divider' || $item['itemtype'] == 'text' || $item['itemtype'] == 'block') {
                    if ($inUL) {
                        $str .= '</ul>';
                        $inUL = FALSE;
                    }
                    $str .= $text;
                } else {
                    if (!$inUL) {
                        $str .= '<ul class="glossymenu">';
                        $inUL = TRUE;
                    }
                    $str .= '<li>'.$text.'</li>'."\n\n";
                }
            }

            if ($inUL) {
                $str .= '</ul>';
            }

            $str .= '</div>';

            return $str;
        }
    }

    public function getMenuItems()
    {
        return $this->getAll('ORDER BY itemorder');
    }

    public function getItem($id)
    {
        return $this->getRow('id', $id);
    }

    public function prepareItem(&$item)
    {
        $functionName = 'prepareItem_'.$item['itemtype'];
        return $this->$functionName($item);
    }

    private function prepareItem_url($item)
    {
        $link = new link ($item['itemvalue']);
        $link->link = $item['itemname'];
        return $link->show();
    }

    private function prepareItem_module($item)
    {
        $link = new link ($this->uri(NULL, $item['itemvalue']));
        $link->link = $item['itemname'];
        return $link->show();
    }

    private function prepareItem_divider()
    {
        return '<hr />';
    }

    private function prepareItem_text($item)
    {
        return '<br /><h3>'.$item['itemvalue'].'</h3>';
    }

    private function prepareItem_category($item)
    {
        $link = new link ($this->uri(array('action'=>'viewcategory', 'id'=>$item['itemvalue'])));
        $link->link = $item['itemname'];
        return $link->show();
    }
    
    private function prepareItem_block($item)
    {
        $block = explode('|', $item['itemvalue']);
        //var_dump($block);
        return $this->objBlocks->showBlock($block[1], $block[0], NULL, 20, TRUE, FALSE);
    }

    private function addMenuItem($itemtype, $itemvalue, $itemname)
    {
        return $this->insert(array(
            'itemtype' => $itemtype,
            'itemvalue' => $itemvalue,
            'itemname' => $itemname,
            'itemorder' => $this->getLastOrder()+1
        ));
    }

    public function addDivider()
    {
        return $this->addMenuItem('divider', 'divider', 'divider');
    }

    public function addWebsite($title, $url)
    {
        return $this->addMenuItem('url', $url, $title);
    }

    public function addText($text)
    {
        return $this->addMenuItem('text', $text, $text);
    }

    public function addModule($module)
    {
        return $this->addMenuItem('module', $module, ucwords($this->objLanguage->code2Txt('mod_'.$module.'_name',$module)));
    }
    
    public function addBlock($block)
    {
        return $this->addMenuItem('block', $block, $block);
    }

    public function addCategory($id, $name)
    {
        return $this->addMenuItem('category', $id, $name);
    }

    private function getLastOrder()
    {
        $results = $this->getAll('ORDER BY itemorder DESC LIMIT 1');

        if (count($results) == 0) {
            return 0;
        } else {
            return $results[0]['itemorder'];
        }
    }

    public function moveItemUp($id)
    {
        $item = $this->getItem($id);

        $prevItem = $this->getPreviousItem($id);

        if ($item == FALSE || $prevItem == FALSE) {
            return FALSE;
        } else {
            $this->update('id', $item['id'], array('itemorder' => $prevItem['itemorder']));
            $this->update('id', $prevItem['id'], array('itemorder' => $item['itemorder']));

            return TRUE;
        }
    }

    public function moveItemDown($id)
    {
        $item = $this->getItem($id);

        $prevItem = $this->getNextItem($id);

        if ($item == FALSE || $prevItem == FALSE) {
            return FALSE;
        } else {
            $this->update('id', $item['id'], array('itemorder' => $prevItem['itemorder']));
            $this->update('id', $prevItem['id'], array('itemorder' => $item['itemorder']));

            return TRUE;
        }
    }

    public function getPreviousItem($id)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else {
            $item2 = $this->getAll(' WHERE itemorder < '.$item['itemorder'].' ORDER BY itemorder DESC LIMIT 1');

            if (count($item2) == 0) {
                return FALSE;
            } else {
                return $item2[0];
            }
        }
    }

    public function getNextItem($id)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else {
            $item2 = $this->getAll(' WHERE itemorder > '.$item['itemorder'].' ORDER BY itemorder LIMIT 1');

            if (count($item2) == 0) {
                return FALSE;
            } else {
                return $item2[0];
            }
        }
    }

    function deleteDivider($id)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else if ($item['itemtype'] == 'divider') {
            return $this->delete('id', $id);
        } else {
            return FALSE;
        }
    }

    function deleteText($id)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else if ($item['itemtype'] == 'text') {
            return $this->delete('id', $id);
        } else {
            return FALSE;
        }
    }

    function deleteUrl($id)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else if ($item['itemtype'] == 'url') {
            return $this->delete('id', $id);
        } else {
            return FALSE;
        }
    }

    function deleteModule($id)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else if ($item['itemtype'] == 'module') {
            return $this->delete('id', $id);
        } else {
            return FALSE;
        }
    }
    
    function deleteBlock($id)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else if ($item['itemtype'] == 'block') {
            return $this->delete('id', $id);
        } else {
            return FALSE;
        }
    }

    function deleteCategory($id)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else if ($item['itemtype'] == 'category') {
            return $this->delete('id', $id);
        } else {
            return FALSE;
        }
    }

    function updateText($id, $text)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else if ($item['itemtype'] == 'text') {
            return $this->update('id', $id, array('itemvalue'=>$text, 'itemname'=>$text));
        } else {
            return FALSE;
        }
    }

    function updateCategory($id, $name)
    {
        $item = $this->getItem($id);

        if ($item == FALSE) {
            return FALSE;
        } else if ($item['itemtype'] == 'category') {
            return $this->update('id', $id, array('itemname'=>$name));
        } else {
            return FALSE;
        }
    }

    public function getIdCategoryItem($id)
    {
        $sql = 'SELECT id FROM tbl_news_menu WHERE itemtype=\'category\' and itemvalue=\''.$id.'\'';

        $results = $this->getArray($sql);

        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0]['id'];
        }
    }

}
?>
