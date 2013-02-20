define([
    'text!library/view-models/data-grid/data-grid-tmpls.html'
], function (dataGridTmpls) {
    var body = $('body');
    body
        .append(dataGridTmpls);
});