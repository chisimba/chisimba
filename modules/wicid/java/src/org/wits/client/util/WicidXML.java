/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client.util;

import com.google.gwt.http.client.URL;

/**
 *
 * @author luigi
 */
public class WicidXML {

    private String xml = new String();
    private String rootTag;

    public WicidXML(String rootTag) {
        this.rootTag = rootTag;
    }

    public void addElement(String name, String value) {
        //value = value.replaceAll("<>", "");
        value=URL.encode(value);
        xml += "<" + name + ">" + value + "</" + name + ">";
    }

    public String toString() {
        return "<" + rootTag + ">" + xml + "</" + rootTag + ">";
    }

    public String getXml() {
        rootTag = "<" + rootTag + ">" + xml + "</" + rootTag + ">";
        return rootTag;// = rootTag.replaceAll("<>", "");
    }
}
