<?php

class PluginTest extends Shopware\Components\Test\Plugin\TestCase
{
    protected static $ensureLoadedPlugins = array(
        'SmMeleven' => array(
        )
    );

    public function setUp()
    {
        parent::setUp();

        $helper = \TestHelper::Instance();
        $loader = $helper->Loader();


        $pluginDir = getcwd() . '/../';

        $loader->registerNamespace(
            'Shopware\\SmMeleven',
            $pluginDir
        );
    }

    public function testCanCreateInstance()
    {
        /** @var Shopware_Plugins_Frontend_SmMeleven_Bootstrap $plugin */
        $plugin = Shopware()->Plugins()->Frontend()->SmMeleven();

        $this->assertInstanceOf('Shopware_Plugins_Frontend_SmMeleven_Bootstrap', $plugin);
    }
}