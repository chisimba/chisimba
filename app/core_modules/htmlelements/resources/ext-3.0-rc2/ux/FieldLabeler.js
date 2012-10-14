/*!
 * Ext JS Library 3.0.3
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ns("Ext.ux");

/**
 * @class Ext.ux.FieldLabeler
 * <p>A plugin for Field Components which renders standard Ext form wrapping and labels
 * round the Field at render time regardless of the layout of the Container.</p>
 * <p>Usage:</p>
 * <pre><code>
    {
        xtype: 'combo',
        plugins: [ Ext.ux.FieldLabeler ],
        triggerAction: 'all',
        fieldLabel: 'Select type',
        store: typeStore
    }
 * </code></pre>
 */
Ext.ux.FieldLabeler = (function(){

//  Pulls a named property down from the first ancestor Container it's found in
    function getParentProperty(propName) {
        for (var p = this.ownerCt; p; p = p.ownerCt) {
            if (p[propName]) {
                return p[propName];
            }
        }
    }

    return {

//      Add behaviour at important points in the Field's lifecycle.
        init: function(f) {
            f.onRender = f.onRender.createSequence(this.onRender);
            f.onResize = f.onResize.createSequence(this.onResize);
            f.onDestroy = f.onDestroy.createSequence(this.onDestroy);
        },

        onRender: function() {
//          Do nothing if being rendered by a form layout
            if (this.ownerCt) {
                if (this.ownerCt.layout instanceof Ext.layout.FormLayout) {
                    return;
                }
                if (this.nextSibling()) {
                    this.margins = '0 0 5 0';
                }
            }

            this.resizeEl = this.el.wrap({
                cls: 'x-form-element'
            });
            this.positionEl = this.itemCt = this.resizeEl.wrap({
                cls: 'x-form-item '
            });
            this.actionMode = 'itemCt';

//          If we are hiding labels, then we're done!
            if (!Ext.isDefined(this.hideLabels)) {
                this.hideLabels = getParentProperty.call(this, "hideLabels");
            }
            if (this.hideLabels) {
                this.resizeEl.setStyle('padding-left', '0px');
                return;
            }

//          Collect info we need to render the label.
            if (!Ext.isDefined(this.labelSeparator)) {
                this.labelSeparator = getParentProperty.call(this, "labelSeparator");
            }
            if (!Ext.isDefined(this.labelPad)) {
                this.labelPad = getParentProperty.call(this, "labelPad");
            }
            if (!Ext.isDefined(this.labelAlign)) {
                this.labelAlign = getParentProperty.call(this, "labelAlign") || 'left';
            }
            this.itemCt.addClass('x-form-label-' + this.labelAlign);

            if(this.labelAlign == 'top'){
                if (!this.labelWidth) {
                    this.labelWidth = 'auto';
                }
                this.resizeEl.setStyle('padding-left', '0px');
            } else {
                if (!Ext.isDefined(this.labelWidth)) {
                    this.labelWidth = getParentProperty.call(this, "labelWidth") || 100;
                }
                this.resizeEl.setStyle('padding-left', (this.labelWidth + (this.labelPad || 5)) + 'px');
                this.labelWidth += 'px';
            }

            this.label = this.itemCt.insertFirst({
                tag: 'label',
                cls: 'x-form-item-label',
                style: {
                    width: this.labelWidth
                },
                html: this.fieldLabel + (this.labelSeparator || ':')
            });
        },
    
//      private
//      Ensure the input field is sized to fit in the content area of the resizeEl (to the right of its padding-left)
        onResize: function() {
            this.el.setWidth(this.resizeEl.getWidth(true));
            if (this.el.dom.tagName.toLowerCase() == 'textarea') {
                var h = this.resizeEl.getHeight(true);
                if (!this.hideLabels && (this.labelAlign == 'top')) {
                    h -= this.label.getHeight();
                }
                this.el.setHeight(h);
            }
        },

//      private
//      Ensure that we clean up on destroy.
        onDestroy: function() {
            this.itemCt.remove();
        }
    };
})();