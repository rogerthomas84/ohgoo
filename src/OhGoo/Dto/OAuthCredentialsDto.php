<?php
namespace OhGoo\Dto;

use DateInterval;
use DateTime;
use Exception;

class OAuthCredentialsDto
{
    /**
     * @var DateTime
     */
    private $created = null;

    /**
     * @var DateTime
     */
    private $expires = null;

    /**
     * @var string
     */
    private $accessToken = null;

    /**
     * @var string
     */
    private $refreshToken = null;

    /**
     * Set the created DateTime object of the oauth credentials.
     *
     * @param DateTime $val
     * @return $this
     */
    public function setCreatedDateTime(DateTime $val)
    {
        $this->created = $val;
        return $this;
    }

    /**
     * Set the expiry DateTime object of the oauth credentials.
     *
     * @param DateTime $val
     * @return $this
     */
    public function setExpiryDateTime(DateTime $val)
    {
        $this->expires = $val;
        return $this;
    }

    /**
     * Set the number of seconds before the token expires.
     *
     * @param int $val
     * @return $this
     */
    public function setExpiresInSeconds($val)
    {
        try {
            if ($val > 30) {
                $val = $val-30;
            }
            $dt = new DateTime();
            $this->created = $dt;
            $ex = clone $dt;
            $ex->add(new DateInterval(sprintf('PT%sS', $val)));
            $this->expires = $ex;
        } catch (Exception $e) {
        }
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setAccessToken($val)
    {
        $this->accessToken = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setRefreshToken($val)
    {
        $this->refreshToken = $val;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @return DateTime
     */
    public function getCreatedDate()
    {
        return $this->created;
    }

    /**
     * @return DateTime
     */
    public function getExpiresDate()
    {
        return $this->expires;
    }

    /**
     * Has the token expired.
     *
     * @return bool
     */
    public function hasExpired()
    {
        try {
            return $this->expires->getTimestamp() > (new DateTime())->getTimestamp();
        } catch (Exception $e) {
            return true;
        }
    }

    /**
     * Taking an array of data, populate a new instance of this DTO.
     *
     * @param array $data
     * @return OAuthCredentialsDto
     * @throws Exception
     */
    public static function fromArray($data)
    {
        $inst = new OAuthCredentialsDto();
        $inst->setAccessToken($data['accessToken']);
        if ($data['refreshToken']) {
            $inst->setRefreshToken($data['refreshToken']);
        }
        if ($data['created'] instanceof DateTime) {
            $inst->setCreatedDateTime($data['created']);
        } else {
            $inst->setCreatedDateTime(new DateTime($data['created']));
        }
        if ($data['expires'] instanceof DateTime) {
            $inst->setExpiryDateTime($data['expires']);
        } else {
            $inst->setExpiryDateTime(new DateTime($data['expires']));
        }

        return $inst;
    }

    /**
     * Get an array representation of this DTO.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'accessToken' => $this->getAccessToken(),
            'expires' => $this->getExpiresDate()->format('Y-m-d H:i:s'),
            'created' => $this->getCreatedDate()->format('Y-m-d H:i:s'),
        ];
        if ($this->getRefreshToken() !== null) {
            $data['refreshToken'] = $this->getRefreshToken();
        }
        return $data;
    }
}
