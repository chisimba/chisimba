/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.widget.Dialog;

/**
 *
 * @author davidwaf
 */
public class FileInfo {

    private String refNo;
    private String lastMod;
    private String owner;
    private String fileSize;
    private String filename;
    private String thumbnail;
    private String group;
    private Dialog fileInfoDialog = new Dialog();

    public FileInfo(String filename,
            String refNo,
            String lastMod,
            String owner,
            String fileSize,
            String thumbnail,
            String group) {
        this.refNo = refNo;
        this.filename = filename;
        this.lastMod = lastMod;
        this.owner = owner;
        this.fileSize = fileSize;
        this.thumbnail = thumbnail;
        this.group=group;
    }

    public void show() {
        fileInfoDialog.addText(
                "<b>File name:</b>" + filename + "<br/><br/>"
                + "<b>Ref no:</b> " + refNo + "<br/><br/>"
                + "<b>Owner:</b> " + owner + "<br/><br/>"
                + "<b>Group:</b> " + group + "<br/><br/>"
                + "<b>Last modified:</b> " + lastMod + "<br/><br/>"
                + "<b>File size:</b>" + fileSize + "<br/><br/>"
                + "<img src=\"" + thumbnail + "\">");
        fileInfoDialog.setWidth(300);

        fileInfoDialog.setOnEsc(true);
        fileInfoDialog.setHideOnButtonClick(true);
        fileInfoDialog.setHeight(300);
        fileInfoDialog.setTitle(filename);
        fileInfoDialog.show();
    }
}
