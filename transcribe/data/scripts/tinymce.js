var users;
tinymce.init({
    selector: '#report',
    auto_focus: "report",
    branding: false,
    resize: true,
    nanospell_dictionary: "en,en_ca,en_med",
    height: "400",
    content_style: "body {font-size: 14.3pt; font-family: system-ui, Georgia, serif}",

    external_plugins: { "nanospell": "/tinymce/thirdparty/nanospell/plugin.js" },
    nanospell_server: "php",
    nanospell_autostart: false,
    nanospell_ignore_words_with_numerals: true,
    nanospell_ignore_block_caps: false,
    nanospell_compact_menu: false,

    toolbar: "nanospell | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | undo redo | code | formatselect | mybutton",

    // plugins: "autosave,mention",
    // plugins: "autosave",
    plugins: "mention",
    // autosave_interval: "5s",
    // autosave_ask_before_unload: true,
    readonly: 1,
    setup: function (ed) {

        ed.on("KeyDown", function (e) {

            if (e.keyCode === 112) { // F1

                e.preventDefault();
                e.stopPropagation();
                tinymce.activeEditor.execCommand('mceInsertContent', false, lastShortcutValue);
                return false;
            }
            else if (e.keyCode === 113) { // F2

                e.preventDefault();
                e.stopPropagation();
                tinymce.activeEditor.execCommand('mceInsertContent', false, "<<");
                tinymce.activeEditor.execCommand('mceInsertContent', false, "INAUDIBLE");
                tinymce.activeEditor.execCommand('mceInsertContent', false, ">>");
                return false;
            }

        });

        ed.addButton('mybutton', {
            text: "Edit Shortcuts",
            icon: 'line',
            tooltip: "Edit (/) expandable shortcuts",
            onclick: function () {
                editUserShortcuts();
            }
        });
    },
    mentions: {
        source: function (query, process, delimiter) {

            if (delimiter === '/') {
                if(users == null || refreshShortcuts) {
                    let defaultData, customUserShortcuts = null;

                    $.getJSON('data/lists/names.json?v=1.0', function (data) {
                        defaultData = data;

                        $.getJSON('api/v1/users/shortcuts', function (customData) {
                            customUserShortcuts = customData;

                            users = defaultData.concat(customUserShortcuts);

                            refreshShortcuts = false;
                            process(users);
                        });
                    });

                }else{
                    process(users);
                }
            }

        },
        render: function(item) {
            if(item.custom)
            {
                return '<li>' +
                    '<a href="javascript:;"><span>' + item.name + ' <i class="fas fa-star" style="color: #f2b01e"></i>' +  '</span></a>' +
                    '</li>';
            }
            else{
                return '<li>' +
                    '<a href="javascript:;"><span>' + item.name + '</span></a>' +
                    '</li>';
            }
        },
        insert: function(item) {
            lastShortcutValue = item.val;
            // return '<span>' + item.val + '</span>';
            return item.val;
        },
        delimiter: ['/'],
        delay: 200
    }
});
