function mailaction(){
    var el = $("input_mailAction");        
    var url = "index.php";
    var target = "actionLayer";
    var pars = "module=internalmail&action=actiondisplay&mailAction=" + el.value + "&target=" + target;
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});
    var target = "destLayer";
    var pars = "module=internalmail&action=actiondisplay&mailAction=" + el.value + "&target=" + target;
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});
}

function messagefield(){        
    var el = $("input_messageField");        
    var url = "index.php";
    var target = "fieldLayer";
    var pars = "module=internalmail&action=filterdisplay&messageField=" + el.value + "&target=" + target;
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});
    var target = "criteriaLayer";
    var pars = "module=internalmail&action=filterdisplay&messageField=" + el.value + "&target=" + target;
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});
}

function mailfield(){        
    var el = $("input_mailField");        
    var url = "index.php";
    var target = "criteriaLayer";
    var pars = "module=internalmail&action=criteriadisplay&mailField=" + el.value;
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});
}

function ruleaction(){        
    var el = $("input_ruleAction");        
    var el1 = $("input_mailAction");        
    var url = "index.php";
    var target = "destLayer";
    var pars = "module=internalmail&action=destdisplay&ruleAction="+el.value+"&mailAction="+el1.value;
    var myAjax = new Ajax.Updater(target, url, {method: "get", parameters: pars});
}