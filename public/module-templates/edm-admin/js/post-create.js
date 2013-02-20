$(function() {
    var parent_id = $('#parent_id'),
        taxonomy = $('#taxonomy');
    
    taxonomy.change(function(e) {
        
        $.get('/edm-admin/term-taxonomy/get/taxonomyFilter' +
                '/' + $(this).val() + '/format/json', function(data) {
                
            parent_id.html(data.result);    

        }); // ajax call
        
    }); // taxonomy change
    
    function setSelectOptions(options) {
        var selected = false, value;
        if (options.length > 0) {
            for (var item in options) {
                item = options[item];

                // Value
                value = item.term_alias;

                // is item selected
                if (parent_id.value == value) {
                    selected = true;
                }

                // Create new option
                parent_id.get(0).add(
                new Option(item.term_name, value, selected,selected));
                selected = false;
            }
        }
    }
    
}); // main