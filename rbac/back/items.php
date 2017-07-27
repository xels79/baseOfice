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
        ],
    ],
    'bugalter' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'bugalterlist',
            'logist',
        ],
    ],
    'desiner' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'upload',
            'bugalter',
            'download',
        ],
    ],
    'moder' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'index',
            'list',
            'details',
            'ajaxlist',
            'ajaxupdaterequest',
            'add',
            'ajaxchange',
            'ajaxadd',
            'change',
            'copy',
            'desiner',
            'remfile',
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
];
