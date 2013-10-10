/*
 * @author: Nguni Phakela
 *
 */
package org.wits.client.search;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
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
import com.extjs.gxt.ui.client.widget.form.TextField;
import com.extjs.gxt.ui.client.widget.form.ComboBox;
import com.extjs.gxt.ui.client.widget.form.ComboBox.TriggerAction;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import com.google.gwt.i18n.client.DateTimeFormat;

import org.wits.client.Constants;
import org.wits.client.DocumentType;

import java.util.ArrayList;
import java.util.List;
import java.util.Date;

import com.extjs.gxt.ui.client.data.BaseListLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.JsonLoadResultReader;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.store.ListStore;
import com.extjs.gxt.ui.client.Style.SortDir;
import com.extjs.gxt.ui.client.widget.ListView;
import com.extjs.gxt.ui.client.widget.menu.MenuItem;
import com.extjs.gxt.ui.client.widget.layout.RowData;
import com.extjs.gxt.ui.client.widget.ContentPanel;
import com.extjs.gxt.ui.client.widget.Text;
import com.extjs.gxt.ui.client.widget.layout.RowLayout;
import com.extjs.gxt.ui.client.Style.Orientation;

/**
 *
 * @author davidwaf
 */
public class AdvancedSearchDialog {

    private Dialog newDocumentDialog = new Dialog();
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private DateTimeFormat fmt = DateTimeFormat.getFormat("y/M/d");
    private final TextField<String> firstnameField = new TextField<String>();
    private final TextField<String> lastnameField = new TextField<String>();
    private final TextField<String> docnameField = new TextField<String>();
    private final TextField<String> refno = new TextField<String>();
    private final TextField<String> topic = new TextField<String>();
    private final TextField<String> department = new TextField<String>();
    private final DateField dateField = new DateField();
    private final DateField dateField2 = new DateField();
    final ComboBox<DocumentType> numberField = new ComboBox<DocumentType>();
    private RadioGroup radioGroup = new RadioGroup();
    Radio activeYes = new Radio();
    Radio activeNo = new Radio();
    private Button searchButton = new Button("Search");
    
    private Date date = new Date();
    private Date date2 = new Date();
    private String myFirstName = "";
    private String myLastName = "";
    private String myDocName = "";
    private String myDocType = "";
    private String myRefNo = "";
    private String myTopic = "";
    private String myDept = "";
    private String myActive = "";
    private String url = "";
    private ListView<ModelData> view = Constants.main.getView();
    private ModelData selectedFolder = Constants.main.getSelectedFolder();
    private ContentPanel panel;
    
    public AdvancedSearchDialog() {
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(600);

        /*panel = new ContentPanel();
         
        panel.setHeaderVisible(false);
        panel.setLayout(new RowLayout(Orientation.HORIZONTAL));
        panel.setSize(450, 45);
        panel.setFrame(true);
        panel.setCollapsible(false);*/
        

        dateField.setFieldLabel("Date From");
        dateField.setValue(new Date());
        dateField.getPropertyEditor().setFormat(fmt);
        dateField.setName("datefield");
        dateField.setEditable(false);
        dateField.setAllowBlank(true);
        dateField.setWidth(200);
        
        dateField2.setFieldLabel("Date To");
        dateField2.setValue(new Date());
        dateField2.getPropertyEditor().setFormat(fmt);
        dateField2.setName("datefield");
        dateField2.setEditable(false);
        dateField2.setAllowBlank(true);
        dateField2.setWidth(200);

        /*panel.add(dateField, new RowData(-1, 1, new Margins(4)));
        panel.add(dateField2, new RowData(1, 1, new Margins(4, 0, 4, 0)));
        mainForm.add(panel, formData);*/

        mainForm.add(dateField, formData);
        mainForm.add(dateField2, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));

        showFirstNameField();
        showLastNameField();
        showDocNameField();
        showDocTypeField();
        showRefnoField();
        showTopicField();
        showDepartmentField();
        showActiveField();

        /*panel.add(firstnameField, formData);
        //panel.add(lastnameField, new RowData(1, 1, new Margins(4, 0, 4, 0)));
        mainForm.add(panel, formData);

        /*panel.add(docnameField, new RowData(-1, 1, new Margins(4)));
        panel.add(refno, new RowData(1, 1, new Margins(4, 0, 4, 0)));
        mainForm.add(panel, formData);

        panel.add(topic, new RowData(-1, 1, new Margins(4)));
        panel.add(department, new RowData(1, 1, new Margins(4, 0, 4, 0)));
        mainForm.add(panel, formData);

        panel.add(groupid, new RowData(-1, 1, new Margins(4)));
        panel.add(ext, new RowData(1, 1, new Margins(4, 0, 4, 0)));
        mainForm.add(panel, formData);

        panel.add(firstnameField, new RowData(-1, 1, new Margins(4)));
        panel.add(lastnameField, new RowData(1, 1, new Margins(4, 0, 4, 0)));
        mainForm.add(panel, formData);

        panel.add(mode, new RowData(-1, 1, new Margins(4)));
        panel.add(radioGroup, new RowData(1, 1, new Margins(4, 0, 4, 0)));
        mainForm.add(panel, formData);*/

        searchButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                newDocumentDialog.setVisible(false);
                /*date = dateField.getValue();
                date2 = dateField2.getValue();*/
                getSearchValues();
                createSearchUrl();
                searchDocuments();
            }
        });
        mainForm.addButton(searchButton);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);

        newDocumentDialog.setBodyBorder(false);
        newDocumentDialog.setHeading("Advanced Search");
        newDocumentDialog.setSize(600, 450);
        newDocumentDialog.setHideOnButtonClick(true);
        newDocumentDialog.setButtons(Dialog.CLOSE);
        newDocumentDialog.setButtonAlign(HorizontalAlignment.LEFT);

        newDocumentDialog.add(mainForm);
    }

    public void show() {
        newDocumentDialog.show();
    }

    private void showFirstNameField() {
        firstnameField.setFieldLabel("First Name");
        firstnameField.setAllowBlank(false);
        firstnameField.setName("firstname");
        //mainForm.add(firstnameField, formData);
    }

    private void showLastNameField() {
        lastnameField.setFieldLabel("Last Name");
        lastnameField.setAllowBlank(false);
        lastnameField.setName("lastname");
        mainForm.add(lastnameField, formData);
    }

    private void showDocNameField() {
        docnameField.setFieldLabel("Document Name");
        docnameField.setAllowBlank(false);
        docnameField.setName("docname");
        mainForm.add(docnameField, formData);
    }
    
    private void showDocTypeField() {
        ListStore<DocumentType> docTypeStore = new ListStore<DocumentType>();
        List<DocumentType> types = new ArrayList<DocumentType>();

        types.add(new DocumentType("S"));
        types.add(new DocumentType("C"));
        types.add(new DocumentType("A"));
        docTypeStore.add(types);
        
        numberField.setFieldLabel("Number");
        numberField.setName("numberfield");
        numberField.setDisplayField("type");
        numberField.setEmptyText("Select number ..");
        numberField.setTriggerAction(TriggerAction.ALL);
        numberField.setStore(docTypeStore);
        numberField.setAllowBlank(false);
        numberField.setEditable(false);
        mainForm.add(numberField, formData);
    }
    
    private void showRefnoField() {
        refno.setFieldLabel("Reference No");
        refno.setAllowBlank(false);
        refno.setName("refno");
        mainForm.add(refno, formData);
    }

    private void showTopicField() {
        topic.setFieldLabel("Topic");
        topic.setAllowBlank(false);
        topic.setName("topic");
        mainForm.add(topic, formData);
    }

    private void showDepartmentField() {
        department.setFieldLabel("Department");
        department.setAllowBlank(false);
        department.setName("department");
        mainForm.add(department, formData);
    }

    private void showActiveField() {
        activeYes.setBoxLabel("Yes");
        activeYes.setValue(true);
        activeNo.setBoxLabel("No");

        radioGroup.setFieldLabel("Active");
        radioGroup.add(activeYes);
        radioGroup.add(activeNo);

        mainForm.add(radioGroup, formData);
    }

    private void getSearchValues() {
        date = dateField.getValue();
        date2 = dateField2.getValue();
        myFirstName = firstnameField.getValue();
        myLastName = lastnameField.getValue();
        myDocName = docnameField.getValue();
        if(numberField.getValue() != null) {
            myDocType = numberField.getValue().getType();
        }
        myRefNo = refno.getValue();
        myTopic = topic.getValue();
        myDept = department.getValue();
        myActive = activeYes.getValue() ? "Y" : "N";
    }

    private void createSearchUrl() {
        url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN;

        url += "?module=wicid&action=advancedsearch";
        if (date != null) {
            url += "&date=" + fmt.format(date);
        }
        if (date2 != null) {
            url += "&date2=" + fmt.format(date2);
        }
        if (myFirstName != null) {
            url += "&fname=" + myFirstName;
        }
        if (myLastName != null) {
            url += "&lname=" + myLastName;
        }
        if (myDocName != null) {
            url += "&docname=" + myDocName;
        }
        if (myDocType != null) {
            url += "&doctype=" +myDocType;
        }
        if (myRefNo != null) {
            url += "&refno=" + myRefNo;
        }
        if (myTopic != null) {
            url += "&topic=" + myTopic;
        }
        if (myDept != null) {
            url += "&dept=" + myDept;
        }
        if (myActive != null) {
            url += "&active=" + myActive;
        }
    }

    private void searchDocuments() {
        final ModelType type2 = new ModelType();
        type2.setRoot("files");
        type2.addField("id", "id");
        type2.addField("docid", "docid");
        type2.addField("text", "text");
        type2.addField("thumbnailpath", "thumbnailpath");
        type2.addField("lastmod", "lastmod");
        type2.addField("owner", "owner");
        type2.addField("filesize", "filesize");
        type2.addField("refno", "refno");
        type2.addField("group", "group");

        final MessageBox wait = MessageBox.wait("Wait",
                "Searching, please wait...", "Searching...");
        final RequestBuilder builder =
                new RequestBuilder(RequestBuilder.GET, url);

        HttpProxy<String> proxy = new HttpProxy<String>(builder);
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type2);
        final BaseListLoader<ListLoadResult<ModelData>> loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);
        ListStore<ModelData> store = new ListStore<ModelData>(loader);
        view.setStore(store);
        loader.setSortDir(SortDir.ASC);
        loader.setSortField("text");
        store.sort("text", SortDir.ASC);
        //removeFolderMenuItem.setEnabled(selectedFolder == null ? true : false);
        loader.load();
        view.refresh();

        try {
            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot send search query", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        wait.close();
                        Constants.main.selectFileListTab();
                        view.refresh();
                        //Constants.main.refreshFileList();
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot post advanced search", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot post advanced search", null);
        }
    }
}
