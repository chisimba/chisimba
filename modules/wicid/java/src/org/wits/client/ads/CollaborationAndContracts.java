package org.wits.client.ads;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.event.BaseEvent;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.Events;
import com.extjs.gxt.ui.client.event.Listener;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;

import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.Radio;
import com.extjs.gxt.ui.client.widget.form.RadioGroup;
import com.extjs.gxt.ui.client.widget.form.TextArea;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
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
 * @author nguni
 */
public class CollaborationAndContracts {

    private Dialog newCollaborationAndContractsDialog = new Dialog();
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private final RadioGroup F1a = new RadioGroup();
    private final Radio radioNo1 = new Radio();
    private final Radio radioYes1 = new Radio();
    private final Radio radioNo2 = new Radio();
    private final Radio radioYes2 = new Radio();
    private final TextArea F1b = new TextArea();
    private final RadioGroup F2a = new RadioGroup();
    private final TextArea F2b = new TextArea();
    private final TextArea F3a = new TextArea();
    private final TextArea F3b = new TextArea();
    private final TextArea F4 = new TextArea();
    private Button saveButton = new Button("Next");
    private Button backButton = new Button("Back");
    private Button forwardButton = new Button("Forward to...");
    private Resources resources;
    private Review review;
    private Review oldReview;
    private CollaborationAndContracts oldCollaborationAndContracts;
    private String collaborationAndContractsData;
    private String qF1a, qF1b, qF2a, qF2b, qF3a, qF3b, qF4;
    private Boolean[] quesF1a = new Boolean[2];
    private Boolean[] quesF2a = new Boolean[2];

    public CollaborationAndContracts(Resources resources) {
        this.resources = resources;
        createUI();
        getFormData();
    }

    public CollaborationAndContracts(Review review) {
        this.review = review;
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(680);
        mainForm.setLabelWidth(250);

        radioNo1.setBoxLabel("No");
        radioYes1.setBoxLabel("Yes");
        radioYes1.setValue(true);
        radioNo2.setBoxLabel("No");
        radioYes2.setBoxLabel("Yes");
        radioYes2.setValue(true);

        F1a.setFieldLabel("F.1.a Is approval for the course/unit required from a professional body?");
        F1a.add(radioYes1);
        F1a.add(radioNo1);

        F1b.setFieldLabel("F.1.b If yes, state the name of the professional body and provide details of the body's prerequisites and/or contraints.");
        F1b.setAllowBlank(false);
        F1b.setPreventScrollbars(false);
        F1b.setHeight(50);
        F1b.setName("F1b");

        F1a.addListener(Events.Change, new Listener<BaseEvent>() {

            public void handleEvent(BaseEvent be) {
                if (radioYes1.getValue() == false) {
                    F1b.disable();
                }
                if (radioYes1.getValue() == true) {
                    F1b.enable();
                }
            }
        });

        F2a.setFieldLabel("F.2.a Are other Schools or Faculties involved in and/or have an interest in the course/unit?");
        F2a.add(radioYes2);
        F2a.add(radioNo2);

        F2b.setFieldLabel("F.2.b If yes, provide the details of the other School's or Faculties's involvement/interest, including support and provisioning for the course/unit.");
        F2b.setAllowBlank(false);
        F2b.setPreventScrollbars(false);
        F2b.setHeight(50);
        F2b.setName("F2b");

        F2a.addListener(Events.Change, new Listener<BaseEvent>() {

            public void handleEvent(BaseEvent be) {
                if (radioYes2.getValue() == false) {
                    F2b.disable();
                }
                if (radioYes2.getValue() == true) {
                    F2b.enable();
                }
            }
        });

        F3a.setFieldLabel("F.3.a Does the course/unit provide service learning?");
        F3a.setAllowBlank(false);
        F3a.setPreventScrollbars(false);
        F3a.setHeight(50);
        F3a.setName("F3a");

        F3b.setFieldLabel("F.3.b If yes, provide the details on the nature as well as the provisioning for the service learning component and methodology.");
        F3b.setAllowBlank(false);
        F3b.setPreventScrollbars(false);
        F3b.setHeight(50);
        F3b.setName("F3b");

        F4.setFieldLabel("F.4 Specify whether collaboratoin, contracts or other cooperatoin agreements have been, or will need to be, entered into with entities outside of the univerty?");
        F4.setAllowBlank(false);
        F4.setPreventScrollbars(false);
        F4.setHeight(50);
        F4.setName("F4");

        mainForm.add(F1a, formData);
        mainForm.add(F1b, formData);
        mainForm.add(F2a, formData);
        mainForm.add(F2b, formData);
        mainForm.add(F3a, formData);
        mainForm.add(F3b, formData);
        mainForm.add(F4, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));


        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                quesF1a[0] = radioYes1.getValue();
                quesF1a[1] = radioNo1.getValue();
                qF1a = "";
                for (int i = 0; i < 2; i++) {
                    switch (new Boolean(quesF1a[i]).toString().charAt(0)) {
                        case 't':
                            qF1a = qF1a + "1";
                            break;
                        case 'f':
                            qF1a = qF1a + "0";
                            break;
                    }
                }

