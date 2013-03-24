/**
 * @todo Overhaul the term taxonomy by doing the following:
 * remove childCount column, and change parent_id to parent_term_alias
 */$(function() {
    var parent_id = $('#parent_id'),
        taxonomy = $('#taxonomy');
    
    taxonomy.change(function(e) {
        
        $.get('/edm-admin/term-taxonomy/get/taxonomyFilter' +
                '/' + taxonomy.val() + '/format/json', function(data) {
                //trace(var_dump(data.result)); return;
                parent_id.html('<option value="0" label=" -- Optional -- "> n' +
                    '-- Optional -- </option>');
            setSelectOptions(data.result);
        }); // ajax call
        
    }); // taxonomy change
    
    function setSelectOptions(options) {
        var selected = false, value;
        if (options.length > 0) {
            for (var item in options) {
                item = options[item];

                // Value
                value = item.term_taxonomy_id;

                // is item selected
                if (parent_id.val() == value) {
                    selected = true;
                }

                // Create new option
                parent_id.get(0).add(
                new Option(item.term_name, value, selected,selected));
                selected = false;
            }
        }
    }
    
    /**
     * @todo create a method for ordering term taxonomies by their parent
     */
    
}); // main