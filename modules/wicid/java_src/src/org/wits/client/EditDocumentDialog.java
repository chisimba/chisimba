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
import com.extjs.gxt.ui.client.widget.Label;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.Window;

import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.form.ComboBox;
import com.extjs.gxt.ui.client.widget.form.ComboBox.TriggerAction;
import com.extjs.gxt.ui.client.widget.form.DateField;
import com.extjs.gxt.ui.client.widget.form.FileUploadField;

import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.FormPanel.Encoding;
import com.extjs.gxt.ui.client.widget.form.FormPanel.Method;
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
public class EditDocumentDialog {

    private Dialog newDocumentDialog = new Dialog();
    private ModelData selectedFolder;
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private DateTimeFormat fmt = DateTimeFormat.getFormat("y/M/d");
    private final TextField<String> titleField = new TextField<String>();
    private final TextField<String> deptField = new TextField<String>();
    private final TextField<String> telField = new TextField<String>();
    private final TextField<String> numberField = new TextField<String>();
    private Button saveButton = new Button("Save");
    private Button browseTopicsButton = new Button("Browse Topics");
    private TopicListingFrame topicListingFrame;
    private TextArea topicField = new TextArea();
    private Dialog topicListingDialog = new Dialog();
    private Document document;
    private FormPanel uploadpanel = new FormPanel();
    private Button uploadButton = new Button("Add attachment");
    private ComboBox<Group> groupField = new ComboBox<Group>();
    private Label namesField = new Label();
    private String mode;
    private Main main;

