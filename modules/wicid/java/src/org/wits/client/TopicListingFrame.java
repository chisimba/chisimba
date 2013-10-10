/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.event.SelectionChangedEvent;
import java.util.Arrays;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.data.BaseTreeLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.data.XmlReader;
import com.extjs.gxt.ui.client.event.SelectionChangedListener;
import com.extjs.gxt.ui.client.store.TreeStore;
import com.extjs.gxt.ui.client.widget.ContentPanel;
import com.extjs.gxt.ui.client.widget.LayoutContainer;
import com.extjs.gxt.ui.client.widget.grid.ColumnConfig;
import com.extjs.gxt.ui.client.widget.grid.ColumnModel;
import com.extjs.gxt.ui.client.widget.layout.FitLayout;
import com.extjs.gxt.ui.client.widget.layout.FlowLayout;
import com.extjs.gxt.ui.client.widget.treegrid.TreeGrid;
import com.extjs.gxt.ui.client.widget.treegrid.TreeGridCellRenderer;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.user.client.Element;
import java.util.List;
import org.wits.client.ads.NewCourseProposalDialog;

public class TopicListingFrame extends LayoutContainer {

    private EditDocumentDialog editDocumentDialog;
    private NewDocumentDialog newDocumentDialog;
    private NewCourseProposalDialog newCourseProposalDialog;

    public TopicListingFrame(NewDocumentDialog newDocumentDialog) {
        this.newDocumentDialog = newDocumentDialog;
    }

    public TopicListingFrame(EditDocumentDialog editDocumentDialog) {
        this.editDocumentDialog = editDocumentDialog;
    }

    public TopicListingFrame(NewCourseProposalDialog newCourseProposalDialog) {
        this.newCourseProposalDialog = newCourseProposalDialog;
    }



    @Override
    protected void onRender(Element parent, int index) {
        super.onRender(parent, index);

        setLayout(new FlowLayout(10));
        ModelType treeFieldtype = new ModelType();
        treeFieldtype.setRecordName("item");
        treeFieldtype.setRoot("items");
        treeFieldtype.addField("id", "@id");
        treeFieldtype.addField("name", "@name");
        treeFieldtype.addField("folder", "@folder");
        String foldersUrl = Constants.MAIN_URL_PATTERN + "?module=wicid&action=getfolders";
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL()+foldersUrl);
        HttpProxy<ListLoadResult<ModelData>> proxy = new HttpProxy<ListLoadResult<ModelData>>(builder);
        XmlReader folderReader = new XmlReader<List<ModelData>>(treeFieldtype);
        BaseTreeLoader folderLoader = new BaseTreeLoader<ModelData>(proxy, folderReader) {

            @Override
            public boolean hasChildren(ModelData parent) {
                return "true".equals(parent.get("folder"));
            }

            @Override
            protected Object prepareLoadConfig(Object config) {
                return super.prepareLoadConfig(config);
            }
        };

        TreeStore folderStore = new TreeStore<ModelData>(folderLoader);


        ColumnConfig name = new ColumnConfig("name", "Name", 700);
        name.setRenderer(new TreeGridCellRenderer<ModelData>());

       ColumnModel cm = new ColumnModel(Arrays.asList(name));

        ContentPanel cp = new ContentPanel();
        cp.setBodyBorder(false);
        cp.setFrame(false);
        cp.setButtonAlign(HorizontalAlignment.CENTER);
        cp.setLayout(new FitLayout());

        cp.setSize(700, 250);

        TreeGrid<ModelData> tree = new TreeGrid<ModelData>(folderStore, cm);
        tree.getSelectionModel().addSelectionChangedListener(new SelectionChangedListener<ModelData>() {

            @Override
            public void selectionChanged(SelectionChangedEvent<ModelData> se) {
               if(newDocumentDialog != null){
                   newDocumentDialog.setSelectedFolder(se.getSelectedItem());
               }
               if(editDocumentDialog != null){
                   editDocumentDialog.setSelectedFolder(se.getSelectedItem());
               }
               if(newCourseProposalDialog != null){
                   newCourseProposalDialog.setSelectedFaculty(se.getSelectedItem());
               }
            }
        });
        tree.setBorders(true);

        tree.setAutoExpandColumn("name");
        tree.setTrackMouseOver(false);

        cp.add(tree);

        add(cp);
    }
}
