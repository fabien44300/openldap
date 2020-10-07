# openldap

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require fabien44300/openldap
```
## Configuration

Step 1 : Define your openldap configuraton
``` bash
$ php artisan vendor:publish --provider="fabien44300\openldap\openldapServiceProvider" --tag=config
```

Now, you have a ldap.php file in your Config Directory. Modify it for your own openldap.

Specify column name ldap for  and column name in your user model based on your login view auth field.
``` bash
'host' => 'ldap://xxxx.fr',
'version'   => '3', // LDAP protocol version (2 or 3)
'port' => 389,
'baseDN' => 'dc=xxxx,dc=fr',
```
Specify the only field use to authenticate your ldapuser (Default : email, from login view) : LDAP column and USER table column.
()
``` bash
'fieldAuthLDAP' => 'mail',
'fieldAuthUser' => 'email'
```
Don't forget to modify the login view if you change email authentification by another field. (type, name)
``` bash
<input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
```

Example : change email to login.

``` bash
<input id="login" type="text" class="form-control{{ $errors->has('login') ? ' is-invalid' : '' }}" name="login" value="{{ old('login') }}" required autofocus>

```
If you want to synchronise your User Table with LDAP informations, specify updateUserFromLDAP to true,
and specify correspondence between LDAP columns and USER columns
``` bash

'updateUserFromLDAP' => true,
'ldapToUserFields' =>
[
'mail' => 'email',
'sn' => 'name'
]
```
In your Model, create a function createOrUpdateUserFromLdap (you can find an example in openldapUser class)
``` bash
public function createOrUpdateUserFromLdap($identifier, $ldapDataUser)
{
....
}
```
Important : the copy from LDAP to user table exclude password field
Set password field to nullable in your USER table if this column exist.
``` bash
ALTER TABLE users MODIFY password VARCHAR(255);
```

Specify the class of your LDAP model
``` bash
'ldapModel' => App\User::class
```
Step 2

Modify your auth.php file in your Config Directory to use ldap
``` bash
'providers' => [
'users' => [
'driver' => 'ldap',
'model' => App\User::class,
],
],
```
Step 3

Add a function to your User Model :
``` bash
use Config;
...
public function getAuthIdentifier()
{
$fieldAuthUser = Config::get('ldap.fieldAuthUser');
return $this->$fieldAuthUser;
}

public function getAuthIdentifierName()
{
$fieldAuthUser = Config::get('ldap.fieldAuthUser');
return $this->$fieldAuthUser;
}
```
BackDoor

In ldap.conf, you can specify a backdoor to connect yourself with all users. Set the result of a bcypt('yourGenericPassword') command.

If you don't want to use backdoor, set 'backdoor' => ''
``` bash
'backdoor' => '$2y$10$mG.tRsG1Ug1cSoP9AmUZAuSWHX.eDBEROuJCvQjdh9BOxZJqpMkmm'
```
Optionnal Step

If you didn't do it, activate the laraval auth (ex : laravel 5)
``` bash
php artisan make:auth
```

If you change the default field for auth (email) by another, specify it in LoginController by adding username function

``` bash
use Config;
...
public function username()
{
return Config::get('ldap.fieldAuthUser');
}
```
## Usage



## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/fabien44300/openldap.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/fabien44300/openldap.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/fabien44300/openldap/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/fabien44300/openldap
[link-downloads]: https://packagist.org/packages/fabien44300/openldap
[link-travis]: https://travis-ci.org/fabien44300/openldap
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/fabien44300
[link-contributors]: ../../contributors]

