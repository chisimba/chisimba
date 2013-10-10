<?php
// scripts
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';


$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);

//data grid from db
$reg = $this->getObject('dbregistration');
$dbdata=$reg->getRegistrations($eventid);
$total=count($dbdata);

if(!$this->objUser->isAdmin()){
 $this->nextAction('success',array('title1'=>$this->objLanguage->languageText('mod_simpleregistration_alreadysignedup', 'simpleregistration'),
 'title2'=>''));
}
$xlsUrl = $this->uri(array('action'=>'xls','eventid'=>$eventid));

$scheduleTitle='<h2>'.$total.' '.$this->objLanguage->languageText('mod_simpleregistration_registeredmembers', 'simpleregistration').'</h2>';
$scheduleTitle.='
          <p></p>
         ';
$excelButton = new button('add','Export to Spreadsheet');
$excelButton->setId('xls-btn');
$scheduleTitle.=$excelButton->show().'';
//load class
$this->loadclass('link','htmlelements');
$objIcon= $this->newObject('geticon','htmlelements');
$content= '<div id="grouping-grid">'.$scheduleTitle.'</div>';
$data='';


$index=0;
foreach($dbdata as $row){

    $deleteLink=new link();
    $deleteLink->link($this->uri(array('action'=>'deletemember','id'=>$row['id'],'eventid'=>$eventid)));
    $objIcon->setIcon('delete');
    $deleteLink->link=$objIcon->show();

    $data.="[";
    $data.="'".$row['first_name']."',";
    $data.="'".$row['last_name']."',";
    $data.="'".$row['company']."',";
    $data.="'".$row['email']."',";
    $data.="'".$deleteLink->show()."'";
    $data.="]";
    $index++;
    if($index <= $total-1){
        $data.=',';
    }
}


$firstName='First Name';
$lastName='Last Name';
$company='Company';
$email='Email';
$delete='Delete';


$mainjs = "/*!
                 * Ext JS Library 3.0.0
                 * Copyright(c) 2006-2009 Ext JS, LLC
                 * licensing@extjs.com
                 * http://www.extjs.com/license
                 */
                Ext.onReady(function(){

                    Ext.QuickTips.init();
                    var xg = Ext.grid;

                    // shared reader
                    var reader = new Ext.data.ArrayReader({}, [
                       {name: 'firstname'},
                       {name: 'lastname'},
                       {name: 'company'},
                       {name: 'email'},
                       {name: 'delete'}
                    ]);


                   Ext.ToolTip.prototype.onTargetOver =
                        Ext.ToolTip.prototype.onTargetOver.createInterceptor(function(e) {
                            this.baseTarget = e.getTarget();
                        });
                    Ext.ToolTip.prototype.onMouseMove =
                        Ext.ToolTip.prototype.onMouseMove.createInterceptor(function(e) {
                            if (!e.within(this.baseTarget)) {
                                this.onTargetOver(e);
                                return false;
                            }
                        });

                    var grid = new xg.GridPanel({
                        store: new Ext.data.GroupingStore({
                            reader: reader,
                            data: xg.Data,
                            sortInfo:{field: 'firstname', direction: \"ASC\"},
                            groupField:'company'
                        }),

                        columns: [
                            {id:'".$firstName."',header: \"".$firstName."\", width: 225, dataIndex: 'firstname'},
                            {header: \"".$lastName."\", width: 225, dataIndex: 'lastname'},
                            {header: \"".$company."\", width: 100, dataIndex: 'company'},
                            {header: \"".$email."\", width: 100, dataIndex: 'email'},
                            {header: \"".$delete."\", width: 50, dataIndex: 'delete'}
                        ],

                        view: new Ext.grid.GroupingView({
                            forceFit:true,
                            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Members\" : \"Member\"]})'
                        }),

                        frame:false,
                        width: 700,
                        height: 350,
                        x: 20,
                        collapsible: false,
                        animCollapse: false,

                        renderTo: 'grouping-grid',
                        listeners: {
                                    render: function(g) {
                                        g.on(\"beforetooltipshow\", function(grid, row, col) {
                                            var tipText=\"\";
                                            if(col == 0){
                                              tipText=\"First Name.\";
                                            }
                                            if(col == 1){
                                              tipText=\"Last Name.\";
                                            }
                                            if(col == 2){
                                              tipText=\"Company From.\";
                                            }
                                            if(col == 3){
                                              tipText=\"Email contact.\";
                                            }

                                            grid.tooltip.body.update(tipText);
                                        });
                                    }
                                },


                        onRender: function() {
                                Ext.grid.GridPanel.prototype.onRender.apply(this, arguments);
                                this.addEvents(\"beforetooltipshow\");
                                this.tooltip = new Ext.ToolTip({
                                    renderTo: Ext.getBody(),
                                    target: this.view.mainBody,
                                    listeners: {
                                        beforeshow: function(qt) {
                                            var v = this.getView();
                                            var row = v.findRowIndex(qt.baseTarget);
                                            var cell = v.findCellIndex(qt.baseTarget);
                                            this.fireEvent(\"beforetooltipshow\", this, row, cell);
                                        },
                                        scope: this
                                    }
                                });
                            }


                    });
                });

     var addEventWin;
    var button = Ext.get('xls-btn');
    button.on('click', function(){
      window.location.href = '".str_replace("amp;", "",$xlsUrl)."';
    });
                // Array data for the grids
                Ext.grid.Data = [".$data."];";

$content.= "<script type=\"text/javascript\">".$mainjs."</script>";
// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);
//Add the table to the centered layer
$rightSideColumn .=  $content;

$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent( $postLoginMenu->show());

// Add Right Column
$cssLayout->setMiddleColumnContent( $rightSideColumn);

//Output the content to the page
echo $cssLayout->show();

?>
