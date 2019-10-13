<?php
namespace OhGoo;

use Monolog\Logger;

class OhGooConfig
{
    /**
     * Instance of this class
     *
     * @var OhGooConfig
     */
    protected static $_instance = null;

    /**
     * @var Logger|null
     */
    private $logger = null;

    /**
     * @var string|null
     */
    private $clientId = null;

    /**
     * @var string|null
     */
    private $clientSecret = null;

    /**
     * @var string|null
     */
    private $redirectUri = null;

    /**
     * Set the instance of Logger
     *
     * @param Logger $logger
     * @return $this
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Set the client ID as shown on the credentials area of GCP console.
     *
     * @param string $val
     * @return $this
     */
    public function setClientId($val)
    {
        $this->clientId = $val;
        return $this;
    }

    /**
     * Set the client secret as shown on the credentials area of GCP console.
     *
     * @param string $val
     * @return $this
     */
    public function setClientSecret($val)
    {
        $this->clientSecret = $val;
        return $this;
    }

    /**
     * Set the URL of where the user should return to after confirming authentication with Google.
     *
     * @param string $val
     * @return $this
     */
    public function setRedirectUri($val)
    {
        $this->redirectUri = $val;
        return $this;
    }

    /**
     * Get the client ID as shown on the credentials area of GCP console.
     *
     * @return string|null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get the client secret as shown on the credentials area of GCP console.
     *
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Get the URL of where the user should return to after confirming authentication with Google.
     *
     * @return string|null
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Get the instance of Monolog.
     *
     * @return Logger|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Has the Logger been applied?
     *
     * @return bool
     */
    public function hasLogger()
    {
        return $this->getLogger() instanceof Logger;
    }

    /**
     * Retrieve an instance of this object
     *
     * @return OhGooConfig
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
