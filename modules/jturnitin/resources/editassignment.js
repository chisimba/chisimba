Ext.onReady(function(){
    var msg = function(title, msg){
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };
    var startDateStr=dstart;
    var startDateArray=startDateStr.split("-");
    var startDate=new Date();
    var dayStr2=startDateArray[2];
    var day2=dayStr2.split(" ");
    startDate.setYear(startDateArray[0]);
    startDate.setMonth(startDateArray[1])
    startDate.setDate(day2[0]);
    startDate.setMonth(startDate.getMonth()-1);


    var endDateStr=dend;
    var endDateArray=endDateStr.split("-");
    var endDate=new Date();
    var dayStr=endDateArray[2];
    var day=dayStr.split(" ");
    endDate.setYear(endDateArray[0]);
    endDate.setMonth(endDateArray[1])
    endDate.setDate(day[0]);
    endDate.setMonth(endDate.getMonth()-1);


    var win;
    var advancedbutton = new Ext.Button({
        text: 'Advanced Options',

        handler: function(){
            showAdvancedOptions();

        }

    });

    Ext.QuickTips.init();
    var addForm = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url: baseuri+'?module=jturnitin&action=ajax_updateassignment',
        frame:true,
    
        shadow: true,
        title: 'Edit Assessment',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {
            width: 230
        },
        defaultType: 'textfield',

        items: [{
            fieldLabel: 'Title',
            name: 'title',
            emptyText: 'Title of the paper...',
            anchor:'95%',
            value: title,
            // disabled:true,
            allowBlank:false
        },

        //assTypeCombo  ,

        {

            xtype:'fieldset',
            fieldLabel: 'Dates',
            defaults:{
                bodyStyle:'padding:10px'
            },

            collapsible: false,
            autoHeight:true,
            defaults: {
                anchor: '-20' // leave room for error icon
            },
            defaultType: 'textfield',

            //items on the fieldset
            items :[
            new Ext.form.DateField({
                fieldLabel: 'Start Date',
                anchor:'95%',
                name: 'startdt',
                id: 'initdt',
                format: 'o-m-d',
                value:startDate,
                allowBlank:false,
                blankText: 'The Start Date is a Required Field!',
                endDateField: 'enddt'
            }),
            new Ext.form.DateField({
                fieldLabel: 'End Date',
                type: 'datefield',
                format: 'o-m-d',
                name: 'duedt',
                id: 'enddt',
                value:endDate,
                allowBlank:false,
                startDateField: 'startdt' // id of the start date field
            })

            ]
        },{
            xtype:'htmleditor',
            id:'instructions',
            name:'instructions',
            fieldLabel:'Special Instructions',
            height:100,
            value:ainst,
            anchor:'98%'
        }
        ]

    });
    
    var submitbutton = new Ext.Button({
        text: 'Save',

        handler: function(){

            if(addForm.getForm().isValid()){

                addForm.getForm().submit({
                    url:baseuri,
                    waitMsg:'Updating Assignment...',
                    timeout:10,

                    params: {
                        module: 'jturnitin',
                        action: 'ajax_updateassignment',

                        report_gen_speed:reportGenSpeed,
                        exclude_biblio:excludeBiographicMaterial,
                        exclude_quoted:excludeQuotedMaterial,
                        exclude_value:excludeSmallMatches,
                        late_accept_flag:allowLateSubmissions,
                        submit_papers_to:submitPapersTo,
                        s_paper_check:s_paper_check,
                        journal_check:journal_check,
                        internet_check:internet_check,
                        s_view_report:studentsViewOriginalityReports,
                        assignmentid:assignmentid,
                        oldtitle:oldtitle

                    },
                    success: function(form, action) {
                        win.hide();
                        addForm.getForm().reset();
                        window.opener.location.href = window.opener.location.href;
                        Ext.MessageBox.alert('Success',action.result.msg,function(){

                            window.close();
                        });
                    },
                    failure: function(form, action) {

                        msg('Error', action.result.msg);


                    }
                });
            }
        }
    });


    win = new Ext.Window({
	                
        layout:'fit',
        y:100,
        width:500,
        height:450,
        closeAction:'hide',
        plain: true,
        renderTo:'surface',	
        items:[addForm],
	
        buttons: [advancedbutton,submitbutton]
    });
        
    win.show(this);

});

