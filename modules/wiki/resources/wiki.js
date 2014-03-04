/* 
* ===================================================================== 
*  File to hold the javascript functions for the wiki version 2 module
* =====================================================================
*/

var lockTimer;
var VAL = false;
var LOGGED_IN = "false";

/**
* Method to validate the create form fields
*
* @param string err_page: The no page name error message
* @param string err_name: The no capital letter error message
* @param string err_summary: The no summary error message
* @param string err_content: The no content error message 
*/
function validateCreatePage(err_page, err_summary, err_content)
{
    var name_input = $("input_name");
    var summary_input = $("input_summary");
    var choice_input = $("input_choice");
    var content_input = document.getElementsByName("wikiContent");
    
    if(name_input.value == ""){
        alert(err_page);
        name_input.focus();
        return false;
    }
    
    validateName(name_input);
    if(!VAL){
        return false;
    }
    
    if(summary_input.value == ""){
        if(confirm(err_summary)){
            choice_input.value = "yes";
        }else{
            summary_input.focus();
            return false;
        }
    }
    
    if(content_input[0].value == ""){
        alert(err_content);
        content_input[0].focus
        return false;
    }
    
    $("form_create").submit();
}

/**
* Method to validate the update form fields
*
* @param string err_content: The no content error message 
* @param string err_summary: The no summary error message
* @param string err_comment: The no comment error message 
*/
function validateUpdatePage(err_summary, err_content, err_comment)
{
    var summary_input = $("input_summary");
    var choice_input = $("input_choice");
    var content_input = document.getElementsByName("wikiContent");
    var comment_input = $("input_comment");
    
    
    if(summary_input.value == ""){
        if(confirm(err_summary)){
            choice_input.value = "yes";
        }else{
            summary_input.focus();
            return false;
        }
    }
    if(content_input[0].value == ""){
        alert(err_content);
        content_input[0].focus();
        return false;
    }
 
    if(comment_input.value == ""){
        alert(err_comment);
        comment_input.focus();
        return false;
    }
   var temp = document.getElementById('form_update');
   temp.submit();
}

/**
* Method to validate the page name
*
* @param string name_input: The page name input element
*/
function validateName(name_input)
{
    var url = "index.php";
    var target = "errorDiv";
    var pars = "module=wiki&action=validate_name&name="+name_input.value;
    var validateAjax = new Ajax.Updater(target, url, {method: "post", parameters: pars, onComplete: validationEffects});    
}

/**
* Method to show effects if valudation fails
*/
function validationEffects()
{
    var name_input = $("input_name"); 
    var summary_input = $("input_summary");   
    var div_errorDiv = $("errorDiv");
    if(Element.empty(div_errorDiv) == null){
        name_input.style.backgroundColor = "yellow";
        name_input.focus();
        name_input.select();
        VAL = false;        
    }else{
        name_input.style.backgroundColor = "";
        summary_input.focus();
        VAL = true;
    }
    adjustLayout();
}

/**
* Method to adjust the layout
*/
function resizeRefresh()
{
    Element.hide("loadingDiv");
    adjustLayout();
}

/**
* Method to link ajax functions to the tab onclick events
* 
* @param string edit_state
* @param string logged_in
*/
function tabClickEvents(edit_state, logged_in)
{
    if(edit_state == "can_edit"){
        $("mainTabnav3").parentNode.style.display = "none";
        $("mainTabnav4").parentNode.style.display = "none";
        $("mainTabnav6").parentNode.style.display = "none";
        var editLink = $("mainTabnav2");
        editLink.onclick = function(){
            checkLock();
        }
    }else if(edit_state == 'no_edit'){
        if(logged_in == "true"){
            $("mainTabnav2").parentNode.style.display = "none";
            $("mainTabnav3").parentNode.style.display = "none";
            $("mainTabnav4").parentNode.style.display = "none";
            $("mainTabnav6").parentNode.style.display = "none";
        }else{
            $("mainTabnav3").parentNode.style.display = "none";
        }
    }else{
        var previewLink = $("addTabnav2");
        previewLink.onclick = function(){
            $("addTab").tabber.tabShow(1);
            moveContent();
            adjustLayout();
        }        
    }
}

/**
* Method to check if the user can edit the page
*/
function checkLock()
{
    var id = $F("input_id");
    var target = "lockedDiv";
    var url = "index.php";
    var pars = "module=wiki&action=check_lock&id="+id;
    var checkAjax = new Ajax.Updater(target, url, {method: "post", parameters: pars, onComplete: updatePage});    
}

