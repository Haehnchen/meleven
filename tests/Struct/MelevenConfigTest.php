<?php

namespace Shopware\SmMeleven\Struct;

use Shopware\Components\Test\Plugin\TestCase;

class MelevenConfigTest extends TestCase
{
    protected static $ensureLoadedPlugins = array(
        'SmArticleExporter' => array()
    );

    public function setUp()
    {
        parent::setUp();
        Shopware()->Plugins()->Backend()->SmArticleExporter();
    }

    public function testCreateFromConfig()
    {
        $config = MelevenConfig::createFormConfigArray([
            'sm_meleven_enabled' => true,
            'sm_meleven_user' => 'user',
            'sm_meleven_password' => 'password',
            'sm_meleven_channel' => 'foo',
        ]);

        $this->assertEquals('user', $config->getUser());
        $this->assertEquals('password', $config->getPassword());
        $this->assertEquals('foo', $config->getChannel());
        $this->assertTrue($config->isEnabled());
    }

    public function testCreateFromConfigAsDefault()
    {
        $config = MelevenConfig::createFormConfigArray([]);
        $this->assertFalse($config->isEnabled());
    }

    public function testCreateFromConfigInvalid()
    {
        $config = MelevenConfig::createFormConfigArray([
            'password' => 'foo',
            'sm_meleven_foo' => null,
        ]);

        $this->assertNull($config->getPassword());
    }
}