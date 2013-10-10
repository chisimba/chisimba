/**
* ================================
*  Scriptaculous global variables
* ================================
*/
var URL = "index.php";
var userTimer;
var chatTimer;
var chatMode;
var URI;
var settingsTimer;
var imTimer;


/**
* =====================================
*  Scriptaculous page onload functions
* =====================================
*/
/**
* Function to initialise onload events
* @param string mode: The mode of the onload - Context or Null
*/
function jsOnloadChat(mode){
    chatMode = mode;
    if(mode == "context"){
        jsGetChat();    
    }else{
        jsGetChat();    
        jsGetOnlineUsers();    
    }
}

/**
* ===========================================
*  Js function to show or hide the help divs
* ===========================================
*/
/**
* Function to display or hide the help div
* @param object el: The help div object
*/            
function jsShowHelp(el)
{
    if(el.style.display == "none"){
        Element.show(el.id);
        window.setTimeout("Element.hide('"+el.id+"')",10000);        
    }else{
        Element.hide(el.id);
    }            
}

/**
* =========================================
*  Js functions for the smiley feature box
* =========================================
*/
/**
* Function to insert the smiley code from the smiley block into the chat box 
* @param string el_id: The smiley code - id of the clicked smiley
*/            
function jsInsertBlockSmiley(el_id)
{
    var arrNames = new Array("angry", "cheeky", "confused", "cool", "evil", "idea", "grin", "sad", "smile", "wink");            
    var arrCodes = new Array("X-(", ":-P", ":-/", "B-)", ">:-)", "*-:-)", ":-D", ":-(", ":-)", ";-)");
    var el_Message = $("input_message");
    for(i = 0; i <= arrNames.length-1; i++){
        if(arrNames[i] == el_id){
            if(el_Message.value == ""){
                el_Message.value = arrCodes[i];
            }else{
                el_Message.value = el_Message.value + " " + arrCodes[i];
            }
        }
    }
    el_Message.focus();
}
            
/**
* ===============================================
*  Js functions for the more smiley popup window
* ===============================================
*/
/**
* Function to insert the smiley code form the smiley popup into the chat box 
* @param string el_id: The smiley code - id of the clicked smiley
*/            
function jsInsertPopupSmiley(el_id)
{
    var arrNames = new Array("alien" ,"angel", "angry", "applause", "black_eye", "bye", "cheeky", "chicken", "clown", "confused", "cool", "cowboy", "crazy", "cry", "dance_of_joy", "doh", "drool", "embarrassed", "evil", "frustrated", "grin", "hug", "hypnotised", "idea", "kiss", "laugh", "love", "nerd", "not_talking", "praying", "raise_eyebrow", "roll_eyes", "rose", "sad", "shame_on_you", "shocked", "shy", "sick", "skull", "sleeping", "smile", "straight_face", "thinking", "tired", "victory", "whistle", "wink", "worried");
    var arrCodes = new Array(">-)", "O:-)", "X-(", "=D>", "b-(", ":-[", ":-P", "~:>", ":o)", ":-/", "B-)", "<):-)>", "8-}", ":-((", "/:-D/", "#-o", "=P~", ":\"->", ">:-)", ":-L", ":-D", ">:-D<", "@-)", "*-:-)", ":-*", ":-))", ":-x", ":-B", "[-(", "[-o<", "/:-)", "8-|", "@};-", ":-(", "[-X", ":-O", ";;-)", ":-&", "8-X", "I-)", ":-)", ":-|", ":-?", "(:-|", ":-)>-", ":-\"", ";-)", ":-s");                        
    var el_Message = opener.$("input_message");
    for(i = 0; i <= arrNames.length-1; i++){
        if(arrNames[i] == el_id){
            if(el_Message.value == ""){
                el_Message.value = arrCodes[i];
            }else{
                    el_Message.value = el_Message.value + " " + arrCodes[i];
            }
        }
    }
    window.close();
    el_Message.focus();
}

/**
* ===============================================
*  Js functions for the online users feature box
* ===============================================
*/
/*
* Function to get the online users via ajax
*/            
function jsGetOnlineUsers()
{
    var target = "usersListDiv";
    var pars = "module=messaging&action=getusers";
    var usersAjax = new Ajax.Updater(target, URL, {method: "post", parameters: pars, onComplete: jsUserListTimer});
}

