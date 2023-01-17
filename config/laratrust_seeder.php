<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'superAdmin' => [
            'users' => 'r',
            'files' => 'r',
            'groups' => 'r'
        ],
        'owner' => [
            'users' => 'c, r, d',
            'files' => 'c, r, u, d',
            'groups' => 'c, r, u, d'
        ],
        'user' => [
            'files' => 'c, r, u, d',
            'groups' => 'c, r, u, d',
        ]
       
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
