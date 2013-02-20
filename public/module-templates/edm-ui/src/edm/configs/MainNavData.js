define({
    ulAttribs: {
        'class': 'horiz-menu-left menu grid_16'
    },
    collection: [
    {
        route: '#!/index',
        htmlSrc: 'text!controllers/index/index.html',
        controller: 'controllers/index/IndexController',
        clickFunction: 'changeCurrentView',
        label: 'Index'
    },
    {
        route: '#!/content',
        //htmlSrc: 'text!view-template/index.html',
        label: 'Content',
        ulAttribs: {
            'class': 'vert-menu-left'
        },
        clickFunction: 'changeCurrentView',
        collection: [
        {
            route: '#!/post',
            htmlSrc: 'text!controllers/post/index.html',
            attribs: {
                'class': 'first'
            },
            label: 'Posts',
            clickFunction: 'changeCurrentView'
        },
        {
            route: '#!/post/category',
            htmlSrc: 'text!controllers/post-category/index.html',
            label: 'Category',
            clickFunction: 'changeCurrentView'
        },
        {
            route: '#!/post/tag',
            htmlSrc: 'text!controllers/post-tag/index.html',
            attribs: {
                'class': 'last'
            },
            label: 'Tag',
            clickFunction: 'changeCurrentView'
        }]
    }]
});