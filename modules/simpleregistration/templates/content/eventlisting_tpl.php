<?php
$this->loadclass('link','htmlelements');
$objIcon= $this->newObject('geticon','htmlelements');


$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/session.css').'"/>';
$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);

$saveEventUrl = $this->uri(array('action'=>'editevent'));
$editEventUrl = $this->uri(array('action'=>'editevent'));

$events=$this->dbevents->getMyEvents();

$total=count($events);
$data="";
$index=0;

foreach($events as $row){

    $deleteLink=new link();
    $deleteLink->link($this->uri(array('action'=>'deleteevent','id'=>$row['id'])));
    $objIcon->setIcon('delete');
    $deleteLink->link=$objIcon->show();

    $editLink=new link();
    $editLink->link($this->uri(array('action'=>'editevent','eventid'=>$row['id'],'eventtitle'=>$row['event_title'])));
    $objIcon->setIcon('edit');
    $editLink->link=$objIcon->show();

    $previewLink=new link();
    $previewLink->link($this->uri(array('action'=>'showevent','eventid'=>$row['id'])));
    $previewLink->link=$row['event_title'];

    $data.="[";
    $data.="'".$previewLink->show()."',";
    $data.="'".$row['short_name']."',";
    $data.="'".$row['max_people']."',";
    $data.="'".$row['event_date']."',";
    $data.="'".$row['id']."',";
    $data.="'".$editLink->show().$deleteLink->show()."'";
    $data.="]\n";
    $index++;
    if($index <= $total-1){
        $data.=',';
    }
}
$mainjs="/*!
 * Ext JS Library 3.0+
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
     Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
     var myData = [

       ".$data."
       
    ];

    /**
     * Custom function used for column renderer
     * @param {Object} val
     */
    function change(val){
        if(val > 0){
            return '<span style=\"color:green;\">' + val + '</span>';
        }else if(val < 0){
            return '<span style=\"color:red;\">' + val + '</span>';
        }
        return val;
    }

    /**
     * Custom function used for column renderer
     * @param {Object} val
     */
    function pctChange(val){
        if(val > 0){
            return '<span style=\"color:green;\">' + val + '%</span>';
        }else if(val < 0){
            return '<span style=\"color:red;\">' + val + '%</span>';
        }
        return val;
    }

    // create the data store
    var store = new Ext.data.ArrayStore({
        fields: [
           {name: 'eventtitle'},
           {name: 'shortname'},
	   {name: 'maxpeople'},
           {name: 'datecreated', type: 'date', dateFormat: 'Y-m-d'},
	   {name: 'eventid'},
           {name: 'edit'}
        ]
    });

    // manually load local data
    store.loadData(myData);

    // create the Grid
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
            {id:'eventtitle',header: 'Event', width: 160, sortable: true, dataIndex: 'eventtitle'},
            {header: 'Short Name', dataIndex:'shortname'},
	    {header: 'Maximum No. of People', dataIndex:'maxpeople'},
            {header: 'Date Created', width: 85, sortable: true, renderer: Ext.util.Format.dateRenderer('Y-m-d'), dataIndex: 'datecreated'},
	    {header: 'Event ID', dataIndex:'eventid'},
            {header: 'Edit', dataIndex:'edit'}
        ],
        stripeRows: true,
        autoExpandColumn: 'eventtitle',
        height: 350,
        width: 600,
        title: 'Event Listing',
        // config options for stateful behavior
        stateful: true,
        stateId: 'grid'
    });

    // render the grid to the specified div in the page
    grid.render('eventlisting');
});


var form = new Ext.form.FormPanel({
        baseCls: 'x-plain',

        labelWidth: 55,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
         url:'".str_replace("amp;", "", $saveEventUrl)."',
        defaultType: 'textfield',
items:[
 new Ext.form.TextField({
        fieldLabel: 'Event Title',
        name: 'eventtitlefield',
        width: 400,
        allowBlank: false
                        }),
 new Ext.form.TextField({
        fieldLabel: 'Short name',
        name: 'shortnamefield',
        allowBlank: false
    }),
 new Ext.form.TextField({
        fieldLabel: 'Maximum No. of People',
        name: 'maxpeoplefield',
        allowBlank: false
    }),
 new Ext.form.DateField({
        fieldLabel: 'Date',
        name: 'eventdatefield',
        format:'Y-m-d',
       allowBlank: false

              })
  ]

  });


   var addEventWin;
    var button = Ext.get('add-event-btn');
    button.on('click', function(){

       if(!addEventWin){
            addEventWin = new Ext.Window({
                applyTo:'addcomments-win',
                layout:'fit',
                title:'Enter Event Details',
                width:500,
                height:500,
                x:250,
                y:50,
                closeAction:'hide',
                plain: true,
                items: [
                contentform
                ],
                  buttons: [{
                    text:'Save',
                    handler: function(){
                  if (form.url){
                      form.getForm().getEl().dom.action = form.url;
                       }
                     form.getForm().submit();
                  }
                  }
                  ,{
                    text: 'Cancel',
                    handler: function(){
                       addEventWin.hide();
                    }
                  }
                ]

            });
        }
        addEventWin.show(this);
});



var contentform = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 55,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
         url:'".str_replace("amp;", "", $saveEventUrl)."',
        defaultType: 'textfield',
    items:[
new Ext.form.TextField({
        fieldLabel: 'Event Title',
        name: 'eventtitlefield',
        width: 400,
        allowBlank: false
                        }),
 new Ext.form.TextField({
        fieldLabel: 'Short name',
        name: 'shortnamefield',
        allowBlank: false
    }),
 new Ext.form.TextField({
        fieldLabel: 'Maximum No. of People',
        name: 'maxpeoplefield',
        allowBlank: false
    }),
 new Ext.form.DateField({
        fieldLabel: 'Date',
        name: 'eventdatefield',
        format:'Y-m-d',
       allowBlank: false

              }),
     new Ext.form.TextArea({
        fieldLabel: 'Date/Time/Venue',
        name: 'venuefield',
	width: 400
       }),
       new Ext.form.TextArea({
        fieldLabel: 'Main Content',
        name: 'contentfield',
	width: 400

       }),
     new Ext.form.TextArea({
        fieldLabel: 'Left Title1',
	width: 400,
        name: 'lefttitle1field'

       }),
     new Ext.form.TextArea({
        fieldLabel: 'Left Title2',
	width: 400,
        name: 'lefttitle2field'

       })

]

  });
";
$addButton = new button('add','Add Event');
$addButton->setId('add-event-btn');

$addLink=new link();
$addLink->link($this->uri(array('action'=>'addevent','eventid'=>$row['id'])));
$objIcon->setIcon('add');
$addLink->link=$objIcon->show();

$content = $message;

$renderSurface='<div id="addcomments-win" class="x-hidden">
        <div class="x-window-header">Add Session</div>
        </div>';
$content= '<div id="eventlisting">'.$addLink->show().$renderSurface.'<br /><br /></div>';
$content.= "<script type=\"text/javascript\">".$mainjs."</script>";


// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$rightSideColumn .= $content;
$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent( $postLoginMenu->show());

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();
?>
