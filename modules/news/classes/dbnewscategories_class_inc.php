<?php

class dbnewscategories extends dbtable
{

    public function init()
    {
        parent::init('tbl_news_categories');
        $this->loadClass('link', 'htmlelements');

        //$this->categoryOptions = array('list', 'page', 'previous', 'summaries');
        $this->categoryOptions = array('list', 'page', 'previous', 'summaries', 'nextprevious', 'thumbnails', 'calendar');

        $this->objLanguage = $this->getObject('language', 'language');

    }

    public function getCategory($id)
    {
        return $this->getRow('id', $id);
    }

    public function deleteCategory($id)
    {
        return $this->delete('id', $id);
    }

    public function getCategoryName($id)
    {
        $category = $this->getRow('id', $id);

        return $category['categoryname'];

        if (!is_array($category)) {
            return FALSE;
        } else {
            $category['categoryname'];
        }
    }

    public function addBasicCategory($categoryName, $categoryType)
    {
        $function = 'addBasicCategory_'.$categoryType;

        return $this->$function($categoryName);
    }

    private function addBasicCategory_news($categoryName)
    {
        $array = array (
            'categoryname'=>$categoryName,
            'categoryorder'=>$this->getLastOrder()+1,
            'defaultsticky'=>'Y',
            'itemsorder'=>'storydate_desc',
            'itemsview'=>'summaries',
            'usingbasic'=>'Y',
            'basicview'=>'news',
            'showintroduction'=>'N',
            'blockonfrontpage'=>'Y',
            'showrelateditems'=>'Y',
            'ownrssfeed'=>'Y',
            'showsocialbookmarking'=>'Y',
			'pagination' => 20,
        );

        return $this->insert($array);
    }

    private function addBasicCategory_content($categoryName)
    {
        $array = array (
            'categoryname'=>$categoryName,
            'categoryorder'=>$this->getLastOrder()+1,
            'defaultsticky'=>'N',
            'itemsorder'=>'storyorder',
            'itemsview'=>'page',
            'usingbasic'=>'Y',
            'basicview'=>'content',
            'showintroduction'=>'N',
            'blockonfrontpage'=>'N',
            'showrelateditems'=>'Y',
            'ownrssfeed'=>'N',
            'showsocialbookmarking'=>'N',
        );

        return $this->insert($array);
    }

    public function updateBasicCategory_content($id, $name)
    {
        $array = array (
            'categoryname'=>$name,
            'defaultsticky'=>'N',
            'itemsorder'=>'storyorder',
            'itemsview'=>'page',
            'usingbasic'=>'Y',
            'basicview'=>'content',
            'showintroduction'=>'N',
            'blockonfrontpage'=>'N',
            'showrelateditems'=>'Y',
			'ownrssfeed'=>'N',
            'showsocialbookmarking'=>'N',
			'pagination' => 0,
        );

        return $this->update('id', $id, $array);
    }

    public function updateBasicCategory_news($id, $name)
    {
        $array = array (
            'categoryname'=>$name,
            'defaultsticky'=>'Y',
            'itemsorder'=>'storydate_desc',
            'itemsview'=>'summaries',
            'usingbasic'=>'Y',
            'basicview'=>'news',
            'showintroduction'=>'N',
            'blockonfrontpage'=>'Y',
            'showrelateditems'=>'Y',
            'ownrssfeed'=>'Y',
            'showsocialbookmarking'=>'Y',
			'pagination' => 20,
        );

        return $this->update('id', $id, $array);
    }

    public function addAdvancedCategory($name, $defaultSticky, $itemsOrder, $itemsView, $introduction, $showIntroduction, $blockOnFrontPage, $pagination, $rssFeeds, $socialBookmarks)
    {
        return $this->insert(array(
            'categoryname' => $name,
            'categoryorder' => $this->getLastOrder()+1,
            'defaultsticky' => $defaultSticky,
            'itemsorder' => $itemsOrder,
            'itemsview' => $itemsView,
            'introduction' => $introduction,
            'usingbasic' => 'N',
            'showintroduction' => $showIntroduction,
            'blockonfrontpage' => $blockOnFrontPage,
            'showrelateditems' => 'Y',
            'pagination' => $pagination,
            'ownrssfeed' => $rssFeeds,
            'showsocialbookmarking' => $socialBookmarks,
        ));

    }