/*
* Function to repeat the online users ajax call
* also handles the banned user display
*/
function jsUserListTimer(){
    var el_Banned = $("input_banned");
    var el_UserId = $("input_userId");
    var el_Type = $("input_type");
    var el_Date = $("input_date");
    if(el_Banned.value == "Y"){                    
        var target = "bannedDiv";
        var pars = "module=messaging&action=getbanmsg&type="+el_Type.value+"&date="+el_Date.value;
        var bannedAjax = new Ajax.Updater(target, URL, {method: "post", parameters: pars});
        if(el_Type.value != 2){
            Element.show("bannedDiv");
            Element.hide("sendDiv");
        }else{
            Element.show("bannedDiv");
            Element.show("sendDiv");
        }
    }else{
        Element.show("sendDiv");
        Element.hide("bannedDiv");
    }                
    userTimer = setTimeout("jsGetOnlineUsers()", 2000);
}

/**
* ========================================
*  Js functions for the chat messages div
* ========================================
*/
/*
* Function to get the chat messages via ajax
* @param string mode: The mode of the onload - Context or Null
*/            
function jsGetChat()
{
    var el_Counter = $("input_counter");
    var target = "chatDiv";
    var pars = "module=messaging&action=getchat&counter="+el_Counter.value+"&mode="+chatMode;
    var chatAjax = new Ajax.Updater(target, URL, {method: "post", parameters: pars, insertion: Insertion.Bottom, onComplete: jsChatTimer});
}

/*
* Function to repeat the chat messages ajax call
*/
function jsChatTimer()
{
    if(chatMode != "context"){
        Element.hide("loadDiv");
    }
    var el_Counter = $("input_counter");
    var el_Count = $("input_count");
    el_Counter.value = Number(el_Counter.value) + Number(el_Count.value);
    Element.remove("input_count");
    var el_ChatDiv = $("chatDiv");
    el_ChatDiv.scrollTop = el_ChatDiv.scrollHeight
    chatTimer = setTimeout("jsGetChat()", 2000);
}

/*
* Function to trap the enter key
* @param event e: The onkeyup event
*/
function jsTrapKeys(e)
{
    var keynum;
    var isShift = false;
    if(window.event){
        // IE
        keynum = e.keyCode
        if(window.event.shiftKey){
            isShift = true;
        }
    }else if(e.which){
        // Netscape/Firefox/Opera
        keynum = e.which
        if(e.shiftKey){
            isShift = true;
        }
    }
    if(!isShift && keynum == 13){
        jsSendMessage();
    }
}

/*
* Function to send the chat message 
* Moves the contents of the textarea to a hidden iframe for submission
*/
function jsSendMessage()
{
    var el_Message = $("input_message");
    if(el_Message.value != ""){
        var el_ChatIframe = $("chatIframe");
        var el_iframe = el_ChatIframe.contentWindow || el_ChatIframe.contentDocument;
        if(el_iframe.document){
            el_iframe = el_iframe.document;
        }
        var el_form = el_iframe.getElementById("form_chat");
        var el_Msg = el_iframe.getElementById("input_msg");
        el_Msg.value = el_Message.value;                             
        el_form.submit();    
        Element.show("iconDiv");
        el_Message.value = "";
        el_Message.disabled = true;
    }
}

/*
* Function to hide the loading icon
*/
function jsHideLoading()
{
    parent.$("iconDiv").style.display = 'none';
    var el_Message = parent.$("input_message");
    el_Message.disabled = false;
    el_Message.value = "";
    //el_Message.focus();    
}

/*
* Function to process the clearing of the message window
*/
function jsClearWindow()
{
    Element.show("loadDiv");
    Element.update("chatDiv", "");
    var el_Counter = $("input_counter");
    el_Counter.value = Number(el_Counter.value) - 1;
}

/**
* =====================================
*  Js functions for the ban user popup
* =====================================
*/
/*
* Function to validate the ban user form
* @param string warn_err: The error message for warning
* @param string ban_err: The error message for ban
*/            
function jsValidateBan(warn_err, ban_err)
{
    var el_Type = document.getElementsByName("type");
    var el_Reason = $("input_reason");
    var len = el_Type.length;
    for(var i = 0; i <= len-1; i++){
        if(el_Type[i].value == 2){
            if(el_Type[i].checked){
                if(el_Reason.value == ""){
                    alert(warn_err);
                    return false;
                }
            }            
        }else if(el_Type[i].value == 1){
            if(el_Type[i].checked){
                if(el_Reason.value == ""){
                    alert(ban_err);
                    return false;
                }
            }            
        }else{
            if(el_Type[i].checked){
                if(el_Reason.value == ""){
                    alert(ban_err);
                    return false;
                }
            }            
        }
    }
    return true;
}

