/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.google.gwt.core.client.JavaScriptObject;

/**
 *
 * @author davidwaf
 */
public class JSonDocument extends JavaScriptObject {

    protected JSonDocument() {
    }

    public final native String getDocName() /*-{ return this.docname; }-*/;

    public final native String getRefNo() /*-{ return this.refno; }-*/;

    public final native String getTopic() /*-{ return this.topic; }-*/;

    public final native String getTelephone() /*-{ return this.telephone; }-*/;

    public final native String getGroup() /*-{ return this.groupid; }-*/;

    public final native String getOwner() /*-{ return this.owner; }-*/;

    public final native String getOwnerName() /*-{ return this.ownername; }-*/;

    public final native String getAttachmentStatus() /*-{ return this.attachmentstatus; }-*/;
}
