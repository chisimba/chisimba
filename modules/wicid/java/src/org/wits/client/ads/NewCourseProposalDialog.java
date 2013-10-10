package org.wits.client.ads;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;
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
import java.util.Date;
import org.wits.client.Constants;
import org.wits.client.TopicListingFrame;

/**
 *
 * @author davidwaf
 */
public class NewCourseProposalDialog {

    private Dialog newCourseProposalDialog = new Dialog();
    private Dialog facultyListingDialog = new Dialog();
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private DateTimeFormat fmt = DateTimeFormat.getFormat("y/M/d");
    private final TextField<String> titleField = new TextField<String>();
    private final TextField<String> deptField = new TextField<String>();
    private final TextField<String> telField = new TextField<String>();
    private final TextField<String> numberField = new TextField<String>();
    private final DateField dateField = new DateField();
    private Button saveButton = new Button("Save");
    private Button browseFacultiesButton = new Button("Browse Faculties");
    private TextArea facultyField = new TextArea();
    private String newCourseProposalDialogData, dept, title, telephone, faculty;
    private TopicListingFrame facultyListingFrame;
    private ModelData selectedFaculty;
    private OverView oldOverView;
    private boolean status = false;
    private Date date = new Date();

    public NewCourseProposalDialog() {

        createUI();
    }

    public NewCourseProposalDialog(OverView oldOverView) {
        this.oldOverView = oldOverView;
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(480);


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
        mainForm.add(dateField, formData);
        dateField.setValue(new Date());
        dateField.setEditable(false);
        dateField.setAllowBlank(false);


        numberField.setFieldLabel("Reference Number");
        numberField.setAllowBlank(false);

        numberField.setName("numberfield");
        //mainForm.add(numberField, formData);
        facultyField.setEnabled(false);

        deptField.setFieldLabel("Originating department");
        deptField.setAllowBlank(false);

        deptField.setName("deptfield");
        mainForm.add(deptField, formData);

        telField.setFieldLabel("Tel. Number");
        telField.setAllowBlank(true);
        telField.setName("telfield");
        //    telField.setAllowDecimals(false);
        //  telField.setAllowNegative(false);
        mainForm.add(telField, formData);

        titleField.setFieldLabel("Course title");
        titleField.setAllowBlank(false);
        titleField.setName("titlefield");
        mainForm.add(titleField, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));

        BorderLayoutData eastData = new BorderLayoutData(LayoutRegion.EAST, 150);
        eastData.setSplit(true);
        eastData.setMargins(new Margins(0, 0, 0, 5));
        facultyField.setName("faculty");
        facultyField.setFieldLabel("Faculty");
        facultyField.setReadOnly(true);

        FormPanel panel = new FormPanel();
        panel.setSize(400, 70);
        panel.setHeading("Faculty");
        panel.setLayout(new BorderLayout());
        panel.add(facultyField, centerData);

        browseFacultiesButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if (facultyListingFrame == null) {
                    facultyListingFrame = new TopicListingFrame(NewCourseProposalDialog.this);
                    facultyListingDialog.setBodyBorder(false);
                    facultyListingDialog.setHeading("Faculty Listing");
                    facultyListingDialog.setWidth(700);
                    facultyListingDialog.setHeight(350);
                    facultyListingDialog.setHideOnButtonClick(true);
                    facultyListingDialog.setButtons(Dialog.OK);
                    facultyListingDialog.setButtonAlign(HorizontalAlignment.CENTER);

                    facultyListingDialog.add(facultyListingFrame);
                }
                facultyListingDialog.show();
            }
        });

        panel.add(browseFacultiesButton, eastData);
        mainForm.add(panel, formData);

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

                dept = deptField.getValue();// deptField.getValue().getId();
                title = titleField.getValue();


                try {
                    telephone = telField.getValue().toString();
                } catch (NullPointerException npe) {
                    MessageBox.info("Missing telephone number", "Provide a telephone number.", null);
                }


                /*  try {
                if (dateField.getDatePicker() != null) {
                date = dateField.getDatePicker().getValue();
                }
                } catch (Exception ex) {
                }*/
                date = dateField.getValue();

                if (dept == null) {
                    MessageBox.info("Missing department", "Provide originating department", null);
                    return;
                }
                if (dept.trim().equals("")) {
                    MessageBox.info("Missing department", "Provide department", null);
                    return;
                }

                if (title == null) {
                    MessageBox.info("Missing title", "Provide title", null);
                    return;
                }
                if (title.trim().equals("")) {
                    MessageBox.info("Missing title", "Provide title", null);
                    return;
                }


                if (telephone == null) {
                    MessageBox.info("Missing telephone", "Provide telephone", null);
                    return;
                }

                if (selectedFaculty == null) {
                    MessageBox.info("Missing faculty", "Please select faculty", null);
                    return;
                }
                String number = "S";
                String group = "Draft";
                String version = "1";

                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=registerdocument&date=" + fmt.format(date)
                        + "&number=" + number + "&department=" + dept + "&telephone=" + telephone
                        + "&topic=" + facultyField.getValue() + "&title=" + title + "&group=" + group + "&version=" + version;

                createDocument(url);
                //select documents panel
                Constants.main.selectDocumentsTab();
                // refresh the documentlist panel
                String defaultParams = "?module=wicid&action=getdocuments&mode=" + Constants.main.getMode();
                Constants.main.getDocumentListPanel().refreshDocumentList(defaultParams);
            }
        });
        mainForm.addButton(saveButton);

        mainForm.setButtonAlign(HorizontalAlignment.LEFT);
        //FormButtonBinding binding = new FormButtonBinding(mainForm);
        //binding.addButton(saveButton);
        newCourseProposalDialog.setBodyBorder(false);
        newCourseProposalDialog.setHeading("New Course Proposal");
        newCourseProposalDialog.setWidth(470);
        newCourseProposalDialog.setHeight(340);
        newCourseProposalDialog.setHideOnButtonClick(true);
        newCourseProposalDialog.setButtons(Dialog.CLOSE);
        newCourseProposalDialog.setButtonAlign(HorizontalAlignment.LEFT);
/*
        newCourseProposalDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {

                dept = deptField.getValue();// deptField.getValue().getId();
                title = titleField.getValue();
                faculty = facultyField.getValue();
                telephone = telField.getValue();
                date = dateField.getValue();

                if (telephone != null && title != null && faculty != null) {

                    String number = "S";
                    String group = "Draft";
                    String version = "1";

                    String url =
                            GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                            + "?module=wicid&action=registerdocument&date=" + fmt.format(date)
                            + "&number=" + number + "&department=" + dept + "&telephone=" + telephone
                            + "&topic=" + faculty + "&title=" + title + "&group=" + group + "&version=" + version;

                    createDocument(url);
                    //select documents panel
                    Constants.main.selectDocumentsTab();
                    // refresh the documentlist panel
                    String defaultParams = "?module=wicid&action=getdocuments&mode=" + Constants.main.getMode();
                    Constants.main.getDocumentListPanel().refreshDocumentList(defaultParams);
                }
            }
        });
*/
        newCourseProposalDialog.add(mainForm);
        setDepartment();


    }

    public void show() {
        newCourseProposalDialog.show();
    }

    public void setOldOverView(OverView oldOverView) {
        this.oldOverView = oldOverView;
    }

    public TextField<String> getTitleField() {
        return titleField;
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

    private void createDocument(String url) {

        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new document", null);
                }

                public void onResponseReceived(Request request, Response response) {

                    newCourseProposalDialog.setVisible(false);
                    MessageBox.info("Save", "Your course proposal information is saved.\nYou can continue filling the rest of the forms by accessing it from the 'Documents' tab to make any changes", null);
                    telField.setValue(null);
                    titleField.setValue(null);
                    facultyField.setValue(null);
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create new document", null);
        }

    }

    public void setSelectedFaculty(ModelData selectedFaculty) {
        this.selectedFaculty = selectedFaculty;
        facultyField.setValue((String) this.selectedFaculty.get("id"));
        facultyField.setToolTip((String) this.selectedFaculty.get("id"));
    }
}