/**
* Function to hide/display the temp ban dropdown div
* @param object el: The ban type radio
*/
function jsBanLengthDiv(el)
{
    var el_TypeFeature = $("typeFeature");
    var el_LengthFeature = $("lengthFeature");
    var el_TypeDiv = $("typeDiv");
    var el_LengthDiv = $("lengthDiv");
    if(el.value == 0){
        el_TypeDiv.style.width = "49%";
        el_LengthDiv.style.width = "49%";
        Element.show(el_LengthDiv.id)
        xHeight(el_LengthFeature, xHeight(el_TypeFeature));
    }else{
        el_TypeDiv.style.width = "100%";
        Element.hide(el_LengthDiv.id)
    }    
}

/**
* ========================================
*  Js functions for the invite user popup
* ========================================
*/
/*
* Function to search for users using an ajax call 
*/            
function jsInviteUserList()
{        
    var el_option = document.getElementsByName("option");
    var len = el_option.length;
    var myValue = "";
    for(var i = 0; i <= len-1; i++){
        if(el_option[i].checked){
            myValue = el_option[i].value;
        }
    }
    var input = "input_username";
    var target = "userDiv";
    var pars = "module=messaging&action=invitelist&option="+myValue;
    new Ajax.Autocompleter(input, target, URL, {parameters: pars});
}

/*
* Function to validate the invite user input
* @param string err_invite: The user invite error message
*/            
function jsValidateInvite(err_invite)
{
    var el_UserId = $("input_userId");
    var el_Username = $("input_username");
    if(el_UserId.value == ""){
        alert(err_invite);
        el_Username.value = "";
        el_Username.focus();
        return false;
    }else{
        $("form_invite").submit();
    }    
}

/**
* =====================================
*  Js functions for the chat log popup
* =====================================
*/
/*
* Function to show hide the log dates div
* @param object el: The log type radio
*/            
function jsLogDateDiv(el)
{        
    var el_DateDiv = $("dateDiv");
    if(el.value == 2){
        Element.show(el_DateDiv.id);
        window.resizeTo(500, 420);
    }else{
        Element.hide(el_DateDiv.id);
        window.resizeTo(500, 280);
    }
}

/*
* Function to validate chat log dates 
* @param string err_start: The start date error message
* @param string err_end: The end date error message
* @param string err_date: The date comparision error message
*/            
function jsValidateDate(err_start, err_end, err_date)
{
    var el_Type = document.getElementsByName("type");
    var el_InputStart = $("input_start");
    var el_InputEnd = $("input_end");
    var el_Log = $("form_log");
    var len = el_Type.length;
    if(el_Type[1].checked){
        if(el_InputStart.value == ""){
            alert(err_start);
            return false;
        }else{
            if(el_InputEnd.value == ""){
                alert(err_end);
                return false;
            }else{
                var startString = el_InputStart.value;
                var arrStartDateAndTime = startString.split(" ");
                var arrStartDate = arrStartDateAndTime[0].split("-");
                var arrStartTime = arrStartDateAndTime[1].split(":");
                var startDate = new Date();
                startDate.setYear(arrStartDate[0]);
                startDate.setMonth(arrStartDate[1]-1);
                startDate.setDate(arrStartDate[2]);
                startDate.setHours(arrStartTime[0]-1);
                startDate.setMinutes(arrStartTime[1]);
                        
                var endString = el_InputEnd.value;
                var arrEndDateAndTime = endString.split(" ");
                var arrEndDate = arrEndDateAndTime[0].split("-");
                var arrEndTime = arrEndDateAndTime[1].split(":");
                var endDate = new Date();
                endDate.setYear(arrEndDate[0]);
                endDate.setMonth(arrEndDate[1]-1);
                endDate.setDate(arrEndDate[2]);
                endDate.setHours(arrEndTime[0]-1);
                endDate.setMinutes(arrEndTime[1]);

                if(endDate <= startDate){
                    alert(err_date);
                    return false; 
                }
            }
        }                    
    }
    return true;   
}

