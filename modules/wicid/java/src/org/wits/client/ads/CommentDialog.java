/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client.ads;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;

import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.TextArea;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.Response;
import org.wits.client.Constants;
import org.wits.client.util.Util;
import org.wits.client.util.WicidXML;

/**
 *
 * @author Jacqueline Gil
 */
public class CommentDialog {

    private Dialog commentDialog = new Dialog();
    private FormPanel panel = new FormPanel();
    private FormData formData = new FormData("-20");
    private Button commentButton = new Button("Done");
    private TextArea comments = new TextArea();
    private String currentUserId, formname, commentData, data;

    public CommentDialog(String formName) {
        createUI();
        formname = formName;
        getComments();
    }

    public void createUI() {
        comments.setEmptyText("Enter any comments here");
        comments.setSize(285, 270);
        commentDialog.add(comments, formData);


        commentDialog.setButtons(Dialog.OKCANCEL);
        commentDialog.getButtonById(Dialog.OK).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                addCommentInfo(formname);
                commentDialog.hide();
            }
        });

        commentDialog.getButtonById(Dialog.CANCEL).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                commentDialog.hide();
            }
        });

        commentDialog.setBodyBorder(false);
        commentDialog.setHeading("Comments");
        commentDialog.setHeight(300);
        commentDialog.setWidth(300);
        commentDialog.setButtonAlign(HorizontalAlignment.CENTER);
        commentDialog.setHideOnButtonClick(true);
        //commentDialog.add(panel, formData);
    }

    public void show() {
        commentDialog.show();
    }

    public void addCommentInfo(String formname) {
        WicidXML wicidxml = new WicidXML(formname);
        wicidxml.addElement("comments", comments.getValue());
        commentData = data+wicidxml.getXml();
        storeCommentInfo();
    }

    public void storeCommentInfo() {
        String url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                + "?module=wicid&action=saveFormData&docid=" + Constants.docid + "&formname=" + formname + "&formdata=" + commentData;
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);
        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot change currentuser", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    MessageBox.info("Done", "Your comments have been added", null);
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot change currentuser", null);
        }
    }

    public String getComments() {

        String url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=getFormData&formname="
                +formname + "&docid=" + Constants.docid;

        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot change currentuser", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    data = response.getText();

                    String comment = Util.getTagText(data, "comments");

                    if (comment == null) {
                        comments.setValue("");
                    } else {
                        comments.setValue(comment);
                    }
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot change currentuser", null);
        }
        return data;
    }
}
