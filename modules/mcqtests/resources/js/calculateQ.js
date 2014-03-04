function showCalcQForm() {
    Ext.Ajax.request({
        url: calcQFormUrl,
        success: calcQFormSuccess,
        failure: calcQFormFailure,
        params: {}
    });
}

var calcQFormSuccess = function(response) {
    Ext.DomHelper.overwrite('calcquestions', "");
    Ext.DomHelper.append('calcquestions', response.responseText);
}

var calcQFormFailure = function(response) {
    Ext.MessageBox.alert("Form Error", "There was an error retrieving the form. If this problem persists, Please Contact your Administrator!");
}