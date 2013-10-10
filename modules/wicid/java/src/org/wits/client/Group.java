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
public class Group extends BaseModel {

   

    public Group(String name) {
      setName(name);
    }

    public String getName() {
        return get("name");
    }

    public void setName(String name) {
        set("name",name);
    }
     public String toString(){
        return get("name");
    }
}
