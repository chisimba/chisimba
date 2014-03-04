package org.wits.client.ads;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;

import com.extjs.gxt.ui.client.widget.form.FormPanel;
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
public class Review {

    private Dialog newReviewDialog = new Dialog();
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private final TextArea G1a = new TextArea();
    private final TextArea G1b = new TextArea();
    private final TextArea G2a = new TextArea();
    private final TextArea G2b = new TextArea();
    private final TextArea G3a = new TextArea();
    private final TextArea G3b = new TextArea();
    private final TextArea G4a = new TextArea();
    private final TextArea G4b = new TextArea();
    private Button saveButton = new Button("Next");
    private Button backButton = new Button("Back");
    private Button forwardButton = new Button("Forward to...");
    private CollaborationAndContracts collaborationAndContracts;
    private ContactDetails contactDetails;
    private CollaborationAndContracts oldCollaborationAndContracts;
    private Review oldReview;
    private ContactDetails oldContactDetails;
    private String reviewData, qG1a, qG1b, qG2a, qG2b, qG3a, qG3b, qG4a, qG4b;

    public Review(CollaborationAndContracts collaborationAndContracts) {
        this.collaborationAndContracts = collaborationAndContracts;
        createUI();
        getFormData();
    }

    public Review(ContactDetails contactDetails) {
        this.contactDetails = contactDetails;
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(690);
        mainForm.setLabelWidth(250);

        G1a.setFieldLabel("G.1.a How will the course/unit syllabus be reviewed?");
        G1a.setAllowBlank(false);
        G1a.setPreventScrollbars(false);
        G1a.setHeight(50);
        G1a.setName("G1a");

        G1b.setFieldLabel("G.1.b  How often will the course/unit syllabus be reviewed?");
        G1b.setAllowBlank(false);
        G1b.setPreventScrollbars(false);
        G1b.setHeight(50);
        G1b.setName("G1b");

        G2a.setFieldLabel("G.2.a How will the integration of course/unit outcomes, syllabus, teaching methods and assessment methods be evaluated? ");
        G2a.setAllowBlank(false);
        G2a.setPreventScrollbars(false);
        G2a.setHeight(50);
        G2a.setName("G2a");

        G2b.setFieldLabel("G.2.b How often will the above integration be evaluated?");
        G2b.setAllowBlank(false);
        G2b.setPreventScrollbars(false);
        G2b.setHeight(50);
        G2b.setName("G2b");

        G3a.setFieldLabel("G.3.a How will the course/unit through-put rate be evaluated?");
        G3a.setAllowBlank(false);
        G3a.setPreventScrollbars(false);
        G3a.setHeight(50);
        G3a.setName("G3a");

        G3b.setFieldLabel("G.3.b How often will the course/unit through-put rate be evaluated?");
        G3b.setAllowBlank(false);
        G3b.setPreventScrollbars(false);
        G3b.setHeight(50);
        G3b.setName("G3b");

        G4a.setFieldLabel("G.4.a How will the teaching on the course/unit be evaluated from a student perspective and from the lecturer’s perspective?");
        G4a.setAllowBlank(false);
        G4a.setPreventScrollbars(false);
        G4a.setHeight(50);
        G4a.setName("G4a");

        G4b.setFieldLabel("G.4.b How often will the teaching on the course/unit be evaluated from these two perspectives?");
        G4b.setAllowBlank(false);
        G4b.setPreventScrollbars(false);
        G4b.setHeight(50);
        G4b.setName("G4b");

        mainForm.add(G1a, formData);
        mainForm.add(G1b, formData);
        mainForm.add(G2a, formData);
        mainForm.add(G2b, formData);
        mainForm.add(G3a, formData);
        mainForm.add(G3b, formData);
        mainForm.add(G4a, formData);
        mainForm.add(G4b, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));


        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                qG1a = G1a.getValue();
                qG1b = G1b.getValue();
                qG2a = G2a.getValue();
                qG2b = G2b.getValue();
                qG3a = G3a.getValue();
                qG3b = G3b.getValue();
                qG4a = G4a.getValue();
                qG4b = G4b.getValue();

