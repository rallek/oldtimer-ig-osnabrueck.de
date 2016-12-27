CKEDITOR.plugins.add('rkteammodule', {
    requires: 'popup',
    lang: 'en,nl,de',
    init: function (editor) {
        editor.addCommand('insertRKTeamModule', {
            exec: function (editor) {
                var url = Routing.generate('rkteammodule_external_finder', { objectType: 'person', editor: 'ckeditor' });
                // call method in RKTeamModule.Finder.js and provide current editor
                RKTeamModuleFinderCKEditor(editor, url);
            }
        });
        editor.ui.addButton('rkteammodule', {
            label: editor.lang.rkteammodule.title,
            command: 'insertRKTeamModule',
            icon: this.path.replace('docs/scribite/plugins/CKEditor/vendor/ckeditor/plugins/rkteammodule', 'public/images') + 'admin.png'
        });
    }
});
