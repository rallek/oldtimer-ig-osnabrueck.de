CKEDITOR.plugins.add('rkparkhausmodule', {
    requires: 'popup',
    lang: 'en,nl,de',
    init: function (editor) {
        editor.addCommand('insertRKParkhausModule', {
            exec: function (editor) {
                var url = Routing.generate('rkparkhausmodule_external_finder', { objectType: 'vehicle', editor: 'ckeditor' });
                // call method in RKParkhausModule.Finder.js and provide current editor
                RKParkhausModuleFinderCKEditor(editor, url);
            }
        });
        editor.ui.addButton('rkparkhausmodule', {
            label: editor.lang.rkparkhausmodule.title,
            command: 'insertRKParkhausModule',
            icon: this.path.replace('docs/scribite/plugins/CKEditor/vendor/ckeditor/plugins/rkparkhausmodule', 'public/images') + 'admin.png'
        });
    }
});
