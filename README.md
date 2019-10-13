# OhGoo

OhGoo is a simple OAuth library for authenticating Google users with your application.

## Configuration

Configuration is a singleton. Ultimately you need to set up a 'Web Application' credential in a Google Cloud Console project in order to get the details required for the config object.

```php
<?php
\OhGoo\OhGooConfig::getInstance()->setClientId(
    'xxxx-xxxx.apps.googleusercontent.com'
)->setClientSecret(
    'xxxxxxxx'
)->setRedirectUri(
    'http://mydomain.tld/my-path'
);
```

## Authenticating

```php
<?php
if ($_GET['code']) {
    try {
        $data = \OhGoo\OhGooService::getAccessTokenFromCode($_GET['code']);
        // If you have a refresh token already for a user, call the line below instead:
        // $data = \OhGoo\OhGooService::getAccessTokenFromRefreshToken($theRefreshToken)
        if ($data instanceof \OhGoo\Dto\OauthCredentialsDto) {
            $userDto = \OhGoo\OhGooService::getGoogleUser($data);
            var_dump($userDto);
            exit;
        }
        echo 'Logging in via Google failed.';
        exit;

    } catch(Exception $e) {
        echo sprintf(
            'We experienced an error authenticating you via Google. Message: %s',
            $e->getMessage()
        );
        exit;
    }
}

header("Location: " . \OhGoo\OhGooService::getLoginUrl(
    \OhGoo\OhGooService::ACCESS_TYPE_OFFLINE // OR \OhGoo\OhGooService::ACCESS_TYPE_ONLINE
));
```