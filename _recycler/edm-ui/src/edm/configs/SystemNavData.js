define({
    title: 'System sub menu',
    ulAttribs: {
        'class': 'vert-menu-left menu'
    },
    collection: [
        {
            route: '#!/term',
            htmlSrc: 'text!view-templates/term/index.html',
            tmplsSrc: 'text!view-templates/term/tmpls.html',
            controller: 'controllers/term/Term',
            tmplIds: [
//                    'table-tmpl',
//                    'thead-header-txt-tmpl',
//                    'thead-columns-tmpl',
//                    'tbody-tmpl'
            ],
            attribs: {
                'class': 'first'
            },
            label: 'Terms',
            clickFunction: 'changeCurrentView'
        },
        {
            route: '#!/term-taxonomy',
            htmlSrc: 'text!controllers/term-taxonomy/index.html',
            controller: 'controllers/term-taxonomy/TermTaxonomy',
            label: 'Term Taxonomies',
            clickFunction: 'changeCurrentView'
        },
        {
            route: '#!/user',
            htmlSrc: 'text!view-template/user/index.html',
            attribs: {
                'class': 'first'
            },
            label: 'Users',
            clickFunction: 'changeCurrentView'
        }]
});