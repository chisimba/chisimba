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

    Ext.MessageBox.alert('Warning', 'This might take a moment to initialize.', init);

}
function init(btn){
 Ext.MessageBox.show({
           msg: 'Please wait...',
           progressText: 'Initializing...',
           width:300,
           wait:true,
           waitConfig: {interval:200}
       });

    window.location="?module=jturnitin&action=loadusertemplate";
}

