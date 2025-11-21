/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.enterMode = CKEDITOR.ENTER_DIV;
	config.shiftEnterMode = CKEDITOR.ENTER_BR;

	// 禁止自动填充空内容为 <p>&nbsp;</p>
	config.autoParagraph = false;

	// 允许空文档
	config.fillEmptyBlocks = false;
	config.allowedContent = true;
	config.filebrowserUploadUrl = 'index.php?document-api-uploader-ckeditor';
	config.filebrowserImageUploadUrl = 'index.php?document-api-uploader-ckeditor';
    config.filebrowserFlashUploadUrl = 'index.php?document-api-uploader-ckeditor';
    config.filebrowserMusicUploadUrl = 'index.php?document-api-uploader-ckeditor&action=music';
    config.image_prefillDimensions = false;
	
	config.toolbar = [
		{ name: 'document', items: [ 'Source' ] },
		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		{ name: 'styles', items: [ 'Styles', 'Format','Font','FontSize','TextColor','wikititle','lineheight'] },
		'/',
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
		{ name: 'insert', items: [ 'Image','Mathjax', 'Table', 'HorizontalRule', 'SpecialChar','music','video'] },
		{ name: 'tools', items: [ 'Maximize' ] }
	];
	config.extraPlugins = 'mathjax,music,wikititle,lineheight,video';
	config.mathJaxLib = 'files/public/js/ckeditor/js/MathJax.js?config=TeX-AMS_HTML';
	CKEDITOR.config.contentsCss = [];
};
