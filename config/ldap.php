<?php

return
[
    'host' => 'ldap://yourhost.yourdomain.fr',
    'version'   => '3', // LDAP protocol version (2 or 3)
    'port' => 389,
    'baseDN' => 'dc=yourdomain,dc=fr',

    'fieldAuthLDAP' => 'mail',
    'fieldAuthUser' => 'email',

    'updateUserFromLDAP' => true,

    'ldapToUserFields' =>
        [
            'mail' => 'email',
            'sn' => 'name'
        ],

    'createOrUpdateUserClass' => App\openldapUser::class //

    ];