    public function updateAdvancedCategory($id, $name, $defaultSticky, $itemsOrder, $itemsView, $introduction, $showIntroduction, $blockOnFrontPage, $pagination, $rssFeeds, $socialBookmarks)
    {
        return $this->update('id', $id, array(
            'categoryname' => $name,
            'defaultsticky' => $defaultSticky,
            'itemsorder' => $itemsOrder,
            'itemsview' => $itemsView,
            'introduction' => $introduction,
            'usingbasic' => 'N',
            'showintroduction' => $showIntroduction,
            'blockonfrontpage' => $blockOnFrontPage,
            'showrelateditems' => 'Y',
            'pagination' => $pagination,
            'ownrssfeed' => $rssFeeds,
            'showsocialbookmarking' => $socialBookmarks,
        ));

    }


    private function getLastOrder()
    {
        $result = $this->getAll(' ORDER BY categoryorder DESC LIMIT 1');

        if (count($result) == 0) {
            return 0;
        } else {
            return $result[0]['categoryorder'];
        }
    }

    public function getCategories($orderBy='categoryorder')
    {
        return $this->getAll(' ORDER BY '.$orderBy);
    }


    /**
      * Method to get category id based on category name
      *
      *
      * @param string $name The name of the category
      */
    public function getCategoryId($name) {
        $category = $this->getRow('categoryname', $name);

        return $category['id'];
    }

