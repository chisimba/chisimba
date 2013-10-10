/*
 * EflaView.java
 */
package efla;

import com.jgoodies.looks.LookUtils;
import com.jgoodies.looks.Options;
import efla.util.EssayErrorCodes;
import efla.util.HighlightColors;
import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.JobAttributes;
import java.awt.Toolkit;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.util.ArrayList;
import javax.swing.BorderFactory;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JMenuBar;
import javax.swing.JMenuItem;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPopupMenu;
import javax.swing.JScrollPane;
import javax.swing.JTextArea;
import javax.swing.SwingUtilities;
import javax.swing.UIManager;
import javax.swing.text.BadLocationException;
import javax.swing.text.DefaultHighlighter;
import javax.swing.text.DefaultStyledDocument;
import javax.swing.text.Element;
import javax.swing.text.MutableAttributeSet;
import javax.swing.text.SimpleAttributeSet;
import javax.swing.text.Style;
import javax.swing.text.StyleConstants;
import javax.swing.text.StyleContext;
import javax.swing.text.StyledDocument;

/**
 * The application's main frame.
 */
public class EflaView extends JFrame implements ActionListener {

    private int missingWords = 0;
    private int articleErrors = 0;
    private int conjunctionErrors = 0;
    private int grammarErrors = 0;
    private int punctuationErrors = 0;
    private int partOfSpeechErrors = 0;
    private int prepositionErrors = 0;
    private int registerStyleErrors = 0;
    private int spellingErrors = 0;
    private int subjectVerbErrors = 0;
    private int tenseErrors = 0;
    private int vocubularyErrors = 0;
    private int wordOrderErrors = 0;
    private int wrongWordErrors = 0;
    private Dimension ss = Toolkit.getDefaultToolkit().getScreenSize();
    private ArrayList<EssayHighlightPainter> errors = new ArrayList<EssayHighlightPainter>();
    private JPopupMenu popup = new JPopupMenu();
    private EssayEditor essayField;
    private String initText = "this is an essay as viewed by the administrator"; //$content
    private String baseURL;

    public EflaView(String baseUrl) {
        this.baseURL = baseUrl;
        try {
            SwingUtilities.invokeAndWait(new Runnable() {

                public void run() {
                    initView();
                }
            });
        } catch (Exception e) {
            System.err.println("createGUI didn't complete successfully");
        }
    }

    /**
     * Configures the UI; tries to set the system look on Mac,
     * <code>WindowsLookAndFeel</code> on general Windows, and
     * <code>Plastic3DLookAndFeel</code> on Windows XP and all other OS.<p>
     *
     * The JGoodies Swing Suite's <code>ApplicationStarter</code>,
     * <code>ExtUIManager</code>, and <code>LookChoiceStrategies</code>
     * classes provide a much more fine grained algorithm to choose and
     * restore a look and theme.
     */
    private void configureUI() {
        UIManager.put(Options.USE_SYSTEM_FONTS_APP_KEY, Boolean.TRUE);
        Options.setDefaultIconSize(new Dimension(18, 18));

        String lafName =
                LookUtils.IS_OS_WINDOWS_XP
                ? Options.getCrossPlatformLookAndFeelClassName()
                : Options.getSystemLookAndFeelClassName();

        try {
            UIManager.setLookAndFeel(lafName);
        } catch (Exception e) {
            System.err.println("Can't set look & feel:" + e);
        }
    }

    private void initView() {
        configureUI();
        initComponents();
        saveButton.setText("Save");
        finishButton.setText("Finish");
        // Create the StyleContext, the document and the pane
        StyleContext sc = new StyleContext();
        final DefaultStyledDocument doc = new DefaultStyledDocument(sc);


        // Create and add the main document style
        Style defaultStyle = sc.getStyle(StyleContext.DEFAULT_STYLE);
        final Style mainStyle = sc.addStyle("MainStyle", defaultStyle);
        StyleConstants.setAlignment(mainStyle, StyleConstants.ALIGN_JUSTIFIED);
        StyleConstants.setLeftIndent(mainStyle, 16);
        StyleConstants.setRightIndent(mainStyle, 200);
        StyleConstants.setFirstLineIndent(mainStyle, 16);
        StyleConstants.setFontFamily(mainStyle, "serif");
        StyleConstants.setFontSize(mainStyle, 12);
        StyleConstants.setLineSpacing(mainStyle, 1.5f);


        essayField = new EssayEditor(this, doc);
        essayField.setLogicalStyle(mainStyle);
        //setSize(ss);
        splitPane.setDividerLocation((ss.width / 4) * 3);

        //essayField.setContentType("text/html");

        try {
            // Set the logical style
            doc.setLogicalStyle(0, mainStyle);

            // Add the text to the document
            doc.insertString(0, initText, null);
        } catch (BadLocationException e) {
            e.printStackTrace();
        }

        JPanel xleftPanel = new JPanel(new BorderLayout());
        xleftPanel.setBorder(BorderFactory.createTitledBorder("Essay"));
        rightPanel.setBorder(javax.swing.BorderFactory.createTitledBorder("Results"));

        xleftPanel.add(new JScrollPane(essayField), BorderLayout.CENTER);
        splitPane.setLeftComponent(xleftPanel);
        initDocumentSettings();
        setLayout(new BorderLayout());
        add(mainPanel, BorderLayout.CENTER);
        finishButton.setActionCommand("finish");

        finishButton.addActionListener(this);
        setJMenuBar(menuBar);
        initPopup();
    }

    public JMenuBar getTMenuBar() {
        return menuBar;
    }

