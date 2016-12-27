CKEDITOR.plugins.add('rksponsoringmodule', {
    requires: 'popup',
    lang: 'en,nl,de',
    init: function (editor) {
        editor.addCommand('insertRKSponsoringModule', {
            exec: function (editor) {
                var url = Routing.generate('rksponsoringmodule_external_finder', { objectType: 'sponsor', editor: 'ckeditor' });
                // call method in RKSponsoringModule.Finder.js and provide current editor
                RKSponsoringModuleFinderCKEditor(editor, url);
            }
        });
        editor.ui.addButton('rksponsoringmodule', {
            label: editor.lang.rksponsoringmodule.title,
            command: 'insertRKSponsoringModule',
            icon: this.path.replace('docs/scribite/plugins/CKEditor/vendor/ckeditor/plugins/rksponsoringmodule', 'public/images') + 'admin.png'
        });
    }
});
