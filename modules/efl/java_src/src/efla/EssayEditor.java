/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package efla;

import java.awt.Color;
import java.awt.GradientPaint;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Rectangle;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.awt.font.FontRenderContext;
import java.awt.font.LineBreakMeasurer;
import java.awt.font.TextLayout;
import java.text.AttributedCharacterIterator;
import java.text.AttributedString;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import javax.swing.JTextPane;
import javax.swing.text.DefaultStyledDocument;

/**
 *
 * @author kim
 */
public class EssayEditor extends JTextPane {

    private ArrayList<Note> notes = new ArrayList<Note>();
    private ArrayList<Long> notesIds = new ArrayList<Long>();
    private EflaView eflaView;
    private LineBreakMeasurer lineMeasurer;
    // index of the first character in the paragraph.
    private int paragraphStart;
    // index of the first character after the end of the paragraph.
    private int paragraphEnd;
    int yValue;

    public EssayEditor(EflaView eflaView, DefaultStyledDocument doc) {
        super(doc);
        this.eflaView = eflaView;
        addMouseListener(new MMouseListener());

    }

    public void addNote(Rectangle rect, String text) {
        long id = System.currentTimeMillis();
        notesIds.add(id);
        notes.add(new Note(rect, text, id));
        repaint();
    }

    class MMouseListener extends MouseAdapter {

        @Override
        public void mouseClicked(MouseEvent e) {
            boolean notefound = false;
            for (Note note : notes) {
                Rectangle clickArea = new Rectangle(note.getRect().x - 5, note.getRect().y + note.getRect().height, 10, 10);
                if (clickArea.contains(e.getPoint())) {
                    eflaView.getNotesField().setText(note.getText());
                    EssayEditor.this.repaint();
                }
            }
            if (!notefound) {
                EssayEditor.this.repaint();
            }
        }
    }

    @Override
    public void paintComponent(Graphics g) {
        super.paintComponent(g);

        Graphics2D g2 = (Graphics2D) g;
        //g2.setColor(new Color(230, 230, 230));
        GradientPaint gp = new GradientPaint(getWidth() - 200, 0, Color.WHITE, 200, getHeight(), new Color(255, 255, 0, 100));
        g2.setPaint(gp);
        g2.fillRect(getWidth() - 200, 0, 200, getHeight());
        Object[] ids = notesIds.toArray();

        Arrays.sort(ids);
        Collections.sort(notes);
        //sort them notes out first
        int lastY = 0;

        for (Note note : notes) {
            //Note note = getNote((Long) id);
            if (note != null) {

                int currentY = note.getRect().y + 20;

                if (currentY <= lastY) {
                    currentY = lastY + 15;
                }
                g2.setColor(Color.RED);
                // g2.drawLine(line.x, line.y + line.height, getWidth(), line.y + line.height);
                //firt point is the apex
                int[] xPoints = {note.getRect().x, note.getRect().x - 5, note.getRect().x + 5};
                int[] yPoints = {note.getRect().y + 10, note.getRect().y + 20, note.getRect().y + 20};
                g2.fillPolygon(xPoints, yPoints, 3);

                g2.setColor(new Color(244, 255, 182));
                g2.fillRoundRect(getWidth() - 200, currentY - 15, 200, 100, 10, 10);
                g2.setColor(new Color(198, 146, 0));
                g2.drawRoundRect(getWidth() - 200, currentY - 15, 200, 100, 10, 10);
                g2.drawLine(note.getRect().x, note.getRect().y + 20, note.getRect().x, currentY);
                g2.drawLine(note.getRect().x, currentY, getWidth() - 200, currentY);
                g2.setColor(Color.BLACK);
                //g2.drawString(note.getText(), getWidth() - 195, currentY + 50);
                yValue=currentY + 50;
                drawText(note.getText(), g2);
                lastY = currentY + 100;
            }

        }
    }

    private void drawText(String text, Graphics2D g2d) {
        AttributedString atr = new AttributedString(text);
        // Create a new LineBreakMeasurer from the paragraph.
        // It will be cached and re-used.
        if (lineMeasurer == null) {
            AttributedCharacterIterator paragraph = atr.getIterator();
            paragraphStart = paragraph.getBeginIndex();
            paragraphEnd = paragraph.getEndIndex();
            FontRenderContext frc = g2d.getFontRenderContext();
            lineMeasurer = new LineBreakMeasurer(paragraph, frc);
        }

        // Set break width to width of Component.
        
        float breakWidth = (float) getSize().width;
        float drawPosY = yValue;
        // Set position to the index of the first character in the paragraph.
        lineMeasurer.setPosition(paragraphStart);

        // Get lines until the entire paragraph has been displayed.
        while (lineMeasurer.getPosition() < paragraphEnd) {

            // Retrieve next layout. A cleverer program would also cache
            // these layouts until the component is re-sized.
            TextLayout layout = lineMeasurer.nextLayout(breakWidth);

            // Compute pen x position. If the paragraph is right-to-left we
            // will align the TextLayouts to the right edge of the panel.
            // Note: this won't occur for the English text in this sample.
            // Note: drawPosX is always where the LEFT of the text is placed.
            int xx=getWidth() - 195;
            float drawPosX = layout.isLeftToRight()
                    ? xx :xx+ breakWidth - layout.getAdvance();

            // Move y-coordinate by the ascent of the layout.
            drawPosY += layout.getAscent();

            // Draw the TextLayout at (drawPosX, drawPosY).
            layout.draw(g2d, drawPosX, drawPosY);

            // Move y-coordinate in preparation for next layout.
            drawPosY += layout.getDescent() + layout.getLeading();
            yValue+=20;
        }
    }

    private Note getNote(long id) {
        for (Note note : notes) {
            if (note.getId() == id) {
                return note;
            }
        }
        return null;
    }

    class Note implements Comparable<Note> {

        private Rectangle rect;
        private String text;
        private long id;

        public Note(Rectangle rect, String text, long id) {
            this.rect = rect;
            this.text = text;
            this.id = id;
        }

        public long getId() {
            return id;
        }

        public int compareTo(Note note) {
            int result = 0;
            if (rect.getY() < note.getRect().getY()) {
                result = -1;
            }

            if (rect.getY() > note.getRect().getY()) {
                result = 1;
            }
            return result;
        }

        public Rectangle getRect() {
            return rect;
        }

        public void setRect(Rectangle rect) {
            this.rect = rect;
        }

        public String getText() {
            return text;
        }

        public void setText(String text) {
            this.text = text;
        }
    }
}
