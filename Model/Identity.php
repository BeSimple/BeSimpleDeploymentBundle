<?php

namespace BeSimple\DeploymentBundle\Model;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Identity
{
    /**
     * @var string
     */
    protected $user;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $publicKeyFile;

    /**
     * @var string|null
     */
    protected $privateKeyFile;

    /**
     * @var string|null
     */
    protected $keyPassphrase;

    public function __construct($user = null)
    {
        $this->user           = $user;
        $this->password       = null;
        $this->publicKeyFile  = null;
        $this->privateKeyFile = null;
        $this->keyPassphrase  = null;
    }

    /**
     * @return boolean
     */
    public function hasKey()
    {
        return $this->getPublicKeyFile() && $this->getPrivateKeyFile();
    }

    /**
     * @param null|string $keyPassphrase
     */
    public function setKeyPassphrase($keyPassphrase)
    {
        $this->keyPassphrase = $keyPassphrase;
    }

    /**
     * @return null|string
     */
    public function getKeyPassphrase()
    {
        return $this->keyPassphrase;
    }

    /**
     * @param null|string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null|string $privateKeyFile
     */
    public function setPrivateKeyFile($privateKeyFile)
    {
        $this->privateKeyFile = $privateKeyFile;
    }

    /**
     * @return null|string
     */
    public function getPrivateKeyFile()
    {
        return $this->privateKeyFile;
    }

    /**
     * @param null|string $publicKeyFile
     */
    public function setPublicKeyFile($publicKeyFile)
    {
        $this->publicKeyFile = $publicKeyFile;
    }

    /**
     * @return null|string
     */
    public function getPublicKeyFile()
    {
        return $this->publicKeyFile;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }
}
