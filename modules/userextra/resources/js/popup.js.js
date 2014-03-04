/*!
 * Ext JS Library 3.0.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
    showActivationMessage();
});
function showActivationMessage(){

    Ext.MessageBox.confirm('Activation', 'It appears your account has not been activated.\n\
<br/> If you are a lecturer/student, the system will grant you appropriate lecturer/student permisions.\n\
<br/>Your account will now be activated.', activate);

}
function activate(btn){
     Ext.MessageBox.show({
           msg: 'Please wait...',
           progressText: 'Initializing...',
           width:300,
           wait:true,
           waitConfig: {interval:200}
       });

    window.location="?module=userextra&action=activate";
}
