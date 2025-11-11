// ckeditor remove button list
CKEDITOR.config.removePlugins = 'flash';
CKEDITOR.config.entities = false;
CKEDITOR.config.allowedContent = true;
//    CKEDITOR.config.font_names =
//        'Arial/Arial, Helvetica, sans-serif;' +
//        'Times New Roman/Times New Roman, Times, serif;' +
//        'Verdana';
//
CKEDITOR.config.font_names = 'Arial;Arial Black;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana;儷黑Pro;微軟正黑體;新細明體;細明體;標楷體;宋体;Microsoft Yahei';
CKEDITOR.on('dialogDefinition', function (ev) {
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    if (dialogName === 'image') {
        var infoTab = dialogDefinition.getContents('info');
        //
        //            infoTab.remove( 'txtBorder' ); //Remove Element Border From Tab Info
        //            infoTab.remove( 'txtHSpace' ); //Remove Element Horizontal Space From Tab Info
        //            infoTab.remove( 'txtVSpace' ); //Remove Element Vertical Space From Tab Info
        infoTab.remove('txtWidth'); //Remove Element Width From Tab Info
        infoTab.remove('txtHeight'); //Remove Element Height From Tab Info
        //Remove tab Link
        //            dialogDefinition.removeContents( 'Link' );
    }
});


CKEDITOR.on('instanceReady', function (ev) {
    ev.editor.dataProcessor.htmlFilter.addRules({
        elements: {
            img: function (el) {
                el.addClass('img-responsive');
            }
        }
    });
});
//CKEDITOR.config.removePlugins = 'save,print,preview,find,about,maximize,showblocks';