    private void initDocumentSettings() {
        StyledDocument doc = (StyledDocument) essayField.getDocument();

        int paragraph = 0;
        float newlinespacing = .4f;

        Element el = doc.getParagraphElement(paragraph);

        MutableAttributeSet mas = new SimpleAttributeSet();
        StyleConstants.setLineSpacing(mas, newlinespacing);

        doc.setParagraphAttributes(
                0, el.getEndOffset() - el.getStartOffset(), mas, false);

    }

    public void refresh() {
        int divLoc = splitPane.getDividerLocation();
        splitPane.setDividerLocation(divLoc + 2);
        splitPane.setDividerLocation(divLoc - 2);
    }

    /**
     * this removes the highlight from the selected text, if it was highlighted.
     * this is helped by using getStartSelection and getEndSelection
     * @param textComp
     */
    public void removeHighlights() {
        int start = essayField.getSelectionStart();
        int end = essayField.getSelectionEnd();

        for (int i = 0; i < errors.size(); i++) {
            if ((errors.get(i).getStart() == start) && (errors.get(i).getEnd() == end)) {
                int code = errors.get(i).getCode();
                switch (code) {
                    case EssayErrorCodes.ARTICLE_ERRORS: {
                        articleErrors--;
                        break;
                    }
                    case EssayErrorCodes.CONJUNCTION_ERRORS: {
                        conjunctionErrors--;
                        break;
                    }
                    case EssayErrorCodes.MISSING_WORDS: {
                        missingWords--;
                        break;
                    }
                    case EssayErrorCodes.GRAMMAR_ERRORS: {
                        grammarErrors--;
                        break;
                    }
                }
                errors.remove(i);

            }
        }

        essayField.getHighlighter().removeAllHighlights();
        try {
            for (EssayHighlightPainter error : errors) {
                essayField.getHighlighter().addHighlight(error.getStart(), error.getEnd(), error);
            }
        } catch (BadLocationException ble) {
            ble.printStackTrace();
        }
        resultsPanel.repaint();
    }

    public void highLight(ArrayList<Integer> codes, ArrayList<Color> colors) {
        try {
            int start = essayField.getSelectionStart();
            int end = essayField.getSelectionEnd();
            int selectionLength = end - start;
            if (selectionLength < 1) {
                JOptionPane.showMessageDialog(null, "Please select text to mark");
                return;
            }

            int interval = selectionLength / codes.size();

            int rem = selectionLength % codes.size();
            if (rem != 0) {
                //interval += 1;
            }

            int lastStart = start;
            for (int i = 0; i < codes.size(); i++) {

                int xend = lastStart + interval + 1;
                if (xend > end) {
                    xend = end;
                }

                EssayHighlightPainter highlighter = new EssayHighlightPainter(colors.get(i), lastStart, xend, codes.get(i));
                essayField.getHighlighter().removeAllHighlights();
                errors.add(highlighter);
                for (EssayHighlightPainter error : errors) {
                    essayField.getHighlighter().addHighlight(error.getStart(), error.getEnd(), error);

                }
                increaseErrorCount(codes.get(i));
                lastStart = xend;

            }
            resultsPanel.repaint();
        } catch (BadLocationException ble) {
            ble.printStackTrace();
        }
    }

    private void increaseErrorCount(int code) {
        switch (code) {
            case EssayErrorCodes.MISSING_WORDS: {
                missingWords++;
                break;
            }
            case EssayErrorCodes.ARTICLE_ERRORS: {
                articleErrors++;
                break;
            }
            case EssayErrorCodes.CONJUNCTION_ERRORS: {
                conjunctionErrors++;
                break;
            }
            case EssayErrorCodes.GRAMMAR_ERRORS: {
                grammarErrors++;
                break;
            }
            case EssayErrorCodes.PUNCTUATION_ERRORS: {
                punctuationErrors++;
                break;
            }
            case EssayErrorCodes.PART_OF_SPEECH_ERRORS: {
                partOfSpeechErrors++;
                break;
            }
            case EssayErrorCodes.PREPOSITION_ERRORS: {
                prepositionErrors++;
                break;
            }
            case EssayErrorCodes.REGISTER_STYLE_ERRORS: {
                registerStyleErrors++;
                break;
            }
            case EssayErrorCodes.SPELLING_ERRORS: {
                spellingErrors++;
                break;
            }
            case EssayErrorCodes.SUBJECT_VERB_ERRORS: {
                subjectVerbErrors++;
                break;
            }
            case EssayErrorCodes.TENSE_ERRORS: {
                tenseErrors++;
                break;
            }
            case EssayErrorCodes.VOCABULARY_ERRORS: {
                vocubularyErrors++;
                break;
            }
            case EssayErrorCodes.WORD_ORDER_ERRORS: {
                wordOrderErrors++;
                break;
            }
            case EssayErrorCodes.WRONG_WORD_ERRORS: {
                wrongWordErrors++;
                break;
            }

        }
    }

    /**
     * Highlights the selected text with appropriate color, depending on the error
     * spotted
     * @param code
     * @param color
     */
    private void highLight(int code, Color color) {
        try {
            int start = essayField.getSelectionStart();
            int end = essayField.getSelectionEnd();
            int selectionLength = end - start;
            if (selectionLength < 1) {
                JOptionPane.showMessageDialog(null, "Please select text to mark");
                return;
            }

            EssayHighlightPainter highlighter = new EssayHighlightPainter(color, start, end, code);
            essayField.getHighlighter().removeAllHighlights();
            errors.add(highlighter);
            for (EssayHighlightPainter error : errors) {
                essayField.getHighlighter().addHighlight(error.getStart(), error.getEnd(), error);
            }
            increaseErrorCount(code);
            resultsPanel.repaint();
            //remove selection
            essayField.select(end, end);
        } catch (BadLocationException ble) {
            ble.printStackTrace();
        }
    }

