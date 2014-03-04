/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.server;

import java.io.*;
import java.net.URLEncoder;
import java.util.Enumeration;
import javax.servlet.*;
import javax.servlet.http.*;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.methods.GetMethod;


public class ChisimbaServlet extends HttpServlet {

    @Override
    public void doPost(HttpServletRequest request,
            HttpServletResponse response)
            throws ServletException, IOException {
        response.setContentType("text/html");
        PrintWriter out = response.getWriter();


        Enumeration paramNames = request.getParameterNames();

        String paramStr = "";


        while (paramNames.hasMoreElements()) {
            String paramName = (String) paramNames.nextElement();
            paramStr += paramName + "=" + request.getParameter(paramName) + "&";
        }
        out.print(execChisimba(paramStr));
        out.close();
    }

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        doPost(req, resp);
    }

    private String execChisimba(String params) {
        HttpClient httpClient = new HttpClient();
        String result = "";
        GetMethod get=new GetMethod();
        try {
        get = new GetMethod("http://localhost/chisimba/app/?" + params);
        int iGetResultCode = -1;
        
            iGetResultCode = httpClient.executeMethod(get);
            result = get.getResponseBodyAsString();


        } catch (Exception ex) {
            return ex.getMessage();
        } finally {
            get.releaseConnection();

        }
        return result;
    }
}
