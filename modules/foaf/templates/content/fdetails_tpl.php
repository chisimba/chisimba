<?php
$script = '<script type="text/javascript">

//<![CDATA[

           // Initialize and render the menu when it is available in the DOM

            YAHOO.util.Event.onContentReady("userfields", function () {

                /*
                     Instantiate the menu.  The first argument passed to the 
                     constructor is the id of the element in the DOM that 
                     represents the menu; the second is an object literal 
                     representing a set of configuration properties for 
                     the menu.
                */

                var oMenu = new YAHOO.widget.Menu(
                                    "userfields", 
                                    {
                                        position: "static", 
                                        hidedelay: 750, 
                                        lazyload: true 
                                    }
                                );

                /*
                     Call the "render" method with no arguments since the 
                     markup for this menu already exists in the DOM.
                */

                oMenu.render();            
            
            });
 //]]>  
</script>';

$objmsg = $this->getObject('timeoutmessage', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('form', 'htmlelements');

$pane = $this->newObject('tabpane', 'htmlelements');
$userMenu = $this->newObject('usermenu', 'toolbar');
$dbFoaf= $this->getObject('dbfoaf' , 'foaf');

// Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$orgs = $dbFoaf->getRecordSet($objUser->userId() , 'tbl_foaf_organization');
$funders = $dbFoaf->getFunders();
$pages = $dbFoaf->getPgs();
$accounts = $dbFoaf->getAccounts();
$interests = $dbFoaf->getInterests();
$images = $dbFoaf->getDepictions();
$friends = $dbFoaf->getFriends();

$fields = array('Organizations' => $orgs , 'Funders' => $funders , 'Pages' => $pages , 'Accounts' => $accounts ,
        'Interests' => $interests , 'Images' => $images);

foreach($fields as $field) {
    if(!is_array($field))
    {
        $field = array();
    }
}

if(!is_array($friends))
{
   $friends = array();
}

$objmsg->timeout = 20000;

if ($msg == 'update') {
    $objmsg->message = $this->objLanguage->languageText('mod_foaf_recupdated', 'foaf');
    echo $objmsg->show();
} else {
    $objmsg->message = $msg;    
    echo $objmsg->show();
}

//Tab names
$mydetails = $this->objLanguage->languageText('mod_foaf_mydetails', 'foaf');
$myfriends = $this->objLanguage->languageText('mod_foaf_myfriends', 'foaf');
$allfriends = $this->objLanguage->languageText('mod_foaf_allfriends', 'foaf');
$searchfriends = $this->objLanguage->languageText('mod_foaf_searchfriends', 'foaf');
$myorganizations = $this->objLanguage->languageText('mod_foaf_myorganizations', 'foaf');
$myfunders = $this->objLanguage->languageText('mod_foaf_myfunders', 'foaf');
$myinterests = $this->objLanguage->languageText('mod_foaf_myinterests', 'foaf');
$mydepictions = $this->objLanguage->languageText('mod_foaf_myimages', 'foaf');
$mypages = $this->objLanguage->languageText('mod_foaf_mypages', 'foaf');
$myaccounts = $this->objLanguage->languageText('mod_foaf_myaccounts', 'foaf');
$accountTypes = $this->objLanguage->languageText('mod_foaf_accounttypes', 'foaf');
$myevents = $this->objLanguage->languageText('mod_foaf_myevents', 'foaf');
$allevents = $this->objLanguage->languageText('mod_foaf_allevents', 'foaf');
$extras = $this->objLanguage->languageText('mod_foaf_extras', 'foaf');
$gallery = $this->objLanguage->languageText('mod_foaf_gallery', 'foaf');
$query = $this->objLanguage->languageText('mod_foaf_query', 'foaf');
$visualise = $this->objLanguage->languageText('mod_foaf_visualize', 'foaf');
$surprise = $this->objLanguage->languageText('mod_foaf_surprise', 'foaf');
$foafLinks = $this->objLanguage->languageText('mod_foaf_foaflinks', 'foaf');
$noresults = $this->objLanguage->code2Txt('mod_foaf_noresults' , 'foaf' , array('FIELD' => $predicate ,'VALUE' => $object));

$noResultsMsg = null;
$matches = $this->objFoafParser->queryFoaf($foafFile , $predicate , $object , $noResultsMsg);

$matches = $this->objFoafParser->queryFoaf($foafFile , $predicate , $object);

//boxes
$box = $this->getObject('featurebox', 'navigation');

//extras
//icons
$icon = $this->getObject('geticon', 'htmlelements');  
$icon->setIcon('rss', 'gif', 'icons/filetypes');
$icon->align = 'left';


$inviteBox = $this->objFoafOps->inviteForm();

$linksBox = $this->objFoafOps->linksBox();
//friends

$table = NULL;
$table = $this->newObject('htmltable' , 'htmlelements');

foreach($friends as $key => $friend){

//Display only the first three friends
if($key < 3){

$table->startRow();
$fLink = new href($this->uri(array('action' =>'fields', 'content' => 'friend' , 'friend' => $key)) , $friend['name'], 'class="itemlink" title="See '.$friend['name'].' profile" id="friend'.$key.'"');
$table->addCell('<h6>'.$fLink->show().'</h6>');
$table->endRow();

}

}
$friendsLink = new href($this->uri(array('action' =>'fields', 'content' => 'friends')) , $allfriends.'>>', 'class="itemlink"');
$table->startRow();
$table->addCell('<em>'.$friendsLink->show().'</em>');
$table->endRow();

$searchLink = new href($this->uri(array('action' =>'fields', 'content' => 'search')) , $searchfriends, 'class="itemlink"');
$table->startRow();
$table->addCell('<em>'.$searchLink->show().'</em>');
$table->endRow();


$friendsHeader = new href($this->uri(array('action' =>'fields', 'content' => 'friends')) , $myfriends, 'class="headerlink" title="'.$allfriends.'"');
$friendsBox = $box->showContent($friendsHeader->show() , $table->show());


//profilebox
$profileBox = $box->show('<a href="#">Profile</a>', 'Profile content' , 'profilebox' ,'none',TRUE);



//events
$eventsBox = $box->showContent('<a href="#" class="headerlink" title="'.$allevents.'">'.$myevents.'</a>','Events');


//Insert script for generating tree menu
$this->appendArrayVar('headerParams', $this->getJavascriptFile('yahoo/yahoo.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('event/event.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('dom/dom.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('container/container.js', 'yahoolib'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('menu/menu.js', 'yahoolib'));
$this->appendArrayVar('headerParams',$script);

$css = '<link rel="stylesheet" type="text/css" media="all" href="'.$this->getResourceURI("menu/assets/menu.css", 'yahoolib').'" />';
$this->appendArrayVar('headerParams', $css);

        //$css = '<link rel="stylesheet" type="text/css" media="all" href="'.$this->getResourceURI("foaf.css", 'foaf').'" />';
        //$this->appendArrayVar('headerParams', $css);




$userFields = '
               

                       <div id="userfields" class="yuimenu">
                            <div class="bd">
                                <ul class="first-of-type">
                                    <li class="yuimenuitem first-of-type"><a class="yuimenuitemlabel" href="http://communication.yahoo.com">'.$this->objLanguage->languageText('mod_foaf_myfoaf', 'foaf').'</a>';
                                                    $userFields.= '<div id="me" class="yuimenu">
                                                                     <div class="bd">
                                                                       <ul>';
//each field is a group of organizations or friends or interests , etc..
foreach($fields as $key => $field){
$userFields.= '<li class="yuimenuitem"><a class="yuimenuitemlabel" href="'.$this->uri(array('action'=>'fields', 'content'=>$key)).'" title="'.$this->objLanguage->languageText('mod_foaf_manage', 'foaf').' '.$key.'">'.$key.'</a>';
    $userFields.= ' <div id="'.$key.'" class="yuimenu">
                           <div class="bd">
                 <ul>';
//echo "<h1>".$key.'  '.$objUser->userId()."</h1>";
//var_dump($field);
if(!empty($field))
{
//each fld is an element of a group i.e. a single organization or a single friend or a single interest, etc.
//display a usefull fld property i.e. display name (for organizations), urls(for funders), title (for pages) ,etc.
$url = NULL;
switch($key){
    case 'Organizations':
    $key = 'name';
    $url = 'homepage';
    break;
    case 'Funders':
    $key ='funderurl';
    $url = 'funderurl';
    break;
    case 'Pages':
    $key = 'title';
    $url = 'page';
    break;
    case 'Interests':
    $key ='interesturl' ;
    $url = 'interesturl';
    break;
    case 'Images':
    $key ='depictionurl' ;
    $url = 'depictionurl';
    break;

}

foreach($field as $fld){        
//As people use the same name for several services it would be better
//to show the account type ($fld['type']) in brackets as well , so the user knows what
//service is the account refering to.
if($key=='Accounts')
{

    $userFields.= '<li class="yuimenuitem"><a class="yuimenuitemlabel" href="'.$fld['accountservicehomepage'].'" title="'.$this->objLanguage->languageText('mod_foaf_goto' , 'foaf').' '.$fld['accountservicehomepage'].'">'.$fld['accountname'].' ('.$fld['type'].')'.'</a></li>';

} else {

    //If it's a very long string to be displayed on screen
    // then make it smaller
    if(strlen($fld[$key]) > 20)
    {
      $fld[$key] = substr($fld[$key] , 0 , 30).'....';
    }

    $userFields.= '<li class="yuimenuitem"><a class="yuimenuitemlabel" href="'.$fld[$url].'" title="'.$this->objLanguage->languageText('mod_foaf_goto' , 'foaf').' '.$fld[$url].'">'.$fld[$key].'</a></li>';
}




}

} else {

    $userFields.= '<li class="yuimenuitem">'.$this->objLanguage->languageText('mod_foaf_noitems' , 'foaf').'</li>';


}
$userFields.= '</ul></div></div></li>';
}
$userFields.= '</ul></div></div></li></ul></div></div>';
//end userfields (labeled My foaf in the interface) menu




//start the tabbedpane
$pane->addTab(array(
    'name' => $mydetails,
    'content' => $this->objUi->myFoaf($tcont),

));






//define the contenttab content
switch($content){
    case 'friends':
    $pane->addTab(array(
            'name' => $myfriends,
            'content' => $this->objUi->foafFriends($tcont)
    ));
    break;

    case 'fndadmin':
    $pane->addTab(array(
            'name' => $myfriends,
            'content' => $this->objUi->manageFriends()
    ));
    break;


    case 'friend':
    $pane->addTab(array(
            'name' => $this->objLanguage->languageText('mod_foaf_friend','foaf') ,
            'content' => $this->objUi->showFriend($tcont , $fIndex)
    ));
    break;

    case 'Organizations':
         $pane->addTab(array(
        'name' => $myorganizations,
        'content' => $this->objUi->foafOrgs($tcont)
    ));
    break;

    case 'orgsadmin':
         $pane->addTab(array(
        'name' => $myorganizations,
        'content' => $this->objUi->manageOrgs()
    ));
    break;


    case 'Funders':
        $pane->addTab(array(
            'name' => $myfunders,
        'content' => $this->objUi->foafFunders($tcont)
    ));
    break;

    case 'fnsadmin':
        $pane->addTab(array(
            'name' => $myfunders,
        'content' => $this->objUi->manageFunders()
    ));
    break;


    case 'Interests':
         $pane->addTab(array(
        'name' => $myinterests,
        'content' => $this->objUi->foafInterests($tcont)
    ));
    break;


    case 'intadmin':
         $pane->addTab(array(
        'name' => $myinterests,
        'content' => $this->objUi->manageInterests()
    ));
    break;

    

    case 'Images':
        $pane->addTab(array(
        'name' => $mydepictions,
        'content' => $this->objUi->foafDepictions($tcont)
     ));
    break;


    case 'imgadmin':
        $pane->addTab(array(
        'name' => $mydepictions,
        'content' => $this->objUi->manageDepictions()
     ));
    break;



    case 'Pages':
        $pane->addTab(array(
        'name' => $mypages,
        'content' => $this->objUi->foafPages($tcont)
    ));
    break;

    case 'pgsadmin':
        $pane->addTab(array(
        'name' => $mypages,
        'content' => $this->objUi->managePages()
    ));
    break;
    
    
    case 'Accounts':
        $pane->addTab(array(
        'name' => $myaccounts,
        'content' =>  $this->objUi->foafAccounts($tcont)
    ));
    break;

    case 'accadmin':
        $pane->addTab(array(
        'name' => $myaccounts,
        'content' =>  $this->objUi->manageAccounts()
    ));
    break;



    case 'search':
        $pane->addTab(array(
        'name' => $searchfriends,
        'content' => $this->objUi->foafSearch()
    ));
    break;

    case 'results':
        $pane->addTab(array(
        'name' => 'Search Results',
        'content' => $this->objUi->foafResults($matches , $predicate)."<p>&nbsp;</p>".$this->objUi->foafSearch()
    ));
    break;


    
    case 'links':
        $pane->addTab(array(
        'name' => $foafLinks,
        'content' => $this->objUi->foafLinks()
    ));
    break;

    case 'lnkadmin':
        $pane->addTab(array(
        'name' => $foafLinks,
        'content' => $this->objUi->linksAdmin()
    ));
    break;


    case 'network':
        $pane->addTab(array(
        'name' => $visualise,
        'content' => 'Visulalise the Network'
    ));
    break;

    case 'surprise':
    $pane->addTab(array('name'=>$surprise,'content' => $game));
    break;

    case 'gallery':
    $pane->addTab(array('name'=>$gallery,'content' => $this->objUi->foafGallery( $fields['Images'] , $page)));
    break;
    //account types
    case 'actypes':
        $pane->addTab(array(
        'name' => $accountTypes,
        'content' => $this->objUi->foafAccountTypes()
    ));
    break;

    case 'profile':
    default:
        $pane->addTab(array(
            'name' => 'FOAF',
                'content' => '<div id="about"><h2>'.$this->objLanguage->languageText('mod_foaf_welcometofoaf', 'foaf').'</h2>'.$this->objLanguage->languageText('mod_foaf_instructions','foaf').'</div>'
        ));



}


$leftColumn = NULL;
$middleColumn = NULL;
$rightColumn = NULL;

$leftColumn = $userFields.$linksBox;
$rightColumn = $friendsBox.'<p>&nbsp;</p>'.$eventsBox;
$middleColumn = $pane->show();

$cssLayout->setNumColumns(3);
$cssLayout->setLeftColumnContent($leftColumn);
$cssLayout->setRightColumnContent($rightColumn);
$cssLayout->setMiddleColumnContent($middleColumn);
//$cssLayout->putThreeColumnFixInHeader();
echo $cssLayout->show();


?>
