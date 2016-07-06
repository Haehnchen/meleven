<?php

namespace Shopware\SmMeleven\Media;

use Shopware\Bundle\MediaBundle\Strategy\PlainStrategy;
use Shopware\Components\Test\Plugin\TestCase;
use Shopware\SmMeleven\Struct\MelevenConfig;

class MediaStrategyTest extends TestCase
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
     * @covers Shopware\SmMeleven\Media\MediaStrategy::isEncoded
     */
    public function testThatEncodedImagesAreDetected()
    {
        $mock = $this->getMockBuilder('Shopware\SmMeleven\Exporter\PathEncoder')
            ->disableOriginalConstructor()
            ->getMock();

        $strategy = new MediaStrategy(
            new PlainStrategy(),
            $mock,
            MelevenConfig::createFormConfigArray([])
        );

        $this->assertFalse($strategy->isEncoded('media/image/SW10104.jpg'));
        $this->assertFalse($strategy->isEncoded('media/image/thumbnail/SW10104_200x200@2x.jpg'));

        $this->assertTrue($strategy->isEncoded('api.meleven.de/out/premiumstore/d9.e2.93.SW10104_2.jpg'));
    }
}