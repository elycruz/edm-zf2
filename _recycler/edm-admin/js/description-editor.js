$(function()
{
    var config = {
        toolbar:
        [
            ['Source','-','Save',   'NewPage','Preview','-','Templates'],
            ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print',
                'SpellChecker', 'Scayt'],
            '/',   
            ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
            //['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select',
            //'Button', 'ImageButton', 'HiddenField'],
            //['BidiLtr', 'BidiRtl'],
            //'/',
            ['Bold','Italic','Underline','Strike','-','Subscript',
                'Superscript'],
            ['NumberedList','BulletedList'],
            '/',
            ['Outdent','Indent','Blockquote','CreateDiv'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['Link','Unlink','Anchor'],
            ['Image', 'Flash','Table','HorizontalRule','Smiley',
                'SpecialChar','PageBreak'],
            '/',
            ['Styles','Format','Font','FontSize'],
            ['TextColor','BGColor'],
            ['Maximize', 'ShowBlocks','-'] //,'About']
        ]
//        entities: false,
//        basicEntities: false,
//        entities_latin: false,
//        entities_greek: false,
//        entities_additional: false
    };

    // Initialize the editor.
    // Callback function can be passed and executed after
    // full instance creation.
    try {
        $('#content').ckeditor(config);        
        if ($('#excerpt')) {
            $('#excerpt').ckeditor(config);
        }
    }
    catch (e) {
        alert(e);
        $('#description').ckeditor(config);
    }

});