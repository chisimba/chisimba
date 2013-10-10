/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.store.ListStore;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.form.ComboBox;
import com.extjs.gxt.ui.client.widget.form.ComboBox.TriggerAction;
import com.extjs.gxt.ui.client.widget.form.DateField;

import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.Radio;
import com.extjs.gxt.ui.client.widget.form.RadioGroup;
import com.extjs.gxt.ui.client.widget.form.TextArea;
import com.extjs.gxt.ui.client.widget.form.TextField;
import com.extjs.gxt.ui.client.widget.layout.BorderLayout;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import com.google.gwt.i18n.client.DateTimeFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 *
 * @author davidwaf
 */
public class NewDocumentDialog {

    private Dialog newDocumentDialog = new Dialog();
    private Dialog topicListingDialog = new Dialog();
    private ModelData selectedFolder;
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private DateTimeFormat fmt = DateTimeFormat.getFormat("y/M/d");
    private final TextField<String> titleField = new TextField<String>();
    private final TextField<String> deptField = new TextField<String>();
    private final TextField<String> telField = new TextField<String>();
    private Button saveButton = new Button("Save");
    private Button browseTopicsButton = new Button("Browse Topics");
    private TopicListingFrame topicListingFrame;
    private TextArea topicField = new TextArea();
    private Date date = new Date();
    private String number = "A";
    private String dept = "";
    private String title = "";
    private String telephone = "";
    private String topic = "";
    private String group="";
    private ComboBox<Group> groupField = new ComboBox<Group>();
    public NewDocumentDialog() {
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(480);

        final DateField dateField = new DateField();
        dateField.setFieldLabel("Entry date");
        dateField.setValue(new Date());
        dateField.getPropertyEditor().setFormat(fmt);
        dateField.setName("datefield");
        mainForm.add(dateField, formData);
        dateField.setEditable(false);
        dateField.setAllowBlank(false);

        ListStore<DocumentType> docTypeStore = new ListStore<DocumentType>();
        List<DocumentType> types = new ArrayList<DocumentType>();
        types.add(new DocumentType("S"));
        types.add(new DocumentType("C"));
        types.add(new DocumentType("A"));
        docTypeStore.add(types);

        final ComboBox<DocumentType> numberField = new ComboBox<DocumentType>();
        numberField.setFieldLabel("Number");
        numberField.setName("numberfield");
        numberField.setDisplayField("type");
        numberField.setEmptyText("Select number ..");
        numberField.setTriggerAction(TriggerAction.ALL);
        numberField.setStore(docTypeStore);
        numberField.setAllowBlank(false);
        numberField.setEditable(false);
        mainForm.add(numberField, formData);

        deptField.setFieldLabel("Originating department");
        deptField.setAllowBlank(false);
        deptField.setName("department");
        mainForm.add(deptField, formData);

        telField.setFieldLabel("Tel. Number");
        telField.setAllowBlank(false);
        telField.setName("telephone");
        mainForm.add(telField, formData);

        titleField.setFieldLabel("Course Title");
        titleField.setAllowBlank(false);
        titleField.setName("titlefield");
        mainForm.add(titleField, formData);

                ListStore<Group> groupStore = new ListStore<Group>();
        List<Group> groups = new ArrayList<Group>();
        groups.add(new Group("Public"));
        groups.add(new Group("Council"));
        groups.add(new Group("Administration"));
        groupStore.add(groups);

        groupField.setFieldLabel("Group");
        groupField.setName("groupField");
        groupField.setDisplayField("name");
        groupField.setEmptyText("Select group ..");
        groupField.setTriggerAction(TriggerAction.ALL);
        groupField.setStore(groupStore);
        groupField.setAllowBlank(false);
        groupField.setEditable(false);

        mainForm.add(groupField, formData);
        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));

        BorderLayoutData eastData = new BorderLayoutData(LayoutRegion.EAST, 150);
        eastData.setSplit(true);
        eastData.setMargins(new Margins(0, 0, 0, 5));
        topicField.setName("topic");
        topicField.setFieldLabel("Topic");

        FormPanel panel = new FormPanel();
        panel.setSize(400, 70);
        panel.setHeading("Topic");
        panel.setLayout(new BorderLayout());
        panel.add(topicField, centerData);
        panel.add(browseTopicsButton, eastData);

        mainForm.add(panel, formData);

        browseTopicsButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if (topicListingFrame == null) {
                    topicListingFrame = new TopicListingFrame(NewDocumentDialog.this);
                    topicListingDialog.setBodyBorder(false);
                    topicListingDialog.setHeading("Topic Listing");
                    topicListingDialog.setWidth(700);
                    topicListingDialog.setHeight(350);
                    topicListingDialog.setHideOnButtonClick(true);
                    topicListingDialog.setButtons(Dialog.OK);
                    topicListingDialog.setButtonAlign(HorizontalAlignment.CENTER);

                    topicListingDialog.add(topicListingFrame);
                }
                topicListingDialog.show();
            }
        });
        Radio publicOpt = new Radio();
        publicOpt.setBoxLabel("Public");
        publicOpt.setValue(true);

        Radio privateOpt = new Radio();
        privateOpt.setBoxLabel("Private");

        Radio draftOpt = new Radio();
        draftOpt.setBoxLabel("Draft");

        RadioGroup radioGroup = new RadioGroup();
        radioGroup.setFieldLabel("Access");
        radioGroup.add(publicOpt);
        radioGroup.add(privateOpt);
        radioGroup.add(draftOpt);

        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {

                try {
                    if (dateField.getDatePicker() != null) {
                        date = dateField.getDatePicker().getValue();
                    }
                } catch (Exception ex) {
                }

                try {
                    number = numberField.getValue().getType();
                } catch (Exception ex) {
                    MessageBox.info("Missing Number", "Select document number please", null);
                    return;
                }
                dept = deptField.getValue();// deptField.getValue().getId();
                if (dept == null) {
                    MessageBox.info("Missing department", "Provide originating department", null);
                    return;
                }
                if (dept.trim().equals("")) {
                    MessageBox.info("Missing department", "Provide department", null);
                    return;
                }
                title = titleField.getValue();
                if (title == null) {
                    MessageBox.info("Missing title", "Provide title", null);
                    return;
                }
                if (title.trim().equals("")) {
                    MessageBox.info("Missing title", "Provide title", null);
                    return;
                }

                group = groupField.getValue().getName();
                if (group == null) {
                    MessageBox.info("Missing group", "Select group", null);
                    return;
                }
                telephone = telField.getValue();
                if (telephone == null) {
                    MessageBox.info("Missing telephone", "Provide telephone", null);
                    return;
                }
                if (telephone.trim().equals("")) {
                    MessageBox.info("Missing telephone", "Provide telephone", null);
                    return;
                }
                if (selectedFolder == null) {
                    MessageBox.info("Missing Topic", "Please select topic", null);
                    return;
                }
                topic = (String) selectedFolder.get("id");
                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=registerdocument&date=" + fmt.format(date)
                        + "&number=" + number + "&department=" + dept + "&telephone=" + telephone
                        + "&topic=" + topic + "&title=" + title+"&group="+group;
                createNewDocument(url);


            }
        });
        mainForm.addButton(saveButton);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);
        //FormButtonBinding binding = new FormButtonBinding(mainForm);
        //binding.addButton(saveButton);

        newDocumentDialog.setBodyBorder(false);
        newDocumentDialog.setHeading("New Document");
        newDocumentDialog.setWidth(500);
        newDocumentDialog.setHeight(450);
        newDocumentDialog.setHideOnButtonClick(true);
        newDocumentDialog.setButtons(Dialog.CLOSE);
        newDocumentDialog.setButtonAlign(HorizontalAlignment.LEFT);

        newDocumentDialog.add(mainForm);
        setDepartment();
    }

    public void show() {
        newDocumentDialog.show();
    }

    private void setDepartment() {
        /*      UrlBuilder urlBuilder = new UrlBuilder();
        urlBuilder.setParameter("module", "wicid");
        urlBuilder.setParameter("action", "getdepartment");
        urlBuilder.setHost("localhost");
        urlBuilder.setProtocol("http");
        urlBuilder.setPort(8888);
        urlBuilder.setPath(Constants.MAIN_URL_PATTERN);
         */
        String url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=getdepartment";
        RequestBuilder builder =
                new RequestBuilder(RequestBuilder.GET, url);

        try {
            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get department", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        deptField.setValue(response.getText());
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot get department", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create new document", null);
        }
    }

    public void setSelectedFolder(ModelData selectedFolder) {
        this.selectedFolder = selectedFolder;
        topicField.setValue((String) this.selectedFolder.get("id"));
        topicField.setToolTip((String) this.selectedFolder.get("id"));
    }

    private void createNewDocument(String url) {
        final MessageBox wait = MessageBox.wait("Wait",
                "Saving your data, please wait...", "Saving...");
        RequestBuilder builder =
                new RequestBuilder(RequestBuilder.GET, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new document", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        String responseTxt[] = response.getText().split(",");
                       
                        Document doc = new Document();
                        doc.setDate(fmt.format(date));
                        doc.setRefNo(responseTxt[0]);
                        doc.setId(responseTxt[1]);
                        doc.setDepartment(dept);
                        doc.setTelephone(telephone);
                        doc.setGroup(group);
                        doc.setTitle(title);
                        doc.setTopic(topic);
                        EditDocumentDialog editDocumentDialog = new EditDocumentDialog(doc,"all",null);
                        editDocumentDialog.show();
                        newDocumentDialog.setVisible(false);
                        wait.close();
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot create document", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create new document", null);
        }
    }
}