/**
* =======================================
*  Js function for the remove user popup
* =======================================
*/
/*
* Function to validate the removed user form
* @param string err_remove: The user remove error message
*/            
function jsValidateRemove(err_remove)
{
    var el_checkbox = document.getElementsByName("userId[]");
    var myValue = false;
    for(var i = 0; i<el_checkbox.length; i++){
        if(el_checkbox[i].checked == true){
            myValue = true;
        }
    }
    if(myValue){
        $("form_remove").submit();
    }else{
        alert(err_remove);
        return false;
    }
}

/**
* ==============================
*  Js function for the im popup
* ==============================
*/
/*
* Function to validate the im input
* @param string err_invite: The user im error message
*/            
function jsValidateUser(err_user)
{
    var el_UserId = $("input_userId");
    var el_Username = $("input_value");
    if(el_UserId.value == ""){
        alert(err_user);
        el_Username.value = "";
        el_Username.focus();
        return false;
    }else{
        $("form_sendim").submit();
    }    
}

/*
* Function to search for users using an ajax call 
*/            
function jsImUserList()
{        
    var el_option = document.getElementsByName("option");
    var len = el_option.length;
    var myValue = "";
    for(var i = 0; i <= len-1; i++){
        if(el_option[i].checked){
            myValue = el_option[i].value;
        }
    }
    var input = "input_value";
    var target = "userDiv";
    var pars = "module=messaging&action=getimusers&option="+myValue;
    new Ajax.Autocompleter(input, target, URL, {parameters: pars});
}

/**
* Function to hide/display the interval dropdown div
* @param object el: The delivery type radio
*/
function jsIntervalDiv(el)
{
    var el_IntervalDiv = $("intervalDiv");
    if(el.value == 2){
        Element.show(el_IntervalDiv.id)
    }else{
        Element.hide(el_IntervalDiv.id)
    }    
}

/**
* ========================================
*  Js Functions to generate the IM popups
* ========================================
*/
/*
* Function to get IM settings
* @param string uri: The current uri
*/
function jsGetImSettings(uri)
{
    URI = uri;
    var target = $("settingsDiv");
    var pars = "module=messaging&action=getimsettings";
    var myAjax = new Ajax.Updater(target, URL, {method: "post", parameters: pars, onComplete: jsImSettingsTimer});        
}

/*
* Function to check the Im settings every half hour
*/
function jsImSettingsTimer()
{
    settingsTimer = setTimeout("jsGetImSettings('"+URI+"')", 900000);
    clearTimeout(imTimer);
    jsImTimer();
}

/*
* Function to set Im request interval
*/
function jsImTimer()
{
    var login = $F("input_im_login");
    var delivery = $F("input_im_delivery");
    var interval = $F("input_im_interval");
    if(delivery == 0){
        imInterval = 60000;
        jsCheckForIm();
    }else if(delivery == 2){
        imInterval = Number(interval) * 60000;
        jsCheckForIm();
    }else{
        imInterval = "null"
        if(login == "true"){
            jsCheckForIm();
        }        
    }
}

/*
* Function to check for IM
*/
function jsCheckForIm()
{
    var target = $("imDiv");
    var pars = "module=messaging&action=checkforim";
    var myAjax = new Ajax.Updater(target, URL, {method: "post", parameters: pars, onComplete: jsDisplayIm});            
}

/*
* Function to generate popups for each IM and iterate for interval
*/
function jsDisplayIm()
{
    var imcount = $F("input_imcount");
    var count = Number(imcount);
    if(count > 0){
        for(var i = 1; i <= count; i++){
            openWindow(URI+"&action=displayim", "new_"+i, "toolbar=no, menubar=no, width=500, height=300, resizable=no, scrollbars=yes, toolbar=no top=100 screenY=100 left=100 screenX=100");
        }
    }
    if(imInterval != "null"){
        imTimer = setTimeout("jsCheckForIm()", imInterval);    
    }
}

