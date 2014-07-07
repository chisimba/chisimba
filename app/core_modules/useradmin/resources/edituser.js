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

/*
// create the combo instance
var countryCombo = new Ext.form.ComboBox({
    typeAhead: true,-
    fieldLabel:'Country',-
    name: 'country',-
    hiddenName: 'countryId',-
    triggerAction: 'all',-
    emptyText:'Select a Country...',-
    editable: false,-
    lazyRender:true,
    mode: 'local',-
    store: countrydata,-
    valueField: 'countryId',-
    displayField: 'countryText',-
    allowBlank:false,-
    width: 150,-
    readOnly: true
});
*/
var countryCombo = new Ext.form.ComboBox({
    xtype: 'combo',
    fieldLabel: 'Country',
    name: 'country',
    hiddenName: 'countryId',
    autoSelect: false,
    allowBlank: false,
    editable: false,
    triggerAction: 'all',
    typeAhead: true,
    width:150,
    listWidth: 150,
    enableKeyEvents: true,
    mode: 'local',
    //forceSelection: true,
    emptyText:'Select a Country...',
    valueField: 'countryId',
    displayField: 'countryText',
    //autoSelect: true,
    store: countrydata,
    chisimba_searchstring: '',
    listeners: {
        focus: function(comboBoxObj) {
            comboBoxObj.chisimba_searchstring = '';
        },
        keypress: function(comboBoxObj, keyEventObj) {
            // Ext.Store names anonymous fields (like in array above) "field1", "field2", etc.
            var valueFieldName = "countryId";
            var displayFieldName = "countryText";

            // Which drop-down item is already selected (if any)?
            //var selectedIndices = this.view.getSelectedIndexes();
            //var currentSelectedIndex = (selectedIndices.length > 0) ? selectedIndices[0] : null;

            // Prepare the search criteria we'll use to query the data store
            var charCode = keyEventObj.getCharCode()
            if (charCode == 0x08) {
                //alert('!');
                var len = comboBoxObj.chisimba_searchstring.length;
                if (len > 0) {
                    comboBoxObj.chisimba_searchstring = comboBoxObj.chisimba_searchstring.substr(0, len-1);
                }
            }
            else if (charCode >= 0x20) {
                var typedChar = String.fromCharCode(charCode);
                comboBoxObj.chisimba_searchstring += typedChar;
            }
            //else {
            //}

            //var startIndex = (currentSelectedIndex == null) ? 0 : ++currentSelectedIndex;

            //var matchIndex = this.store.find(displayFieldName, typedChar, startIndex, false);
            var matchIndex = this.store.find(displayFieldName, comboBoxObj.chisimba_searchstring, 0/*startIndex*/, false);

            if( matchIndex >= 0 ) {
                this.select(matchIndex);
                var record = this.store.getAt(matchIndex);
                this.setValue(record.get(valueFieldName));
            }
            /*
            else if (matchIndex == -1 && startIndex > 0) {
                // If nothing matched but we didn't start the search at the beginning of the list
                // (because the user already had somethign selected), search again from beginning.
                //matchIndex = this.store.find(displayFieldName, typedChar, 0, false);
                matchIndex = this.store.find(displayFieldName, comboBoxObj.chisimba_searchstring, 0, false);
                if( matchIndex >= 0 ) {
                    this.select(matchIndex);
                }
            }
            if( matchIndex >= 0 ) {
                var record = this.store.getAt(matchIndex);
                this.setValue(record.get(valueFieldName));
            }
            */
        }
    }
});

/*
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
*/
var editcountryCombo = new Ext.form.ComboBox({
    xtype: 'combo',
    fieldLabel: 'Country',
    name: 'country',
    hiddenName: 'countryId',
    autoSelect: false,
    allowBlank: false,
    editable: false,
    triggerAction: 'all',
    typeAhead: true,
    width:150,
    listWidth: 150,
    enableKeyEvents: true,
    mode: 'local',
    //forceSelection: true,
    emptyText:'Select a Country...',
    valueField: 'countryId',
    displayField: 'countryText',
    //autoSelect: true,
    store: countrydata,
    chisimba_searchstring: '',
    listeners: {
        focus: function(comboBoxObj) {
            comboBoxObj.chisimba_searchstring = '';
        },
        keypress: function(comboBoxObj, keyEventObj) {
            // Ext.Store names anonymous fields (like in array above) "field1", "field2", etc.
            var valueFieldName = "countryId";
            var displayFieldName = "countryText";

            // Which drop-down item is already selected (if any)?
            //var selectedIndices = this.view.getSelectedIndexes();
            //var currentSelectedIndex = (selectedIndices.length > 0) ? selectedIndices[0] : null;

            // Prepare the search criteria we'll use to query the data store
            var charCode = keyEventObj.getCharCode()
            if (charCode == 0x08) {
                //alert('!');
                var len = comboBoxObj.chisimba_searchstring.length;
                if (len > 0) {
                    comboBoxObj.chisimba_searchstring = comboBoxObj.chisimba_searchstring.substr(0, len-1);
                }
            }
            else if (charCode >= 0x20) {
                var typedChar = String.fromCharCode(charCode);
                comboBoxObj.chisimba_searchstring += typedChar;
            }
            //else {
            //}

            //var startIndex = (currentSelectedIndex == null) ? 0 : ++currentSelectedIndex;

            //var matchIndex = this.store.find(displayFieldName, typedChar, startIndex, false);
            var matchIndex = this.store.find(displayFieldName, comboBoxObj.chisimba_searchstring, 0/*startIndex*/, false);

            if( matchIndex >= 0 ) {
                this.select(matchIndex);
                var record = this.store.getAt(matchIndex);
                this.setValue(record.get(valueFieldName));
            }
            /*
            else if (matchIndex == -1 && startIndex > 0) {
                // If nothing matched but we didn't start the search at the beginning of the list
                // (because the user already had somethign selected), search again from beginning.
                //matchIndex = this.store.find(displayFieldName, typedChar, 0, false);
                matchIndex = this.store.find(displayFieldName, comboBoxObj.chisimba_searchstring, 0, false);
                if( matchIndex >= 0 ) {
                    this.select(matchIndex);
                }
            }
            if( matchIndex >= 0 ) {
                var record = this.store.getAt(matchIndex);
                this.setValue(record.get(valueFieldName));
            }
            */
        }
    }
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
            editcountryCombo,
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
