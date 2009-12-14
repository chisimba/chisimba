/*!
 * Ext JS Library 3.0.3
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
﻿/*
 * Russian translation
 * By ZooKeeper (utf-8 encoding)
 * 6 November 2007
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Идет загрузка...</div>';

if(Ext.View){
  Ext.View.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
  Ext.grid.GridPanel.prototype.ddText = "{0} выбранных �?трок";
}

if(Ext.TabPanelItem){
  Ext.TabPanelItem.prototype.closeText = "Закрыть �?ту вкладку";
}

if(Ext.form.Field){
  Ext.form.Field.prototype.invalidText = "Значение в �?том поле неверное";
}

if(Ext.LoadMask){
  Ext.LoadMask.prototype.msg = "Загрузка...";
}

Date.monthNames = [
    "Январь",
    "Февраль",
    "Март",
    "�?прель",
    "Май",
    "Июнь",
    "Июль",
    "�?вгу�?т",
    "Сент�?брь",
    "Окт�?брь",
    "�?о�?брь",
    "Декабрь"
];

Date.shortMonthNames = [
  "Янв",
  "Февр",
  "Март",
  "�?пр",
  "Май",
  "Июнь",
  "Июль",
  "�?вг",
  "Сент",
  "Окт",
  "�?о�?б",
  "Дек"
];

Date.getShortMonthName = function(month) {
  return Date.shortMonthNames[month];
};

Date.monthNumbers = {
  'Янв': 0,
  'Фев': 1,
  'Мар': 2,
  '�?пр': 3,
  'Май': 4,
  'Июн': 5,
  'Июл': 6,
  '�?вг': 7,
  'Сен': 8,
  'Окт': 9,
  '�?о�?': 10,
  'Дек': 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
  "Во�?кре�?енье",
  "Понедельник",
  "Вторник",
  "Среда",
  "Четверг",
  "П�?тница",
  "Суббота"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

if(Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : "OK",
    cancel : "Отмена",
    yes    : "Да",
    no     : "�?ет"
  };
}

if(Ext.util.Format){
  Ext.util.Format.date = function(v, format){
    if(!v) return "";
    if(!(v instanceof Date)) v = new Date(Date.parse(v));
    return v.dateFormat(format || "d.m.Y");
  };
}

if(Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText          : "Сегодн�?",
    minText            : "Эта дата раньше минимальной даты",
    maxText            : "Эта дата позже мак�?имальной даты",
    disabledDaysText   : "",
    disabledDatesText  : "",
    monthNames         : Date.monthNames,
    dayNames           : Date.dayNames,
    nextText           : 'Следующий ме�?�?ц (Control+Вправо)',
    prevText           : 'Предыдущий ме�?�?ц (Control+Влево)',
    monthYearText      : 'Выбор ме�?�?ца (Control+Вверх/Вниз дл�? выбора года)',
    todayTip           : "{0} (Пробел)",
    format             : "d.m.y",
    okText             : "&#160;OK&#160;",
    cancelText         : "Отмена",
    startDay           : 1
  });
}

if(Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : "Страница",
    afterPageText  : "из {0}",
    firstText      : "Перва�? �?траница",
    prevText       : "Предыдуща�? �?траница",
    nextText       : "Следующа�? �?траница",
    lastText       : "По�?ледн�?�? �?траница",
    refreshText    : "Обновить",
    displayMsg     : "Отображают�?�? запи�?и �? {0} по {1}, в�?его {2}",
    emptyMsg       : '�?ет данных дл�? отображени�?'
  });
}

if(Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : "Минимальна�? длина �?того пол�? {0}",
    maxLengthText : "Мак�?имальна�? длина �?того пол�? {0}",
    blankText     : "Это поле об�?зательно дл�? заполнени�?",
    regexText     : "",
    emptyText     : null
  });
}

if(Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    minText : "Значение �?того пол�? не может быть меньше {0}",
    maxText : "Значение �?того пол�? не может быть больше {0}",
    nanText : "{0} не �?вл�?ет�?�? чи�?лом"
  });
}

if(Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : "�?е до�?тупно",
    disabledDatesText : "�?е до�?тупно",
    minText           : "Дата в �?том поле должна быть позде {0}",
    maxText           : "Дата в �?том поле должна быть раньше {0}",
    invalidText       : "{0} не �?вл�?ет�?�? правильной датой - дата должна быть указана в формате {1}",
    format            : "d.m.y",
    altFormats        : "d.m.y|d/m/Y|d-m-y|d-m-Y|d/m|d-m|dm|dmy|dmY|d|Y-m-d"
  });
}

if(Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : "Загрузка...",
    valueNotFoundText : undefined
  });
}

if(Ext.form.VTypes){
  Ext.apply(Ext.form.VTypes, {
    emailText     : 'Это поле должно �?одержать адре�? �?лектронной почты в формате "user@example.com"',
    urlText       : 'Это поле должно �?одержать URL в формате "http:/'+'/www.example.com"',
    alphaText     : 'Это поле должно �?одержать только латин�?кие буквы и �?имвол подчеркивани�? "_"',
    alphanumText  : 'Это поле должно �?одержать только латин�?кие буквы, цифры и �?имвол подчеркивани�? "_"'
  });
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Пожалуй�?та введите адре�?:',
    buttonTips : {
      bold : {
        title: 'Полужирный (Ctrl+B)',
        text: 'Применение полужирного начертани�? к выделенному тек�?ту.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Кур�?ив (Ctrl+I)',
        text: 'Применение кур�?ивного начертани�? к выделенному тек�?ту.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Подчёркнутый (Ctrl+U)',
        text: 'Подчёркивание выделенного тек�?та.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Увеличить размер',
        text: 'Увеличение размера шрифта.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'Уменьшить размер',
        text: 'Уменьшение размера шрифта.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'Заливка',
        text: 'Изменение цвета фона дл�? выделенного тек�?та или абзаца.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Цвет тек�?та',
        text: 'Измение цвета тек�?та.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Выровн�?ть тек�?т по левому краю',
        text: 'Выровнивание тек�?та по левому краю.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'По центру',
        text: 'Выровнивание тек�?та по центру.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Выровн�?ть тек�?т по правому краю',
        text: 'Выровнивание тек�?та по правому краю.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'Маркеры',
        text: '�?ачать маркированный �?пи�?ок.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: '�?умераци�?',
        text: '�?ачать нумернованный �?пи�?ок.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'В�?тавить гипер�?�?ылку',
        text: 'Создание �?�?ылки из выделенного тек�?та.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'И�?ходный код',
        text: 'Переключить�?�? на и�?ходный код.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.form.BasicForm){
  Ext.form.BasicForm.prototype.waitTitle = "Пожалуй�?та подождите...";
}

if(Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : "Сортировать по возра�?танию",
    sortDescText : "Сортировать по убыванию",
    lockText     : "Закрепить �?толбец",
    unlockText   : "Сн�?ть закрепление �?толбца",
    columnsText  : "Столбцы"
  });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(Пу�?то)',
    groupByText    : 'Группировать по �?тому полю',
    showGroupsText : 'Отображать по группам'
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : "�?азвание",
    valueText  : "Значение",
    dateFormat : "d.m.Y"
  });
}

if(Ext.SplitLayoutRegion){
  Ext.apply(Ext.SplitLayoutRegion.prototype, {
    splitTip            : "Т�?ните дл�? изменени�? размера.",
    collapsibleSplitTip : "Т�?ните дл�? изменени�? размера. Двойной щелчок �?пр�?чет панель."
  });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : "Т�?ните дл�? изменени�? размера.",
    collapsibleSplitTip : "Т�?ните дл�? изменени�? размера. Двойной щелчок �?пр�?чет панель."
  });
}