                if (F1a.getValue() == null) {
                    MessageBox.info("Missing Selection", "Please make a selection to question F1a", null);
                    return;
                }

                quesF2a[0] = radioYes2.getValue();
                quesF2a[1] = radioNo2.getValue();
                qF2a = "";
                for (int i = 0; i < 2; i++) {
                    switch (new Boolean(quesF2a[i]).toString().charAt(0)) {
                        case 't':
                            qF2a = qF2a + "1";
                            break;
                        case 'f':
                            qF2a = qF2a + "0";
                            break;
                    }
                }

                if (F2a.getValue() == null) {
                    MessageBox.info("Missing Selection", "Please make a selection to question F1a", null);
                    return;
                }

                if (F2b.getValue() == null) {
                    MessageBox.info("Missing Selection", "Please make a selection to question F1a", null);
                    return;
                }

                if (F3a.getValue() == null) {
                    MessageBox.info("Missing Selection", "Please make a selection to question F1a", null);
                    return;
                }

                if (F3b.getValue() == null) {
                    MessageBox.info("Missing Selection", "Please make a selection to question F1a", null);
                    return;
                }

                if (F4.getValue() == null) {
                    MessageBox.info("Missing Selection", "Please make a selection to question F1a", null);
                    return;
                }

                storeDocumentInfo();

                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=saveFormData&formname=" + "collaborationandcontracts" + "&formdata=" + collaborationAndContractsData + "&docid=" + Constants.docid;

                createDocument(url);

