package org.wits.client.ads;

import com.extjs.gxt.ui.client.Style.HideMode;
import java.util.ArrayList;
import java.util.List;
import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.Style.Scroll;
import com.extjs.gxt.ui.client.Style.SelectionMode;
import com.extjs.gxt.ui.client.data.BaseListLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.JsonLoadResultReader;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;

import com.extjs.gxt.ui.client.store.ListStore;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.ContentPanel;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.Label;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.TextField;
import com.extjs.gxt.ui.client.widget.grid.ColumnConfig;
import com.extjs.gxt.ui.client.widget.grid.ColumnModel;
import com.extjs.gxt.ui.client.widget.grid.Grid;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FitLayout;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.core.client.GWT;
import com.google.gwt.core.client.JsArray;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.Response;
import org.wits.client.Constants;
import org.wits.client.EditDocumentDialog;

/**
 *
 * @author luigi
 */
public class ForwardTo {

    private Dialog forwardToDialog = new Dialog();
    private Dialog searchDialog = new Dialog();
    private RulesAndSyllabusTwo rulesAndSyllabusTwo;
    private TextField forwardTo = new TextField();
    private Button forwardButton = new Button();
    private Button searchButton = new Button("Search");
    private FormPanel mainForm = new FormPanel();
    private FormPanel searchForm = new FormPanel();
    private FormData formData = new FormData("-20");
    //private FormData formData = new FormData();
    private String email;
    private String selectedUserid;
    private BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
    private BorderLayoutData eastData = new BorderLayoutData(LayoutRegion.EAST, 80);
    private ListStore<ModelData> userStore;
    private BaseListLoader<ListLoadResult<ModelData>> loader;
    private Grid<ModelData> emailGrid;
    private ColumnModel cm;
    private int versionV;

    public ForwardTo() {
        creatUI();
    }