    public EditDocumentDialog(Document document, String mode, Main main) {
        this.document = document;
        this.mode = mode;
        this.main = main;
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(480);

        final DateField dateField = new DateField();
        dateField.setFieldLabel("Entry date");
        /*String date[] = document.getDate().split("/");
        try {
        Calendar cal = new GregorianCalendar(Integer.parseInt(date[0]), Integer.parseInt(date[1])-1, Integer.parseInt(date[2]));
        Date xdate = cal.getTime();
        dateField.setValue(xdate);
        } catch (Exception ex) {
        ex.printStackTrace();
        }*/
        dateField.getPropertyEditor().setFormat(fmt);
        dateField.setName("datefield");
        //mainForm.add(dateField, formData);
        dateField.setEditable(false);
        dateField.setAllowBlank(false);

        ListStore<Group> groupStore = new ListStore<Group>();
        List<Group> groups = new ArrayList<Group>();
        groups.add(new Group("Public"));
        groups.add(new Group("Council"));
        groups.add(new Group("Administration"));
        groupStore.add(groups);

        namesField.setText(document.getOwnerName());
        mainForm.add(namesField, formData);
        groupField.setFieldLabel("Group");
        groupField.setName("groupField");
        groupField.setDisplayField("name");
        groupField.setEmptyText("Select group ..");
        groupField.setValue(new Group(document.getGroup()));
        groupField.setTriggerAction(TriggerAction.ALL);
        groupField.setStore(groupStore);
        groupField.setAllowBlank(false);
        groupField.setEditable(false);


        numberField.setFieldLabel("Reference Number");
        numberField.setAllowBlank(false);
        numberField.setEnabled(false);
        numberField.setValue(document.getRefNo());
        numberField.setName("numberfield");
        mainForm.add(numberField, formData);


        deptField.setFieldLabel("Originating department");
        deptField.setAllowBlank(false);
        deptField.setValue(document.getDepartment());
        deptField.setName("deptfield");
        if (mode.equals("all")) {
            mainForm.add(deptField, formData);
        }

        telField.setFieldLabel("Tel. Number");
        telField.setValue("edit mode");
        telField.setValue(document.getTelephone());
        telField.setAllowBlank(false);
        telField.setName("telfield");
        if (mode.equals("all")) {
            mainForm.add(telField, formData);
        }

        titleField.setFieldLabel("Document title");
        titleField.setAllowBlank(false);
        titleField.setValue(document.getTitle());
        titleField.setName("titlefield");
        if (mode.equals("all")) {
            mainForm.add(titleField, formData);
        }
        if (mode.equals("all")) {
            mainForm.add(groupField, formData);
        }
        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));

        BorderLayoutData eastData = new BorderLayoutData(LayoutRegion.EAST, 150);
        eastData.setSplit(true);
        eastData.setMargins(new Margins(0, 0, 0, 5));
        topicField.setName("topic");
        topicField.setValue(document.getTopic());
        topicField.setFieldLabel("Topic");

        FormPanel panel = new FormPanel();
        panel.setSize(400, 70);
        panel.setHeading("Topic");
        panel.setLayout(new BorderLayout());
        panel.add(topicField, centerData);
        panel.add(browseTopicsButton, eastData);
        if (mode.equals("all")) {
            mainForm.add(panel, formData);
        }

        browseTopicsButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if (topicListingFrame == null) {
                    topicListingFrame = new TopicListingFrame(EditDocumentDialog.this);
                    topicListingDialog.setBodyBorder(false);
                    topicListingDialog.setHeading("Topic Listing");
                    topicListingDialog.setWidth(500);
                    topicListingDialog.setHeight(350);
                    topicListingDialog.setHideOnButtonClick(true);
                    topicListingDialog.setButtons(Dialog.CLOSE);
                    topicListingDialog.setButtonAlign(HorizontalAlignment.LEFT);

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


        uploadpanel.setHeading("File Upload");
        uploadpanel.setFrame(true);
        uploadpanel.setAction(GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                + "?module=wicid&action=doupload&docname=" + document.getTitle() + "&path="
                + document.getTopic() + "&docid=" + document.getId());
        uploadpanel.setEncoding(Encoding.MULTIPART);
        uploadpanel.setMethod(Method.POST);
        uploadpanel.setButtonAlign(HorizontalAlignment.CENTER);
        uploadpanel.setWidth(350);

        FileUploadField fileUploadField = new FileUploadField();
        fileUploadField.setName("filenamefield");
        fileUploadField.setFieldLabel("Upload file");
        //uploadpanel.add(fileUploadField);
        uploadpanel.add(uploadButton);
        mainForm.add(uploadpanel, formData);
        uploadButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                Window w = new Window();
                w.setHeading("Upload file");
                w.setModal(true);
                w.setSize(800, 300);
                w.setMaximizable(true);
                w.setToolTip("Upload file");
                w.setUrl(GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=uploadfile&docname=" + document.getTitle()
                        + "&docid=" + document.getId() + "&topic=" + document.getTopic());
                w.show();

            }
        });
        // mainForm.add(uploadButton, formData);

        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if (uploadpanel.isValid()) {

                    uploadpanel.submit();

                }
                Date date = new Date();
                try {
                    if (dateField.getDatePicker() != null) {
                        date = dateField.getDatePicker().getValue();
                    }
                } catch (Exception ex) {
                }

                String dept = deptField.getValue();// deptField.getValue().getId();
                if (dept == null) {
                    MessageBox.info("Missing department", "Provide originating department", null);
                    return;
                }
                if (dept.trim().equals("")) {
                    MessageBox.info("Missing department", "Provide department", null);
                    return;
                }
                String title = titleField.getValue();
                if (title == null) {
                    MessageBox.info("Missing title", "Provide title", null);
                    return;
                }
                if (title.trim().equals("")) {
                    MessageBox.info("Missing title", "Provide title", null);
                    return;
                }
                String topic = topicField.getValue();

                if (topic == null) {
                    MessageBox.info("Missing topic", "Provide topic", null);
                    return;
                }
                if (topic.trim().equals("")) {
                    MessageBox.info("Missing topic", "Provide topic", null);
                    return;
                }

                String group = groupField.getValue().getName();
                if (group == null) {
                    MessageBox.info("Missing group", "Select group", null);
                    return;
                }
                if (group.trim().equals("")) {
                    MessageBox.info("Missing group", "Select group", null);
                    return;
                }
                String url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?"
                        + "module=wicid&action=updatedocument&dept=" + dept + "&topic=" + topic
                        + "&title=" + title + "&group=" + group;

                updateDocument(url);


            }
        });
        if (mode.equals("all")) {

            mainForm.addButton(saveButton);
        }

        mainForm.setButtonAlign(HorizontalAlignment.LEFT);
        //FormButtonBinding binding = new FormButtonBinding(mainForm);
        //binding.addButton(saveButton);
        newDocumentDialog.setBodyBorder(false);
        newDocumentDialog.setHeading("Document Details");
        newDocumentDialog.setWidth(500);
        newDocumentDialog.setHeight(450);
        newDocumentDialog.setHideOnButtonClick(true);

        newDocumentDialog.setButtons(Dialog.CLOSE);
        newDocumentDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if (main != null) {
                      main.refreshFileList();
                }
            }
        });
        newDocumentDialog.setButtonAlign(HorizontalAlignment.LEFT);

        newDocumentDialog.add(mainForm);
        setDepartment();
    }

    public void show() {
        newDocumentDialog.show();
    }

    private void setDepartment() {
        String url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=getdepartment";

        RequestBuilder builder =
                new RequestBuilder(RequestBuilder.GET, url);

        try {
            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new document", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        deptField.setValue(response.getText());
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot create document", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create new document", null);
        }
    }

    private void updateDocument(String url) {

        RequestBuilder builder =
                new RequestBuilder(RequestBuilder.GET, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new document", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        //main.getDocumentListPanel().
                        // main.selectDocumentsTab();
                        if (main != null) {
                            main.refreshFileList();
                        }
                        newDocumentDialog.setVisible(false);
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot create document", null);
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
}
