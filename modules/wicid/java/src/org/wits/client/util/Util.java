/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client.util;

/**
 *
 * @author davidwaf
 */
public class Util {

    public static String getTagText(String xmlContent, String tag) {
        String content = null;
        int start = (xmlContent.indexOf("<" + tag + ">")) + (("<" + tag + ">").length());
        int end = xmlContent.indexOf("</" + tag + ">");

        if (start > -1 && end > -1) {
            content = xmlContent.substring(start, end);
        }

        return content;
    }
}
