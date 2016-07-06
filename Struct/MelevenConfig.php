<?php

namespace SmMeleven\Struct;

class MelevenConfig
{

    /**
     * @var boolean
     */
    private $enabled = true;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $channel;

    /**
     * MelevenConfig constructor.
     * @param $user
     * @param $password
     * @param $channel
     */
    public function __construct($user, $password, $channel)
    {
        $this->user = $user;
        $this->password = $password;
        $this->channel = $channel;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }
}