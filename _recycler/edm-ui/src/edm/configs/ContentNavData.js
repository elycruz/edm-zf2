define({
    title: 'Content sub menu',
    ulAttribs: {
        'class': 'vert-menu-left menu'
    },
    collection: [
    {
        route: '#!/post',
        htmlSrc: 'text!view-template/post/index.html',
        attribs: {
            'class': 'first'
        },
        label: 'Posts',
        clickFunction: 'changeCurrentView'
    },
    {
        route: '#!/category',
        htmlSrc: 'text!view-template/post-category/index.html',
        label: 'Categories',
        clickFunction: 'changeCurrentView'
    },
    {
        route: '#!/tag',
        htmlSrc: 'text!view-template/post-tag/index.html',
        label: 'Tags',
        clickFunction: 'changeCurrentView'
    },
    {
        route: '#!/media',
        htmlSrc: 'text!view-template/post-media/index.html',
        attribs: {
            'class': 'last'
        },
        label: 'Media',
        clickFunction: 'changeCurrentView'
    }]
});