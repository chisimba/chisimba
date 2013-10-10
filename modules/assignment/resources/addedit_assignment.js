function initRadioButtons(
    _type,
    _isReflection,
    _allowMultiple,
    _emailAlert,
    _filenameConversion,
    canChangeField
    )
{
    var isOnline = _type=='0';
    var isReflection = _isReflection=="1";
    var allowMultiple = _allowMultiple=="1";
    var emailAlert=_emailAlert=="1";
    var filenameConversion=_filenameConversion=="1";
    customPanel = Ext.extend(Ext.Panel, {
        id:'customPanel',
        border:false,
        constructor: function(radiobuttongroup){
            var items = [radiobuttongroup];
            customPanel.superclass.constructor.call(this, {
                items: items
            });
        }
    });
    if (canChangeField) {
        customPanel.override({
            renderTo : '_type'
        });
        var panel1 = new customPanel(
        {
            defaultType: 'radio',
            border:false,
            width:200,
            items: [
            {
            xtype: 'radiogroup',
            items: [
                {boxLabel: 'Online', name: 'type', inputValue: 0, checked: isOnline},
                {boxLabel: 'Upload', name: 'type', inputValue: 1, checked: !isOnline}

            ]
        } ]
        }
        );
    }
    customPanel.override({
        renderTo : 'isReflection'
    });
    var panel2 = new customPanel(
    {
        defaultType: 'radio',
        border:false,
        width:100,
        items: [
               
        {
            xtype: 'radiogroup',
            items: [
                {boxLabel: 'Yes', name: 'assesment_type', inputValue: 1, checked: isReflection},
                {boxLabel: 'No', name: 'assesment_type', inputValue: 0, checked: !isReflection}

            ]
        }


        ]
    }
    );
    customPanel.override({
        renderTo : 'allowMultiple'
    });
    var panel3 = new customPanel(
    {
        defaultType: 'radio',
        border:false,
        width:100,
        items: [
        
        {
            xtype: 'radiogroup',
            items: [
                {boxLabel: 'Yes', name: 'resubmit', inputValue: 1, checked: allowMultiple},
                {boxLabel: 'No', name: 'resubmit', inputValue: 0, checked: !allowMultiple}

            ]
        }
        ]
    }
    );
    customPanel.override({
        renderTo : 'emailAlert'
    });
    var panel4 = new customPanel(
    {
        defaultType: 'radio',
        border:false,
        layout:'anchor',
        width:100,
        items: [
            {
            xtype: 'radiogroup',
            items: [
                {boxLabel: 'Yes', name: 'emailalert', inputValue: 1, checked: emailAlert},
                {boxLabel: 'No', name: 'emailalert', inputValue: 0, checked: !emailAlert}
                
            ]
        }
        ]
    });
    customPanel.override({
        renderTo : 'filenameConversion'
    });
    var panel5 = new customPanel(
     {
        defaultType: 'radio',
        border:false,
        layout:'anchor',
        width:100,
        items: [
            {
            xtype: 'radiogroup',
            items: [
                {boxLabel: 'Yes', name: 'filenameconversion', inputValue: 1, checked: filenameConversion},
                {boxLabel: 'No', name: 'filenameconversion', inputValue: 0, checked: !filenameConversion}
                
            ]
        }
        ]
    }
    );

}
