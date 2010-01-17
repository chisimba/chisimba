/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';

    config.extraPlugins = 'contexttools';
    config.toolbar = 'advanced';
    config.image_browseServer=true;
    config.resize_minWidth = 400;
    config.resize_maxWidth = '100%';

    config.enterMode = CKEDITOR.ENTER_BR;
    config.shiftEnterMode = CKEDITOR.ENTER_P;
    config.toolbar_advanced =
    [
    ['Source','-','Save','NewPage','Preview','-','Templates'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
    '/',
    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['Link','Unlink','Anchor'],
    ['Image','Flash','contexttools','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
    '/',
    ['Styles','Format','Font','FontSize'],
    ['TextColor','BGColor'],
    ['Maximize', 'ShowBlocks','-','About']
    ];


    config.toolbar_default =
    [
    ['Source','DocProps','-','Save','NewPage','Preview','-','Templates'],
    ['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'],
    '/',
    ['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
    ['OrderedList','UnorderedList','-','Outdent','Indent','Blockquote'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
    ['Link','Unlink','Anchor'],
    ['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'],
    '/',
    ['Styles','Format','Font','FontSize'],
    ['TextColor','BGColor'],
    ['FitWindow','ShowBlocks','-','About']		// No comma for the last row.

    ];


    config.toolbar_DefaultWithoutSave =
    [
    ['Source','DocProps','-','NewPage','Preview','-','Templates'],
    ['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField'],
    '/',
    ['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
    ['OrderedList','UnorderedList','-','Outdent','Indent','Blockquote'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
    ['Link','Unlink','Anchor'],
    ['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'],
    '/',
    ['Styles','Format','Font','FontSize'],
    ['TextColor','BGColor'],
    ['FitWindow','ShowBlocks','-','About']		// No comma for the last row.

    ];


    config.toolbar_cms =
    [
    ['Source','-','Save','Preview', 'Templates'],
    ['Cut','Copy','Paste','PasteText','PasteWord','-','Print','SpellCheck'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
    ['OrderedList','UnorderedList','-','Outdent','Indent'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
    '/',
    ['Image','Flash','Table','Rule','Smiley','SpecialChar','PageBreak'],
    ['TextColor','BGColor'],['Link','Unlink','Anchor','FitWindow'],
    '/',
    ['Styles','Format','Font','FontSize'],
    ];

    config.toolbar_forms =
    [
    ['Source','-','Save','NewPage','Preview','-','Templates'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
    '/',
    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['Link','Unlink','Anchor'],
    ['Image','Flash','contexttools','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
    '/',
    ['Styles','Format','Font','FontSize'],

    ['TextColor','BGColor'],
    ['Maximize', 'ShowBlocks','-','About']
    ];


    config.toolbar_simple =
    [
    ['Styles', 'Format'],
    ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', '-', 'About']

    ];


};
