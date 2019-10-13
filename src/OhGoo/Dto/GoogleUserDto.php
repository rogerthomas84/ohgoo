<?php
namespace OhGoo\Dto;


class GoogleUserDto
{
    /**
     * @var string
     */
    private $id = null;

    /**
     * @var string
     */
    private $email = null;

    /**
     * @var bool
     */
    private $emailVerified = null;

    /**
     * @var string
     */
    private $firstName = null;

    /**
     * @var string
     */
    private $lastName = null;

    /**
     * @var string
     */
    private $locale = null;

    /**
     * @var string
     */
    private $picture = null;

    /**
     * Set the users Google ID.
     *
     * @param string $val
     * @return $this
     */
    public function setId($val)
    {
        $this->id = $val;
        return $this;
    }

    /**
     * Set the users email address.
     *
     * @param string $val
     * @return $this
     */
    public function setEmail($val)
    {
        $this->email = $val;
        return $this;
    }

    /**
     * Set the first name of the user.
     *
     * @param string $val
     * @return $this
     */
    public function setFirstName($val)
    {
        $this->firstName = $val;
        return $this;
    }

    /**
     * Set the last name of the user.
     *
     * @param string $val
     * @return $this
     */
    public function setLastName($val)
    {
        $this->lastName = $val;
        return $this;
    }

    /**
     * Set the locale of the user.
     *
     * @param string $val
     * @return $this
     */
    public function setLocale($val)
    {
        $this->locale = $val;
        return $this;
    }

    /**
     * Set the profile picture URL of the user.
     *
     * @param string $val
     * @return $this
     */
    public function setProfilePictureUrl($val)
    {
        $this->picture = $val;
        return $this;
    }

    /**
     * Set whether the user is verified with Google.
     *
     * @param bool $verified
     * @return $this
     */
    public function setEmailVerified($verified)
    {
        $this->emailVerified = $verified;
        return $this;
    }

    /**
     * Get the users Google ID, this is a string.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the users email address as returned by Google.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Is the user verified with Google
     *
     * @return bool
     */
    public function getEmailVerified()
    {
        return $this->isEmailVerified();
    }

    /**
     * Is the user verified with Google
     *
     * @return bool
     */
    public function isEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * Get the first name of the user.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Get the last name of the user.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get the profile picture URL of the user.
     *
     * @return string
     */
    public function getProfilePictureUrl()
    {
        return $this->picture;
    }

    /**
     * Get the locale of the user.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Taking an array of data, populate a new instance of this DTO.
     *
     * @param array $data
     * @return GoogleUserDto
     */
    public static function fromArray($data)
    {
        $inst = new GoogleUserDto();
        $inst->setId($data['id']);
        $inst->setEmail($data['email']);
        $inst->setEmailVerified($data['verified']);
        $inst->setFirstName($data['firstName']);
        $inst->setLastName($data['lastName']);
        $inst->setLocale($data['locale']);
        $inst->setProfilePictureUrl($data['picture']);

        return $inst;
    }

    /**
     * Get an array representation of this DTO.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'email' => $this->getEmail(),
            'id' => $this->getId(),
            'verified' => $this->isEmailVerified(),
            'locale' => $this->getLocale(),
            'picture' => $this->getProfilePictureUrl()
        ];
    }
}
