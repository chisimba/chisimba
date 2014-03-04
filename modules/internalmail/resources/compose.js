
function addRecipient(username)
{
	
	 jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=internalmail&action=ajaxaddrecicienpt&username="+username,
            success: function(msg){              
				loadRecipientList();
				jQuery('#result').html(msg);
            }
        });
}


function removeRecipient(username)
{
	
	 jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=internalmail&action=ajaxremoverecicienpt&username="+username,
            success: function(msg){              
				loadRecipientList();
				jQuery('#result').html(msg);
            }
        });
}

function loadRecipientList()
{	
	jQuery.ajax({
            type: "GET", 
            url: "index.php", 
            data: "module=internalmail&action=ajaxgetrecipientlist",
            success: function(msg){              
				//loadRecipientList();
				jQuery('#toList').html(msg);
            }
        });
}

function listfirstname()
{        
    $("input_surname").value = "";
    pars = "module=internalmail&action=composelist&field=firstname&firstname=a";
    new Ajax.Autocompleter("input_firstname", "firstnameDiv", "index.php", {parameters: pars});
}

function listsurname()
{        
    $("input_firstname").value = "";

    var pars = "module=internalmail&action=composelist&field=surname";
    new Ajax.Autocompleter("input_surname", "surnameDiv", "index.php", {parameters: pars});
}
    
function addRecipient2(userid)
{
    var el = $("input_recipient");
    var elArr = el.value.split("|");
    var len = elArr.length
    var exist = false;
    for(i=0; i<len; i++){
        if(elArr[i] == userid){
            exist = true;
        }
    }
    if(exist == false){
        if(el.value == ""){
            el.value = userid;
        }else{
            el.value = el.value + "|" + userid;
        }
    }
    var url = "index.php";
    var pars = "module=internalmail&action=makelist&recipientList=" + el.value;
    var target = "toList";
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars, onLoading: addLoad, onComplete: addComplete});
}
    
function addLoad()
{
    $("add_load").style.visibility = "visible";
}

function addComplete()
{
    $("add_load").style.visibility = "hidden";
}
    
function deleteRecipient(userid)
{
    var el = $("input_recipient");
    var elArr = el.value.split("|");
    el.value = "";
    var len = elArr.length;
    for(i=0; i<len; i++){
        if(elArr[i] != userid){
            if(el.value == ""){
                el.value = elArr[i];
            }else{
                el.value = el.value + "|" + elArr[i];
            }
        }
    }
    var url = "index.php";
    var pars = "module=internalmail&action=makelist&recipientList=" + el.value;
    var target = "toList";
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars, onLoading: addLoad, onComplete: addComplete});
}