    public void actionPerformed(ActionEvent e) {
        if (e.getActionCommand().equals("finish")) {
            /* try {
            this.getAppletContext().showDocument(new URL(this.getParameter("baseurl") + "?module=efl"), "_self");
            } catch (Exception ex) {
            ex.printStackTrace();
            }*/
        }
        if (e.getActionCommand().equals("combo")) {
            showComboMarkerTools();
        }
        if (e.getActionCommand().equals("missingwordserrors")) {
            highLight(EssayErrorCodes.MISSING_WORDS, HighlightColors.MISSING_WORDS);
        }
        if (e.getActionCommand().equals("articleerrors")) {
            highLight(EssayErrorCodes.ARTICLE_ERRORS, HighlightColors.ARTICLE_ERRORS);
        }
        if (e.getActionCommand().equals("conjunctionerrors")) {
            highLight(EssayErrorCodes.CONJUNCTION_ERRORS, HighlightColors.CONJUNCTION_ERRORS);
        }
        if (e.getActionCommand().equals("grammarerrors")) {
            highLight(EssayErrorCodes.GRAMMAR_ERRORS, HighlightColors.GRAMMAR_ERRORS);
        }
        if (e.getActionCommand().equals("punctuationerrors")) {
            highLight(EssayErrorCodes.PUNCTUATION_ERRORS, HighlightColors.PUNCTUATION_ERRORS);
        }
        if (e.getActionCommand().equals("partofspeecherrors")) {
            highLight(EssayErrorCodes.PART_OF_SPEECH_ERRORS, HighlightColors.PART_OF_SPEECH_ERRORS);
        }
        if (e.getActionCommand().equals("prepositionerrors")) {
            highLight(EssayErrorCodes.PREPOSITION_ERRORS, HighlightColors.PREPOSITION_ERRORS);
        }
        if (e.getActionCommand().equals("registerstyleerrors")) {
            highLight(EssayErrorCodes.REGISTER_STYLE_ERRORS, HighlightColors.REGISTER_STYLE_ERRORS);
        }
        if (e.getActionCommand().equals("spellingerrors")) {
            highLight(EssayErrorCodes.SPELLING_ERRORS, HighlightColors.SPELLING_ERRORS);
        }
        if (e.getActionCommand().equals("subjectverberrors")) {
            highLight(EssayErrorCodes.SUBJECT_VERB_ERRORS, HighlightColors.SUBJECT_VERB_ERRORS);
        }
        if (e.getActionCommand().equals("tenseerrors")) {
            highLight(EssayErrorCodes.TENSE_ERRORS, HighlightColors.TENSE_ERRORS);
        }
        if (e.getActionCommand().equals("vocabularyerrors")) {
            highLight(EssayErrorCodes.VOCABULARY_ERRORS, HighlightColors.VOCABULARY_ERRORS);
        }
        if (e.getActionCommand().equals("wordordererrors")) {
            highLight(EssayErrorCodes.WORD_ORDER_ERRORS, HighlightColors.WORD_ORDER_ERRORS);
        }
        if (e.getActionCommand().equals("wrongworderrors")) {
            highLight(EssayErrorCodes.WRONG_WORD_ERRORS, HighlightColors.WRONG_WORD_ERRORS);
        }
        if (e.getActionCommand().equals("removehighlight")) {
            removeHighlights();
        }
        if (e.getActionCommand().equals("insertnote")) {
            insertNote();
        }
    }

    /**
     * initializes the popup 
     */
    private void initPopup() {
        insertMenu.setText("Insert");
        toolsMenu.setText("Tools");
        fileMenu.setFont(toolsMenu.getFont());
        fileMenu.setText("File");
        helpMenu.setText("Help");
        helpMenu.setFont(toolsMenu.getFont());
        aboutMenuItem.setText("About");
        exitMenuItem.setText("Exit");
        popup.add(createMenuItem("Missing words", "missingwordserrors"));
        missingWordsMenuItem.setText("Missing Words");
        popup.add(createMenuItem("Article errors", "articleerrors"));
        articleErrorMenuItem.setText("Article Errors");
        popup.add(createMenuItem("Conjuction errors", "conjunctionerrors"));
        conjunctionMenuItem.setText("Conjunction errors");
        popup.add(createMenuItem("Grammar errors", "grammarerrors"));
        grammarMenuItem.setText("Grammah errors");
        popup.add(createMenuItem("Punctuation errors", "punctuationerrors"));
        punctuationErrorMenuItem.setText("Punctuation errors");
        popup.add(createMenuItem("Part of speech errors", "partofspeecherrors"));
        partOfSpeechErrorsMenuItem.setText("Part of speech");
        popup.add(createMenuItem("Preposition errors", "prepositionerrors"));
        prepositionErrorsMenuItem.setText("Preposition errors");
        popup.add(createMenuItem("Register/Style errors", "registerstyleerrors"));
        registerStyleErrorsMenuItem.setText("Register/Style errors");
        popup.add(createMenuItem("Spelling errors", "spellingerrors"));
        spellingErrorsMenuItem.setText("Spelling errors");
        popup.add(createMenuItem("Subject/verb errors", "subjectverberrors"));
        subjectVerbErrorsMenuItem.setText("Subkect/verb errors");
        popup.add(createMenuItem("Tense errors", "tenseerrors"));
        tenseErrorsMenuItem.setText("Tense errors");
        popup.add(createMenuItem("Vocabulary errors", "vocabularyerrors"));
        vocabularyErrorsMenuItem.setText("Vocabulary errors");
        popup.add(createMenuItem("Word order errors", "wordordererrors"));
        wordOrderErrorsMenuitem.setText("Word order errors");
        popup.add(createMenuItem("Wrong word errors", "wrongworderrors"));
        wrongWordOrderMenuItem.setText("Wrong word order errors");
        popup.addSeparator();
        popup.add(createMenuItem("Combo marker tools", "combo"));
        comboOptionsMenuItem.setText("Combo marker tools");
        popup.addSeparator();
        popup.add(createMenuItem("Remove highlight", "removehighlight"));
        removeHighlightMenuItem.setText("Remove highlight");
        popup.addSeparator();
        popup.add(createMenuItem("Insert Note", "insertnote"));
        insertNoteMenuItem.setText("Insert note");

        essayField.addMouseListener(new MouseAdapter() {

            @Override
            public void mouseClicked(MouseEvent e) {
                if (e.getButton() == MouseEvent.BUTTON3) {
                    popup.show(essayField, e.getX(), e.getY());
                }
            }
        });
    }

