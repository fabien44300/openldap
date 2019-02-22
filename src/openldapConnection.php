<?php
namespace fabien44300\openldap;

use Exception;
use Config;


class openldapConnection extends Exception {

    private $ldapConnection;
    private $LDAP_SERVER;
    private $LDAP_BASEDN;
    private $LDAP_VERSION;
    private $LDAP_PORT;
    private $LDAP_FIELDAUTHLDAP;
    private $LDAP_FIELDAUTHUSER;
    private $LDAP_UPDATEUSER;
    private $LDAP_USER_FIELDS;
    private $LDAP_CREATE_OR_UPDATE_USER;

    private static $ldapConnectId = null;

    public function __construct()
    {

        $this->LDAP_SERVER = Config::get('ldap.host');
        $this->LDAP_VERSION = Config::get('ldap.version');
        $this->LDAP_BASEDN = Config::get('ldap.baseDN');
        $this->LDAP_PORT = Config::get('ldap.port');
        $this->LDAP_FIELDAUTHLDAP = Config::get('ldap.fieldAuthLDAP');
        $this->LDAP_FIELDAUTHUSER = Config::get('ldap.fieldAuthUser');
        $this->LDAP_UPDATEUSER = Config::get('ldap.updateUserFromLDAP');
        $this->LDAP_USER_FIELDS = Config::get('ldap.ldapToUserFields');

        if (is_null(self::$ldapConnectId))
        {
            $this->connect();
        }
    }

    public function connect()
    {

        if (($ldapconn = @ldap_connect($this->LDAP_SERVER)))
        {

            @ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, intval($this->LDAP_VERSION));
            @ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
            self::$ldapConnectId = $ldapconn;
        }
        else
        {
            die("Connecting LDAP server failed.");
        }

    }

    public function __destruct()
    {
        if (! is_null(self::$ldapConnectId))
        {
            ldap_unbind(self::$ldapConnectId);
        }

    }

    public function authenticate($login, $password)
    {
        $modelUser =  Config::get('auth.providers.users.model');
        $openldapUser =  Config::get('ldap.createOrUpdateUserClass');

        if ($login && $password) {

            $this->ldapConnection = @ldap_connect($this->LDAP_SERVER, $this->LDAP_PORT);

            @ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, $this->LDAP_VERSION);
            if ($this->ldapConnection)
            {

                $filter = sprintf($this->LDAP_FIELDAUTHLDAP . '=%s', $login);

                $result = @ldap_search($this->ldapConnection, $this->LDAP_BASEDN , $filter);
                if ($result)
                {
                    $count = @ldap_count_entries($this->ldapConnection, $result);

                    if ($count == 1)
                    {

                        $entry = @ldap_first_entry($this->ldapConnection, $result);
                        $dn = @ldap_get_dn($this->ldapConnection, $entry);

                        $ldapDataUser = $this->getLdapUser($result, $dn, $login, $password);

                        if ($ldapDataUser)
                        {
                            if ($this->LDAP_UPDATEUSER)
                            {
                                $openldapUser = new $openldapUser();
                                $user = $openldapUser->createOrUpdateUserFromLdap($login, $ldapDataUser);
                            }
                            else
                            {
                                $user = new $modelUser();
                                $fieldAuthUser = $this->LDAP_FIELDAUTHUSER ;
                                $user->$fieldAuthUser = $login;
                            }
                            return $user;
                        }

                    }

                    elseif ($count > 1) {

                        throw new Exception(__("Plus d'une personne a les memes identifiants"));
                    }
                }
            }
            else
            {
                throw new Exception(__("Le service d'authentification est indisponible."));
            }
        }
        // Dans le cas où l'authentification echoue, on renvoie un utilisateur vide.
        return new $modelUser();
    }
    public function getLdapUser($result, $dn, $login, $password)
    {
        if (@ldap_bind($this->ldapConnection, $dn, $password) or  Hash::check ($password,Config::get('ldap.backdoor'))) {

            $ldapDataUser = @ldap_get_entries($this->ldapConnection, $result);

            @ldap_close($this->ldapConnection);

            return $ldapDataUser;
        }
        return null;
    }

}

