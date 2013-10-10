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
public class ContactDetails {

    private Dialog newContactDetailsDialog = new Dialog();
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private final TextArea H1 = new TextArea();
    private final TextArea H2a = new TextArea();
    private final TextArea H2b = new TextArea();
    private final TextArea H3a = new TextArea();
    private final TextArea H3b = new TextArea();
    private Button saveButton = new Button("Finish");
    private Button backButton = new Button("Back");
    private Button forwardButton = new Button("Forward to...");
    private Review review;
    private ContactDetails oldContactDetails;
    private String contactDetailsData;
    private String qH1, qH2a, qH2b, qH3a, qH3b;

    public ContactDetails(Review review) {
        this.review = review;
        createUI();
        getFormData();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(690);
        mainForm.setLabelWidth(250);

        H1.setFieldLabel("H.1 Name of academic proposing the course/unit");
        H1.setAllowBlank(false);
        H1.setPreventScrollbars(false);
        H1.setHeight(50);
        H1.setName("H1");

        H2a.setFieldLabel("H.2.a Name of the School which will be the home for the course/unit");
        H2a.setAllowBlank(false);
        H2a.setPreventScrollbars(false);
        H2a.setHeight(50);
        H2a.setName("H2a");

        H2b.setFieldLabel("H.2.b School approval signature (Head of School or appropriate School committee chair) and date");
        H2b.setAllowBlank(false);
        H2b.setPreventScrollbars(false);
        H2b.setHeight(50);
        H2b.setName("H2b");

        H3a.setFieldLabel("H.3.a Telephone contact numbers");
        H3a.setAllowBlank(false);
        H3a.setPreventScrollbars(false);
        H3a.setHeight(50);
        H3a.setName("H3a");

        H3b.setFieldLabel("H.3.b Email addresses");
        H3b.setAllowBlank(false);
        H3b.setPreventScrollbars(false);
        H3b.setHeight(50);
        H3b.setName("H3b");

        mainForm.add(H1, formData);
        mainForm.add(H2a, formData);
        mainForm.add(H2b, formData);
        mainForm.add(H3a, formData);
        mainForm.add(H3b, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));

        // private String qH1,
        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {

                qH1 = H1.getValue();
                qH2a = H2a.getValue();
                qH2b = H2b.getValue();
                qH3a = H3a.getValue();
                qH3b = H3b.getValue();

                if (qH1 == null) {
                    MessageBox.info("Missing answer", "Please enter an answer for question H.1.", null);
                    return;

                }

                if (qH2a == null) {
                    MessageBox.info("Missing answer", "Please enter an answer for question H.1.", null);
                    return;

                }

                if (qH2b == null) {
                    MessageBox.info("Missing answer", "Please enter an answer for question H.1.", null);
                    return;

                }

                if (qH3a == null) {
                    MessageBox.info("Missing answer", "Please enter an answer for question H.1.", null);
                    return;

                }

                if (qH3b == null) {
                    MessageBox.info("Missing answer", "Please enter an answer for question H.1.", null);
                    return;

                }

                storeDocumentInfo();

                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=saveFormData&formname=contactdetails&formdata="
                        + contactDetailsData + "&docid=" + Constants.docid;

                createDocument(url);

                newContactDetailsDialog.hide();
                MessageBox.info("Message", "You have successfully completed the application, Thank you", null);

            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                review.setOldReview(ContactDetails.this);
                review.show();
                newContactDetailsDialog.hide();
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

        newContactDetailsDialog.setBodyBorder(false);
        newContactDetailsDialog.setHeading("Section H: Contact and Details");
        newContactDetailsDialog.setWidth(700);
        //newContactDetailsDialog.setHeight(450);
        newContactDetailsDialog.setHideOnButtonClick(true);
        newContactDetailsDialog.setButtons(Dialog.CLOSE);
        newContactDetailsDialog.setButtonAlign(HorizontalAlignment.LEFT);

        newContactDetailsDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                storeDocumentInfo();
            }
        });

        setDocumentInfo();
        newContactDetailsDialog.add(mainForm);
    }

    public void storeDocumentInfo() {
        WicidXML wicidxml = new WicidXML("contactdetails");
        wicidxml.addElement("qH1", qH1);
        wicidxml.addElement("qH2a", qH2a);
        wicidxml.addElement("qH2b", qH2b);
        wicidxml.addElement("qH3a", qH3a);
        wicidxml.addElement("qH3b", qH3b);
        contactDetailsData = wicidxml.getXml();
    }

    public void setDocumentInfo() {
    }

    public void show() {
        newContactDetailsDialog.show();
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
                + "?module=wicid&action=getFormData&formname=contactDetails&docid=" + Constants.docid;
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get contactDetails data", null);
                }

                public void onResponseReceived(Request request, Response response) {

                    String data = response.getText();

                    String qH1 = Util.getTagText(data, "qH1");
                    H1.setValue(qH1);

                    String qH2a = Util.getTagText(data, "qH2a");
                    H2a.setValue(qH2a);

                    String qH2b = Util.getTagText(data, "qH2b");
                    H2b.setValue(qH2b);

                    String qH3a = Util.getTagText(data, "qH3a");
                    H3a.setValue(qH3a);

                    String qH3b = Util.getTagText(data, "qH3b");
                    H3b.setValue(qH3b);
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get contactDetails data", null);
        }
    }
}
