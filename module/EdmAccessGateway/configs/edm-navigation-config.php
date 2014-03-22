<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    'default' => array(
        array(
            'label' => 'Terms',
            'route' => 'edm-admin/term',
            'resource' => 'term',
            'privilege' => 'cms-super',
            'pages' => array(
                array(
                    'label' => 'Term Index',
                    'route' => 'edm-admin/term',
                    'privilege' => 'cms-user',
                    'resource' => 'index'
                ),
                array(
                    'label' => 'Term Create',
                    'route' => 'edm-admin/term/create',
                    'privilege' => 'cms-author',
                    'resource' => 'create'
                ),
                array(
                    'label' => 'Term Update',
                    'route' => 'edm-admin/term/update',
                    'privilege' => 'cms-author',
                    'resource' => 'update'
                ),
                array(
                    'label' => 'Term Delete',
                    'route' => 'edm-admin/term/delete',
                    'privilege' => 'cms-author',
                    'resource' => 'delete'
                ),
            )
        ),
        array(
            'label' => 'Term Taxonomies',
            'route' => 'edm-admin/term-taxonomy',
            'privilege' => 'cms-user',
            'resource' => 'term-taxonomy',
            'pages' => array(
                array(
                    'label' => 'Term Taxonomy Index',
                    'route' => 'edm-admin/term',
                    'privilege' => 'cms-user',
                    'resource' => 'index'
                ),
                array(
                    'label' => 'Term Taxonomy Create',
                    'route' => 'edm-admin/term-taxonomy/create',
                    'privilege' => 'cms-author',
                    'resource' => 'create'
                ),
                array(
                    'label' => 'Term Taxonomy Update',
                    'route' => 'edm-admin/term-taxonomy/update',
                    'privilege' => 'cms-author',
                    'resource' => 'update'
                ),
                array(
                    'label' => 'Term Taxonomy Delete',
                    'route' => 'edm-admin/term-taxonomy/delete',
                    'privilege' => 'cms-author',
                    'resource' => 'delete'
                ),
            )
        ),
    ),
);