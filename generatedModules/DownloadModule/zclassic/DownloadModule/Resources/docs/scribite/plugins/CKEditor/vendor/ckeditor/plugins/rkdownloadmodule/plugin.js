CKEDITOR.plugins.add('rkdownloadmodule', {
    requires: 'popup',
    lang: 'en,nl,de',
    init: function (editor) {
        editor.addCommand('insertRKDownloadModule', {
            exec: function (editor) {
                var url = Routing.generate('rkdownloadmodule_external_finder', { objectType: 'file', editor: 'ckeditor' });
                // call method in RKDownloadModule.Finder.js and provide current editor
                RKDownloadModuleFinderCKEditor(editor, url);
            }
        });
        editor.ui.addButton('rkdownloadmodule', {
            label: editor.lang.rkdownloadmodule.title,
            command: 'insertRKDownloadModule',
            icon: this.path.replace('docs/scribite/plugins/CKEditor/vendor/ckeditor/plugins/rkdownloadmodule', 'public/images') + 'admin.png'
        });
    }
});