    /**
     * creates a menu item, and hooks an actionlistener to it
     * @param txt
     * @param action
     * @return
     */
    private JMenuItem createMenuItem(String txt, String action) {
        JMenuItem item = new JMenuItem(txt);
        item.setActionCommand(action);
        item.addActionListener(this);
        return item;
    }

    /**
     * t
     */
    class EssayHighlightPainter extends DefaultHighlighter.DefaultHighlightPainter {

        private int start;
        private int end;
        private int code;

        public EssayHighlightPainter(Color color, int start, int end, int code) {

            super(color);
            this.start = start;
            this.end = end;
            this.code = code;
        }

        public int getCode() {
            return code;
        }

        public int getEnd() {
            return end;
        }

        public int getStart() {
            return start;
        }
    }

    class ResultsPanel extends JPanel {

        public ResultsPanel() {
            setBackground(Color.WHITE);
        }

        @Override
        public void paintComponent(Graphics g) {
            Graphics2D g2 = (Graphics2D) g;
            g2.setColor(Color.WHITE);
            g2.fillRect(0, 0, getWidth(), getHeight());
            int y = 50;
            g2.setColor(new Color(0, 131, 0));
            g2.setFont(new Font("Dialog", 1, 18));
            g2.drawString("Summary", 50, y);
            g2.setFont(new Font("Dialog", 1, 12));
            y += 30;
            if (missingWords > 0) {
                g2.setColor(HighlightColors.MISSING_WORDS);
                g2.drawString("Missing words: ", 50, y);
                g2.drawString("" + missingWords, 200, y);
                y += 30;
            }
            if (articleErrors > 0) {
                g2.setColor(HighlightColors.ARTICLE_ERRORS);
                g2.drawString("Article errors: ", 50, y);
                g2.drawString("" + articleErrors, 200, y);
                y += 30;
            }
            if (conjunctionErrors > 0) {
                g2.setColor(HighlightColors.CONJUNCTION_ERRORS);
                g2.drawString("Conjunction errors: ", 50, y);
                g2.drawString("" + conjunctionErrors, 200, y);
                y += 30;
            }
            if (grammarErrors > 0) {
                g2.setColor(HighlightColors.GRAMMAR_ERRORS);
                g2.drawString("Grammar errors: ", 50, y);
                g2.drawString("" + grammarErrors, 200, y);
                y += 30;
            }
            if (punctuationErrors > 0) {
                g2.setColor(HighlightColors.PUNCTUATION_ERRORS);
                g2.drawString("Punctuation errors: ", 50, y);
                g2.drawString("" + punctuationErrors, 200, y);
                y += 30;
            }
            if (partOfSpeechErrors > 0) {
                g2.setColor(HighlightColors.PART_OF_SPEECH_ERRORS);
                g2.drawString("Part of speech errors: ", 50, y);
                g2.drawString("" + partOfSpeechErrors, 200, y);
                y += 30;
            }
            if (prepositionErrors > 0) {
                g2.setColor(HighlightColors.PREPOSITION_ERRORS);
                g2.drawString("Preposition errors: ", 50, y);
                g2.drawString("" + prepositionErrors, 200, y);
                y += 30;
            }
            if (registerStyleErrors > 0) {
                g2.setColor(HighlightColors.REGISTER_STYLE_ERRORS);
                g2.drawString("Register/Style errors: ", 50, y);
                g2.drawString("" + registerStyleErrors, 200, y);
                y += 30;
            }

            if (spellingErrors > 0) {
                g2.setColor(HighlightColors.SPELLING_ERRORS);
                g2.drawString("Spelling errors: ", 50, y);
                g2.drawString("" + spellingErrors, 200, y);
                y += 30;
            }

            if (subjectVerbErrors > 0) {
                g2.setColor(HighlightColors.SUBJECT_VERB_ERRORS);
                g2.drawString("Subject verb errors: ", 50, y);
                g2.drawString("" + subjectVerbErrors, 200, y);
                y += 30;
            }
            if (tenseErrors > 0) {
                g2.setColor(HighlightColors.TENSE_ERRORS);
                g2.drawString("Tense errors: ", 50, y);
                g2.drawString("" + tenseErrors, 200, y);
                y += 30;
            }
            if (vocubularyErrors > 0) {
                g2.setColor(HighlightColors.VOCABULARY_ERRORS);
                g2.drawString("Vocabulary errors: ", 50, y);
                g2.drawString("" + vocubularyErrors, 200, y);
                y += 30;
            }
            if (wordOrderErrors > 0) {
                g2.setColor(HighlightColors.WORD_ORDER_ERRORS);
                g2.drawString("Word order errors: ", 50, y);
                g2.drawString("" + wordOrderErrors, 200, y);
                y += 30;
            }
            if (wrongWordErrors > 0) {
                g2.setColor(HighlightColors.WRONG_WORD_ERRORS);
                g2.drawString("Wrong word errors: ", 50, y);
                g2.drawString("" + wrongWordErrors, 200, y);
                y += 30;
            }
        }
    }

