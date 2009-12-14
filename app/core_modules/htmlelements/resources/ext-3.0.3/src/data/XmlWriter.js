/*!
 * Ext JS Library 3.0.3
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.data.XmlWriter
 * @extends Ext.data.DataWriter
 * DataWriter extension for writing an array or single {@link Ext.data.Record} object(s) in preparation for executing a remote CRUD action via XML.
 */
Ext.data.XmlWriter = function(params) {
    Ext.data.XmlWriter.superclass.constructor.apply(this, arguments);
    this.tpl = new Ext.XTemplate(this.tpl).compile();
};
Ext.extend(Ext.data.XmlWriter, Ext.data.DataWriter, {
    /**
     * @cfg {String} root [records] The name of the root element when writing <b>multiple</b> records to the server.  Each
     * xml-record written to the server will be wrapped in an element named after {@link Ext.data.XmlReader#record} property.
     * eg:
<code><pre>
&lt;?xml version="1.0" encoding="UTF-8"?>
&lt;user>&lt;first>Barney&lt;/first>&lt;/user>
</code></pre>
     * However, when <b>multiple</b> records are written in a batch-operation, these records must be wrapped in a containing
     * Element.
     * eg:
<code><pre>
&lt;?xml version="1.0" encoding="UTF-8"?>
    &lt;records>
        &lt;first>Barney&lt;/first>&lt;/user>
        &lt;records>&lt;first>Barney&lt;/first>&lt;/user>
    &lt;/records>
</code></pre>
     * Defaults to <tt>records</tt>
     */
    root: 'records',
    /**
     * @cfg {String} xmlVersion [1.0] The <tt>version</tt> written to header of xml documents.
<code><pre>&lt;?xml version="1.0" encoding="ISO-8859-15"?></pre></code>
     */
    xmlVersion : '1.0',
    /**
     * @cfg {String} xmlEncoding [ISO-8859-15] The <tt>encoding</tt> written to header of xml documents.
<code><pre>&lt;?xml version="1.0" encoding="ISO-8859-15"?></pre></code>
     */
    xmlEncoding: 'ISO-8859-15',
    /**
     * @cfg {String} tpl The xml template.  Defaults to
<code><pre>
&lt;?xml version="{version}" encoding="{encoding}"?>
    &lt;tpl if="{[values.nodes.length>1]}">&lt;{root}}>',
    &lt;tpl for="records">
        &lt;{parent.record}>
        &lt;tpl for="fields">
            &lt;{name}>{value}&lt;/{name}>
        &lt;/tpl>
        &lt;/{parent.record}>
    &lt;/tpl>
    &lt;tpl if="{[values.records.length>1]}">&lt;/{root}}>&lt;/tpl>
</pre></code>
     */
    // Break up encoding here in case it's being included by some kind of page that will parse it (eg. PHP)
    tpl: '<tpl for="."><' + '?xml version="{version}" encoding="{encoding}"?' + '><tpl if="documentRoot"><{documentRoot}><tpl for="baseParams"><tpl for="."><{name}>{value}</{name}</tpl></tpl></tpl><tpl if="records.length&gt;1"><{root}></tpl><tpl for="records"><{parent.record}><tpl for="."><{name}>{value}</{name}></tpl></{parent.record}></tpl><tpl if="records.length&gt;1"></{root}></tpl><tpl if="documentRoot"></{documentRoot}></tpl></tpl>',

    /**
     * Final action of a write event.  Apply the written data-object to params.
     * @param {String} action [Ext.data.Api.create|read|update|destroy]
     * @param {Ext.data.Record/Ext.data.Record[]} rs
     * @param {Object} http params
     * @param {Object/Object[]} rendered data.
     */
    render : function(action, rs, params, data) {
        params.xmlData = this.tpl.applyTemplate({
            version: this.xmlVersion,
            encoding: this.xmlEncoding,
            record: this.meta.record,
            root: this.root,
            records: (Ext.isArray(rs)) ? data : [data]
        });
    },

    /**
     * Converts an Ext.data.Record to xml
     * @param {Ext.data.Record} rec
     * @return {String} rendered xml-element
     * @private
     */
    toXml : function(data) {
        var fields = [];
        Ext.iterate(data, function(k, v) {
            fields.push({
                name: k,
                value: v
            });
        },this);
        return {
            fields: fields
        };
    },

    /**
     * createRecord
     * @param {Ext.data.Record} rec
     * @return {String} xml element
     * @private
     */
    createRecord : function(rec) {
        return this.toXml(this.toHash(rec));
    },

    /**
     * updateRecord
     * @param {Ext.data.Record} rec
     * @return {String} xml element
     * @private
     */
    updateRecord : function(rec) {
        return this.toXml(this.toHash(rec));

    },
    /**
     * destroyRecord
     * @param {Ext.data.Record} rec
     * @return {String} xml element
     */
    destroyRecord : function(rec) {
        var data = {};
        data[this.meta.idProperty] = rec.id;
        return this.toXml(data);
    }
});

