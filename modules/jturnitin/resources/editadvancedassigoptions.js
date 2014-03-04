/**
 * when to generate the Originality Reports (optional,
 * 0 = immediately (first report is final), 1 =immediately (can overwrite reports
 * until the due date), 2 = on the due date
 */
var reportGenSpeed="0";
/**
 * exclude bibliographic material from the Originality Reports
 * generated for that assignment (default 1)
 */
var excludeBiographicMaterial="1";
/**
 * exclude quoted material from the Originality Reports
generated for that assignment (default 1)
exclude_type: for excluding small matches
 */
var excludeQuotedMaterial="1";
/**
 * for excluding small matches and is the exclusion type (by word
count = 1, by percentage = 2)
 */

var excludeSmallMatches="1";

var studentsViewOriginalityReports="1";//done
/**
 * allow submission past due date (optional, default = 0 not
allow)
 */
var allowLateSubmissions="0";
/**
 * where to store the submission (optional, default = 1)
0 = no repository, 1 = standard repository, 2 = institutional repository (if the
institurtion has one)
 */
var submitPapersTo="0";
/**
compare submitted papers with other student papers
(optional, default =1 allow)
*/
var s_paper_check="1";

/**
 * compare submitted papers to internet database (optional,
default = 1 allow)
 */
var internet_check="1";
/**
 * compare submitted papers with journals, periodicals,
publications (optional, default = 1 allow)
 */
var journal_check="1";
var advancedOptWin;
var reportGenSpeedStore = new Ext.data.ArrayStore({
    fields: ['code','name'],
    data : [
    ['0','immediately (first report is final)'],
    ['1','immediately (can overwrite reports until due date)'],
    ['2','on due date']
    ]
});

var generateOpts=new Array();
generateOpts[0]='immediately (first report is final)';
generateOpts[1]='immediately (can overwrite reports until due date)';
generateOpts[2]= 'on due date';

var reportGenSpeedCombo = new Ext.form.ComboBox({
    store: reportGenSpeedStore,
    displayField:'name',

    valueField: 'code',
    width:300,
    typeAhead: true,
    fieldLabel:'Select Assessment type',
    mode: 'local',
    value:generateOpts[generate],
    forceSelection: true,
    triggerAction: 'all',
    emptyText:'Select one option...',
    selectOnFocus:true
});


var excludeBiographicMaterialOptYes=new Ext.form.Radio(

{
        checked: true,
        xtype:'radio',
        boxLabel: 'Yes',
        labelSeparator: '',
        name: 'excludeBiographicMaterialOpt',
        inputValue: '1'
    });

var excludeBiographicMaterialOptNo=new Ext.form.Radio(
{
    checked: true,
    xtype:'radio',
    fieldLabel: '',
    boxLabel: 'No',
    labelSeparator: '',
    name: 'excludeBiographicMaterialOpt',
    inputValue: '0'
}
);

var excludeQuotedMaterialOptYes=new Ext.form.Radio(

{
        checked: true,
        xtype:'radio',
        boxLabel: 'Yes',
        labelSeparator: '',
        name: 'excludeQuotedMaterial',
        inputValue: '1'
    });

var excludeQuotedMaterialOptNo=new Ext.form.Radio(
{
    checked: true,
    xtype:'radio',
    fieldLabel: '',
    boxLabel: 'No',
    labelSeparator: '',
    name: 'excludeQuotedMaterial',
    inputValue: '0'
}
);


var option1Panel = new Ext.Panel({
    width:600,
    height: 450,
    items: [

    {
        xtype:'fieldset',
        fieldLabel: 'Generate Originality Reports for student submissions',
        labelWidth:150,

        defaults:{
            bodyStyle:'padding:10px'
        },
        items :[

        {
            html: '<b>Generate Originality Reports for student submissions</b>   '
        },

        reportGenSpeedCombo
        ]
    },
    {
        xtype:'fieldset',
        labelWidth:15,
        defaults:{
            bodyStyle:'padding:10px'
        },
        items :[
        {
            border:false,
            html: '<b>Exclude bibliographic materials from Similarity Index for all papers in this assignment?</b> Bibliographic materials can also be included and excluded when viewing the Originality Report. This setting cannot be modified after the first paper has been submitted.'
        },
        excludeBiographicMaterialOptYes,
        excludeBiographicMaterialOptNo

        ]
    },
    {
        xtype:'fieldset',
        labelWidth:15,
        defaults:{
            bodyStyle:'padding:10px'
        },

        items :[
        {
            border:false,
            html: '<b>Exclude quoted materials from Similarity Index for all papers in this assignment?</b>Quoted materials can also be included and excluded when viewing the Originality Report. This setting cannot be modified after the first paper has been submitted.'
        },
        excludeQuotedMaterialOptYes,
        excludeQuotedMaterialOptNo
        ]
    }


    ]
});
function showAdvancedOptions(){

    if(!advancedOptWin){
        advancedOptWin = new Ext.Window({
            title:'Advanced Options - Step 1',
            layout:'fit',
            width:600,
            height:450,
            closeAction:'hide',
            plain: true,

            items:[
            option1Panel
            ],

            buttons: [
           
            {
                text: 'Next',
                handler: function(){
                    updateValues1();
                    showAdvancedOptions2();
                //       advancedOptWin.hide();
                }
            }
        
            
            ]
        });
      
    }
    advancedOptWin.show(this);
}


function updateValues1(){
    reportGenSpeed=reportGenSpeedCombo.getValue();
    excludeBiographicMaterial=excludeBiographicMaterialOptYes.getGroupValue();
    excludeQuotedMaterial=excludeQuotedMaterialOptYes.getGroupValue();

}