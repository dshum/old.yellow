var uiTinymce = angular.module('uiTinymceCtrl', []);

uiTinymce.value('uiTinymceConfig', {
	language: 'ru',
	plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste textcolor responsivefilemanager',
	toolbar: 'newdocument | undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview code',
	image_advtab: true,
	forced_root_block: 'p',
	entity_encoding: 'raw',
	convert_urls: false,
	file_browser_callback: function(field_name, url, type, win) {
		var
			w = window,
			d = document,
			e = d.documentElement,
			g = d.getElementsByTagName('body')[0],
			x = w.innerWidth || e.clientWidth || g.clientWidth,
			y = w.innerHeight|| e.clientHeight|| g.clientHeight;

		tinyMCE.activeEditor.windowManager.open({
			file : '/admin#/filemanager',
			title : 'Файловый менеджер',
			width : x * 0.5,
			height : y * 0.5,
			resizable : "yes",
			close_previous : "no"
		});
	},
//	filemanager_title: 'Файловый менеджер',
//	external_filemanager_path: '/packages/lemon-tree/admin/filemanager/plugin.min.js',
//	external_plugins: {
//		'filemanager': '/packages/lemon-tree/admin/filemanager/plugin.min.js'
//	}
});