    public void creatUI() {
        centerData.setMargins(new Margins(0));

        forwardTo.setWidth(300);
        forwardTo.setHeight(25);
        forwardTo.setAllowBlank(false);
        forwardTo.setReadOnly(true);

        //mainForm.add(forwardTo, centerData);

        searchButton.setSize(80, 25);
        searchButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                searchDialog();
            }
        });

        com.google.gwt.user.client.ui.Grid forwardGrid = new com.google.gwt.user.client.ui.Grid(2, 2);
        forwardGrid.setWidget(0, 0, forwardTo);
        forwardGrid.setWidget(0, 1, searchButton);

        FormPanel panel = new FormPanel();
        panel.setSize(400, 140);
        panel.add(forwardGrid);

        forwardButton.setText("Forward");
        forwardButton.setSize(80, 22);
        forwardButton.enableEvents(true);
        forwardButton.setPagePosition(150, 100);
        forwardButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                increaseVersion();
                forwardToDialog.hide();
                String params = "?module=wicid&action=getdocuments&mode=" + Constants.main.getMode();
                Constants.main.getDocumentListPanel().refreshDocumentList(params);
            }
        });
        panel.add(forwardButton);

        forwardToDialog.setBodyBorder(false);
        forwardToDialog.setHeading("Forward to...");
        forwardToDialog.setHeight(180);
        forwardToDialog.setWidth(412);
        forwardToDialog.setButtons(Dialog.CLOSE);
        forwardToDialog.setButtonAlign(HorizontalAlignment.LEFT);
        forwardToDialog.setHideOnButtonClick(true);
        forwardToDialog.add(panel, formData);
    }

    public void changeCurrentUser(int version) {

        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL()
                + Constants.MAIN_URL_PATTERN + "?module=wicid&action=changecurrentuser&userid="
                + selectedUserid + "&docid=" + Constants.docid + "&version=" + version);
        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot change currentuser", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    MessageBox.info("Done", "The current user for the document has been changed.", null);
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot change currentuser", null);
        }
    }

    public void increaseVersion() {

        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL()
                + Constants.MAIN_URL_PATTERN + "?module=wicid&action=increaseversion&docid=" + Constants.docid);
        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get latest version", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    versionV = Integer.parseInt(response.getText());
                    changeCurrentUser(versionV);
                    MessageBox.info("Done", "The version for the document has been changed to " + versionV, null);
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get version", null);
        }
    }

    public void show() {
        forwardToDialog.show();
    }

    public void searchDialog() {
        searchDialog.setBodyBorder(false);
        searchDialog.setHeading("Search");
        searchDialog.setWidth(400);
        searchDialog.setHeight(350);
        searchDialog.setButtons(Dialog.CLOSE);
        searchDialog.setButtonAlign(HorizontalAlignment.LEFT);

        searchForm.setHeight(280);

        final TextField inputField = new TextField();
        inputField.setEmptyText("Enter partial email");
        inputField.setSize(250, 25);

        Button searchB = new Button("Search");
        searchB.setSize(80, 25);
        searchB.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                String value = inputField.getValue().toString();
                search(value);
            }
        });

        com.google.gwt.user.client.ui.Grid inputGrid = new com.google.gwt.user.client.ui.Grid(2, 2);
        inputGrid.setWidget(0, 0, inputField);
        inputGrid.setWidget(0, 1, searchB);
        searchForm.add(inputGrid, formData);

        List<ColumnConfig> configs = new ArrayList<ColumnConfig>();

        ColumnConfig column = new ColumnConfig();
        column.setId("firstname");
        column.setHeader("First Name");
        column.setWidth(75);
        configs.add(column);

        column = new ColumnConfig();
        column.setId("surname");
        column.setHeader("Surname");
        column.setWidth(75);
        configs.add(column);

        column = new ColumnConfig();
        column.setId("emailaddress");
        column.setHeader("Email Address");
        column.setAlignment(HorizontalAlignment.LEFT);
        column.setWidth(200);
        configs.add(column);

        cm = new ColumnModel(configs);

        ContentPanel cp = new ContentPanel();
        cp.setBodyBorder(false);
        //cp.setIcon(Resources.ICONS.table());
        cp.setHeading("Results");
        cp.setButtonAlign(HorizontalAlignment.CENTER);
        cp.setLayout(new FitLayout());
        cp.setSize(600, 150);
        cp.setScrollMode(Scroll.AUTO);

        ModelType type = new ModelType();
        type.setRoot("users");
        type.addField("userid", "userid");
        type.addField("firstname", "firstname");
        type.addField("surname", "surname");
        type.addField("emailaddress", "emailaddress");

        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + "?module=wicid&action=searchusers");
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);
        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        userStore = new ListStore<ModelData>(loader);

        emailGrid = new Grid<ModelData>(userStore, cm);
        emailGrid.setStyleAttribute("borderTop", "none");
        emailGrid.setAutoExpandColumn("firstname");
        emailGrid.setBorders(true);
        emailGrid.setStripeRows(true);
        emailGrid.getSelectionModel().setSelectionMode(SelectionMode.SINGLE);
        cp.add(emailGrid);

        searchForm.add(cp, formData);
        searchForm.add(new Label());

        Button doneButton = new Button("Done");
        doneButton.setSize(80, 25);
        doneButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                email = emailGrid.getSelectionModel().getSelectedItem().get("emailaddress");
                selectedUserid = emailGrid.getSelectionModel().getSelectedItem().get("userid");
                forwardTo.setRawValue(email);
                searchDialog.hide();
            }
        });
        searchForm.add(doneButton);

        searchForm.setHeading("Enter email address:");

        searchDialog.add(searchForm);
        searchDialog.setVisible(true);
    }

    private void sendEmail(String url) {
        /*email = forwardTo.getValue().toString();

        if (forwardTo.getValue() == null) {
        MessageBox.info("Error", "Please provide a person to forward to.", null);
        return;
        }

        String url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
        + "?module=wicid&action=forwardto&link=" + "link" + "&email=" + email + "&docid=" + Constants.docid;
        MessageBox.info("Message", url, null);
        sendEmail(url);

        forwardToDialog.close();*/
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new document", null);
                }

                public void onResponseReceived(Request request, Response response) {
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create new document", null);
        }

    }

    /*  private List<User> getUsers() {
    List<User> users = new ArrayList<User>();
    users.add(new User("Jane", "Smith", "janesmith@wits.ac.za"));
    users.add(new User("Jacqueline", "Gil", "jacqueline.gil@students.wits.ac.za"));
    return users;
    }

    /**
     * Convert the string of JSON into JavaScript object.
     */
    private final native JsArray<JSonUser> asArrayOfUser(String json) /*-{
    return eval(json);
    }-*/;

    private void search(String val) {
        // defines the xml structure
        ModelType type = new ModelType();
        type.setRoot("users");
        type.addField("userid", "userid");
        type.addField("firstname", "firstname");
        type.addField("surname", "surname");
        type.addField("emailaddress", "emailaddress");

        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=searchusers&filter=" + val);
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);

        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        userStore = new ListStore<ModelData>(loader);

        emailGrid.reconfigure(userStore, cm);
        loader.load();
    }
}