/**
* ============================================================
*  Js function to insert formatting codes around selected text
* ============================================================
*/
/*
* Function to validate the im input
* @param string err_invite: The user im error message
*/            
function jsGetSelText(format){
    var el_Message = $("input_message");
    if(el_Message.selectionStart >= 0){
        var txt = el_Message.value;
        var startPos = el_Message.selectionStart;
        var endPos = el_Message.selectionEnd;
        var startTxt = txt.substr(0, startPos);
        var midTxt = txt.substr(startPos, endPos-startPos);
        var endTxt = txt.substr(endPos);
        if(format == "bold"){
            el_Message.value = startTxt+"[b]"+midTxt+"[/b]"+endTxt;
        }else if(format == "underline"){
            el_Message.value = startTxt+"[u]"+midTxt+"[/u]"+endTxt;
        }else if(format == "italics"){
            el_Message.value = startTxt+"[i]"+midTxt+"[/i]"+endTxt;
        }else if(format == "red"){
            el_Message.value = startTxt+"[red]"+midTxt+"[/red]"+endTxt;
        }else if(format == "orange"){
            el_Message.value = startTxt+"[orange]"+midTxt+"[/orange]"+endTxt;
        }else if(format == "yellow"){
            el_Message.value = startTxt+"[yellow]"+midTxt+"[/yellow]"+endTxt;
        }else if(format == "green"){
            el_Message.value = startTxt+"[green]"+midTxt+"[/green]"+endTxt;
        }else if(format == "blue"){
            el_Message.value = startTxt+"[blue]"+midTxt+"[/blue]"+endTxt;
        }else if(format == "purple"){
            el_Message.value = startTxt+"[purple]"+midTxt+"[/purple]"+endTxt;
        }else if(format == "pink"){
            el_Message.value = startTxt+"[pink]"+midTxt+"[/pink]"+endTxt;
        }else if(format == "s1"){
            el_Message.value = startTxt+"[s1]"+midTxt+"[/s1]"+endTxt;
        }else if(format == "s2"){
            el_Message.value = startTxt+"[s2]"+midTxt+"[/s2]"+endTxt;
        }else if(format == "s3"){
            el_Message.value = startTxt+"[s3]"+midTxt+"[/s3]"+endTxt;
        }else if(format == "s4"){
            el_Message.value = startTxt+"[s4]"+midTxt+"[/s4]"+endTxt;
        }
    }else{
        var rng = document.selection.createRange();
        if(format == "bold"){
            rng.text = "[b]" + rng.text + "[/b]";
        }else if(format == "underline"){
            rng.text = "[u]" + rng.text + "[/u]";
        }else if(format == "italics"){
            rng.text = "[i]" + rng.text + "[/i]";
        }else if(format == "red"){
            rng.text = "[red]" + rng.text + "[/red]";
        }else if(format == "orange"){
            rng.text = "[orange]" + rng.text + "[/orange]";
        }else if(format == "yellow"){
            rng.text = "[yellow]" + rng.text + "[/yellow]";
        }else if(format == "green"){
            rng.text = "[green]" + rng.text + "[/green]";
        }else if(format == "blue"){
            rng.text = "[blue]" + rng.text + "[/blue]";
        }else if(format == "purple"){
            rng.text = "[purple]" + rng.text + "[/purple]";
        }else if(format == "pink"){
            rng.text = "[pink]" + rng.text + "[/pink]";
        }else if(format == "s1"){
            rng.text = "[s1]" + rng.text + "[/s1]";
        }else if(format == "s2"){
            rng.text = "[s2]" + rng.text + "[/s2]";
        }else if(format == "s3"){
            rng.text = "[s3]" + rng.text + "[/s3]";
        }else if(format == "s4"){
            rng.text = "[s4]" + rng.text + "[/s4]";
        }
    }
}

/*
* Function to expand the colour format div
*/
function jsExpandStyle()
{
    var el_Style = $("styleDiv");
    var el_Colour = $("colourDiv");
    var el_Font = $("fontDiv");
    if(el_Style.style.display == "none"){
        Element.hide(el_Colour.id);
        Element.hide(el_Font.id);
        Element.show(el_Style.id);
    }else{
        Element.hide(el_Style.id);
    }
}

/*
* Function to expand the colour format div
*/
function jsExpandColour()
{
    var el_Style = $("styleDiv");
    var el_Colour = $("colourDiv");
    var el_Font = $("fontDiv");
    if(el_Colour.style.display == "none"){
        Element.hide(el_Style.id);
        Element.hide(el_Font.id);
        Element.show(el_Colour.id);
    }else{
        Element.hide(el_Colour.id);
    }
}

/*
* Function to expand the font size format div
*/
function jsExpandFont()
{
    var el_Style = $("styleDiv");
    var el_Colour = $("colourDiv");
    var el_Font = $("fontDiv");
    if(el_Font.style.display == "none"){
        Element.hide(el_Style.id);
        Element.hide(el_Colour.id);
        Element.show(el_Font.id);
    }else{
        Element.hide(el_Font.id);
    }
}
