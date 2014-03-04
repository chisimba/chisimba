package org.wits.client;

import com.google.gwt.user.client.rpc.AsyncCallback;

/**
 * The async counterpart of <code>GreetingService</code>.
 */
public interface ChisimbaServiceAsync {
  void exec(String url, AsyncCallback<String> callback);
}
