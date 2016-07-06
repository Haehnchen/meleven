<?php

namespace Shopware\SmMeleven\tests\Utils;

use Shopware\Components\Test\Plugin\TestCase;
use Shopware\SmMeleven\Utils\MelevenUtil;

class MelevenUtilTest extends TestCase
{
    protected static $ensureLoadedPlugins = array(
        'SmArticleExporter' => array()
    );

    public function setUp()
    {
        parent::setUp();
        Shopware()->Plugins()->Backend()->SmArticleExporter();
    }

    public function testNormalizePath()
    {
        $this->assertEquals('foo/foo/foo.jpg', MelevenUtil::normalizePath('foo/foo/00.00.00.foo.jpg'));
    }

    public function testIsDerivativesPath()
    {
        $this->assertTrue(MelevenUtil::isDerivativesPath('out/premiumstore/h_144,w_120,m_limit,o_pad,c_ffffff/d9.e2.93.SW10104_2.jpg'));
        $this->assertTrue(MelevenUtil::isDerivativesPath('out/pre-miumstore/h_144,w_120,m_limit,o_pad,c_ffffff/d9.e2.93.SW10104_2.jpg'));
        $this->assertTrue(MelevenUtil::isDerivativesPath('out/pre_miumstore/h_144,w_120,m_limit,o_pad,c_ffffff/d9.e2.93.SW10104_2.jpg'));
        $this->assertTrue(MelevenUtil::isDerivativesPath('out/pre_mium33store/h_144,w_120,m_limit,o_pad,c_ffffff/d9.e2.93.SW10104_2.jpg'));

        $this->assertFalse(MelevenUtil::isDerivativesPath('/out/premiumstore/h_144,w_120,m_limit,o_pad,c_ffffff/d9.e2.93.SW10104_2.jpg'));
        $this->assertFalse(MelevenUtil::isDerivativesPath('out/premiumstore/..../d9.e2.93.SW10104_2.jpg'));
        $this->assertFalse(MelevenUtil::isDerivativesPath('/out/premiumstore/,/d9.e2.SW10104_2.jpg'));
    }
}