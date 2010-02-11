	/*
	 * Ext JS Library 3.0 RC2
	 * Copyright(c) 2006-2009, Ext JS, LLC.
	 * licensing@extjs.com
	 * 
	 * http://extjs.com/license
	 * By Qhamani Fenama
	 * qfenama@gmail.com/qfenama@uwc.ac.za
	 */

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
						vtype: 'email',
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
						allowBlank:false
					
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

	function userNameAvailable(val)
	{   
		Ext.Ajax.request({
		    url: baseuri,
		    method: 'POST',
		    params: {
		           module: 'useradmin',
		           action: 'checkusername',
		           username: val
		    },
		    success: function(response) {
			var jsonData = Ext.util.JSON.decode(response.responseText);
		    val2 = jsonData.data;
			},
		    failure: function(xhr,params) {
			return false;
		    }
		});
}