    public EssayEditor getEssayField() {
        return essayField;
    }

    public JTextArea getNotesField() {
        return notesField;
    }

    /**
     * Displays the combo window to allow applying more than one tools at
     * same time
     */
    private void showComboMarkerTools() {
        ComboMarkerTools comboMarkerTools = new ComboMarkerTools(null, false, this);
        comboMarkerTools.setSize(ss.width / 2, ss.height / 2);
        comboMarkerTools.setLocationRelativeTo(this);
        comboMarkerTools.setVisible(true);
    }

    private void insertNote() {
        try {
            if (essayField.getCaretPosition() < 0) {
                JOptionPane.showMessageDialog(null, "Put the cursor against the text you wish to insert a note");
                return;
            }
            NoteFrame fr = new NoteFrame(null, true, this);
            fr.setSize(400, 300);
            fr.setLocationRelativeTo(this);
            fr.setVisible(true);
        } catch (Exception ex) {
            ex.printStackTrace();
        }
    }

    /** This method is called from within the constructor to
     * initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is
     * always regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        mainPanel = new javax.swing.JPanel();
        leftPanel = new javax.swing.JPanel();
        topPanel = new javax.swing.JPanel();
        jLabel1 = new javax.swing.JLabel();
        jLabel2 = new javax.swing.JLabel();
        jPanel1 = new javax.swing.JPanel();
        finishButton = new javax.swing.JButton();
        saveButton = new javax.swing.JButton();
        bottomPanel = new javax.swing.JPanel();
        splitPane = new javax.swing.JSplitPane();
        rightPanel = new javax.swing.JPanel();
        leftBottomPanel = new javax.swing.JPanel();
        jLabel3 = new javax.swing.JLabel();
        gradeField = new javax.swing.JTextField();
        resultsPanel = new ResultsPanel();
        jScrollPane1 = new javax.swing.JScrollPane();
        notesField = new javax.swing.JTextArea();
        menuBar = new javax.swing.JMenuBar();

        toolsMenu = new javax.swing.JMenu();
        missingWordsMenuItem = new javax.swing.JMenuItem();
        articleErrorMenuItem = new javax.swing.JMenuItem();
        conjunctionMenuItem = new javax.swing.JMenuItem();
        grammarMenuItem = new javax.swing.JMenuItem();
        punctuationErrorMenuItem = new javax.swing.JMenuItem();
        partOfSpeechErrorsMenuItem = new javax.swing.JMenuItem();
        prepositionErrorsMenuItem = new javax.swing.JMenuItem();
        registerStyleErrorsMenuItem = new javax.swing.JMenuItem();
        spellingErrorsMenuItem = new javax.swing.JMenuItem();
        subjectVerbErrorsMenuItem = new javax.swing.JMenuItem();
        tenseErrorsMenuItem = new javax.swing.JMenuItem();
        vocabularyErrorsMenuItem = new javax.swing.JMenuItem();
        wordOrderErrorsMenuitem = new javax.swing.JMenuItem();
        wrongWordOrderMenuItem = new javax.swing.JMenuItem();
        jSeparator2 = new javax.swing.JSeparator();
        comboOptionsMenuItem = new javax.swing.JMenuItem();
        jSeparator1 = new javax.swing.JSeparator();
        removeHighlightMenuItem = new javax.swing.JMenuItem();
        insertMenu = new javax.swing.JMenu();
        insertNoteMenuItem = new javax.swing.JMenuItem();

        statusPanel = new javax.swing.JPanel();
        javax.swing.JSeparator statusPanelSeparator = new javax.swing.JSeparator();
        statusMessageLabel = new javax.swing.JLabel();
        statusAnimationLabel = new javax.swing.JLabel();
        progressBar = new javax.swing.JProgressBar();

        mainPanel.setName("mainPanel"); // NOI18N
        mainPanel.setLayout(new java.awt.BorderLayout());

        leftPanel.setName("leftPanel"); // NOI18N
        leftPanel.setLayout(new java.awt.BorderLayout());
        mainPanel.add(leftPanel, java.awt.BorderLayout.LINE_START);

        topPanel.setName("topPanel"); // NOI18N

        jLabel1.setName("jLabel1"); // NOI18N

        jLabel2.setName("jLabel2"); // NOI18N

        jPanel1.setName("jPanel1"); // NOI18N

        finishButton.setName("finishButton"); // NOI18N

        saveButton.setName("saveButton"); // NOI18N

        org.jdesktop.layout.GroupLayout topPanelLayout = new org.jdesktop.layout.GroupLayout(topPanel);
        topPanel.setLayout(topPanelLayout);
        topPanelLayout.setHorizontalGroup(
            topPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(topPanelLayout.createSequentialGroup()
                .addContainerGap()
                .add(topPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.TRAILING)
                    .add(topPanelLayout.createSequentialGroup()
                        .add(topPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
                            .add(jLabel1)
                            .add(jLabel2))
                        .add(34, 34, 34))
                    .add(topPanelLayout.createSequentialGroup()
                        .add(saveButton)
                        .addPreferredGap(org.jdesktop.layout.LayoutStyle.UNRELATED)))
                .add(topPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
                    .add(topPanelLayout.createSequentialGroup()
                        .add(40, 40, 40)
                        .add(jPanel1, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE))
                    .add(topPanelLayout.createSequentialGroup()
                        .addPreferredGap(org.jdesktop.layout.LayoutStyle.UNRELATED)
                        .add(finishButton, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, 92, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)))
                .addContainerGap(315, Short.MAX_VALUE))
        );
        topPanelLayout.setVerticalGroup(
            topPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(topPanelLayout.createSequentialGroup()
                .add(topPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
                    .add(topPanelLayout.createSequentialGroup()
                        .add(jLabel1)
                        .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED)
                        .add(jLabel2))
                    .add(topPanelLayout.createSequentialGroup()
                        .add(jPanel1, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)
                        .addPreferredGap(org.jdesktop.layout.LayoutStyle.UNRELATED)
                        .add(topPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.BASELINE)
                            .add(finishButton)
                            .add(saveButton))))
                .addContainerGap(14, Short.MAX_VALUE))
        );

        mainPanel.add(topPanel, java.awt.BorderLayout.PAGE_START);

        bottomPanel.setName("bottomPanel"); // NOI18N
        bottomPanel.setLayout(new java.awt.BorderLayout());
        mainPanel.add(bottomPanel, java.awt.BorderLayout.PAGE_END);

        splitPane.setDividerLocation(400);
        splitPane.setName("splitPane"); // NOI18N

        rightPanel.setBorder(javax.swing.BorderFactory.createTitledBorder("Results"));
        rightPanel.setName("rightPanel"); // NOI18N
        rightPanel.setLayout(new java.awt.BorderLayout());

        leftBottomPanel.setName("leftBottomPanel"); // NOI18N

        jLabel3.setName("jLabel3"); // NOI18N
        leftBottomPanel.add(jLabel3);

        gradeField.setColumns(10);
        gradeField.setName("gradeField"); // NOI18N
        leftBottomPanel.add(gradeField);

        rightPanel.add(leftBottomPanel, java.awt.BorderLayout.PAGE_END);

        resultsPanel.setName("resultsPanel"); // NOI18N
        resultsPanel.setLayout(new java.awt.BorderLayout());

        jScrollPane1.setName("jScrollPane1"); // NOI18N

        notesField.setColumns(20);
        notesField.setLineWrap(true);
        notesField.setRows(5);
        notesField.setName("notesField"); // NOI18N
        notesField.setPreferredSize(new java.awt.Dimension(220, 275));
        jScrollPane1.setViewportView(notesField);

        resultsPanel.add(jScrollPane1, java.awt.BorderLayout.PAGE_END);

        rightPanel.add(resultsPanel, java.awt.BorderLayout.CENTER);

        splitPane.setRightComponent(rightPanel);

        mainPanel.add(splitPane, java.awt.BorderLayout.CENTER);

        menuBar.setName("menuBar"); // NOI18N

  
        exitMenuItem.setName("exitMenuItem"); // NOI18N
        exitMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                exitMenuItemActionPerformed(evt);
            }
        });
        fileMenu.add(exitMenuItem);

        menuBar.add(fileMenu);
     toolsMenu.setName("toolsMenu"); // NOI18N

        missingWordsMenuItem.setName("missingWordsMenuItem"); // NOI18N
        missingWordsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                missingWordsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(missingWordsMenuItem);

        articleErrorMenuItem.setName("articleErrorMenuItem"); // NOI18N
        articleErrorMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                articleErrorMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(articleErrorMenuItem);

        conjunctionMenuItem.setName("conjunctionMenuItem"); // NOI18N
        conjunctionMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                conjunctionMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(conjunctionMenuItem);

        grammarMenuItem.setName("grammarMenuItem"); // NOI18N
        grammarMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                grammarMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(grammarMenuItem);

        punctuationErrorMenuItem.setName("punctuationErrorMenuItem"); // NOI18N
        punctuationErrorMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                punctuationErrorMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(punctuationErrorMenuItem);

        partOfSpeechErrorsMenuItem.setName("partOfSpeechErrorsMenuItem"); // NOI18N
        partOfSpeechErrorsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                partOfSpeechErrorsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(partOfSpeechErrorsMenuItem);

        prepositionErrorsMenuItem.setName("prepositionErrorsMenuItem"); // NOI18N
        prepositionErrorsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                prepositionErrorsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(prepositionErrorsMenuItem);

        registerStyleErrorsMenuItem.setName("registerStyleErrorsMenuItem"); // NOI18N
        registerStyleErrorsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                registerStyleErrorsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(registerStyleErrorsMenuItem);

        spellingErrorsMenuItem.setName("spellingErrorsMenuItem"); // NOI18N
        spellingErrorsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                spellingErrorsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(spellingErrorsMenuItem);

        subjectVerbErrorsMenuItem.setName("subjectVerbErrorsMenuItem"); // NOI18N
        subjectVerbErrorsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                subjectVerbErrorsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(subjectVerbErrorsMenuItem);

        tenseErrorsMenuItem.setName("tenseErrorsMenuItem"); // NOI18N
        tenseErrorsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                tenseErrorsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(tenseErrorsMenuItem);

        vocabularyErrorsMenuItem.setName("vocabularyErrorsMenuItem"); // NOI18N
        vocabularyErrorsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                vocabularyErrorsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(vocabularyErrorsMenuItem);

        wordOrderErrorsMenuitem.setName("wordOrderErrorsMenuitem"); // NOI18N
        wordOrderErrorsMenuitem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                wordOrderErrorsMenuitemActionPerformed(evt);
            }
        });
        toolsMenu.add(wordOrderErrorsMenuitem);

        wrongWordOrderMenuItem.setName("wrongWordOrderMenuItem"); // NOI18N
        wrongWordOrderMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                wrongWordOrderMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(wrongWordOrderMenuItem);

        jSeparator2.setName("jSeparator2"); // NOI18N
        toolsMenu.add(jSeparator2);

        comboOptionsMenuItem.setName("comboOptionsMenuItem"); // NOI18N
        comboOptionsMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                comboOptionsMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(comboOptionsMenuItem);

        jSeparator1.setName("jSeparator1"); // NOI18N
        toolsMenu.add(jSeparator1);

        removeHighlightMenuItem.setName("removeHighlightMenuItem"); // NOI18N
        removeHighlightMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                removeHighlightMenuItemActionPerformed(evt);
            }
        });
        toolsMenu.add(removeHighlightMenuItem);

        menuBar.add(toolsMenu);


        insertMenu.setName("insertMenu"); // NOI18N

       
        insertNoteMenuItem.setName("insertNoteMenuItem"); // NOI18N
        insertNoteMenuItem.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                insertNoteMenuItemActionPerformed(evt);
            }
        });
        insertMenu.add(insertNoteMenuItem);

        menuBar.add(insertMenu);

        aboutMenuItem.setName("aboutMenuItem"); // NOI18N
        helpMenu.add(aboutMenuItem);

        menuBar.add(helpMenu);

        statusPanel.setName("statusPanel"); // NOI18N

        statusPanelSeparator.setName("statusPanelSeparator"); // NOI18N

        statusMessageLabel.setName("statusMessageLabel"); // NOI18N

        statusAnimationLabel.setHorizontalAlignment(javax.swing.SwingConstants.LEFT);
        statusAnimationLabel.setName("statusAnimationLabel"); // NOI18N

        progressBar.setName("progressBar"); // NOI18N

        org.jdesktop.layout.GroupLayout statusPanelLayout = new org.jdesktop.layout.GroupLayout(statusPanel);
        statusPanel.setLayout(statusPanelLayout);
        statusPanelLayout.setHorizontalGroup(
            statusPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(statusPanelSeparator, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, 733, Short.MAX_VALUE)
            .add(statusPanelLayout.createSequentialGroup()
                .addContainerGap()
                .add(statusMessageLabel)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED, 549, Short.MAX_VALUE)
                .add(progressBar, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED)
                .add(statusAnimationLabel)
                .addContainerGap())
        );
        statusPanelLayout.setVerticalGroup(
            statusPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.LEADING)
            .add(statusPanelLayout.createSequentialGroup()
                .add(statusPanelSeparator, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, 2, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(org.jdesktop.layout.LayoutStyle.RELATED, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                .add(statusPanelLayout.createParallelGroup(org.jdesktop.layout.GroupLayout.BASELINE)
                    .add(statusMessageLabel)
                    .add(statusAnimationLabel)
                    .add(progressBar, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE, org.jdesktop.layout.GroupLayout.DEFAULT_SIZE, org.jdesktop.layout.GroupLayout.PREFERRED_SIZE))
                .add(3, 3, 3))
        );
    }// </editor-fold>//GEN-END:initComponents

    private void missingWordsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_missingWordsMenuItemActionPerformed
        highLight(EssayErrorCodes.MISSING_WORDS, HighlightColors.MISSING_WORDS);
    }//GEN-LAST:event_missingWordsMenuItemActionPerformed

    private void exitMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_exitMenuItemActionPerformed
        System.exit(0);
    }//GEN-LAST:event_exitMenuItemActionPerformed

    private void articleErrorMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_articleErrorMenuItemActionPerformed
        highLight(EssayErrorCodes.ARTICLE_ERRORS, HighlightColors.ARTICLE_ERRORS);
    }//GEN-LAST:event_articleErrorMenuItemActionPerformed

    private void removeHighlightMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_removeHighlightMenuItemActionPerformed
        removeHighlights();
    }//GEN-LAST:event_removeHighlightMenuItemActionPerformed

    private void conjunctionMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_conjunctionMenuItemActionPerformed
        highLight(EssayErrorCodes.CONJUNCTION_ERRORS, HighlightColors.CONJUNCTION_ERRORS);
    }//GEN-LAST:event_conjunctionMenuItemActionPerformed

    private void grammarMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_grammarMenuItemActionPerformed
        highLight(EssayErrorCodes.GRAMMAR_ERRORS, HighlightColors.GRAMMAR_ERRORS);
    }//GEN-LAST:event_grammarMenuItemActionPerformed

    private void punctuationErrorMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_punctuationErrorMenuItemActionPerformed
        highLight(EssayErrorCodes.PUNCTUATION_ERRORS, HighlightColors.PUNCTUATION_ERRORS);
    }//GEN-LAST:event_punctuationErrorMenuItemActionPerformed

    private void partOfSpeechErrorsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_partOfSpeechErrorsMenuItemActionPerformed
        highLight(EssayErrorCodes.PART_OF_SPEECH_ERRORS, HighlightColors.PART_OF_SPEECH_ERRORS);
    }//GEN-LAST:event_partOfSpeechErrorsMenuItemActionPerformed

    private void prepositionErrorsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_prepositionErrorsMenuItemActionPerformed
        highLight(EssayErrorCodes.PREPOSITION_ERRORS, HighlightColors.PREPOSITION_ERRORS);
    }//GEN-LAST:event_prepositionErrorsMenuItemActionPerformed

    private void registerStyleErrorsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_registerStyleErrorsMenuItemActionPerformed
        highLight(EssayErrorCodes.REGISTER_STYLE_ERRORS, HighlightColors.REGISTER_STYLE_ERRORS);
    }//GEN-LAST:event_registerStyleErrorsMenuItemActionPerformed

    private void spellingErrorsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_spellingErrorsMenuItemActionPerformed
        highLight(EssayErrorCodes.SPELLING_ERRORS, HighlightColors.SPELLING_ERRORS);
    }//GEN-LAST:event_spellingErrorsMenuItemActionPerformed

    private void subjectVerbErrorsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_subjectVerbErrorsMenuItemActionPerformed
        highLight(EssayErrorCodes.SUBJECT_VERB_ERRORS, HighlightColors.SUBJECT_VERB_ERRORS);
    }//GEN-LAST:event_subjectVerbErrorsMenuItemActionPerformed

    private void tenseErrorsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_tenseErrorsMenuItemActionPerformed
        highLight(EssayErrorCodes.TENSE_ERRORS, HighlightColors.TENSE_ERRORS);
    }//GEN-LAST:event_tenseErrorsMenuItemActionPerformed

    private void vocabularyErrorsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_vocabularyErrorsMenuItemActionPerformed
        highLight(EssayErrorCodes.VOCABULARY_ERRORS, HighlightColors.VOCABULARY_ERRORS);
    }//GEN-LAST:event_vocabularyErrorsMenuItemActionPerformed

    private void wordOrderErrorsMenuitemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_wordOrderErrorsMenuitemActionPerformed
        highLight(EssayErrorCodes.WORD_ORDER_ERRORS, HighlightColors.WORD_ORDER_ERRORS);
    }//GEN-LAST:event_wordOrderErrorsMenuitemActionPerformed

    private void wrongWordOrderMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_wrongWordOrderMenuItemActionPerformed
        highLight(EssayErrorCodes.WRONG_WORD_ERRORS, HighlightColors.WRONG_WORD_ERRORS);
    }//GEN-LAST:event_wrongWordOrderMenuItemActionPerformed

    private void comboOptionsMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_comboOptionsMenuItemActionPerformed
        showComboMarkerTools();

    }//GEN-LAST:event_comboOptionsMenuItemActionPerformed

    private void insertNoteMenuItemActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_insertNoteMenuItemActionPerformed
        insertNote();
    }//GEN-LAST:event_insertNoteMenuItemActionPerformed
    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JMenuItem articleErrorMenuItem;
    private javax.swing.JPanel bottomPanel;
    private javax.swing.JMenuItem comboOptionsMenuItem;
    private javax.swing.JMenuItem conjunctionMenuItem;
    private javax.swing.JButton finishButton;
    private javax.swing.JTextField gradeField;
    private javax.swing.JMenuItem grammarMenuItem;
    private javax.swing.JMenu insertMenu;
    private javax.swing.JMenuItem insertNoteMenuItem;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JLabel jLabel3;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JSeparator jSeparator1;
    private javax.swing.JSeparator jSeparator2;
    private javax.swing.JPanel leftBottomPanel;
    private javax.swing.JPanel leftPanel;
    private javax.swing.JPanel mainPanel;
    private javax.swing.JMenuBar menuBar;
    private javax.swing.JMenuItem missingWordsMenuItem;
    private javax.swing.JTextArea notesField;
    private javax.swing.JMenuItem partOfSpeechErrorsMenuItem;
    private javax.swing.JMenuItem prepositionErrorsMenuItem;
    private javax.swing.JProgressBar progressBar;
    private javax.swing.JMenuItem punctuationErrorMenuItem;
    private javax.swing.JMenuItem registerStyleErrorsMenuItem;
    private javax.swing.JMenuItem removeHighlightMenuItem;
    private javax.swing.JPanel resultsPanel;
    private javax.swing.JPanel rightPanel;
    private javax.swing.JButton saveButton;
    private javax.swing.JMenuItem spellingErrorsMenuItem;
    private javax.swing.JSplitPane splitPane;
    private javax.swing.JLabel statusAnimationLabel;
    private javax.swing.JLabel statusMessageLabel;
    private javax.swing.JPanel statusPanel;
    private javax.swing.JMenuItem subjectVerbErrorsMenuItem;
    private javax.swing.JMenuItem tenseErrorsMenuItem;
    private javax.swing.JMenu toolsMenu;
    private javax.swing.JPanel topPanel;
    private javax.swing.JMenuItem vocabularyErrorsMenuItem;
    private javax.swing.JMenuItem wordOrderErrorsMenuitem;
    private javax.swing.JMenuItem wrongWordOrderMenuItem;
    // End of variables declaration//GEN-END:variables
    private JDialog aboutBox;
    javax.swing.JMenu fileMenu = new javax.swing.JMenu("File");
    javax.swing.JMenuItem exitMenuItem = new javax.swing.JMenuItem("Exit");
    javax.swing.JMenu helpMenu = new javax.swing.JMenu("Help");
    javax.swing.JMenuItem aboutMenuItem = new javax.swing.JMenuItem("About");
}