                if (oldReview == null) {

                    Review review = new Review(CollaborationAndContracts.this);
                    review.show();
                    newCollaborationAndContractsDialog.hide();

                } else {
                    oldReview.show();
                    newCollaborationAndContractsDialog.hide();
                }
            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                resources.setOldResources(CollaborationAndContracts.this);
                resources.show();
                newCollaborationAndContractsDialog.hide();
                storeDocumentInfo();
            }
        });

        forwardButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                ForwardTo forwardToDialog = new ForwardTo();
                forwardToDialog.show();
                storeDocumentInfo();
            }
        });

        mainForm.addButton(backButton);
        mainForm.addButton(saveButton);
        mainForm.addButton(forwardButton);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);

        newCollaborationAndContractsDialog.setBodyBorder(false);
        newCollaborationAndContractsDialog.setHeading("Section F: Collaboration and Contracts");
        newCollaborationAndContractsDialog.setWidth(690);
        //newCollaborationAndContractsDialog.setHeight(450);
        newCollaborationAndContractsDialog.setHideOnButtonClick(true);
        newCollaborationAndContractsDialog.setButtons(Dialog.CLOSE);
        newCollaborationAndContractsDialog.setButtonAlign(HorizontalAlignment.LEFT);

        newCollaborationAndContractsDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                storeDocumentInfo();
            }
        });

        setDocumentInfo();
        newCollaborationAndContractsDialog.add(mainForm);
    }

    public void storeDocumentInfo() {
        qF1b = F1b.getValue();
        qF2b = F2b.getValue();
        qF3a = F3a.getValue();
        qF3b = F3b.getValue();
        qF4 = F4.getValue();

        WicidXML wicidxml = new WicidXML("collaborationandcontracts");
        wicidxml.addElement("qF1a", qF1a);
        wicidxml.addElement("qF1b", qF1b);
        wicidxml.addElement("qF2a", qF2a);
        wicidxml.addElement("qF2b", qF2b);
        wicidxml.addElement("qF3a", qF3a);
        wicidxml.addElement("qF3b", qF3b);
        wicidxml.addElement("qF4", qF4);
        collaborationAndContractsData = wicidxml.getXml();
    }

    public void setDocumentInfo() {
    }

    public void show() {
        newCollaborationAndContractsDialog.show();
    }

    /*public void setOldReview(Review oldReview) {
    this.oldReview = oldReview;
    }*/
    public void setOldCollaborationAndContracts(Review oldReview) {
        this.oldReview = oldReview;
    }

    private void createDocument(String url) {

        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new document", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    String resp[] = response.getText().split("|");

                    if (resp[0].equals("")) {
                        /*if (oldOverView == null) {

                        Constants.docid = resp[1];
                        OverView overView = new OverView(NewCourseProposalDialog.this);
                        overView.show();
                        newDocumentDialog.hide();
                        } else {
                        oldOverView.show();
                        newDocumentDialog.hide();

                        }*/
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot create document", null);
                    }
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create new document", null);
        }
    }

    private void getFormData() {
        String url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                + "?module=wicid&action=getFormData&formname=collaborationAndContracts&docid=" + Constants.docid;
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get collaborationAndContracts data", null);
                }

                public void onResponseReceived(Request request, Response response) {

                    String data = response.getText();

                    String qF1a = Util.getTagText(data, "qF1a");
                    if (qF1a != null) {
                        for (int i = 0; i < 2; i++) {
                            if (qF1a.charAt(i) == '0') {
                                quesF1a[i] = false;
                            }
                            if (qF1a.charAt(i) == '1') {
                                quesF1a[i] = true;
                            }
                        }
                        radioYes1.setValue(quesF1a[0]);
                        radioNo1.setValue(quesF1a[1]);
                    } else {
                        radioYes1.setValue(true);
                        radioNo1.setValue(false);
                    }

                    String qF1b = Util.getTagText(data, "qF1b");
                    F1b.setValue(qF1b);

                    String qF2a = Util.getTagText(data, "qF2a");
                    if (qF2a != null) {
                        for (int i = 0; i < 2; i++) {
                            if (qF2a.charAt(i) == '0') {
                                quesF2a[i] = false;
                            }
                            if (qF2a.charAt(i) == '1') {
                                quesF2a[i] = true;
                            }
                        }
                        radioYes2.setValue(quesF2a[0]);
                        radioNo2.setValue(quesF2a[1]);
                    } else {
                        radioYes2.setValue(true);
                        radioNo2.setValue(false);
                    }

                    String qF2b = Util.getTagText(data, "qF2b");
                    F2b.setValue(qF2b);

                    String qF3a = Util.getTagText(data, "qF3a");
                    F3a.setValue(qF3a);

                    String qF3b = Util.getTagText(data, "qF3b");
                    F3b.setValue(qF3b);

                    String qF4 = Util.getTagText(data, "qF4");
                    F4.setValue(qF4);

                    /*String resp[] = response.getText().split("|");

                    if (resp[0].equals("")) {

                    } else {
                    MessageBox.info("Error", "Error occured on the server. Cannot get overview data", null);
                    }*/
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get collaborationAndContracts data", null);
        }
    }
}
