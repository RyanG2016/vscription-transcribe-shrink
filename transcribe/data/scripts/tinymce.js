var users;
tinymce.init({
    selector: '#report',
    auto_focus: "report",
    branding: false,
    resize: true,
    nanospell_dictionary: "en,en_ca,en_med",
    height: "400",

    external_plugins: { "nanospell": "/tinymce/plugins/nanospell/plugin.js" },
    nanospell_server: "php",
    nanospell_autostart: false,
    nanospell_ignore_words_with_numerals: true,
    nanospell_ignore_block_caps: false,
    nanospell_compact_menu: false,
    toolbar: "nanospell toolbar: 'link unlink | image | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | undo redo | code | formatselect",

    // plugins: "autosave,mention",
    // plugins: "autosave",
    plugins: "mention",
    // autosave_interval: "5s",
    // autosave_ask_before_unload: true,
    readonly: 1,
    setup: function (ed) {

        ed.on("KeyDown", function (e) {

            if (e.keyCode === 113) {

                e.preventDefault();
                e.stopPropagation();
                tinymce.activeEditor.execCommand('mceInsertContent', false, "<<");
                tinymce.activeEditor.execCommand('mceInsertContent', false, "INAUDIBLE");
                tinymce.activeEditor.execCommand('mceInsertContent', false, ">>");
                return false;
            }

        });
    },
    mentions: {
        source: function (query, process, delimiter) {

            if (delimiter === '/') {
                if(users == null) {
                    $.getJSON('data/lists/names.json?v=1.0', function (data) {
                        users = data;
                        process(users);
                    });
                }else{
                    process(users);
                }
            }

        },
        delimiter: ['/'],
        delay: 200
    }
});
