/*!
 * Ext JS Library 3.0.3
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * Greek translation
 * By thesilentman (utf8 encoding)
 * 27 Apr 2008
 *
 * Changes since previous (second) Version:
 * + added Date.shortMonthNames 
 * + added Date.getShortMonthName 
 * + added Date.monthNumbers
 * + added Ext.grid.GroupingView
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Μεταφό�?τωση δεδομένων...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
   Ext.grid.GridPanel.prototype.ddText = "{0} Επιλεγμένες σει�?ές";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Κλείστε το tab";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Το πε�?ιεχόμενο του πεδίου δεν είναι αποδεκτό";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "Μεταφό�?τωση δεδομένων...";
}

Date.monthNames = [
   "Ιανουά�?ιος",
   "Φεβ�?ουά�?ιος",
   "Μά�?τιος",
   "Απ�?ίλιος",
   "Μάιος",
   "Ιο�?νιος",
   "Ιο�?λιος",
   "Α�?γουστος",
   "Σεπτέμβ�?ιος",
   "Οκτώβ�?ιος",
   "�?οέμβ�?ιος",
   "Δεκέμβ�?ιος"
];

Date.shortMonthNames = [
   "Ιαν",
   "Φεβ",
   "Μά�?",
   "Απ�?",
   "Μάι",
   "Ιο�?",
   "Ιο�?",
   "Α�?γ",
   "Σεπ",
   "Οκτ",
   "�?οέ",
   "Δεκ"
];

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  Jan : 0,
  Feb : 1,
  Mar : 2,
  Apr : 3,
  May : 4,
  Jun : 5,
  Jul : 6,
  Aug : 7,
  Sep : 8,
  Oct : 9,
  Nov : 10,
  Dec : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};


Date.dayNames = [
   "Κυ�?ιακή",
   "Δευτέ�?α",
   "Τ�?ίτη",
   "Τετά�?τη",
   "Πέμπτη",
   "Πα�?ασκευή",
   "Σάββατο"
];

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Άκυ�?ο",
      yes    : "�?αι",
      no     : "Όχι"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "d/m/Y");
   };
}

if(Ext.DatePicker){
   Ext.apply(Ext.DatePicker.prototype, {
      todayText         : "Σήμε�?α",
      minText           : "Η Ημε�?ομηνία είναι π�?ογενέστε�?η από την παλαιότε�?η αποδεκτή",
      maxText           : "Η Ημε�?ομηνία είναι μεταγενέστε�?η από την νεότε�?η αποδεκτή",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames  : Date.monthNames,
      dayNames    : Date.dayNames,
      nextText          : 'Επόμενος Μήνας (Control+Δεξί Βέλος)',
      prevText          : 'Π�?οηγο�?μενος Μήνας (Control + Α�?ιστε�?ό Βέλος)',
      monthYearText     : 'Επιλογή Μηνός (Control + Επάνω/Κάτω Βέλος για μεταβολή ετών)',
      todayTip          : "{0} (ΠΛήκτ�?ο Διαστήματος)",
      format            : "d/m/y"
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Σελίδα",
      afterPageText  : "από {0}",
      firstText      : "Π�?ώτη Σελίδα",
      prevText       : "Π�?οηγο�?μενη Σελίδα",
      nextText       : "Επόμενη Σελίδα",
      lastText       : "Τελευταία Σελίδα",
      refreshText    : "Ανανέωση",
      displayMsg     : "Εμφάνιση {0} - {1} από {2}",
      emptyMsg       : 'Δεν υπά�?χουν δεδομένα'
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Το μικ�?ότε�?ο αποδεκτό μήκος για το πεδίο είναι {0}",
      maxLengthText : "Το μεγαλ�?τε�?ο αποδεκτό μήκος για το πεδίο είναι {0}",
      blankText     : "Το πεδίο είναι υποχ�?εωτικό",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Η μικ�?ότε�?η τιμή του πεδίου είναι {0}",
      maxText : "Η μεγαλ�?τε�?η τιμή του πεδίου είναι {0}",
      nanText : "{0} δεν είναι αποδεκτός α�?ιθμός"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Ανενε�?γό",
      disabledDatesText : "Ανενε�?γό",
      minText           : "Η ημε�?ομηνία αυτο�? του πεδίου π�?έπει να είναι μετά την {0}",
      maxText           : "Η ημε�?ομηνία αυτο�? του πεδίου π�?έπει να είναι π�?ιν την {0}",
      invalidText       : "{0} δεν είναι έγκυ�?η ημε�?ομηνία - π�?έπει να είναι στη μο�?φή {1}",
      format            : "d/m/y"
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Μεταφό�?τωση δεδομένων...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Το πεδίο δέχεται μόνο διευθ�?νσεις Email σε μο�?φή "user@example.com"',
      urlText      : 'Το πεδίο δέχεται μόνο URL σε μο�?φή "http:/'+'/www.example.com"',
      alphaText    : 'Το πεδίο δέχεται μόνο χα�?ακτή�?ες και _',
      alphanumText : 'Το πεδίο δέχεται μόνο χα�?ακτή�?ες, α�?ιθμο�?ς και _'
   });
}

if(Ext.form.HtmlEditor){
   Ext.apply(Ext.form.HtmlEditor.prototype, {
        createLinkText : 'Δώστε τη διε�?θυνση (URL) για το σ�?νδεσμο (link):',
        buttonTips : {
            bold : {
               title: 'Έντονα (Ctrl+B)',
               text: 'Κάνετε το π�?οεπιλεγμένο κείμενο έντονο.',
               cls: 'x-html-editor-tip'
            },
            italic : {
               title: 'Πλάγια (Ctrl+I)',
               text: 'Κάνετε το π�?οεπιλεγμένο κείμενο πλάγιο.',
               cls: 'x-html-editor-tip'
            },
            underline : {
               title: 'Υπογ�?άμμιση (Ctrl+U)',
               text: 'Υπογ�?αμμίζετε το π�?οεπιλεγμένο κείμενο.',
               cls: 'x-html-editor-tip'
           },
           increasefontsize : {
               title: 'Μεγέθυνση κειμένου',
               text: 'Μεγαλώνετε τη γ�?αμματοσει�?ά.',
               cls: 'x-html-editor-tip'
           },
           decreasefontsize : {
               title: 'Σμίκ�?υνση κειμένου',
               text: 'Μικ�?αίνετε τη γ�?αμματοσει�?ά.',
               cls: 'x-html-editor-tip'
           },
           backcolor : {
               title: 'Χ�?ώμα Φόντου Κειμένου',
               text: 'Αλλάζετε το χ�?ώμα στο φόντο του π�?οεπιλεγμένου κειμένου.',
               cls: 'x-html-editor-tip'
           },
           forecolor : {
               title: 'Χ�?ώμα Γ�?αμματοσει�?άς',
               text: 'Αλλάζετε το χ�?ώμα στη γ�?αμματοσει�?ά του π�?οεπιλεγμένου κειμένου.',               
               cls: 'x-html-editor-tip'
           },
           justifyleft : {
               title: 'Α�?ιστε�?ή Στοίχιση Κειμένου',
               text: 'Στοιχίζετε το κείμενο στα α�?ιστε�?ά.',
               cls: 'x-html-editor-tip'
           },
           justifycenter : {
               title: 'Κεντ�?ά�?ισμα Κειμένου',
               text: 'Στοιχίζετε το κείμενο στο κέντ�?ο.',
               cls: 'x-html-editor-tip'
           },
           justifyright : {
               title: 'Δεξιά Στοίχιση Κειμένου',
               text: 'Στοιχίζετε το κείμενο στα δεξιά.',
               cls: 'x-html-editor-tip'
           },
           insertunorderedlist : {
               title: 'Εισαγωγή Λίστας Κουκίδων',
               text: 'Ξεκινήστε μια λίστα με κουκίδες.',
               cls: 'x-html-editor-tip'
           },
           insertorderedlist : {
               title: 'Εισαγωγή Λίστας Α�?ίθμησης',
               text: 'Ξεκινήστε μια λίστα με α�?ίθμηση.',
               cls: 'x-html-editor-tip'
           },
           createlink : {
               title: 'Hyperlink',
               text: 'Μετατ�?έπετε το π�?οεπιλεγμένο κείμενο σε Link.',
               cls: 'x-html-editor-tip'
           },
           sourceedit : {
               title: 'Επεξε�?γασία Κώδικα',
               text: 'Μεταβαίνετε στη λειτου�?γία επεξε�?γασίας κώδικα.',
               cls: 'x-html-editor-tip'
           }
        }
   });
}


if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Α�?ξουσα ταξινόμηση",
      sortDescText : "Φθίνουσα ταξινόμηση",
      lockText     : "Κλείδωμα στήλης",
      unlockText   : "Ξεκλείδωμα στήλης",
      columnsText  : "Στήλες"
   });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
  emptyGroupText : '(Καμμία)',
  groupByText    : 'Ομαδοποίηση βάσει αυτο�? του πεδίου',
  showGroupsText : '�?α εμφανίζεται στις ομάδες'
  });
}

if(Ext.grid.PropertyColumnModel){
   Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Όνομα",
      valueText  : "Πε�?ιεχόμενο",
      dateFormat : "m/d/Y"
   });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
   Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
      splitTip            : "Σ�?�?ετε για αλλαγή μεγέθους.",
      collapsibleSplitTip : "Σ�?�?ετε για αλλαγή μεγέθους. Διπλό κλικ για απόκ�?υψη."
   });
}