/**
* Method to update the page after the lock check
*/
function updatePage()
{
    var locked_input = $F("input_locked");
    if(locked_input == "locked"){
        var articleLink = $("mainTabnav1");
        articleLink.onclick = function(){
            $("mainTabnav2").parentNode.style.display = "";
            $("mainTabnav3").parentNode.style.display = "none";
            $("mainTabnav4").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(0);
            clearTimeout(lockTimer);
            adjustLayout();
        }
        var historyLink = $("mainTabnav5");
        historyLink.onclick = function(){
            $("mainTabnav2").parentNode.style.display = "";
            $("mainTabnav3").parentNode.style.display = "none";
            $("mainTabnav4").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(4);
            clearTimeout(lockTimer);
            adjustLayout();
        }
        var discussLink = $("mainTabnav7");
        discussLink.onclick = function(){
            $("mainTabnav2").parentNode.style.display = "";
            $("mainTabnav3").parentNode.style.display = "none";
            $("mainTabnav4").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(6);
            clearTimeout(lockTimer);
            adjustLayout();
        }     
        var previewLink = $("mainTabnav4");
        previewLink.onclick = function(){
            $("mainTab").tabber.tabShow(3);
            moveContent();
            adjustLayout();
        }
        $("mainTabnav2").parentNode.style.display = "none";
        $("mainTabnav3").parentNode.style.display = "";
        $("mainTabnav4").parentNode.style.display = "";
        $("mainTab").tabber.tabShow(2);
        adjustLayout();
        lockPage();        
    }else{
        $("mainTabnav3").parentNode.style.display = "none";
        $("mainTabnav4").parentNode.style.display = "none";
        $("mainTab").tabber.tabShow(1);
    }
}

/**
* Method to lock the page for editing
*/
function lockPage()
{
    var id = $F("input_id");
    var target = "input_locked";
    var url = "index.php";
    var pars = "module=wiki&action=lock_page&id="+id;
    var lockAjax = new Ajax.Updater(target, url, {method: "post", parameters: pars, onComplete: wikiLockTimer});    
}

/*
* Function to set the lock timer
*/
function wikiLockTimer()
{
    lockTimer = setTimeout("lockPage()", 60000);    
}

/**
* Method to add a rating
* 
* @param string rating: The rating that was given
*/
function addRating(rating)
{
    var name_value = $F("input_name");
    var target = "ratingDiv";
    var url = "index.php";
    var pars = "module=wiki&action=add_rating&name="+name_value+"&rating="+rating;
    var ratingAjax = new Ajax.Updater(target, url, {method: "post", parameters: pars});      
}

/**
* Method to update the watchlist
*
* @param bool $watch: The state of the checkbox
*/
function updateWatchlist(watch)
{
    if(watch){
        var mode = "add";
    }else{
        var mode = "delete";
    }
    var name_value = $F("input_name");
    var url = "index.php";
    var pars = "module=wiki&action=update_watch&name="+name_value+"&mode="+mode;
    var watchAjax = new Ajax.Request(url, {method: "post", parameters: pars});          
}

/**
* Method to hide/display diff radios
*
* @param object el_radio: The radio element clicked
*/
function manipulateRadios(el_radio)
{
    var fromRadios = document.getElementsByName("from");
    var toRadios = document.getElementsByName("to");

    if(el_radio.name == "from"){
        for(var i = 0; i <= toRadios.length - 1; i++){
            if(Number(toRadios[i].value) > Number(el_radio.value)){
                toRadios[i].style.visibility = "";
            }else{
                toRadios[i].style.visibility = "hidden";
            }
        }
    }else{
        for(var i = 0; i <= fromRadios.length - 1; i++){
            if(Number(fromRadios[i].value) < Number(el_radio.value)){
                fromRadios[i].style.visibility = "";
            }else{
                fromRadios[i].style.visibility = "hidden";
            }
        }
    }   
}

/**
* Method to send a ajax call to get the diff
*
* @param string logged_in
* @param string page_name
*/
function getDiff(logged_in, page_name)
{
    LOGGED_IN = logged_in;
    var fromRadios = document.getElementsByName("from");
    var toRadios = document.getElementsByName("to");
    for(var i = 0; i <= fromRadios.length - 1; i++){
        if(fromRadios[i].checked == true){
            var from_value = fromRadios[i].value;
        }
    }
    for(var i = 0; i <= toRadios.length - 1; i++){
        if(toRadios[i].checked == true){
            var to_value = toRadios[i].value;
        }
    }
    var target = "diffDiv";
    var url = "index.php";
    var pars = "module=wiki&action=show_diff&name="+page_name+"&from="+from_value+"&to="+to_value;
    var diffAjax = new Ajax.Updater(target, url, {method: "post", parameters: pars, onComplete: showDiff});      
}

