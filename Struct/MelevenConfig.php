<?php

namespace Shopware\SmMeleven\Struct;

class MelevenConfig
{

    /**
     * @var boolean
     */
    private $enabled;

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

    private function __construct(array $config)
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
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

    public static function createFormConfigArray(array $config)
    {
        // strip "sm_meleven_" in config name
        $cfg = [];
        foreach (array_filter($config) as $key => $value) {
            if (strpos($key, 'sm_meleven_') === 0) {
                $cfg[substr($key, 11)] = $value;
            }
        }

        return new MelevenConfig(array_merge([
            'enabled' => false,
        ], $cfg));
    }
}