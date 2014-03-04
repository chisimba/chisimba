/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.data.BaseListLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.JsonLoadResultReader;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.Events;
import com.extjs.gxt.ui.client.event.Listener;
import com.extjs.gxt.ui.client.event.MessageBoxEvent;
import com.extjs.gxt.ui.client.event.SelectionChangedEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.store.ListStore;
import com.extjs.gxt.ui.client.widget.ContentPanel;
import com.extjs.gxt.ui.client.widget.LayoutContainer;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.button.ButtonBar;
import com.extjs.gxt.ui.client.widget.form.CheckBox;
import com.extjs.gxt.ui.client.widget.grid.CellEditor;
import com.extjs.gxt.ui.client.widget.grid.CheckBoxSelectionModel;
import com.extjs.gxt.ui.client.widget.grid.CheckColumnConfig;
import com.extjs.gxt.ui.client.widget.grid.ColumnConfig;
import com.extjs.gxt.ui.client.widget.grid.ColumnModel;
import com.extjs.gxt.ui.client.widget.grid.EditorGrid;
import com.extjs.gxt.ui.client.widget.layout.FitLayout;
import com.extjs.gxt.ui.client.widget.toolbar.ToolBar;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import com.google.gwt.user.client.Element;


import java.util.ArrayList;
import java.util.List;

/**
 *
 * @author davidwaf
 */
public class UserListPanel extends LayoutContainer {

    private PermissionsFrame permissionsFrame;
    private BaseListLoader<ListLoadResult<ModelData>> loader;
    private EditorGrid<ModelData> grid;
    private ColumnModel cm;
    private Button addUserButton = new Button("Add User");
    private Button deleteUserButton = new Button("Delete User");
    private SearchUserPanel searchUserPanel;
    private List<ModelData> selectedRows;
    private CheckBoxSelectionModel<ModelData> sm;
    private boolean removeUsersDone = false;

    public UserListPanel(PermissionsFrame permissionsFrame) {
        super();
        searchUserPanel = new SearchUserPanel(permissionsFrame);
        this.permissionsFrame = permissionsFrame;
        addUserButton.setEnabled(false);
        deleteUserButton.setEnabled(false);
    }

    @Override
    protected void onRender(Element parent, int index) {
        super.onRender(parent, index);
        List<ColumnConfig> columns = new ArrayList<ColumnConfig>();
        sm = new CheckBoxSelectionModel<ModelData>();
        columns.add(sm.getColumn());
        columns.add(new ColumnConfig("Username", "Username", 100));
        columns.add(new ColumnConfig("Names", "Names", 145));
        CellEditor checkBoxEditor = new CellEditor(new CheckBox());

        CheckColumnConfig viewCheckColumn = new CheckColumnConfig("viewfiles", "View", 55);
        viewCheckColumn.setEditor(checkBoxEditor);
        columns.add(viewCheckColumn);

        CheckColumnConfig uploadCheckColumn = new CheckColumnConfig("uploadfiles", "Upload", 55);
        uploadCheckColumn.setEditor(checkBoxEditor);
        columns.add(uploadCheckColumn);

        CheckColumnConfig createFolderCheckColumn = new CheckColumnConfig("createfolder", "Folder", 55);
        createFolderCheckColumn.setEditor(checkBoxEditor);
        columns.add(createFolderCheckColumn);

        // create the column model
        cm = new ColumnModel(columns);

        // defines the xml structure
        ModelType type = new ModelType();
        type.setRoot("users");
        type.addField("userid", "userid");
        type.addField("Username", "username");
        type.addField("Names", "names");
        type.addField("Delete", "delete");
        type.addField("View", "viewfiles");
        type.addField("Upload", "uploadfiles");
        type.addField("Folder", "createfolder");

        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + "?module=wicid&action=getusers1");
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);

        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        ListStore<ModelData> store = new ListStore<ModelData>(loader);
        grid = new EditorGrid<ModelData>(store, cm);
        grid.setBorders(true);
        grid.setLoadMask(true);
        grid.getView().setEmptyText("No users found.");
        grid.setAutoExpandColumn("Names");
        grid.addPlugin(sm);
        grid.setSelectionModel(sm);
        grid.addPlugin(viewCheckColumn);
        grid.addPlugin(uploadCheckColumn);
        grid.addPlugin(createFolderCheckColumn);
        grid.getSelectionModel().addListener(Events.SelectionChange,
                new Listener<SelectionChangedEvent<ModelData>>() {

                    public void handleEvent(SelectionChangedEvent<ModelData> md) {
                        selectedRows = md.getSelection();

                        deleteUserButton.setEnabled(selectedRows.size() > 0);


                    }
                });


        ContentPanel panel = new ContentPanel();
        panel.setFrame(true);
        panel.setButtonAlign(HorizontalAlignment.CENTER);
        ToolBar toolbar = new ButtonBar();

        addUserButton.setIconStyle("add16");
        addUserButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
            }
        });
        toolbar.add(addUserButton);


        deleteUserButton.setIconStyle("delete");
        deleteUserButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                final Listener<MessageBoxEvent> l = new Listener<MessageBoxEvent>() {

                    public void handleEvent(MessageBoxEvent ce) {
                        Button btn = ce.getButtonClicked();
                        if (btn.getText().equalsIgnoreCase("Yes")) {
                            removeUsers();
                        }
                    }
                };
                MessageBox.confirm("Confirm", "Are you sure you want to remove selected users?", l);

            }
        });

        toolbar.add(deleteUserButton);
        panel.setTopComponent(toolbar);
        panel.setHeading("Users");
        panel.setLayout(new FitLayout());
        panel.add(grid);
        panel.setSize(545, 350);

        addUserButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                searchUserPanel.show();
            }
        });


        add(panel);
    }

    public void refreshUsers(String folder) {
        // defines the xml structure
        ModelType type = new ModelType();
        type.setRoot("users");
        type.addField("userid", "userid");
        type.addField("Username", "username");
        type.addField("Names", "names");
        type.addField("Delete", "delete");
        type.addField("View", "viewfiles");
        type.addField("Upload", "uploadfiles");
        type.addField("Folder", "createfolder");

        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() +Constants.MAIN_URL_PATTERN+ "?module=wicid&action=getusers&foldername=" + folder);
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);

        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        ListStore<ModelData> store = new ListStore<ModelData>(loader);

        grid.reconfigure(store, cm);
        loader.load();
        addUserButton.setEnabled(true);
    }

    private void removeUsers() {
        for (ModelData row : selectedRows) {
            RequestBuilder builder =
                    new RequestBuilder(RequestBuilder.POST, GWT.getHostPageBaseURL()
                    + "?module=wicid&action=removeuser&folderpath=" + permissionsFrame.getSelectedFolder() + "&userid=" + row.get("userid"));

            try {
                Request request = builder.sendRequest(null, new RequestCallback() {

                    public void onError(Request request, Throwable exception) {
                        MessageBox.info("Error", "Error, cannot remove user", null);
                    }

                    public void onResponseReceived(Request request, Response response) {
                        if (200 == response.getStatusCode()) {
                            sm.deselectAll();
                            refreshUsers(permissionsFrame.getSelectedFolder());
                        } else {
                            MessageBox.info("Error", "Error occured on the server. Cannot remove user", null);
                        }
                    }
                });
            } catch (RequestException e) {
                MessageBox.info("Fatal Error", "Fatal Error: cannot remove user", null);
            }
        }
        if (removeUsersDone) {
            //  sm.deselectAll();
            //  refreshUsers(permissionsFrame.getSelectedFolder());
        }
    }
}
