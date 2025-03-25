<?php
/**
 * @category  Amadeco
 * @package   Amadeco_StickyCart
 * @copyright Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\StickyCart\Test\Unit\Helper;

use Amadeco\StickyCart\Helper\Data;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Data Helper Test
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var ProductInterface|MockObject
     */
    private $productMock;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->willReturn($this->scopeConfigMock);

        $this->productMock = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->helper = new Data($this->contextMock);
    }

    /**
     * Test isEnabled method
     *
     * @return void
     */
    public function testIsEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'stickycart/general/enabled',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(true);

        $this->assertTrue($this->helper->isEnabled());
    }

    /**
     * Test canShowImage method
     *
     * @return void
     */
    public function testCanShowImage(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'stickycart/general/show_image',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(true);

        $this->assertTrue($this->helper->canShowImage());
    }

    /**
     * Test canShowCart method
     *
     * @return void
     */
    public function testCanShowCart(): void
    {
        $this->productMock->expects($this->once())
            ->method('isSaleable')
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'stickycart/general/show_cart',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(true);

        $this->assertTrue($this->helper->canShowCart($this->productMock));
    }

    /**
     * Test canShowCart method with non-saleable product
     *
     * @return void
     */
    public function testCanShowCartWithNonSaleableProduct(): void
    {
        $this->productMock->expects($this->once())
            ->method('isSaleable')
            ->willReturn(false);

        $this->scopeConfigMock->expects($this->never())
            ->method('isSetFlag');

        $this->assertFalse($this->helper->canShowCart($this->productMock));
    }
}