/**
* Method to display the diff
*/
function showDiff()
{

    if(LOGGED_IN == "true"){
        var articleLink = $("mainTabnav1");
        articleLink.onclick = function(){
            $("mainTabnav6").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(0);
        }
        var historyLink = $("mainTabnav5");
        historyLink.onclick = function(){
            $("mainTabnav6").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(4);
        }
        var discussLink = $("mainTabnav7");
        discussLink.onclick = function(){
            $("mainTabnav6").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(6);
        }
        $("mainTabnav6").parentNode.style.display = "";
        $("mainTab").tabber.tabShow(5);
    }else{
        var articleLink = $("mainTabnav1");
        articleLink.onclick = function(){
            $("mainTabnav3").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(0);
        }
        var historyLink = $("mainTabnav2");
        historyLink.onclick = function(){
            $("mainTabnav3").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(1);
        }
        var discussLink = $("mainTabnav4");
        discussLink.onclick = function(){
            $("mainTabnav3").parentNode.style.display = "none";
            $("mainTab").tabber.tabShow(3);
        }
        $("mainTabnav3").parentNode.style.display = "";
        $("mainTab").tabber.tabShow(2);
    }
    adjustLayout();
}

/**
* Method to exit the edit page
*/
function exitEdit()
{
    $("mainTabnav1").parentNode.style.display = ""
    $("mainTabnav2").parentNode.style.display = ""
    $("mainTabnav3").parentNode.style.display = "none"
    $("mainTabnav4").parentNode.style.display = "none"
    $("mainTabnav5").parentNode.style.display = ""
    $("mainTabnav6").parentNode.style.display = "none"
    $("mainTabnav7").parentNode.style.display = ""
    $("mainTab").tabber.tabShow(0);
    clearTimeout(lockTimer);
    scrollTo(0,0);
    adjustLayout();
}

/**
* Method to populate the interwiki link fields
*/
function setEdit(id, wiki, url)
{
    Element.show("updateLinkLayer");
    Element.hide("addLink");
    Element.hide("addLinkLayer");
    $("input_id").value = id;
    $("input_update_name").value = wiki;
    $("input_update_url").value = url;   
}

/*
* Method to show the add interwiki link
*/
function showAddLink()
{
    Element.hide("addLink");
    Element.hide("updateLinkLayer");
    Element.show("addLinkLayer");
}

/*
* Method to cancel the add interwiki link
*/
function cancelAddLink()
{
    $("input_name").value = "";
    $("input_url").value = "";
    Element.show("addLink");
    Element.hide("addLinkLayer");
}

/*
* Method to cancel the update interwiki link
*/
function cancelUpdateLink()
{
    Element.show("addLink");
    Element.hide("updateLinkLayer");
}

/*
* Method to show add discussion post
*/
function showAddPost()
{
    Element.hide("addLink");
    Element.show("addDiv");
    adjustLayout();
}

/*
* Method to cancel add discussion post
*/
function cancelAddPost()
{
    $("input_post_title").value = "";
    $("input_post_content").value = "";
    Element.hide("addDiv");
    Element.show("addLink");
}

/*
* Method to show update discussion post
*/
function showUpdatePost(postId, title, content)
{
    Element.hide("tableLayer");
    Element.show("tab_"+postId);
    $("input_post_title_"+postId).value = title;
    $("input_post_content_"+postId).value = content;
    adjustLayout();
}

/*
* Method to cancel update discussion post
*/
function cancelUpdatePost(postId)
{
    Element.hide("tab_"+postId);
    Element.show("tableLayer");
    adjustLayout();
}

/*
* Method to move the input contents for preview submission
*/
function moveContent()
{
    var el_name = $("input_name");
    var el_content =document.getElementsByName("wikiContent");
    var el_pIframe = $("submitIframe");
    var el_iframe = el_pIframe.contentWindow || el_pIframe.contentDocument;
    if(el_iframe.document){
        el_iframe = el_iframe.document;
    }
    var el_form = el_iframe.getElementById("form_iframe_form");
    var el_pName = el_iframe.getElementById("input_preview_name");
    var el_pContent = el_iframe.getElementById("input_preview_content");
    el_pName.value = el_name.value;                             
    el_pContent.value = el_content[0].value;
}
