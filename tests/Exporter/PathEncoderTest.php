<?php

namespace Shopware\SmMeleven\Exporter;

use Shopware\Components\Test\Plugin\TestCase;
use Shopware\SmMeleven\Struct\MelevenConfig;

class PathEncoderTest extends TestCase
{
    protected static $ensureLoadedPlugins = array(
        'SmArticleExporter' => array()
    );

    public function setUp()
    {
        parent::setUp();
        Shopware()->Plugins()->Backend()->SmArticleExporter();
    }

    /**
     * @covers Shopware\SmMeleven\Exporter\PathEncoder::buildPath
     */
    public function testThatShopwarePathAreConverted()
    {
        $encoder = $this->createEncoderWithAliasValue('12.34.45.SW10161_3.jpg');

        $config = MelevenConfig::createFormConfigArray([
            'sm_meleven_channel' => 'premiumstore'
        ]);

        $this->assertEquals(
            'out/premiumstore/12.34.45.SW10161_3.jpg',
            $encoder->buildPath($config, 'image/SW10161_3.jpg')
        );

        $this->assertEquals(
            'out/premiumstore/h_2560,w_2560,m_limit,o_pad,c_ffffff/12.34.45.SW10161_3.jpg',
            $encoder->buildPath($config, 'media/image/00/0e/39/SW10161_3_1280x1280@2x.jpg')
        );

        $this->assertEquals(
            'out/premiumstore/h_1280,w_1280,m_limit,o_pad,c_ffffff/12.34.45.SW10161_3.jpg',
            $encoder->buildPath($config, 'media/image/00/0e/39/SW10161_3_1280x1280.jpg')
        );
    }

    /**
     * @covers Shopware\SmMeleven\Exporter\PathEncoder::buildPath
     */
    public function testThatUnknownImageShouldProvideDummyFallback()
    {
        $encoder = $this->createEncoderWithAliasValue(null);

        $config = MelevenConfig::createFormConfigArray([
            'sm_meleven_channel' => 'premiumstore'
        ]);

        $this->assertEquals(
            'out/premiumstore/00.00.00.SW10161_3.jpg',
            $encoder->buildPath($config, 'image/SW10161_3.jpg')
        );

        $this->assertEquals(
            'out/premiumstore/h_2560,w_2560,m_limit,o_pad,c_ffffff/00.00.00.SW10161_3.jpg',
            $encoder->buildPath($config, 'media/image/00/0e/39/SW10161_3_1280x1280@2x.jpg')
        );
    }

    /**
     * @return PathEncoder
     */
    private function createEncoderWithAliasValue($value)
    {
        $finder = $this->getMockBuilder('Shopware\SmMeleven\Exporter\AliasFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $finder->expects($this->any())
            ->method('findAliasForBasename')
            ->willReturn($value);

        return new PathEncoder($finder);
    }
}