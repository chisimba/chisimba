package org.wits.client;

import com.google.gwt.user.client.rpc.RemoteService;
import com.google.gwt.user.client.rpc.RemoteServiceRelativePath;

/**
 * The client side stub for the RPC service.
 */
@RemoteServiceRelativePath("chisimba")
public interface ChisimbaService extends RemoteService {
  String exec(String url);
}
