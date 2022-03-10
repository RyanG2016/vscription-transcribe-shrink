var users;
tinymce.init({
    selector: '#report',
    auto_focus: "report",
    branding: false,
    resize: true,
    nanospell_dictionary: "en,en_ca,en_med",
    height: 700,
    content_style: "body {font-size: 14.3pt; font-family: system-ui, Georgia, serif}",

    external_plugins: { "nanospell": "/tinymce/thirdparty/nanospell/plugin.js" },
    nanospell_server: "php",
    nanospell_autostart: false,
    nanospell_ignore_words_with_numerals: true,
    nanospell_ignore_block_caps: false,
    nanospell_compact_menu: false,

    toolbar: "nanospell | bold italic underline | alignleft aligncenter alignright alignjustify " +
        "| bullist numlist outdent indent | undo redo | code | formatselect | shortcuts_editor | load_job",

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
                var curPos = new Date(Math.floor(AblePlayerInstances[0].seekBar.position)*1000).toISOString().substr(11, 8);   
                //Had to combine the following together as I couldn't get the span tag to close properly otherwise.
                tinymce.activeEditor.execCommand('mceInsertContent', false, "<span class=\'ima\' style=\'color:blue\'><-INAUDIBLE (" + curPos + ")-></span>\uFEFF");
                return false;
            }
            else if (e.keyCode === 116) { // F5

                e.preventDefault();
                e.stopPropagation();
                tinymce.activeEditor.execCommand('mceInsertContent', false, "<span class=\'ima\'>>>Interviewer 1 - </span>\uFEFF");
                return false;
            }
            else if (e.keyCode === 117) { // F6

                e.preventDefault();
                e.stopPropagation();
                tinymce.activeEditor.execCommand('mceInsertContent', false, "<span class=\'ima\'>>>Participant 1 -  </span>\uFEFF");
                return false;
            }
            else if (e.keyCode === 118) { // F7

                e.preventDefault();
                e.stopPropagation();
                tinymce.activeEditor.execCommand('mceInsertContent', false, "<span class=\'ima\'>>>Participant 2 - </span>\uFEFF");
                return false;
            }
            else if (e.keyCode === 119) { // F8

                e.preventDefault();
                e.stopPropagation();
                tinymce.activeEditor.execCommand('mceInsertContent', false, "<span class=\'ima\' style=\'color:blue\'>XXMASKEDNAMEXX</span>\uFEFF");
                return false;
            }
            else if (e.keyCode === 120) { // F9

                e.preventDefault();
                e.stopPropagation();
                tinymce.activeEditor.execCommand('mceInsertContent', false, "<span class=\'ima\' style=\'color:blue\'>>>Speaker </span>\uFEFF");
                return false;
            }

        });

        ed.addButton('shortcuts_editor', {
            text: "Edit Shortcuts",
            icon: 'line',
            tooltip: "Edit (/) expandable shortcuts",
            onclick: function () {
                editUserShortcuts();
            }
        });

        ed.addButton('load_job', {
            text: 'Load',
            icon: 'fas fa-cloud-download',
            tooltip: "Load a job",
            onclick: function () {
                loadNewJob();
            }
        });

        ed.on('init', function(event) {
            $(ed.getBody()).on("click", ".ima", function() {
                markerLocationHHMMSS = tinymce.activeEditor.selection.getNode().innerHTML.substr(16,8);
                let a = markerLocationHHMMSS.split(':');
                let markerLocationSecs = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]); 
                if (markerLocationSecs > 2) {
                    markerLocationSecs = markerLocationSecs -2;
                }
                tinymce.activeEditor.selection.select(tinymce.activeEditor.selection.getNode());
                gotoInaudiblePosition(markerLocationSecs);
            });
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
