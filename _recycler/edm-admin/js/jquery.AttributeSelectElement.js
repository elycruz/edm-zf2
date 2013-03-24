/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function AttributeSelectElement(elm, actionName, attribName)
{
    return elm.change(function(e)
    {
        var s = $(this).get(0), url = window.location,
            pathname, search = '', key, id = 0, o = $(this);
        s = s.options[s.selectedIndex].value;

        /**
         * Pathname
         */
        if (url.pathname) {
            pathname = url.pathname.split('/');
            pathname = '/'+ pathname[1] +'/'+ pathname[2] +'/'+ actionName;
        }
        else {
            return;
        }

        // Params
        if (!attribName) {
            return
        }

        // Get id to apply these changes to
        id = $(this).attr('id').split('_').pop();

        if (o.attr('class').indexOf(' --') != -1) {
            // Append attribName value to search params
            search += '/'+ id +'/'+ attribName +'/'+ s +'/objectType/'+
                o.attr('class').split(' --').pop();
        }
        else {
            // Append attribName value to search params
            search += '/'+ id +'/'+ attribName +'/'+ s ;
        }

        /**
         * Redirect with new params
         */
        url = url.protocol +'//'+ url.hostname + pathname + search
        window.location = url;
    });
}

