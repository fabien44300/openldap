<?php

namespace fabien44300\openldap;

use Config;

class openldapUser
{
    public function createOrUpdateUserFromLdap($identifier, $ldapDataUser)
    {
        $modelUser =  Config::get('auth.providers.users.model');

        $user = $modelUser::where(Config::get('ldap.fieldAuthUser'), $identifier)->first();

        if (!$user)
        {
            $user = new $modelUser();
            $user = $this->updateUser( $user, $ldapDataUser);
            $user->save();
        }
        else
        {
            $user = $this->updateUser($user, $ldapDataUser);
            $user->update();
        }

        return $user;
    }

    private function updateUser ($user, $ldapDataUser )
    {
        $tableauConversion = Config::get('ldap.ldapToUserFields');

        foreach ($tableauConversion as $ldapfield => $userfield)
        {
            if ( isset($ldapDataUser[0][$ldapfield][0]))
            {
                $user->$userfield = $ldapDataUser[0][$ldapfield][0];
            }
        }

        return $user;
    }
}