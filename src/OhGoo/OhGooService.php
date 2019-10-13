<?php
namespace OhGoo;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OhGoo\Dto\GoogleUserDto;
use OhGoo\Dto\OAuthCredentialsDto;

class OhGooService
{
    /**
     * @var string[]
     */
    protected static $scopes = [
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/userinfo.email',
        'openid'
    ];

    /**
     * Just gain an access token for this user.
     */
    const ACCESS_TYPE_ONLINE = 'online';

    /**
     * Require a refresh token also to be able to refresh the authentication.
     */
    const ACCESS_TYPE_OFFLINE = 'offline';

    /**
     * When exchanging a code from an 'online' access request.
     *
     * @var int
     */
    protected static $ACCESS_TOKEN_FROM_CODE = 0;

    /**
     * When exchanging a code from an 'offline' access request.
     *
     * @var int
     */
    protected static $ACCESS_TOKEN_FROM_REFRESH = 1;

    /**
     * Generate a login URL for either an online or offline auth flow.
     *
     * @param string $accessType
     * @return string
     */
    public static function getLoginUrl($accessType=self::ACCESS_TYPE_ONLINE)
    {
        $config = OhGooConfig::getInstance();
        return sprintf(
            'https://accounts.google.com/o/oauth2/v2/auth?scope=%s&redirect_uri=%s&response_type=code&client_id=%s&access_type=%s',
            urlencode(implode(' ', OhGooService::$scopes)),
            $config->getRedirectUri(),
            $config->getClientId(),
            $accessType
        );
    }

    /**
     * Exchange an authentication code for a new access token.
     *
     * @param string $code
     * @return bool|OAuthCredentialsDto
     * @throws GuzzleException
     */
    public static function getAccessTokenFromCode($code)
    {
        return self::_getAccessToken($code, self::$ACCESS_TOKEN_FROM_CODE);
    }

    /**
     * Exchange a refresh token for a new access token.
     *
     * @param string $refreshToken
     * @return bool|OAuthCredentialsDto
     * @throws GuzzleException
     */
    public static function getAccessTokenFromRefreshToken($refreshToken)
    {
        return self::_getAccessToken($refreshToken, self::$ACCESS_TOKEN_FROM_REFRESH);
    }

    /**
     * Get the Google user account information.
     *
     * @param OAuthCredentialsDto $authDto
     * @return bool|GoogleUserDto
     * @throws GuzzleException
     */
    public static function getGoogleUser(OAuthCredentialsDto $authDto)
    {
        $config = OhGooConfig::getInstance();
        try {
            $data = [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $authDto->getAccessToken())
                ]
            ];

            $client = new Client();
            $response = $client->request('GET', 'https://www.googleapis.com/oauth2/v2/userinfo', $data);
            $body = $response->getBody();

            if ($response->getStatusCode() !== 200) {
                if ($config->hasLogger()) {
                    $config->getLogger()->addError(
                        sprintf(
                            '%s::%s: Invalid response code, expected "200" received "%s"',
                            __CLASS__,
                            __METHOD__,
                            $response->getStatusCode()
                        )
                    );
                }
                return false;
            }
            $decoded = json_decode($body->__toString(), true);
            if (!$decoded) {
                if ($config->hasLogger()) {
                    $config->getLogger()->addError(
                        sprintf(
                            '%s::%s: Unable to decode JSON from response.',
                            __CLASS__,
                            __METHOD__
                        )
                    );
                }
                return false;
            }

            $requiredKeys = ['id', 'email', 'verified_email', 'given_name', 'family_name', 'locale', 'picture'];
            foreach ($requiredKeys as $requiredKey) {
                if (!array_key_exists($requiredKey, $decoded)) {
                    if ($config->hasLogger()) {
                        $config->getLogger()->addError(
                            sprintf(
                                '%s::%s: Missing required key of "%s".',
                                __CLASS__,
                                __METHOD__,
                                $requiredKey
                            )
                        );
                    }
                    return false;
                }
            }

