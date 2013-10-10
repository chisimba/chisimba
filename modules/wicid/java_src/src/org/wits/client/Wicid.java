package org.wits.client;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.user.client.ui.RootLayoutPanel;
import com.google.gwt.user.client.ui.RootPanel;

/**
 * Entry point classes define <code>onModuleLoad()</code>.
 */
public class Wicid implements EntryPoint {

    /**
     * This is the entry point method.
     */
    public void onModuleLoad() {
       
        RootLayoutPanel.get().add(new Main().createGUI());
        RootPanel.get("surface").add(RootLayoutPanel.get());
    }
}
