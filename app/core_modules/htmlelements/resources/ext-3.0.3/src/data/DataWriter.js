/*!
 * Ext JS Library 3.0.3
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.data.DataWriter
 * <p>Ext.data.DataWriter facilitates create, update, and destroy actions between
 * an Ext.data.Store and a server-side framework. A Writer enabled Store will
 * automatically manage the Ajax requests to perform CRUD actions on a Store.</p>
 * <p>Ext.data.DataWriter is an abstract base class which is intended to be extended
 * and should not be created directly. For existing implementations, see
 * {@link Ext.data.JsonWriter}.</p>
 * <p>Creating a writer is simple:</p>
 * <pre><code>
var writer = new Ext.data.JsonWriter();
 * </code></pre>
 * <p>The proxy for a writer enabled store can be configured with a simple <code>url</code>:</p>
 * <pre><code>
// Create a standard HttpProxy instance.
var proxy = new Ext.data.HttpProxy({
    url: 'app.php/users'
});
 * </code></pre>
 * <p>For finer grained control, the proxy may also be configured with an <code>api</code>:</p>
 * <pre><code>
// Use the api specification
var proxy = new Ext.data.HttpProxy({
    api: {
        read    : 'app.php/users/read',
        create  : 'app.php/users/create',
        update  : 'app.php/users/update',
        destroy : 'app.php/users/destroy'
    }
});
 * </code></pre>
 * <p>Creating a Writer enabled store:</p>
 * <pre><code>
var store = new Ext.data.Store({
    proxy: proxy,
    reader: reader,
    writer: writer
});
 * </code></pre>
 * @constructor Create a new DataWriter
 * @param {Object} meta Metadata configuration options (implementation-specific)
 * @param {Object} recordType Either an Array of field definition objects as specified
 * in {@link Ext.data.Record#create}, or an {@link Ext.data.Record} object created
 * using {@link Ext.data.Record#create}.
 */
Ext.data.DataWriter = function(config){
    Ext.apply(this, config);
};
Ext.data.DataWriter.prototype = {

    /**
     * @cfg {Boolean} writeAllFields
     * <tt>false</tt> by default.  Set <tt>true</tt> to have DataWriter return ALL fields of a modified
     * record -- not just those that changed.
     * <tt>false</tt> to have DataWriter only request modified fields from a record.
     */
    writeAllFields : false,
    /**
     * @cfg {Boolean} listful
     * <tt>false</tt> by default.  Set <tt>true</tt> to have the DataWriter <b>always</b> write HTTP params as a list,
     * even when acting upon a single record.
     */
    listful : false,    // <-- listful is actually not used internally here in DataWriter.  @see Ext.data.Store#execute.

    /**
     * Writes data in preparation for server-write action.  Simply proxies to DataWriter#update, DataWriter#create
     * DataWriter#destroy.
     * @param {String} action [CREATE|UPDATE|DESTROY]
     * @param {Object} params The params-hash to write-to
     * @param {Record/Record[]} rs The recordset write.
     */
    write : function(action, params, rs) {
        var data    = [],
        renderer    = action + 'Record';
        // TODO implement @cfg listful here
        if (Ext.isArray(rs)) {
            Ext.each(rs, function(rec){
                data.push(this[renderer](rec));
            }, this);
        }
        else if (rs instanceof Ext.data.Record) {
            data = this[renderer](rs);
        }
        this.render(action, rs, params, data);
    },

    /**
     * abstract method meant to be overridden by all DataWriter extensions.  It's the extension's job to apply the "data" to the "params".
     * The data-object provided to render is populated with data according to the meta-info defined in the user's DataReader config,
     * @param {String} action [Ext.data.Api.actions.create|read|update|destroy]
     * @param {Record[]} rs Store recordset
     * @param {Object} params Http params to be sent to server.
     * @param {Object} data object populated according to DataReader meta-data.
     */
    render : Ext.emptyFn,

    /**
     * @cfg {Function} updateRecord Abstract method that should be implemented in all subclasses
     * (e.g.: {@link Ext.data.JsonWriter#updateRecord JsonWriter.updateRecord}
     */
    updateRecord : Ext.emptyFn,

    /**
     * @cfg {Function} createRecord Abstract method that should be implemented in all subclasses
     * (e.g.: {@link Ext.data.JsonWriter#createRecord JsonWriter.createRecord})
     */
    createRecord : Ext.emptyFn,

    /**
     * @cfg {Function} destroyRecord Abstract method that should be implemented in all subclasses
     * (e.g.: {@link Ext.data.JsonWriter#destroyRecord JsonWriter.destroyRecord})
     */
    destroyRecord : Ext.emptyFn,

    /**
     * Converts a Record to a hash.
     * @param {Record}
     * @private
     */
    toHash : function(rec) {
        var map = rec.fields.map,
            data = {},
            raw = (this.writeAllFields === false && rec.phantom === false) ? rec.getChanges() : rec.data,
            m;
        Ext.iterate(raw, function(prop, value){
            if((m = map[prop])){
                data[m.mapping ? m.mapping : m.name] = value;
            }
        });
        // we don't want to write Ext auto-generated id to hash.  Careful not to remove it on Models not having auto-increment pk though.
        // We can tell its not auto-increment if the user defined a DataReader field for it *and* that field's value is non-empty.
        // we could also do a RegExp here for the Ext.data.Record AUTO_ID prefix.
        if (rec.phantom) {
            if (rec.fields.containsKey(this.meta.idProperty) && Ext.isEmpty(rec.data[this.meta.idProperty])) {
                delete data[this.meta.idProperty];
            }
        } else {
            data[this.meta.idProperty] = rec.id
        }
        return data;
    }
};