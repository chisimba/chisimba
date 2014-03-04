<?php
/* -------------------- dbfoafusers class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class foafui extends object {
    /**
     * UI Elements (forms and stuff) for the FOAF module
     * @author Paul Scott
     * @access public
     * @filesource
     */
    public $objLanguage;

    public function init()
    {
        try {
            $this->loadClass('form', 'htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('fieldset', 'htmlelements');
            $this->loadClass('href', 'htmlelements');
            $this->objLanguage = $this->getObject('language', 'language');
            //the object needed to create FOAF files (RDF)
            $this->objFoaf = $this->getObject('foafcreator');
            //Object to parse and display FOAF RDF
            $this->objFoafParser = $this->getObject('foafparser');
            //load up the foaf factory class
            $this->objFoafOps = $this->getObject('foafops');
            $this->objUser = $this->getObject('user', 'security');
            $this->dbFoaf = $this->getObject('dbfoaf', 'foaf');
        }
        catch (customException $e)
        {
            customException::cleanUp();
            exit;
        }

    }

    /**
     * Method to generate the myfoaf form
     * 
     * @access public
     * @return string
     */
    public function myFoaf($tcont)
    {
        //lets start the forms now. First we do tbl_foaf_myfoaf
        //create the form
        $myFoafForm = new form('myfoaf', $this->uri(array(
        'action' => 'insertmydetails'
        )));
        $fieldset1 = $this->getObject('fieldset', 'htmlelements');
        $fieldset1->setLegend($this->objLanguage->languageText('mod_foaf_detailsfor', 'foaf') ." ".$tcont->foaf['type']." <em>".$tcont->foaf['title']." ".$tcont->foaf['name']."</em>");
        $table1 = $this->getObject('htmltable', 'htmlelements');
        $table1->cellpadding = 5;
        //homepage field
        $table1->startRow();
        $label1 = new label($this->objLanguage->languageText('mod_foaf_homepage', 'foaf') .':', 'input_homepage');
        $homepage = new textinput('homepage');
        if (!isset($tcont->foaf['homepage'][0])) {
            $tcont->foaf['homepage'][0] = NULL;
        }
        $homepage->value = htmlentities($tcont->foaf['homepage'][0]);
        $table1->addCell($label1->show() , 150, NULL, 'right');
        $table1->addCell($homepage->show());
        $table1->endRow();
        //weblog field
        $table1->startRow();
        $label2 = new label($this->objLanguage->languageText('mod_foaf_weblog', 'foaf') .':', 'input_weblog');
        $weblog = new textinput('weblog');
        if (!isset($tcont->foaf['weblog'][0])) {
            $tcont->foaf['weblog'][0] = NULL;
        }
        $weblog->value = htmlentities($tcont->foaf['weblog'][0]);
        //echo $tcont->foaf['weblog'][0];
        $table1->addCell($label2->show() , 150, NULL, 'right');
        $table1->addCell($weblog->show());
        $table1->endRow();
        //phone field
        $table1->startRow();
        $label3 = new label($this->objLanguage->languageText('mod_foaf_phone', 'foaf') .':', 'input_phone');
        $phone = new textinput('phone');
        if (!isset($tcont->foaf['phone'][0])) {
            $tcont->foaf['phone'][0] = NULL;
        }
        $phone->value = $tcont->foaf['phone'][0];
        $table1->addCell($label3->show() , 150, NULL, 'right');
        $table1->addCell($phone->show());
        $table1->endRow();
        //Jabber ID
        $table1->startRow();
        $label4 = new label($this->objLanguage->languageText('mod_foaf_jabberid', 'foaf') .':', 'input_jabberid');
        $jabberid = new textinput('jabberid');
        if (!isset($tcont->foaf['jabberid'][0])) {
            $tcont->foaf['jabberid'][0] = NULL;
        }
        $jabberid->value = $tcont->foaf['jabberid'][0];
        $table1->addCell($label4->show() , 150, NULL, 'right');
        $table1->addCell($jabberid->show());
        $table1->endRow();
        //theme
        $table1->startRow();
        $label5 = new label($this->objLanguage->languageText('mod_foaf_theme', 'foaf') .':', 'input_theme');
        $theme = new textinput('theme');
        if (!isset($tcont->foaf['theme'][0])) {
            $tcont->foaf['theme'][0] = NULL;
        }
        $theme->value = $tcont->foaf['theme'][0];
        $table1->addCell($label5->show() , 150, NULL, 'right');
        $table1->addCell($theme->show());
        $table1->endRow();
        //work homepage field
        $table1->startRow();
        $label6 = new label($this->objLanguage->languageText('mod_foaf_workhomepage', 'foaf') .':', 'input_workhomepage');
        $workhomepage = new textinput('workhomepage');
        if (!isset($tcont->foaf['workplacehomepage'][0])) {
            $tcont->foaf['workplacehomepage'][0] = NULL;
        }
        $workhomepage->value = $tcont->foaf['workplacehomepage'][0];
        $table1->addCell($label6->show() , 150, NULL, 'right');
        $table1->addCell($workhomepage->show());
        $table1->endRow();
        //school homepage field
        $table1->startRow();
        $label7 = new label($this->objLanguage->languageText('mod_foaf_schoolhomepage', 'foaf') .':', 'input_schoolhomepage');
        $schoolhomepage = new textinput('schoolhomepage');
        if (!isset($tcont->foaf['schoolhomepage'][0])) {
            $tcont->foaf['schoolhomepage'][0] = NULL;
        }
        $schoolhomepage->value = $tcont->foaf['schoolhomepage'][0];
        $table1->addCell($label7->show() , 150, NULL, 'right');
        $table1->addCell($schoolhomepage->show());
        $table1->endRow();
        //logo field
        $table1->startRow();
        $label8 = new label($this->objLanguage->languageText('mod_foaf_logo', 'foaf') .':', 'input_logo');
        $logo = new textinput('logo');
        if (!isset($tcont->foaf['logo'][0])) {
            $tcont->foaf['logo'][0] = NULL;
        }
        $logo->value = $tcont->foaf['logo'][0];
        $table1->addCell($label8->show() , 150, NULL, 'right');
        $table1->addCell($logo->show());
        $table1->endRow();
        //basednear field
        /*
        $table1->startRow();
        $label9 = new label($this->objLanguage->languageText('mod_foaf_basednear', 'foaf').':', 'foaf_basednear');
        $basednear = new textinput('basednear');
        if(!isset($tcont->foaf['basednear'][0]))
        {
        $tcont->foaf['basednear'][0] = NULL;
        }
        $basednear->value= $tcont->foaf['basednear'][0];
        $table1->addCell($label9->show(), 150, NULL, 'right');
        $table1->addCell($basednear->show());
        $table1->endRow();
        */
        //geekcode field
        $table1->startRow();
        $label10 = new label($this->objLanguage->languageText('mod_foaf_geekcode', 'foaf') .':', 'input_geekcode');
        $geekcode = new textarea('geekcode');
        if (!isset($tcont->foaf['geekcode'])) {
            $tcont->foaf['geekcode'] = NULL;
        }
        $geekcode->value = $tcont->foaf['geekcode'];
        $table1->addCell($label10->show() , 150, NULL, 'right');
        $table1->addCell($geekcode->show());
        $table1->endRow();
        $fieldset1->addContent($table1->show());
        $myFoafForm->addToForm($fieldset1->show());
        $this->objButton1 = &new button($this->objLanguage->languageText('word_update', 'system'));
        $this->objButton1->setValue($this->objLanguage->languageText('word_update', 'system'));
        $this->objButton1->setToSubmit();
        $myFoafForm->addToForm($this->objButton1->show());
        $myFoafForm = $myFoafForm->show();

        return $myFoafForm;
    }


    //friends
    public function manageFriends()
    {

        //add/remove friends
        $addFriendsForm = $this->objFoafOps->addDD();
        $remFriendsForm = $this->objFoafOps->remDD();
        return $addFriendsForm->show() .$remFriendsForm->show() ;

    }

    public function foafFriends($tcont)
    {


        $manageFriends = new href($this->uri(array('action' => 'admin' , 'content' => 'fndadmin')) , $this->objLanguage->languageText('mod_foaf_mngfriends' , 'foaf'));



        if (isset($tcont->foaf['knows'])) {
            if (is_array($tcont->foaf['knows'])) {
                foreach($tcont->foaf['knows'] as $pals) {
                    if($pals['type'] == 'Person') {
                        $info[] = $this->objFoafOps->fFeatureBoxen($pals);
                    }

                }
            }


            if(empty($info)) {
                $myFriendsForm = $this->objFoafOps->addDD();
                //$myFriendsForm.= $this->objFoafOps->remDD();
                $objFeatureBox = $this->newObject('featurebox', 'navigation');
                $myFbox = $objFeatureBox->show($this->objLanguage->languageText('mod_foaf_nofriends', 'foaf') , $this->objLanguage->languageText('mod_foaf_nofriendstxt', 'foaf'));

            } else {


                //build the featurebox
                $mypfbox = NULL;
                $myFbox = NULL;
                foreach($info as $okes) {


                    $objFeatureBox = $this->newObject('featurebox', 'navigation');
                    //take the pfimage and the pfbox
                    $table2 = $this->newObject('htmltable', 'htmlelements');
                    $table2->cellpadding = 5;
                    $table2->startRow();
                    $table2->addCell($okes[0]);
                    $table2->addCell($okes[1]);
                    $table2->endRow();
                    $mypfbox.= $table2->show() ."<br />";
                    $myFbox.= $objFeatureBox->show($okes[3], $mypfbox) ."<br />";
                    $mypfbox = NULL;

                }
            }

        } else {
            $myFriendsForm = $this->objFoafOps->addDD();
            //$myFriendsForm.= $this->objFoafOps->remDD();
            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $myFbox = $objFeatureBox->show($this->objLanguage->languageText('mod_foaf_nofriends', 'foaf') , $this->objLanguage->languageText('mod_foaf_nofriendstxt', 'foaf'));
        }

        return $manageFriends->show().$myFbox;
    }


    //organizations
    public function manageOrgs()
    {

        return  $this->objFoafOps->orgaRemForm() . $this->objFoafOps->orgaAddForm();

    }

    public function foafOrgs($tcont)
    {
        $myorgFbox = NULL;
        $myorgs = NULL;
        $manageOrgs = new href($this->uri(array('action' => 'admin' , 'content' => 'orgsadmin')) , $this->objLanguage->languageText('mod_foaf_mngorgs' , 'foaf'));
        $myorgs .= $manageOrgs->show();

        //build the featureboxen for the orgs

        if (!array_key_exists('knows', $tcont->foaf)) {
            $tcont->foaf['knows'] = array();
        }
        if (!isset($tcont->foaf['knows'])) {
            $tcont->foaf['knows'] = array();
        }
        foreach($tcont->foaf['knows'] as $pal) {
            if($pal['type'] == 'Organization') {
                $orginfo[] = $this->objFoafOps->orgFbox($pal);
            }
        }


        if(empty($orginfo)) {
            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $orgs = $this->objLanguage->languageText('mod_foaf_organizations' , 'foaf');
            $myorgs .= $objFeatureBox->show($orgs , $this->objLanguage->languageText('mod_foaf_noitemstxt', 'foaf'));

        } else {

            //print_r($orginfo); die();
            $myorgFbox = NULL;
            $myorgbox = NULL;
            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            //take the pfimage and the pfbox
            $tableoo = $this->newObject('htmltable', 'htmlelements');
            $tableoo->cellpadding = 5;
            $tableoo->cellspacing = 5;
            $tableoo->startHeaderRow();
            $tableoo->addHeaderCell($this->objLanguage->languageText('mod_foaf_oname', 'foaf'), NULL, 'top' , 'center');
            $tableoo->addHeaderCell($this->objLanguage->languageText('mod_foaf_page', 'foaf'), NULL, 'top' , 'center');
            $tableoo->endHeaderRow();

            if (!isset($orginfo)) {
                $orginfo = array();
            }
            foreach($orginfo as $orgas) {
                if ($orgas[1] == 'Organization') {
                    $tableoo->startRow();
                    $tableoo->addCell($orgas[0] , 20);
                    $tableoo->addCell($orgas[2] , 20);
                    $tableoo->endRow();
                }
            }
            //add the featureboxen to the main output
            $myorgbox.= $tableoo->show() ."<br />";
            $myorgFbox.= $objFeatureBox->show($this->objLanguage->languageText('mod_foaf_myorganizations', 'foaf'), $myorgbox) ."<br />";
        }
        $myorgbox = NULL;
        
        $myorgs.= $myorgFbox;
        return $myorgs;
    }


    public function manageFunders()
    {
        return $this->objFoafOps->remFunderForm() . $this->objFoafOps->addFunderForm();
    }



    public function foafFunders($tcont)
    {

        $myfunders = NULL;
        $manageFuns = new href($this->uri(array('action' => 'admin' , 'content' => 'fnsadmin')) , $this->objLanguage->languageText('mod_foaf_mngfunders' , 'foaf'));
        $myfunders .= $manageFuns->show();
        $funders = $this->objLanguage->languageText('mod_foaf_funders' , 'foaf');

        //build the featureboxen for the funders

        if (!array_key_exists('fundedby', $tcont->foaf)) {
            $tcont->foaf['fundedby'] = array();
        }
        if (!isset($tcont->foaf['fundedby'])) {
            $tcont->foaf['fundedby'] = array();
        }

        if(!empty($tcont->foaf['fundedby']))
        {
            $myfunFbox = NULL;
            $myfunbox = NULL;
            $link = NULL;




            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $tablefuns = $this->newObject('htmltable', 'htmlelements');
            $tablefuns->cellpadding = 5;

            foreach($tcont->foaf['fundedby'] as $funder) {

                $page = new href(htmlentities($funder) , htmlentities($funder) , "title='".$this->objLanguage->languageText('mod_foaf_goto' , 'foaf')."  ".$funder."'");
                $link = $page->show();
                $tablefuns->startRow();
                $tablefuns->addCell("<em>".$funder."</em>");
                $tablefuns->addCell($link);
                $tablefuns->endRow();

            }

            $myfunbox.= $tablefuns->show() ."<br />";
            $myfunFbox.= $objFeatureBox->show($funders , $myfunbox) ."<br />";

            $myfunders.= $myfunFbox;

        } else {

            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $myfunders .= $objFeatureBox->show($funders , $this->objLanguage->languageText('mod_foaf_noitemstxt', 'foaf'));




        }
        return $myfunders;
    }






    public function manageInterests()
    {
        return $this->objFoafOps->remInterestForm() . $this->objFoafOps->addInterestForm();
    }





    public function foafInterests($tcont)
    {
        $myints = NULL;
        $manageints = new href($this->uri(array('action' => 'admin' , 'content' => 'intadmin')) , $this->objLanguage->languageText('mod_foaf_mnginterests' , 'foaf'));
        $myints .= $manageints->show();
        $interests = $this->objLanguage->languageText('mod_foaf_interests', 'foaf');

        //build the featureboxen for user interests

        if (!array_key_exists('interest', $tcont->foaf)) {
            $tcont->foaf['interest'] = array();
        }
        if (!isset($tcont->foaf['interest'])) {
            $tcont->foaf['interest'] = array();
        }

        if(!empty($tcont->foaf['interest']))
        {
            $myintFbox = NULL;
            $myintbox = NULL;
            $link = NULL;




            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cellpadding = 5;

            foreach($tcont->foaf['interest'] as $interest) {

                $page = new href(htmlentities($interest) , htmlentities($interest) , "title='".$this->objLanguage->languageText('mod_foaf_goto' , 'foaf')."  ".$interest."'");
                $link = $page->show();
                $table->startRow();
                $table->addCell("<em>".$interest."</em>");
                $table->addCell($link);
                $table->endRow();

            }

            $myintbox.= $table->show() ."<br />";
            $myintFbox.= $objFeatureBox->show($interests, $myintbox) ."<br />";

            $myints.= $myintFbox;

        } else {

            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $myints .= $objFeatureBox->show($interests , $this->objLanguage->languageText('mod_foaf_noitemstxt', 'foaf'));




        }
        return $myints;
    }





    public function manageDepictions()
    {
        return $this->objFoafOps->remDepictionForm() . $this->objFoafOps->addDepictionForm();
    }



    public function foafDepictions($tcont)
    {


        $mydeps = NULL;
        $managedeps = new href($this->uri(array('action' => 'admin' , 'content' => 'imgadmin')) , $this->objLanguage->languageText('mod_foaf_mngimages' , 'foaf'));
        $mydeps .= $managedeps->show();
        $imgs = $this->objLanguage->languageText('mod_foaf_myimages' , 'foaf');
        //build the featureboxen for user depictions

        if (!array_key_exists('depiction', $tcont->foaf)) {
            $tcont->foaf['depiction'] = array();
        }
        if (!isset($tcont->foaf['depiction'])) {
            $tcont->foaf['depiction'] = array();
        }

        if(!empty($tcont->foaf['depiction']))
        {
            $mydepFbox = NULL;
            $mydepbox = NULL;
            $link = NULL;




            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cellpadding = 0;
            $table->cellspacing = 0;
            $table->border = 1;

            foreach($tcont->foaf['depiction'] as $depiction) {

                $page = new href(htmlentities($depiction) , htmlentities($depiction) , 'title="'.$this->objLanguage->languageText('mod_foaf_imagelocation' , 'foaf').' "');
                $link = $page->show();
                $image = $this->getObject('image', 'htmlelements');
                $image->src = $depiction;
                $image->width = 50;
                $image->height = 50;
                $image->alt = $this->objLanguage->languageText('mod_foaf_nopreview' , 'foaf');
                $table->startRow();
                $table->addCell($image->show());
                $table->addCell($link);
                $table->endRow();

            }

            $mydepbox.= $table->show() ."<br />";
            $mydepFbox.= $objFeatureBox->show($imgs, $mydepbox) ."<br />";

            $mydeps.= $mydepFbox;

        } else {

            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $mydeps .= $objFeatureBox->show($imgs , $this->objLanguage->languageText('mod_foaf_noitemstxt', 'foaf'));

        }
        return $mydeps;
    }



    //pages

    public function managePages()
    {
        return $this->objFoafOps->remPageForm() . $this->objFoafOps->addPageForm();

    }

    public function foafPages($tcont)
    {


        $mypages = NULL;
        $managepgs = new href($this->uri(array('action' => 'admin' , 'content' => 'pgsadmin')) , $this->objLanguage->languageText('mod_foaf_mngpages' , 'foaf'));
        $mypages .= $managepgs->show();
        $pages = $this->objLanguage->languageText('mod_foaf_pages', 'foaf');
        //build the featureboxen for user pages

        if (!array_key_exists('page', $tcont->foaf)) {
            $tcont->foaf['page'] = array();
        }
        if (!isset($tcont->foaf['page'])) {
            $tcont->foaf['page'] = array();
        }



        if (!array_key_exists('title', $tcont->foaf[0])) {
            $tcont->foaf[0]['title'] = array();
        }
        if (!isset($tcont->foaf[0]['title'])) {
            $tcont->foaf[0]['title'] = array();
        }


        if (!array_key_exists('description', $tcont->foaf[0])) {
            $tcont->foaf[0]['description'] = array();
        }
        if (!isset($tcont->foaf[0]['description'])) {
            $tcont->foaf[0]['description'] = array();
        }




        if(!empty($tcont->foaf['page']))
        {
            $mypageFbox = NULL;
            $mypagebox = NULL;




            //builds pages feature box

            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cellpadding = 5;
            $table->cellspacing = 5;
            $table->startHeaderRow();
            $table->addHeaderCell($this->objLanguage->languageText('mod_foaf_title', 'foaf'), NULL, 'top' , 'center');
            $table->addHeaderCell($this->objLanguage->languageText('mod_foaf_page', 'foaf'), NULL, 'top' , 'center');
            $table->addHeaderCell($this->objLanguage->languageText('mod_foaf_description', 'foaf'), NULL, 'top' , 'center');
            $table->endHeaderRow();

            foreach($tcont->foaf['page'] as $pg)
            {
                $pageTitle = NULL;
                $pageDescription = NULL;
                $link = NULL;

                if(array_key_exists($pg, $tcont->foaf[0]['title']))
                {
                    $pageTitle = "".$tcont->foaf[0]['title'][$pg];
                } else {
                    $pageTitle = "<em>".$this->objLanguage->languageText('mod_foaf_notitle', 'foaf')."</em>";
                }



                if(array_key_exists($pg, $tcont->foaf[0]['description']))
                {
                    $pageDescription = "". $tcont->foaf[0]['description'][$pg];
                } else {
                    $pageDescription = "<em>".$this->objLanguage->languageText('mod_foaf_nodescription', 'foaf')."</em>";
                }

                $page = new href(htmlentities($pg) , htmlentities($pg) , "title='".$this->objLanguage->languageText('mod_foaf_goto' , 'foaf')."  ".$pg."'");
                $link = $page->show();
                $table->startRow();
                $table->addCell($pageTitle);
                $table->addCell("<em>".$link."</em>");
                $table->addCell($pageDescription);
                $table->endRow();

            }

            $mypagebox.= $table->show() ."<br />";
            $mypageFbox.= $objFeatureBox->show($pages , $mypagebox) ."<br />";

            $mypages.= $mypageFbox;

        } else {

            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $mypages .= $objFeatureBox->show($pages , $this->objLanguage->languageText('mod_foaf_noitemstxt', 'foaf'));

        }
        return $mypages;
    }

    //accounts
    public function manageAccounts()
    {
        $manageAcs = $this->objFoafOps->remAccountForm() . $this->objFoafOps->addAccountForm();
        if($this->objUser->isAdmin())
        {
            $manageAcs .= $this->foafAccountTypes();

        }
        return $manageAcs;
    }


    public function foafAccounts($tcont)
    {

        //build the featureboxen for user accounts

        if (!array_key_exists('holdsaccount', $tcont->foaf)) {
            $tcont->foaf['holdsaccount'] = array();
        }
        if (!isset($tcont->foaf['holdsaccount'])) {
            $tcont->foaf['holdsaccount'] = array();
        }


        $myaccounts = NULL;
        $manageAcs = new href($this->uri(array('action' => 'admin' , 'content' => 'accadmin')) , $this->objLanguage->languageText('mod_foaf_mngaccounts' , 'foaf'));
        $myaccounts .= $manageAcs->show();
        $accounts = $this->objLanguage->languageText('mod_foaf_accounts', 'foaf');

        if(!empty($tcont->foaf['holdsaccount']))
        {
            $myaccountFbox = NULL;
            $myaccountbox = NULL;




            //builds accountss feature box

            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cellpadding = 5;
            $table->cellspacing = 5;
            $table->startHeaderRow();
            $table->addHeaderCell($this->objLanguage->languageText('mod_foaf_account', 'foaf'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_foaf_servicehomepage', 'foaf'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_foaf_type', 'foaf'));
            $table->endHeaderRow();

            foreach($tcont->foaf['holdsaccount'] as $account)
            {
                $accountName = NULL;
                $serviceHomepage = NULL;
                $type = NULL;
                $link = NULL;



                $page = new href(htmlentities($account['accountservicehomepage']) , htmlentities($account['accountservicehomepage']) , "title='".$this->objLanguage->languageText('mod_foaf_goto' , 'foaf')."  ".$account['accountservicehomepage']."'");
                $link = $page->show();
                $table->startRow();
                $table->addCell($account['accountname']);
                $table->addCell("<em>".$link."</em>");
                $table->addCell($account['type']);
                $table->endRow();

            }

            $myaccountbox.= $table->show() ."<br />";
            $myaccountFbox.= $objFeatureBox->show($accounts , $myaccountbox) ."<br />";

            $myaccounts.= $myaccountFbox;

        } else {

            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $myaccounts .= $objFeatureBox->show($accounts , $this->objLanguage->languageText('mod_foaf_noitemstxt', 'foaf'));

        }
        return $myaccounts;
    }






    public function foafAccountTypes()
    {
        return $this->objFoafOps->addAccountTypeForm() . $this->objFoafOps->remAccountTypeForm();

    }

    //links

    /**
  *Method that creates forms for adding and removing links
  *returns add and remove links forms
  */
    public function linksAdmin()
    {

        return $this->objFoafOps->remLinkForm() . $this->objFoafOps->addLinkForm();


    }


    public function foafLinks()
    {

        $mylinks = NULL;
        $manageLinks = NULL;
        $flinks = $this->objLanguage->languageText('mod_foaf_links', 'foaf');

        if($this->objUser->isAdmin())
        {
            //build admin links link
            $manageLinks = new href($this->uri(array('action' => 'admin' , 'content' => 'lnkadmin')) , $this->objLanguage->languageText('mod_foaf_mnglinks' , 'foaf'));
            $mylinks .= $manageLinks->show();
        }
        //build the featureboxen for the links
        $links = $this->dbFoaf->getLinks();
        if(!isset($links))
        {
            $links = array();
        }
        if(!empty($links))
        {
            $mylinkFbox = NULL;
            $mylinkbox = NULL;
            $link = NULL;
            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $tablelinks = $this->newObject('htmltable', 'htmlelements');
            $tablelinks->cellpadding = 5;
            $tablelinks->cellspacing = 2;
            $tablelinks->startHeaderRow();
            $tablelinks->addHeaderCell($this->objLanguage->languageText('mod_foaf_title', 'foaf'), NULL, 'top' , 'center');
            $tablelinks->addHeaderCell($this->objLanguage->languageText('mod_foaf_url', 'foaf'), NULL, 'top' , 'center');
            $tablelinks->addHeaderCell($this->objLanguage->languageText('mod_foaf_description', 'foaf'), NULL, 'top' , 'center');
            $tablelinks->endHeaderRow();
            foreach($links as $lnk) {
                $page = new href(htmlentities($lnk['url']) , htmlentities($lnk['url']) , "title='".$this->objLanguage->languageText('mod_foaf_goto' , 'foaf')."  ".$lnk['url']."'");
                $link = $page->show();
                $tablelinks->startRow();
                $tablelinks->addCell($lnk['title']);
                $tablelinks->addCell("<em>".$link."</em>");
                $tablelinks->addCell($lnk['description']);
                $tablelinks->endRow();
            }
            $mylinkbox .= $tablelinks->show() ."<br />";
            $mylinkFbox .= $objFeatureBox->show($flinks, $mylinkbox) ."<br />";
            $mylinks .= $mylinkFbox;
        } else {
            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            if($this->objUser->isAdmin())
            {
                $mylinks .= $objFeatureBox->show($flinks , $this->objLanguage->languageText('mod_foaf_noitemstxt', 'foaf'));
            } else {

                $mylinks .= $objFeatureBox->show($flinks , $this->objLanguage->languageText('mod_foaf_nolinkstxt', 'foaf'));

            }


        }
        return $mylinks;
    }


    //search
    public function foafSearch()
    {
        return $this->objFoafOps->searchForm();
    }


    public function foafResults($results = array() , $field)
    {
        if(!empty($results))
        {

            $fields = array("name" => "Name" ,"firstname" => "First name" , "surname" => "Surname" ,"title" => "Title","mbox" => "E-mail" , "homepage" => "Homepage" ,"weblog" => "Web blog" ,"phone" =>  "Phone","jabberid" => "Jabber Id","geekcode" => "Geek code" ,"theme" => "Theme",
            "workplacehomepage" => "Workplace Homepage" ,"schoolhomepage" => "School Homepage" ,"logo" => "Logo" ,"img" => "Image");

            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cellpadding = 5;
            $table->cellspacing = 5;
            $table->startHeaderRow();
            $table->addHeaderCell($fields[$field], NULL, 'top' , 'center');
            $table->addHeaderCell($this->objLanguage->languageText('mod_foaf_friend', 'foaf'), NULL, 'top' , 'center');
            $table->endHeaderRow();

            foreach($results as $result)
            {

                $table->startRow();
                $table->addCell($result['field']);
                $table->addCell($result['name']);
                $table->endRow();
            }

            return $objFeatureBox->show('Results', $table->show());

        } else {
            return "<h1> No results </h1>";

        }

    }


    //gallery
    /**
  *Method for creating a gallery made of user depictions
   *@param images =>array => contains the urls of the online images
   *return foaf gallery interface
**/
    public function foafGallery($images = array() , $page = 1)
    {

        $managedeps = new href($this->uri(array('action' => 'admin' , 'content' => 'imgadmin')) , $this->objLanguage->languageText('mod_foaf_mngimages' , 'foaf'));


        if(!empty($images))
        {
            //Lets divide images in groups of 16 , so we don't have hundreads
            //of images beng displayed on the screen
            $images = array_chunk($images , 12 , false);

            $table = $this->newObject('htmltable', 'htmlelements');
            $table->id = 'gallery';
            $table->cellspacing = 4;
            /*Dumb row which contains invisible cells.
            *Such row makes that every row and cell have the same size
            *If you want to need such properties change it in the foaf.css file
            */

            $table->startRow('dumb');
            for($i = 0; $i < 4 ; $i++)
            {
                $table->addCell('&nbsp;' , '100');
            }
            $table->endRow();

            //Start row with cells that have content
            $table->startRow();
            //$page specifies the group of images in the group array of images ($images)
            //as $images is an array groups (indexes) start from 0
            $page = $page - 1 ;
            foreach($images[$page] as  $key => $image){

                $img = $this->newObject('image' , 'htmlelements');
                $img->src = $image['depictionurl'];
                $img->alt = $this->objLanguage->languageText('mod_foaf_nopreview' , 'foaf');
                //    $img->src = $image;
                $img->width = '100';
                $img->height = '80';
                $table->addCell('&nbsp;<a href="'.$image['depictionurl'].'" title="'.$this->objLanguage->languageText('mod_foaf_imagelocation' ,'foaf').' '.$image['depictionurl'].'">'.$img->show().'</a>');
                //put only 4 images in each row
                if(($key + 1) % 4 == 0)
                {
                    $table->endRow();
                    $table->startRow();
                }

            }
            $table->endRow();
            //controls row
            //addCell($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null,$border = '0')
            //addRow($content, $tdClass = null, $row_attributes = null)
            $table->startRow('controlrow');
            $pages = '';
            for($page = 1 ; $page <= count($images); $page++){
                $link = new href($this->uri(array('action' =>'fields', 'content' => 'gallery' , 'page' => $page)) , $page , 'class="pagelink" title="'.$this->objLanguage->languageText('mod_foaf_goto' , 'foaf').' '.strtolower($this->objLanguage->languageText('mod_foaf_page' , 'foaf')).' '.$page.'"');
                if($page != 1)
                {
                    $pages .= '- ';
                }
                $pages .= $link->show();
            }


            $table->addCell($pages,0,'middle','center',null,'colspan="4"');
            $table->endRow();

            return  $managedeps->show().$table->show();
        } else {

            $noImages = $this->newObject('htmlheading' , 'htmlelements');
            $objFeatureBox = $this->newObject('featurebox' , 'navigation');
            $noImages->type = 2;
            $noImages->align = 'center';
            $noImages->str = "<em>".$this->objLanguage->languageText('mod_foaf_emptygallery' ,'foaf')."</em>";

            return $managedeps->show(). $objFeatureBox->show(" " , $noImages->show());


        }





    }






    /**
   *Method for displaying a single friend profile
   *@$tcont=>array contains an array with the friends of the user
   *$@fIndex=>int the index of the friend in the friends array
**/
    public function showFriend($tcont , $fIndex)
    {


        if (isset($tcont->foaf['knows'])) {
            if (is_array($tcont->foaf['knows'])) {
                foreach($tcont->foaf['knows'] as $pals) {
                    $info[] = $this->objFoafOps->fFeatureBoxen($pals);
                }
            }
            //build the featurebox
            $mypfbox = NULL;
            $myFbox = NULL;
            $okes = $info[$fIndex];
            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            //take the pfimage and the pfbox
            $table2 = $this->newObject('htmltable', 'htmlelements');
            $table2->cellpadding = 5;
            $table2->startRow();
            $table2->addCell($okes[0]);
            $table2->addCell($okes[1]);
            $table2->endRow();
            $mypfbox.= $table2->show() ."<br />";
            $myFbox.= $objFeatureBox->show($okes[2], $mypfbox) ."<br />";


        }
        return $myFbox;

    }



}

?>