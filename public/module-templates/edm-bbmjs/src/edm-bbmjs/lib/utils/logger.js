/**
 * Created by Ely on 5/24/2014.
 */
define({
    log: function () {
        if (console) {
            console.log.apply(console, sjl.argsToArray(arguments));
        }
    }
});