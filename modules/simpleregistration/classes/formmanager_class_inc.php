<?php

/**
 * Description of formmanager_class_inc
 *
 * @author david
 */
class formmanager extends object {

/**
 * initialize the object, and set the necessary ext js scripts
 */
    public function init() {
    // scripts
        $extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
        $extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
        $extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
        $maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/simpleregistration.css').'"/>';

        $this->appendArrayVar('headerParams', $extbase);
        $this->appendArrayVar('headerParams', $extalljs);
        $this->appendArrayVar('headerParams', $extallcss);
        $this->appendArrayVar('headerParams', $maincss);
        $this->loadclass('link','htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     * Create an Ext Js based form. If mode is 'edit', then the form is pre-populated
     * with values
     * @param <type> $editfirstname
     * @param <type> $editlastname
     * @param <type> $editcompany
     * @param <type> $editemail
     * @param <type> $mode
     * @return <type>
     */
    public function createRegisterForm(
        $editfirstname,
        $editlastname,
        $editcompany,
        $editemail,
        $mode,
        $allowStaffReg,
        $allowExternalReg,
        $eventid
    ) {

        $submitUrl = $this->uri(array('action' => 'register',
            'title1'=>$this->objLanguage->languageText('mod_simpleregistration_registrationsuccess', 'simpleregistration'),
            'title2'=>$this->objLanguage->languageText('mod_simpleregistration_success', 'simpleregistration'),
            'eventid'=>$eventid));
        $expressLink =$this->uri(array('action' => 'expresssignin','eventid'=>$eventid));


        $editfirstname=$mode == 'edit' ? "value:'".$editfirstname."',":"";
        $editlastname=$mode == 'edit' ? "value:'".$editlastname."',":"";
        $editcompany=$mode == 'edit' ? "value:'".$editcompany."',":"";
        $editemail=$mode == 'edit' ? "value:'".$editemail."',":"";
        $regFormJS=
            "Ext.onReady(function(){

     Ext.QuickTips.init();
     new Ext.ToolTip({
        target: 'needhelp',
        html: 'Login using your staff number as username. You will be required to login twice to complete your registration ',
        title: 'My Tip Title',
        autoHide: false,
        closable: true,
        draggable:true
    });
";
        if(strtolower( $allowStaffReg) == 'true') {
    /*
     * ================  Registration form  =======================
     */

            $regFormJS.="  var wform = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 75,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
        url:'".str_replace("amp;", "", $expressLink)."',
        defaultType: 'textfield',
        renderTo: 'registration',
        collapsible: true,
        buttonAlign:'left',
        defaults: {width: 320},
        bodyStyle:'background-color:transparent',
        border:false,
      items: {
            xtype: 'fieldset',
            title: 'Staff registration here',
            autoHeight: true,

       
        buttons: [{
            text: 'Staff Sign Up',
            handler: function(){
            if (wform.url)
            wform.getForm().getEl().dom.action = wform.url;
            wform.getForm().submit();
            }

        }]
 }
    });
";

        }

        if(strtolower( $allowExternalReg) == 'true') {
            $regFormJS.=" var form = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 75,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
        url:'".str_replace("amp;", "", $submitUrl)."',
        defaultType: 'textfield',
        renderTo: 'registration',
        collapsible: true,
        defaults: {width: 320},
        width: 350,

        bodyStyle:'background-color:transparent',
        border:false,
      items: {
            xtype: 'fieldset',
            title: 'Students/Visitors registration here',
            autoHeight: true,


        items: [
             {
                fieldLabel: 'First Name',
                height: 21,
                width: 200,
                xtype: 'textfield',
                name: 'firstname',
                ".$editfirstname."
                allowBlank:false
            },{
                fieldLabel: 'Last Name',
                height: 21,
                width: 200,
                xtype: 'textfield',
                name: 'lastname',
                ".$editlastname."
                allowBlank:false
            },{
                fieldLabel: 'Sch./Inst.',
                height: 21,
                width: 200,
                xtype: 'textfield',
                ".$editcompany."
                name: 'company'
            }, {
                fieldLabel: 'Email',
                height: 21,
                width: 200,
                xtype: 'textfield',
                name: 'emailfield',
                ".$editemail."
                vtype:'email'
            }
        ]
       ,

        buttons: [{
            text: 'Sign Up',
            handler: function(){
            if (form.url)
            form.getForm().getEl().dom.action = form.url;
            form.getForm().submit();
            }

        }]
 }
    });";
        }
        $regFormJS.="
 });
";

        //where we render the frame
        $content='<div id="registration"></div>';
        $content.= "<script type=\"text/javascript\">".$regFormJS."</script>";


        return $content;


    }


    public function createCommentsForm() {
        $submitUrl = $this->uri(array('action' => 'addcomments'));
        $commentFormJS=
            "Ext.onReady(function(){

    /*
     * ================ comments form  =======================
     */
    var wform = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 75,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
        url:'".str_replace("amp;", "", $submitUrl)."',
        defaultType: 'textfield',
        renderTo: 'surface',
        collapsible: true,
        defaults: {width: 320},
        bodyStyle:'background-color:transparent',
        border:false,
        items: {
            xtype: 'fieldset',
            title: 'We would like to hear from you',
            autoHeight: true,
             buttons: [{
            text: 'Log in to give comments',
            handler: function(){
            if (wform.url)
            wform.getForm().getEl().dom.action = wform.url;
            wform.getForm().submit();
            }
           }]
     }
      });
    });
";


        //where we render the frame
        $content='<div id="surface"></div>';
        $content.= "<script type=\"text/javascript\">".$commentFormJS."</script>";


        return $content;


    }
}
?>
