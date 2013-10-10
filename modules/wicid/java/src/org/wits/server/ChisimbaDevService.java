/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.server;

import com.google.gwt.user.server.rpc.RemoteServiceServlet;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.methods.GetMethod;
import org.wits.client.ChisimbaService;

/**
 * The server side implementation of the RPC service.
 */
@SuppressWarnings("serial")
public class ChisimbaDevService extends RemoteServiceServlet implements
        ChisimbaService {

    public String exec(String url) {
        HttpClient httpClient = new HttpClient();
        GetMethod get = new GetMethod(url);
        String result = "";
        try {
            int iGetResultCode = httpClient.executeMethod(get);
            result = get.getResponseBodyAsString();
            

        } catch (Exception ex) {
            return ex.getMessage();
        } finally {
            get.releaseConnection();
        }
        return result;
    }
}
