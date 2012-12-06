/*
 * Ext JS Library 3.0 RC2
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 *
 * http://extjs.com/license
 * By Qhamani Fenama
 * qfenama@gmail.com/qfenama@uwc.ac.za
 */
var editwin;
var addwin;
var vid ;

Ext.QuickTips.init();

// turn on validation errors beside the field globally
Ext.form.Field.prototype.msgTarget = 'side';

var bd = Ext.getBody();

var val2;

// custom Vtype for vtype:'email'
var vemailTest =  /^(\w+)([\-+.\'][\w]+)*@(\w[\-\w]*\.){1,5}([A-Za-z]){2,6}$/;
Ext.apply(Ext.form.VTypes, {
    //  vtype validation function
    vemail: function(val, field) {
        return vemailTest.test(val);
    },
    // vtype Text property: The error text to display when the validation function returns false
    vemailText: 'This field should be an e-mail address in the format "user@example.com".'
	    
});

Ext.apply(Ext.form.VTypes, {
    password : function(val, field) {
        if (field.initialPassField) {
            var pwd = Ext.getCmp(field.initialPassField);
            return (val == pwd.getValue());
        }
        return true;
    },

    passwordText : 'Passwords do not match',

    username : function(val, field){
        userNameAvailable(val);
        return (val2 == '1');
    },

    usernameText: 'Username is already taken'
});

var titledata =  new Ext.data.ArrayStore({
    id: 0,
    fields: [
    'titleId',
    'titleText'
    ],
    data: [[1, 'Mr'], [2, 'Miss'], [3, 'Mrs'], [4, 'Ms'], [5, 'Dr'], [6, 'Professor'], [7, 'Rev'], [8, 'Assoc Prof']]
});


var countrydata = new Ext.data.ArrayStore({
    fields: ['countryId', 'countryText'],
    data: Ext.countrydata.countries
});

// create the combo instance
var titleCombo = new Ext.form.ComboBox({
    typeAhead: true,
    fieldLabel:'Title',
    name:'useradmin_title',
    triggerAction: 'all',
    emptyText:'Select a Title...',
    editable: false,
    lazyRender:true,
    mode: 'local',
    store: titledata,
    valueField: 'titleId',
    displayField: 'titleText',
    allowBlank:false,
    width: 150
});

// create the combo instance
var edittitleCombo = new Ext.form.ComboBox({
    typeAhead: true,
    fieldLabel:'Title',
    name:'useradmin_title',
    triggerAction: 'all',
    emptyText:'Select a Title...',
    editable: false,
    lazyRender:true,
    mode: 'local',
    store: titledata,
    valueField: 'titleId',
    displayField: 'titleText',
    allowBlank:false,
    width: 150
});

// create the combo instance
var countryCombo = new Ext.form.ComboBox({
    typeAhead: true,
    fieldLabel:'Country',
    name: 'country',
    hiddenName: 'countryId',
    triggerAction: 'all',
    emptyText:'Select a Country...',
    editable: false,
    lazyRender:true,
    mode: 'local',
    store: countrydata,
    valueField: 'countryId',
    displayField: 'countryText',
    allowBlank:false,
    width: 150
});

var editcountry = new Ext.form.ComboBox({
    typeAhead: true,
    fieldLabel:'Country',
    name: 'country',
    hiddenName: 'countryId',
    triggerAction: 'all',
    emptyText:'Select a Country...',
    editable: false,
    lazyRender:true,
    mode: 'local',
    store: countrydata,
    valueField: 'countryId',
    displayField: 'countryText',
    allowBlank:false,
    width: 150
});

var edituri;

var edituser = new Ext.FormPanel({
    standardSubmit: true,
    frame:true,
    title: 'Edit User',
    bodyStyle:'padding:5px 5px 0',
    width: "65%",
    waitMsgTarget: true,
    items: [{
        layout:'column',
        items:[
        {
            columnWidth:.5,
            layout: 'form',
            defaultType: 'textfield',
            items: [edittitleCombo,
            {
                fieldLabel: 'First Name',
                name: 'useradmin_firstname',
                allowBlank:false
            },
            {
                fieldLabel: 'Last Name',
                name: 'useradmin_surname',
                allowBlank:false
            },
            {
                defaultType: 'textfield',
                fieldLabel: 'Identification Number',
                name: 'useradmin_staffnumber'
            },
            {
                fieldLabel: 'Email Address',
                name: 'useradmin_email',
                allowBlank:false,
                vtype: 'vemail',
                invalidText : 'Provide a valid email address'
            },
            {
                fieldLabel: 'Cell Number',
                name: 'useradmin_cellnumber'
            }]
        },{
            columnWidth:.5,
            layout: 'form',
            defaultType: 'textfield',
            items: [
            editcountry,
            {
                xtype: 'radiogroup',
                name: 'useradmin_sex',
                fieldLabel: 'Sex',
                items: [
                {
                    xtype: 'radio',
                    id : 'sex_M',
                    labelSeparator:'',
                    name: 'useradmin_sex',
                    boxLabel: 'Male',
                    inputValue: 'M'
                },{
                    xtype: 'radio',
                    id : 'sex_F',
                    labelSeparator:'',
                    name: 'useradmin_sex',
                    boxLabel: 'Female',
                    inputValue: 'F'
                }
                ]
            },{
                xtype: 'radiogroup',
                name: 'useradmin_sex',
                fieldLabel: 'Account Status',
                items: [
                {
                    xtype: 'radio',
                    id : 'active_1',
                    labelSeparator:'',
                    name: 'accountstatus',
                    boxLabel: 'Active',
                    inputValue: '1'
                },{
                    xtype: 'radio',
                    id : 'active_0',
                    labelSeparator:'',
                    name: 'accountstatus',
                    boxLabel: 'InActive',
                    inputValue: '0'
                }
                ]
            },{
                fieldLabel: 'Username',
                readOnly: true,
                name: 'useradmin_username',
                allowBlank:false
            }, {
                fieldLabel: 'Password',
                name: 'useradmin_password',
                id: 'epass',
                vtype: 'password',
                inputType: 'password',
                allowBlank: true
            }, {
                fieldLabel: 'Confirm Password',
                name: 'useradmin_repeatpassword',
                vtype: 'password',
                inputType: 'password',
                allowBlank: true,
                initialPassField: 'epass'
            }]
        }
        ]
    }],

    buttons: [{
        text: 'Update User',
        handler: function (){
            edituri = baseuri+"?module=useradmin&action=jsonupdateuserdetails&id="+vid;
            edituser.getForm().getEl().dom.action = edituri;
            edituser.getForm().submit();
        }
    }]
});
