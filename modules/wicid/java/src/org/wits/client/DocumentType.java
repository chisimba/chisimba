/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.data.BaseModel;

/**
 *
 * @author davidwaf
 */
public class DocumentType extends BaseModel {

   

    public DocumentType(String type) {
      setType(type);
    }

    public String getType() {
        return get("type");
    }

    public void setType(String type) {
        set("type",type);
    }
     public String toString(){
        return get("type");
    }
}
