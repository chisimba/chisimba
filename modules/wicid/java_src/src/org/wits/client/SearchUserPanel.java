/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.data.BaseListLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.JsonLoadResultReader;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.ComponentEvent;
import com.extjs.gxt.ui.client.event.Events;
import com.extjs.gxt.ui.client.event.KeyListener;
import com.extjs.gxt.ui.client.event.Listener;
import com.extjs.gxt.ui.client.event.SelectionChangedEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.store.ListStore;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.button.ButtonBar;
import com.extjs.gxt.ui.client.widget.form.CheckBox;
import com.extjs.gxt.ui.client.widget.form.TextField;
import com.extjs.gxt.ui.client.widget.grid.CellEditor;
import com.extjs.gxt.ui.client.widget.grid.CheckBoxSelectionModel;
import com.extjs.gxt.ui.client.widget.grid.CheckColumnConfig;
import com.extjs.gxt.ui.client.widget.grid.ColumnConfig;
import com.extjs.gxt.ui.client.widget.grid.ColumnModel;
import com.extjs.gxt.ui.client.widget.grid.EditorGrid;

import com.extjs.gxt.ui.client.widget.toolbar.LabelToolItem;
import com.extjs.gxt.ui.client.widget.toolbar.ToolBar;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

/**
 *
 * @author davidwaf
 */
public class SearchUserPanel {

    private Dialog searchUserDialog = new Dialog();
    private BaseListLoader<ListLoadResult<ModelData>> loader;
    private EditorGrid<ModelData> grid;
    private ColumnModel cm;
    private TextField<String> nameField = new TextField<String>();
    private Button addButton = new Button("Add Selected");
    private Button cancelButton = new Button("Cancel");
    private List<ModelData> selectedRows;
    private PermissionsFrame permissionsFrame;
    private CheckBoxSelectionModel<ModelData> sm;
    private boolean completedAddingUsers = false;

    public SearchUserPanel(PermissionsFrame permissionsFrame) {
        this.permissionsFrame = permissionsFrame;
        searchUserDialog.setBodyBorder(false);
        searchUserDialog.setHeading("Search users");
        searchUserDialog.setWidth(800);
        searchUserDialog.setHeight(420);
        searchUserDialog.setHideOnButtonClick(true);
        searchUserDialog.setButtons(Dialog.CLOSE);

        nameField.setFieldLabel("Name");
        nameField.addKeyListener(new KeyListener() {

            @Override
            public void componentKeyPress(ComponentEvent e) {
                if (nameField.getValue().length() > 2) {
                    refreshUsers(nameField.getValue());
                }
            }
        });

        ToolBar toolbar = new ButtonBar();
        toolbar.add(new LabelToolItem("Search:"));
        toolbar.add(nameField);
        Button searchButton = new Button();
        searchButton.setIconStyle("search");
        searchButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                refreshUsers(nameField.getValue());
            }
        });
        toolbar.add(searchButton);
        searchUserDialog.setTopComponent(toolbar);


        List<ColumnConfig> columns = new ArrayList<ColumnConfig>();

        sm = new CheckBoxSelectionModel<ModelData>();
        columns.add(sm.getColumn());
        columns.add(new ColumnConfig("Username", "Username", 200));
        columns.add(new ColumnConfig("Names", "Names", 345));

        CellEditor checkBoxEditor = new CellEditor(new CheckBox());

        CheckColumnConfig viewCheckColumn = new CheckColumnConfig("viewfiles", "View Files", 55);
        viewCheckColumn.setEditor(checkBoxEditor);
        columns.add(viewCheckColumn);

        CheckColumnConfig uploadCheckColumn = new CheckColumnConfig("uploadfiles", "Upload", 55);
        uploadCheckColumn.setEditor(checkBoxEditor);
        columns.add(uploadCheckColumn);

        CheckColumnConfig createFolderCheckColumn = new CheckColumnConfig("createfolder", "Create Folder", 55);
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
        type.addField("Select", "select");
        type.addField("viewfiles", "viewfiles");
        type.addField("uploadfiles", "uploadfiles");
        type.addField("createfolder", "createfolder");


        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + "?module=wicid&action=getallusers&searchfield=a");
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
        grid.setAutoHeight(true);
        grid.addPlugin(sm);
        grid.addPlugin(viewCheckColumn);
        grid.addPlugin(uploadCheckColumn);
        grid.addPlugin(createFolderCheckColumn);
        grid.setSelectionModel(sm);
        grid.getSelectionModel().addListener(Events.SelectionChange,
                new Listener<SelectionChangedEvent<ModelData>>() {

                    public void handleEvent(SelectionChangedEvent<ModelData> md) {
                        selectedRows = md.getSelection();
                    }
                });


        cancelButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                searchUserDialog.setVisible(false);
            }
        });

        addButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                addUsers();
            }
        });
        ButtonBar buttonBar = new ButtonBar();
        buttonBar.add(addButton);
        buttonBar.add(cancelButton);


        searchUserDialog.setBottomComponent(buttonBar);
        searchUserDialog.add(grid);

    }

    public void show() {
        searchUserDialog.show();
    }

    private void addUsers() {

        for (ModelData row : selectedRows) {
            String viewfiles = "false";
            String uploadfiles = "false";
            String createfolder = "false";
            String folders[] = permissionsFrame.getSelectedFolder().split("/");
            String folderToAdd = "";
            int index = 0;
            for (String folder : folders) {
                if (folder.length() > 0) {
                    boolean selections = folder.equals(folders[folders.length - 1]);
                    if (selections) {
                        viewfiles = row.get("viewfiles") + "";
                        uploadfiles = row.get("uploadfiles") + "";
                        createfolder = row.get("createfolder") + "";
                    }
                    folderToAdd += "/" + folder;
                    RequestBuilder builder =
                            new RequestBuilder(RequestBuilder.POST, GWT.getHostPageBaseURL()
                            + "?module=wicid&action=adduser&folderpath="
                            + folderToAdd + "&userid=" + row.get("userid")
                            + "&viewfiles=" + viewfiles
                            + "&uploadfiles=" + uploadfiles
                            + "&createfolder=" + createfolder);

                    try {
                        Request request = builder.sendRequest(null, new RequestCallback() {

                            public void onError(Request request, Throwable exception) {
                                MessageBox.info("Error", "Error, cannot add user", null);
                            }

                            public void onResponseReceived(Request request, Response response) {
                                if (200 == response.getStatusCode()) {
                                    sm.deselectAll();
                                    permissionsFrame.refreshUserList();
                                    searchUserDialog.setVisible(false);

                                } else {
                                    MessageBox.info("Error", "Error occured on the server. Cannot add user", null);
                                }
                            }
                        });
                    } catch (RequestException e) {
                        MessageBox.info("Fatal Error", "Fatal Error: cannot add user", null);
                    }
                    index++;
                }

            }
        }
        if (completedAddingUsers) {
            // sm.deselectAll();
            // permissionsFrame.refreshUserList();
            //  searchUserDialog.setVisible(false);
        }
    }

    private void refreshUsers(String name) {
        // defines the xml structure
        ModelType type = new ModelType();
        type.setRoot("users");
        type.addField("userid", "userid");
        type.addField("Username", "username");
        type.addField("Names", "names");
        type.addField("Select", "select");
        type.addField("viewfiles", "viewfiles");
        type.addField("uploadfiles", "uploadfiles");
        type.addField("createfolder", "createfolder");

        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + "?module=wicid&action=getallusers&searchfield=" + name);
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);

        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        ListStore<ModelData> store = new ListStore<ModelData>(loader);

        grid.reconfigure(store, cm);
        loader.load();

    }
}
