package org.wits.client.ads;

import com.google.gwt.core.client.JavaScriptObject;

/**
 *
 * @author davidwaf
 */
public class JSonUser extends JavaScriptObject {

    protected JSonUser() {
    }

    public final native String getFirstName() /*-{ return this.firstname; }-*/;

    public final native String getSurname() /*-{ return this.surname; }-*/;

    public final native String getEmail() /*-{ return this.email; }-*/;
}
