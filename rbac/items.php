<?php
return [
    'guest' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'login',
            'logout',
            'error',
        ],
    ],
    'logist' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'logistlist',
            'guest',
            'view',
            'update',
            'blocked',
            'testsys',
            'find',
            'ajaxvalidate',
        ],
    ],
    'bugalter' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'bugalterlist',
            'logist',
            'details',
            'ajaxupdaterequest',
        ],
    ],
    'proizvodstvo' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'download',
            'bugalter',
            'deslist',
            'proizvmaterial',
        ],
    ],
    'desiner' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'uploaddesigner',
            'designerremfile',
            'deschange',
            'proizvodstvo',
        ],
    ],
    'moder' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'index',
            'list',
            'ajaxlist',
            'ajaxupdaterequest',
            'add',
            'ajaxchange',
            'ajaxadd',
            'change',
            'copy',
            'desiner',
            'remfile',
            'upload',
            'materiallist',
            'deslistadmin',
        ],
    ],
    'admin' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'moder',
            'delete',
            'create',
            'remove',
            'export',
        ],
    ],
    'login' => [
        'type' => 2,
    ],
    'logout' => [
        'type' => 2,
    ],
    'error' => [
        'type' => 2,
    ],
    'index' => [
        'type' => 2,
    ],
    'view' => [
        'type' => 2,
    ],
    'list' => [
        'type' => 2,
    ],
    'details' => [
        'type' => 2,
    ],
    'ajaxlist' => [
        'type' => 2,
    ],
    'ajaxupdaterequest' => [
        'type' => 2,
    ],
    'update' => [
        'type' => 2,
    ],
    'ajaxchange' => [
        'type' => 2,
    ],
    'change' => [
        'type' => 2,
    ],
    'add' => [
        'type' => 2,
    ],
    'ajaxadd' => [
        'type' => 2,
    ],
    'delete' => [
        'type' => 2,
    ],
    'create' => [
        'type' => 2,
    ],
    'remove' => [
        'type' => 2,
    ],
    'logistlist' => [
        'type' => 2,
    ],
    'bugalterlist' => [
        'type' => 2,
    ],
    'copy' => [
        'type' => 2,
    ],
    'upload' => [
        'type' => 2,
    ],
    'download' => [
        'type' => 2,
    ],
    'remfile' => [
        'type' => 2,
    ],
    'uploaddesigner' => [
        'type' => 2,
    ],
    'designerremfile' => [
        'type' => 2,
    ],
    'deslist' => [
        'type' => 2,
    ],
    'deschange' => [
        'type' => 2,
    ],
    'blocked' => [
        'type' => 2,
    ],
    'testsys' => [
        'type' => 2,
    ],
    'find' => [
        'type' => 2,
    ],
    'ajaxvalidate' => [
        'type' => 2,
    ],
    'materiallist' => [
        'type' => 2,
    ],
    'export' => [
        'type' => 2,
    ],
    'proizvmaterial' => [
        'type' => 2,
    ],
    'deslistadmin' => [
        'type' => 2,
    ],
];
