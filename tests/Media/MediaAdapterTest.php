<?php

namespace Shopware\SmMeleven\Media;

use League\Flysystem\Config;
use Shopware\Components\Test\Plugin\TestCase;
use Shopware\SmMeleven\Exporter\Exception\MediaExportException;
use Shopware\SmMeleven\Struct\MelevenConfig;

class MediaAdapterTest extends TestCase
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
     * @covers Shopware\SmMeleven\Media\MediaAdapter::writeStream
     */
    public function testNonModificationForDerivatives()
    {
        $exporter = $this->getMockBuilder('Shopware\SmMeleven\Exporter\ImageExporter')
            ->disableOriginalConstructor()
            ->getMock();

        $alias = $this->getMockBuilder('Shopware\SmMeleven\Exporter\AliasFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new MediaAdapter($exporter, $alias, MelevenConfig::createFormConfigArray([]));

        $url = 'out/premiumstore/h_144,w_120,m_limit,o_pad,c_ffffff/d9.e2.93.SW10104_2.jpg';

        $this->assertEquals([
            'visibility' => 'public',
            'path' => $url
        ], $adapter->writeStream($url, null, new Config()));
    }

    /**
     * @covers Shopware\SmMeleven\Media\MediaAdapter::writeStream
     */
    public function testUploadForOriginImages()
    {
        $exporter = $this->getMockBuilder('Shopware\SmMeleven\Exporter\ImageExporter')
            ->disableOriginalConstructor()
            ->getMock();

        $alias = $this->getMockBuilder('Shopware\SmMeleven\Exporter\AliasFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $url = 'out/premiumstore/d9.e2.93.SW10104_2.jpg';

        $exporter->expects($this->once())
            ->method('exportMedia')
            ->willReturn($url);

        $adapter = new MediaAdapter($exporter, $alias, MelevenConfig::createFormConfigArray([]));

        $this->assertEquals([
            'visibility' => 'public',
            'path' => $url,
        ], $adapter->writeStream($url, null, new Config()));
    }

    /**
     * @covers Shopware\SmMeleven\Media\MediaAdapter::writeStream
     */
    public function testUploadErrorShouldBeNull()
    {
        $exporter = $this->getMockBuilder('Shopware\SmMeleven\Exporter\ImageExporter')
            ->disableOriginalConstructor()
            ->getMock();

        $alias = $this->getMockBuilder('Shopware\SmMeleven\Exporter\AliasFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $exporter->expects($this->once())
            ->method('exportMedia')
            ->willThrowException(new MediaExportException());

        $adapter = new MediaAdapter($exporter, $alias, MelevenConfig::createFormConfigArray([]));

        $this->assertFalse($adapter->writeStream('out/premiumstore/d9.e2.93.SW10104_2.jpg', null, new Config()));
    }

    /**
     * @covers Shopware\SmMeleven\Media\MediaAdapter::writeStream
     */
    public function testWriteAsContent()
    {
        $exporter = $this->getMockBuilder('Shopware\SmMeleven\Exporter\ImageExporter')
            ->disableOriginalConstructor()
            ->getMock();

        $alias = $this->getMockBuilder('Shopware\SmMeleven\Exporter\AliasFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $url = 'out/premiumstore/d9.e2.93.SW10104_2.jpg';

        $exporter->expects($this->once())
            ->method('exportMedia')
            ->willReturn($url);

        $adapter = new MediaAdapter($exporter, $alias, MelevenConfig::createFormConfigArray([]));

        $result = $adapter->write('out/premiumstore/d9.e2.93.SW10104_2.jpg', null, new Config());

        $this->assertEquals('out/premiumstore/d9.e2.93.SW10104_2.jpg', $result['path']);
        $this->assertEquals('image/jpeg', $result['mimetype']);
    }
}