                if (qG1a == null) {
                    MessageBox.info("Missing answer", "Provide an answer to G.1.a", null);
                    return;
                }
                if (qG1b == null) {
                    MessageBox.info("Missing answer", "Provide an answer to G.1.b", null);
                    return;
                }
                if (qG2a == null) {
                    MessageBox.info("Missing answer", "Provide an answer to G.2.a", null);
                    return;
                }
                if (qG2b == null) {
                    MessageBox.info("Missing answer", "Provide an answer to G.2.b", null);
                    return;
                }
                if (qG3a == null) {
                    MessageBox.info("Missing answer", "Provide an answer to G.3.a", null);
                    return;
                }
                if (qG3b == null) {
                    MessageBox.info("Missing answer", "Provide an answer to G.3.b", null);
                    return;
                }
                if (qG4a == null) {
                    MessageBox.info("Missing answer", "Provide an answer to G.4.a", null);
                    return;
                }
                if (qG4b == null) {
                    MessageBox.info("Missing answer", "Provide an answer to G.4.b", null);
                    return;
                }

                storeDocumentInfo();
                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=saveFormData&formname=review&formdata="
                        + reviewData + "&docid=" + Constants.docid;
                
                createDocument(url);
                if (oldContactDetails == null) {

                    ContactDetails contactDetails = new ContactDetails(Review.this);
                    contactDetails.show();
                    newReviewDialog.hide();
                } else {
                    oldContactDetails.show();
                    ;
                    newReviewDialog.hide();
                    ;
                }
            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                collaborationAndContracts.setOldCollaborationAndContracts(Review.this);
                collaborationAndContracts.show();
                newReviewDialog.hide();
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

        newReviewDialog.setBodyBorder(false);
        newReviewDialog.setHeading("Section G: Review");
        newReviewDialog.setWidth(700);
        //newReviewDialog.setHeight(450);
        newReviewDialog.setHideOnButtonClick(true);
        newReviewDialog.setButtons(Dialog.CLOSE);
        newReviewDialog.setButtonAlign(HorizontalAlignment.LEFT);

        newReviewDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                storeDocumentInfo();

            }
        });

        setDocumentInfo();
        newReviewDialog.add(mainForm);
    }

    public void storeDocumentInfo() {
        WicidXML wicidxml = new WicidXML("review");
        wicidxml.addElement("qG1a", qG1a);
        wicidxml.addElement("qG1b", qG1b);
        wicidxml.addElement("qG2a", qG2a);
        wicidxml.addElement("qG2b", qG2b);
        wicidxml.addElement("qG3a", qG3a);
        wicidxml.addElement("qG3b", qG3b);
        wicidxml.addElement("qG4a", qG4a);
        wicidxml.addElement("qG4b", qG4b);
        reviewData = wicidxml.getXml();
    }

    public void setDocumentInfo() {
    }

    public void show() {
        newReviewDialog.show();
    }

    public void setOldReview(ContactDetails oldContactDetails) {
        this.oldContactDetails = oldContactDetails;

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
                + "?module=wicid&action=getFormData&formname=review&docid=" + Constants.docid;
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get review data", null);
                }

                public void onResponseReceived(Request request, Response response) {

                    String data = response.getText();

                    String qG1a = Util.getTagText(data, "qG1a");
                    G1a.setValue(qG1a);

                    String qG1b = Util.getTagText(data, "qG1b");
                    G1b.setValue(qG1b);

                    String qG2a = Util.getTagText(data, "qG2a");
                    G2a.setValue(qG2a);

                    String qG2b = Util.getTagText(data, "qG2b");
                    G2b.setValue(qG2b);

                    String qG3a = Util.getTagText(data, "qG3a");
                    G3a.setValue(qG3a);

                    String qG3b = Util.getTagText(data, "qG3b");
                    G3b.setValue(qG3b);

                    String qG4a = Util.getTagText(data, "qG4a");
                    G4a.setValue(qG4a);

                    String qG4b = Util.getTagText(data, "qG4b");
                    G4b.setValue(qG4b);
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get review data", null);
        }
    }
}
