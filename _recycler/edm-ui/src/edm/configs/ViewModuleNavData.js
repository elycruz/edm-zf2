define({
    title: 'View Module sub menu',
    ulAttribs: {
        'class': 'vert-menu-left menu'
    },
    collection: [
        {
            route: '#!/view-module',
            htmlSrc: 'text!view-template/view-module/index.html',
            attribs: {
                'class': 'first'
            },
            label: 'View Modules'
        },
        {
            route: '#!/menu',
            htmlSrc: 'text!view-template/view-module/menu/index.html',
            label: 'Menu'
        },
        {
            route: '#!/plain-html',
            htmlSrc: 'text!view-template/view-module/plain-html/index.html',
            attribs: {
                'class': 'first'
            },
            label: 'Plain html'
        }]
});