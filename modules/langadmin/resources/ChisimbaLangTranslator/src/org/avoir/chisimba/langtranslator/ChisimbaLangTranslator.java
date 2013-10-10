/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.avoir.chisimba.langtranslator;

import java.awt.Dimension;
import java.awt.Toolkit;

/**
 *
 * @author davidwaf
 */
public class ChisimbaLangTranslator {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        Dimension ss = Toolkit.getDefaultToolkit().getScreenSize();
        MainFrame mainFrame=new MainFrame();
        mainFrame.setSize((ss.height/8)*7,(ss.height /2));
        mainFrame.setLocationRelativeTo(null);
        mainFrame.setVisible(true);
    }
}
