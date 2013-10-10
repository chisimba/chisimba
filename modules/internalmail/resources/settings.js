function nameOrder(){
    var fieldValue = $F("input_name");
    var otherValue = $F("input_username");
    var url = "index.php";
    var pars = "module=internalmail&action=namedisplay&field=name&value="+fieldValue+"&other="+otherValue;
    var target = "userdisplay";
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});
        
    addButton("user_button");
}

function displayUsername(){
    var fieldValue = $F("input_username");
    var otherValue = $F("input_name");
    var url = "index.php";
    var pars = "module=internalmail&action=namedisplay&field=username&value="+fieldValue+"&other="+otherValue;
    var target = "userdisplay";
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});
        
    addButton("user_button");
}
    
function folderButton(){
    addButton("folder_button");
}
    
function deleteButton(){
    addButton("delete_button");
}
    
function signatureButton(){
    addButton("signature_button");
}
    
function addButton(section){
    var url = "index.php";
    var pars = "module=internalmail&action=buttondisplay&section="+section;
    var target = section;
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});        
}