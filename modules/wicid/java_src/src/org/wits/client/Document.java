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
public class Document extends BaseModel {

     public void setAttachmentStatus(String status){
         set("attachmentstatus",status);
     }

     public String getAttachmentStatus(){
         return get("attachmentstatus");
     }
    public void setGroup(String group) {
        set("group", group);
    }

    public void setOwnerName(String ownerName) {
        set("ownername", ownerName);
    }

    public String getOwnerName() {
        return get("ownername");
    }

    public String getGroup() {
        return get("group");
    }

    public void setDate(String date) {
        set("date", date);
    }

    public String getDate() {
        return get("date");
    }

    public void setRefNo(String refNo) {
        set("refno", refNo);
    }

    public String getRefNo() {
        return get("refno");
    }

    public void setDepartment(String department) {
        set("department", department);
    }

    public String getDepartment() {
        return get("department");
    }

    public void setTelephone(String telephone) {
        set("telephone", telephone);
    }

    public String getTelephone() {
        return get("telephone");
    }

    public void setTitle(String title) {
        set("title", title);
    }

    public String getTitle() {
        return get("title");
    }

    public void setTopic(String topic) {
        set("topic", topic);
    }

    public String getTopic() {
        return get("topic");
    }

    public void setId(String id) {
        set("id", id);
    }

    public String getId() {
        return get("id");
    }

    @Override
    public String toString() {
        return get("title");
    }

    public String setQuestion(String question){
        return get("question");
    }
}