            $dto = new GoogleUserDto();
            $dto->setId($decoded['id']);
            $dto->setEmail($decoded['email']);
            $dto->setEmailVerified($decoded['verified_email']);
            $dto->setFirstName($decoded['given_name']);
            $dto->setLastName($decoded['family_name']);
            $dto->setLocale($decoded['locale']);
            $dto->setProfilePictureUrl($decoded['picture']);

            return $dto;

        } catch (Exception $e) {
            if ($config->hasLogger()) {
                $config->getLogger()->addCritical($e->getMessage());
            }
            return false;
        }
    }

    /**
     * Get an access token dependant on the origin.
     *
     * @param string $authString
     * @param int $accessTokenFrom
     * @return bool|OAuthCredentialsDto
     * @throws GuzzleException
     */
    protected static function _getAccessToken($authString, $accessTokenFrom)
    {
        $config = OhGooConfig::getInstance();
        try {
            $data = [
                'form_params' => [
                    'client_id' => $config->getClientId(),
                    'client_secret' => $config->getClientSecret(),
                    'redirect_uri' => $config->getRedirectUri()
                ]
            ];

            if ($accessTokenFrom === self::$ACCESS_TOKEN_FROM_CODE) {
                $data['form_params']['code'] = $authString;
                $data['form_params']['grant_type'] = 'authorization_code';
            } elseif ($accessTokenFrom === self::$ACCESS_TOKEN_FROM_REFRESH) {
                $data['form_params']['refresh_token'] = $authString;
                $data['form_params']['grant_type'] = 'refresh_token';
            } else {
                if ($config->hasLogger()) {
                    $config->getLogger()->addError(
                        sprintf(
                            '%s::%s: Invalid access token gain type, expected 0 or 1 (int).',
                            __CLASS__,
                            __METHOD__
                        )
                    );
                }
                return false;
            }

            $client = new Client();
            $response = $client->request('POST', 'https://www.googleapis.com/oauth2/v4/token', $data);
            $body = $response->getBody();
            if ($response->getStatusCode() !== 200) {
                if ($config->hasLogger()) {
                    $config->getLogger()->addError(
                        sprintf(
                            '%s::%s: Invalid status code, expected "200" received "%s"',
                            __CLASS__,
                            __METHOD__,
                            $response->getStatusCode()
                        )
                    );
                }
                return false;
            }
            $decoded = json_decode($body->__toString(), true);
            if (!$decoded) {
                if ($config->hasLogger()) {
                    $config->getLogger()->addError(
                        sprintf(
                            '%s::%s: Unable to decode JSON from response.',
                            __CLASS__,
                            __METHOD__
                        )
                    );
                }
                return false;
            }

            $requiredKeys = ['access_token', 'expires_in', 'scope'];
            foreach ($requiredKeys as $requiredKey) {
                if (!array_key_exists($requiredKey, $decoded)) {
                    if ($config->hasLogger()) {
                        $config->getLogger()->addError(
                            sprintf(
                                '%s::%s: Missing required key of "%s".',
                                __CLASS__,
                                __METHOD__,
                                $requiredKey
                            )
                        );
                    }
                    return false;
                }
            }

            $dto = new OAuthCredentialsDto();
            $dto->setExpiresInSeconds($decoded['expires_in']);
            $dto->setAccessToken($decoded['access_token']);
            if (array_key_exists('refresh_token', $decoded)) {
                $dto->setRefreshToken($decoded['refresh_token']);
            } else {
                if ($accessTokenFrom === self::$ACCESS_TOKEN_FROM_REFRESH) {
                    $dto->setRefreshToken($authString);
                }
            }

            return $dto;

        } catch (Exception $e) {
            if ($config->hasLogger()) {
                $config->getLogger()->addCritical($e->getMessage());
            }
            return false;
        }
    }
}
