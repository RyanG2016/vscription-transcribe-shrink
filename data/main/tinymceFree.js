tinymce.init({
	selector: '#report',
	auto_focus: "report",
	external_plugins: {
		"nanospell": "/tinymce/nanospell/plugin.js"
	},
	nanospell_server: "php",
	branding: false,
	resize: false,
	nanospell_dictionary: "en,en_ca,en_med",
	plugins: "autosave,mention",
	autosave_interval: "5s",
	autosave_ask_before_unload: true,
	nanospell_autostart: true,
	setup: function (ed) {

		ed.on("KeyDown", function (e) {

			if (e.keyCode == 113) {

				e.preventDefault();
				e.stopPropagation();
				tinymce.activeEditor.execCommand('mceInsertContent', false, "<<");
				tinymce.activeEditor.execCommand('mceInsertContent', false, "INAUDIBLE");
				tinymce.activeEditor.execCommand('mceInsertContent', false, ">>");
				return false;
			}

			// }
		});
	},
	mentions: {
		source: function (query, process, delimiter) {
			if (delimiter === '/') {
				$.getJSON('data/lists/names.json?v=1.0', function (data) {
					process(data)
				});
			}

		},
		delimiter: ['/'],
		delay: 200
	}
});