    public function categoryExists($name)
    {
        $count = $this->getRecordCount('WHERE categoryname=\''.$name.'\'');

        if ($count == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function getCategoriesMenu()
    {
        $results = $this->getCategoriesWithStories();

        if (count($results) == 0) {
            return '';
        } else {
            $str = '<h2>News Categories</h2><ul id="nav-secondary">';

            $homeLink = new link ($this->uri(array('action'=>'home')));
            $homeLink->link = 'Home';
            $str .= '<li>'.$homeLink->show().'</li>';


            foreach ($results as $result)
            {
                $link = new link ($this->uri(array('action'=>'viewcategory', 'id'=>$result['id'])));
                $link->link = $result['categoryname'];

                $str .= '<li>'.$link->show().'</li>';
            }

            $str .= '</ul>';

            return $str;
        }
    }

    public function getCategoriesWithStories($order='categoryorder')
    {
        $sql = 'SELECT tbl_news_categories.id, categoryname, showintroduction, introduction, blockonfrontpage FROM tbl_news_categories, tbl_news_stories WHERE (tbl_news_categories.id = tbl_news_stories.storycategory) GROUP BY categoryname ORDER BY '.$order;

        return $this->getArray($sql);
    }



    public function showCategoryForm($catId=NULL, $id=NULL, $returnAction='viewcategory')
    {
        $objCache = $this->getObject('cache', 'utilities');
        
        
        
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');

        // Check If User is editing a category
        if ($catId != NULL) {
            // Get Category
            $category = $this->getCategory($catId);

            // Disable if Category does not exist
            if ($category == FALSE) {
                $category = '';
                $id = '';
            }

            // Category Exists - Does Menu Id Exist?
            if ($id == '') {
                // If not, disable.
                $category = '';
            }
        } else {
            $category = '';
        }

        if (isset($category['id'])) {
            $formAction = 'updatebasiccategory';;
        } else {
            $formAction = 'savebasiccategory';
        }

        //$objCache->setup('addnewcategorybasicform', 'news', 1000);
        //$basicForm = $objCache->get();
        
        // Start Basic Form
        //if ($catId==NULL && $id==NULL && $basicForm != FALSE)
        //{
            
        //} else {
        
            $basicForm = new form ('basicsetup', $this->uri(array('action'=>$formAction)));
    
            $table = $this->newObject('htmltable', 'htmlelements');
    
            $label = new label ($this->objLanguage->languageText('mod_blog_cathead_name', 'blog', 'Category Name').':', 'input_basiccategory');
            $basicCategory = new textinput ('basiccategory');
            $basicCategory->size = 60;
            $basicCategory->extra = ' maxlength="50" ';
            //$basicCategory->extra = ' maxlength="50" onkeyup="document.forms[\'advancedsetup\'].advancecategory.value=this.value;"';
    
            if (isset($category['categoryname'])) {
                $basicCategory->value = $category['categoryname'];
            }
    
            $table->startRow();
            $table->addCell($label->show());
            $table->addCell($basicCategory->show());
            $table->endRow();
    
            $basicCategoryType = new radio ('basiccategorytype');
            $basicCategoryType->breakSpace = 'table';
            $basicCategoryType->tableColumns = 2;
            $basicCategoryType->addOption('news', $this->objLanguage->languageText('mod_news_newssection', 'news', 'News Section').'<br /><img src="'.$this->getResourceUri('type_news.gif').'" />');
            $basicCategoryType->addOption('content', $this->objLanguage->languageText('mod_news_contentsection', 'news', 'Content Section').'<br /><img src="'.$this->getResourceUri('type_content.gif').'" />');
    
            if (isset($category['categoryname'])) {
                $basicCategoryType->setSelected($category['basicview']);
            } else {
                $basicCategoryType->setSelected('news');
            }
    
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_categorytype', 'news', 'Category Type').':');
            $table->addCell($basicCategoryType->show());
            $table->endRow();
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
    
            if (isset($category['id'])) {
                $button = new button ('saveform', $this->objLanguage->languageText('mod_news_updatecategory', 'news', 'Update Category'));
                $button->setToSubmit();
            } else {
                $button = new button ('saveform', $this->objLanguage->languageText('mod_news_addcategory', 'news', 'Add Category'));
                $button->setToSubmit();
            }
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell($button->show());
            $table->endRow();
    
            $basicForm->addToForm($table->show());
    
            if (isset($id)) {
                $hiddeninput = new hiddeninput('id', $id);
                $basicForm->addToForm($hiddeninput->show());
            }
    
            if (isset($returnAction)) {
                $hiddenreturnactioninput = new hiddeninput('returnaction', $returnAction);
                $basicForm->addToForm($hiddenreturnactioninput->show());
            }
    
            $basicForm->addRule('basiccategory', addslashes($this->objLanguage->languageText('mod_news_enternameofcategory', 'news', 'Please enter the name of the category')),'required');
            
            $basicForm = $basicForm->show();
            
            // Store Contents into a a cache
            //$objCache->set($basicForm);
        //}
        // End Basic Form


        // Start Advanced Form
        /*$objCache->setup('addnewadvancecategoryform', 'news', 1000);
        $advanceForm = $objCache->get();
        
        // Start Basic Form
        if ($catId==NULL && $id==NULL && $advanceForm != FALSE)
        {
            
        } else {*/
            if (isset($category['id'])) {
                $formAction = 'updateadvancecategory';;
            } else {
                $formAction = 'saveadvancecategory';
            }
            $advancedForm = new form ('advancedsetup', $this->uri(array('action'=>$formAction)));
    
            $table = $this->newObject('htmltable', 'htmlelements');
    
            $label = new label ($this->objLanguage->languageText('mod_blog_cathead_name', 'blog', 'Category Name').':', 'input_basiccategory');
            $advanceCategory = new textinput ('advancecategory');
            $advanceCategory->size = 60;
            $advanceCategory->extra = ' maxlength="50" onkeyup="document.forms[\'basicsetup\'].basiccategory.value=this.value;"';
    
            if (isset($category['categoryname'])) {
                $advanceCategory->value = $category['categoryname'];
            }
    
            $table->startRow();
            $table->addCell($label->show());
            $table->addCell($advanceCategory->show());
            $table->endRow();
    
    
    
            $advanceCategoryType = new radio ('advancecategorytype');
            $advanceCategoryType->breakSpace = 'table';
            $advanceCategoryType->tableColumns = 4;
    
            foreach ($this->categoryOptions as $type)
            {
            $advanceCategoryType->addOption($type, $this->objLanguage->languageText('mod_news_sectiontype_'.$type, 'news').'<br /><img src="'.$this->getResourceUri('section_'.$type.'.gif').'" />');
            }
    
            if (isset($category['itemsview'])) {
                $advanceCategoryType->setSelected($category['itemsview']);
            } else {
                $advanceCategoryType->setSelected('list');
            }
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_categorytype', 'news', 'Category Type').':');
            $table->addCell($advanceCategoryType->show());
            $table->endRow();
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
            
            
            // Items Order
            
            $itemsOrder = new radio ('advanceorder');
            $itemsOrder->setBreakSpace('<br />');
            $itemsOrder->addOption('storyorder', $this->objLanguage->languageText('mod_news_order_manualorder', 'news', 'Manual Order'));
            $itemsOrder->addOption('storydate', $this->objLanguage->languageText('mod_news_order_dateasc', 'news', 'Date in Ascending Order'));
            $itemsOrder->addOption('storydate_desc', $this->objLanguage->languageText('mod_news_order_datedesc', 'news', 'Date in Descending Order'));
            $itemsOrder->addOption('storytitle', $this->objLanguage->languageText('mod_news_order_titleasc', 'news', 'Title in Alphabetical Order'));
            $itemsOrder->addOption('storytitle_desc', $this->objLanguage->languageText('mod_news_order_titledesc', 'news', 'Title in Reversed Alphabetical Order'));
    
            if (isset($category['itemsorder'])) {
                $itemsOrder->setSelected($category['itemsorder']);
            } else {
                $itemsOrder->setSelected('storydate_desc');
            }
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_articleorder', 'news', 'Article Order').':');
            $table->addCell($itemsOrder->show());
            $table->endRow();
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
            
            // Num Items per Page
    
            $numItems = new radio ('numitems');
            $numItems->setBreakSpace(' &nbsp; ');
            $numItems->addOption('0', 'All');
            $numItems->addOption('5', '5');
            $numItems->addOption('10', '10');
            $numItems->addOption('15', '15');
            $numItems->addOption('20', '20');
            $numItems->addOption('30', '30');
            $numItems->addOption('other', 'Other:');
    
            $numItemOptions = array('0', '5', '10', '15', '20', '30');
    
            $otherNum = new textinput('othernum');
            $otherNum->size = 5;
    
            if (isset($category['pagination'])) {
                if (in_array($category['pagination'], $numItemOptions)) {
                    $numItems->setSelected($category['pagination']);
                } else {
                    $numItems->setSelected('other');
                }
    
                $otherNum->value = $category['pagination'];
            } else {
                $numItems->setSelected('5');
            }
    
            //$objInputMasks = $this->getObject('inputmasks', 'htmlelements');
            //$objInputMasks->show();
    
    
            //$otherNum->setCss('text input_mask number');
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_numitemsperpage', 'news', 'Number of Items per Page').':');
            $table->addCell($this->objLanguage->languageText('mod_news_numitemsperpage_explanation', 'news', 'How many items should be displayed on a page before pagination is introducted?').'<br />'.$numItems->show().$otherNum->show().'<p><em>'.$this->objLanguage->languageText('mod_news_numitemsperpage_relevance', 'news', 'Only relevant when the Category Type is').' <strong>Page Summaries</strong>, <strong>List of Pages</strong> or <strong>View Previous Pages</strong></em></p>');
            $table->endRow();
            
            // Block on Front Page
    
            $showBlock = new radio ('blockonfrontpage');
            $showBlock->setBreakSpace(' &nbsp; ');
            $showBlock->addOption('Y', 'Yes');
            $showBlock->addOption('N', 'No');
    
            if (isset($category['blockonfrontpage'])) {
                $showBlock->setSelected($category['blockonfrontpage'] == '' ? 'N' : $category['blockonfrontpage']);
            } else {
                $showBlock->setSelected('Y');
            }
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_showblockonfrontpage', 'news', 'Show Block on Front Page').':');
            $table->addCell($this->objLanguage->languageText('mod_news_showblockonfrontpage_explanation', 'news', 'This will show the five latest stories in this category on the Front Page').' '.$showBlock->show());
            $table->endRow();
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
            
            // Sticky Items
    
            $defaultSticky = new radio ('defaultsticky');
            $defaultSticky->setBreakSpace(' &nbsp; ');
            $defaultSticky->addOption('Y', 'Yes');
            $defaultSticky->addOption('N', 'No');
    
            if (isset($category['defaultsticky'])) {
                $defaultSticky->setSelected($category['defaultsticky'] == '' ? 'N' : $category['defaultsticky']);
            } else {
                $defaultSticky->setSelected('Y');
            }
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_defaultsticky', 'news', 'Default Sticky').':');
            $table->addCell($this->objLanguage->languageText('mod_news_defaultsticky_question', 'news', 'Should New Articles be marked as Sticky by default').'? '.$defaultSticky->show().'<p><em>'.$this->objLanguage->languageText('mod_news_defaultsticky_explanation', 'news', 'Sticky means they will appear on the Home Page as a Latest Item').'</em></p>');
            $table->endRow();
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
            
            // RSS Feeds
            
            $rssFeeds = new radio ('rssfeeds');
            $rssFeeds->setBreakSpace(' &nbsp; ');
            $rssFeeds->addOption('Y', 'Yes');
            $rssFeeds->addOption('N', 'No');
    
            if (isset($category['ownrssfeed'])) {
                $rssFeeds->setSelected($category['ownrssfeed'] == '' ? 'N' : $category['ownrssfeed']);
            } else {
                $rssFeeds->setSelected('Y');
            }
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('phrase_rssfeeds', 'system', 'RSS Feeds').':');
            $table->addCell($this->objLanguage->languageText('mod_news_createrssfeedforsection', 'news', 'Create a RSS Feed for stories in this section?').' '.$rssFeeds->show());
            $table->endRow();
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
    
            
            // Social Bookmarking
    
            $socialBookmarks = new radio ('socialbookmarks');
            $socialBookmarks->setBreakSpace(' &nbsp; ');
            $socialBookmarks->addOption('Y', 'Yes');
            $socialBookmarks->addOption('N', 'No');
    
            if (isset($category['showsocialbookmarking'])) {
                $socialBookmarks->setSelected($category['showsocialbookmarking'] == '' ? 'N' : $category['showsocialbookmarking']);
            } else {
                $socialBookmarks->setSelected('N');
            }
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_enablesocialbookmarks', 'news', 'Enable Social Bookmarks').':');
            $table->addCell($this->objLanguage->languageText('mod_news_enablesocialbookmarks_explanation', 'news', 'This will allow users to bookmark articles on Digg, Del.icio.us, Facebook, etc.').' '.$socialBookmarks->show());
            $table->endRow();
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
            
            
            // Introduction Text
    
            
    
            $showIntroduction = new radio ('showintroduction');
            $showIntroduction->setBreakSpace(' &nbsp; ');
            $showIntroduction->addOption('Y', 'Yes');
            $showIntroduction->addOption('N', 'No');
    
            if (isset($category['showintroduction'])) {
                $showIntroduction->setSelected($category['showintroduction'] == '' ? 'N' : $category['showintroduction']);
            } else {
                $showIntroduction->setSelected('N');
            }
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_showintroduction', 'news', 'Show Introduction').':');
            $table->addCell($this->objLanguage->languageText('mod_news_showintroduction_question', 'news', 'Should the Introduction Text be displayed').'? '.$showIntroduction->show().'<p><em>'.$this->objLanguage->languageText('mod_news_numitemsperpage_relevance', 'news', 'Only relevant when the Category Type is').' <strong>Page Summaries</strong>, <strong>List of Pages</strong> or <strong>Page Thumbnails</strong></em></p>');
            $table->endRow();
    
            //
    
    
    
            $objIntroduction = $this->newObject('htmlarea', 'htmlelements');
            $objIntroduction->name = 'introduction';
    
            if (isset($category['introduction'])) {
                $objIntroduction->value = $category['introduction'];
            }
    
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_news_introductiontext', 'news', 'Introduction Text').':');
            $table->addCell($objIntroduction->show());
            $table->endRow();
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
    
            if (isset($category['id'])) {
                $button = new button ('saveform', $this->objLanguage->languageText('mod_news_updatecategory', 'news', 'Update Category'));
                $button->setToSubmit();
            } else {
                $button = new button ('saveform', $this->objLanguage->languageText('mod_news_addcategory', 'news', 'Add Category'));
                $button->setToSubmit();
            }
    
            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell($button->show());
            $table->endRow();
    
            $advancedForm->addToForm($table->show());
    
            if (isset($id)) {
                $hiddeninput = new hiddeninput('id', $id);
                $advancedForm->addToForm($hiddeninput->show());
            }
    
            if (isset($returnAction)) {
                $hiddenreturnactioninput = new hiddeninput('returnaction', $returnAction);
                $advancedForm->addToForm($hiddenreturnactioninput->show());
            }
            
            $advanceForm = $advancedForm->show();
            
            // Store Contents into a a cache
            //$objCache->set($advanceForm);
        //}

        // End Advanced Form

        $objTabs = $this->getObject('tabcontent', 'htmlelements');
        $objTabs->width = '98%';
        $objTabs->addTab($this->objLanguage->languageText('mod_news_basiccategory', 'news', 'Basic Category'), $basicForm);

        $isAdvanced = FALSE;

        if (isset($category['usingbasic']) && $category['usingbasic'] == 'N') {
            $isAdvanced = TRUE;
        }

        $objTabs->addTab($this->objLanguage->languageText('mod_news_advancedcategory', 'news', 'Advanced Category'), $advanceForm, '', $isAdvanced);

        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        $objHighlightLabels->show();

        //return $basicForm->show();

        return $objTabs->show();
    }



}
?>
