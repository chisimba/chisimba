/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.server;

import java.io.*;
import java.util.Enumeration;
import javax.servlet.*;
import javax.servlet.http.*;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.methods.PostMethod;

public class ChisimbaServlet extends HttpServlet {

    @Override
    public void doPost(HttpServletRequest request,
            HttpServletResponse response)
            throws ServletException, IOException {
        response.setContentType("text/html");
        PrintWriter out = response.getWriter();


        Enumeration paramNames = request.getParameterNames();


        String result = "";
        PostMethod post = new PostMethod("http://localhost/chisimba/?");
        HttpClient httpClient = new HttpClient();
        try {

            while (paramNames.hasMoreElements()) {
                String paramName = (String) paramNames.nextElement();
                String paramValue = request.getParameter(paramName);
                post.addParameter(paramName, paramValue);

            }

            int iGetResultCode = httpClient.executeMethod(post);
            result = post.getResponseBodyAsString();

        } catch (Exception ex) {
            result = ex.getMessage();
        } finally {
            post.releaseConnection();

        }
        out.print(result);
        out.close();
    }

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        doPost(req, resp);
    }

}
