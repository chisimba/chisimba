Ext.apply(Ext.form.VTypes, {

});

   var submitbutton = new Ext.Button({
        text: 'Login',        
        handler: function(){
            loginForm.getForm().submit({
            	url:'http://localhost/das/index.php', 
            	waitMsg:'Trying to login...',
            	timeout:30,
            	params: {
			        module: 'security',
			        action: 'ajax_trylogin'
			    },
			    success: function(form, action) {
			       win.hide();
			       //loginForm.getForm().reset();
			       //assStore.load({params:{start:0, limit:25}});
		           Ext.example.msg('Success!', action.result.msg);
				  
			    },
			    failure: function(form, action) {
			    	
			      // Ext.example.msg('Error', action.result.msg);
			    	/* switch (action.failureType) {
			            case Ext.form.Action.CLIENT_INVALID:
			                Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
			                break;
			            case Ext.form.Action.CONNECT_FAILURE:
			                Ext.Msg.alert('Failure', 'Ajax communication failed');
			                break;
			            case Ext.form.Action.SERVER_INVALID:
			               Ext.Msg.alert('Failure', action.result.msg);
			       }*/
			    }
			});
        }
    });
    
     var loginForm = new Ext.FormPanel({
      labelWidth: 125,
      frame: true,
      /*title: 'Login',*/
      bodyStyle:'padding:5px 5px 0',
      width: 350,
      /*defaults: {
        width: 175,
        inputType: 'password'
      },*/
      defaultType: 'textfield',
      items: [{
        fieldLabel: 'Username',
        name: 'username',
        allowBlank:false
      },{
        fieldLabel: 'Password',
        name: 'pass',
        id: 'pass',
        inputType:"password",
        allowBlank:false
      }]
    });
    
     /*var statusbar = new Ext.ux.StatusBar({
			            id: 'win-statusbar',
			            defaultText: 'Ready'
				})*/

Ext.onReady(function(){

  	Ext.QuickTips.init();


    //pwd.render('login');
    
    var win;
    var button = Ext.get('show-btn');

    button.on('click', function(){
        // create the window on the first click and reuse on subsequent clicks
        /*if(!win){
            win = new Ext.Window({
                applyTo:'login',
                layout:'fit',
                pageX:50,
                pageY:100,
               	title:'Login',
                closeAction:'hide',
                plain: true,
                items:[loginForm],
                buttons: [submitbutton,{
                    text: 'Close',
                    handler: function(){
                        win.hide();
                    }
                }]
            });
        }
        win.show(this);
        */
    });


});