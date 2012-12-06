/*
	 * Ext JS Library 3.0 RC2
	 * Copyright(c) 2006-2009, Ext JS, LLC.
	 * licensing@extjs.com
	 *
	 * http://extjs.com/license
	 * By Qhamani Fenama
	 * qfenama@gmail.com/qfenama@uwc.ac.za
	 */
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

var adduser = new Ext.FormPanel({
    standardSubmit: true,
    url: baseuri + "?module=useradmin&action=jsonsavenewuser",
    frame:true,
    title: 'Add User',
    bodyStyle:'padding:5px 5px 0',
    width: "65%",
    items: [{
        layout:'column',
        items:[
        {
            columnWidth:.5,
            layout: 'form',
            defaultType: 'textfield',
            items: [titleCombo,
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
            countryCombo,
            {
                xtype: 'radiogroup',
                name: 'useradmin_sex',
                fieldLabel: 'Sex',
                items: [
                {
                    xtype: 'radio',
                    labelSeparator:'',
                    name: 'useradmin_sex',
                    boxLabel: 'Male',
                    inputValue: 'M',
                    checked: true

                },{
                    xtype: 'radio',
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
                    labelSeparator:'',
                    name: 'accountstatus',
                    boxLabel: 'Active',
                    inputValue: '1',
                    checked: true

                },{
                    xtype: 'radio',
                    labelSeparator:'',
                    name: 'accountstatus',
                    boxLabel: 'InActive',
                    inputValue: '0'
                }
                ]
            },{
                fieldLabel: 'Username',
                name: 'useradmin_username',
                vtype: 'username',
                allowBlank:false,
                validationEvent: blur
            }, {
                fieldLabel: 'Password',
                name: 'useradmin_password',
                id: 'pass',
                vtype: 'password',
                inputType: 'password',
                allowBlank: false
            }, {
                fieldLabel: 'Confirm Password',
                name: 'useradmin_repeatpassword',
                vtype: 'password',
                inputType: 'password',
                allowBlank: false,
                initialPassField: 'pass' //id of the initial password field
            }]
        }
        ]
    }],
    buttons: [{
        text: 'Add User',
        handler: function (){
            if (adduser.url)
            {
                adduser.getForm().getEl().dom.action = adduser.url;
            }
            adduser.getForm().submit();
        }
    }]
});

/*
	function userNameAvailable_old(val)
	{
		Ext.Ajax.request({
		    url: baseuri,
		    method: 'POST',
		    params: {
		           module: 'useradmin',
		           action: 'checkusername',
		           username: val
		    },
		    timeout: 180000,
		    success: function(response, opts) {
			var jsonData = Ext.util.JSON.decode(response.responseText);
		    val2 = jsonData.data;
			},
            failure: function(response, opts) {
                console.log('server-side failure with status code ' + response.status);
            }
		    //failure: function(xhr,params) {
			//return false;
		    //}
		});
    }
    */

function userNameAvailable(val)
{
    new Ajax.Request(
        baseuri,
        {
            asynchronous: false,
            method:'post',
            parameters: {
                module: 'useradmin',
                action: 'checkusername',
                username: val
            },
            onSuccess: function(transport) //, json
            {
                //val2 = json ? json.data : "no JSON object"; //Object.inspect
                var response = transport.responseText;
                if (!response) {
                    return;
                };
                var jsonData = Ext.util.JSON.decode(response); //response.responseText
                val2 = jsonData.data;
            },
            onFailure: function(transport)
            {
                console.log('server-side failure with status code ' + transport.status);
            }
        }
        );
}
