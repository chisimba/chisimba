var advancedOptWin2;
var repoStore = new Ext.data.ArrayStore({
    fields: ['code','name'],
    data : [
    ['0','no repository'],
    ['1','standard paper repository'],
    ['2','institutional repository (if the institution has one']
    
    ]
});
var repoCombo = new Ext.form.ComboBox({
    store: repoStore,
    displayField:'name',
    valueField:'code',
    labelWidth:200,
    typeAhead: true,
    mode: 'local',
    forceSelection: true,
    triggerAction: 'all',
    fieldLabel:'Submit papers to:',
    emptyText:'Select one option...',
    selectOnFocus:true
});



var excSmallMatchesOptYes=new Ext.form.Radio({
  
    fieldLabel: 'Exclude small matches?',
    boxLabel: 'Yes',
    labelSeparator: '',
    name: 'excSmallMatchesOpt',
    inputValue: '1'
});

var excSmallMatchesOptNo=new Ext.form.Radio({
    boxLabel: 'No',
    labelSeparator: '',
    name: 'excSmallMatchesOpt',
    inputValue: '2'
});

var studentViewOriginalityReportOptYes=new Ext.form.Radio(

{
        checked:true,
        fieldLabel:'Allow students to see Originality Reports?',
        xtype:'radio',
        boxLabel: 'Yes',
        labelSeparator: '',
        name: 'studentViewOriginalityReportOpt',
        inputValue: '1'
    });

var studentViewOriginalityReportOptNo=new Ext.form.Radio(
{

    xtype:'radio',
    fieldLabel: '',
    boxLabel: 'No',
    labelSeparator: '',
    name: 'studentViewOriginalityReportOpt',
    inputValue: '0'
}
);

var lateSubmissionOptYes=new Ext.form.Radio(

{
       
        xtype:'radio',
        fieldLabel:'Allow submissions after the due date?',
        boxLabel: 'Yes',
        labelSeparator: '',
        name: 'lateSubmissionOpt',
        inputValue: '1'
    });

var lateSubmissionOptNo=new Ext.form.Radio(
{
    checked: true,
    xtype:'radio',
    fieldLabel: '',
    boxLabel: 'No',
    labelSeparator: '',
    name: 'lateSubmissionOpt',
    inputValue: '0'
}
);
var studentPaperRepositoryOpt=new Ext.form.Checkbox( {
    fieldLabel: 'student paper repository',
    name: 'studentpaperrepository'
});

var archiveInternetOpt=new Ext.form.Checkbox({
    fieldLabel: 'current and archived internet',
    name: 'archiveinternet'
});

var periodicalsOpt=new Ext.form.Checkbox({
    fieldLabel: 'periodicals, journals, & publications',
    name: 'periodicals'
});
var option2Panel = new Ext.Panel({
    width:600,
    height: 580,
    
    items: [
    {
        xtype: 'fieldset',
        title: '',
        autoHeight: true,
        labelWidth: 300,
        defaultType: 'radio', // each item will be a radio
        items: [
        excSmallMatchesOptYes,
        excSmallMatchesOptNo
        ]
    },

    {
        xtype:'fieldset',
        labelWidth: 300,
        defaults:{
            bodyStyle:'padding:10px'
        },
        items :[
       
        studentViewOriginalityReportOptYes,
        studentViewOriginalityReportOptNo

        ]
    },
    {
        xtype:'fieldset',
        labelWidth: 300,
        defaults:{
            bodyStyle:'padding:10px'
        },

        items :[
        lateSubmissionOptYes,
        lateSubmissionOptNo
        ]
    },
    {
        xtype:'fieldset',
        fieldLabel: 'Generate Originality Reports for student submissions',
        labelWidth: 300,

        defaults:{
            bodyStyle:'padding:10px'
        },
        items :[
        repoCombo

        ]
    },{

        xtype: 'fieldset',
        title: 'Search options',
        autoHeight: true,
        labelWidth: 300,
        defaultType: 'checkbox',
        items: [
        studentPaperRepositoryOpt,
        archiveInternetOpt,
        periodicalsOpt
        ]
    }


    ]
});
function showAdvancedOptions2(){

    if(!advancedOptWin2){
        advancedOptWin2 = new Ext.Window({

            layout:'fit',
            width:600,
            height:580,
            title:'Advanced Options - Step 2',
            closeAction:'hide',
            plain: true,

            items:[
            option2Panel
            ],

            buttons: [
            {
                text: 'Back',
                handler: function(){
                    updateValues2();
                    showAdvancedOptions();
                    //advancedOptWin2.destroy();
                }
            },
        
            {
                text: 'Finish',
                handler: function(){
                    updateValues2();
                    advancedOptWin2.destroy();
                    advancedOptWin.destroy();
                    advancedbutton.disable();
                }
            }
            ]
        });
      
    }
    advancedOptWin2.show(this);
}

function updateValues2(){
    excludeSmallMatches=excSmallMatchesOptNo.getGroupValue();
    studentsViewOriginalityReports=studentViewOriginalityReportOptYes.getGroupValue();
    allowLateSubmissions=lateSubmissionOptNo.getGroupValue();

    submitPapersTo=repoCombo.getValue();

    s_paper_check=studentPaperRepositoryOpt.getValue() == true? "0":"1";
    journal_check=periodicalsOpt.getValue() == true? "0":"1";
    internet_check=archiveInternetOpt.getValue() == true? "0":"1";
    studentsViewOriginalityReports=studentViewOriginalityReportOptNo.getGroupValue();
}