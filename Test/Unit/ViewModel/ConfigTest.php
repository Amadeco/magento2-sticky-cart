<?php
/**
 * @category  Amadeco
 * @package   Amadeco_StickyCart
 * @copyright Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\StickyCart\Test\Unit\ViewModel;

use Amadeco\StickyCart\ViewModel\Config;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Config ViewModel Test
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private Config $viewModel;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

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

        $this->productMock = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->viewModel = new Config($this->scopeConfigMock);
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

        $this->assertTrue($this->viewModel->isEnabled());
    }

    /**
     * Test canShowName method
     *
     * @return void
     */
    public function testCanShowName(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'stickycart/general/show_name',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(true);

        $this->assertTrue($this->viewModel->canShowName());
    }

    /**
     * Test canShowPrice method
     *
     * @return void
     */
    public function testCanShowPrice(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'stickycart/general/show_price',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(false);

        $this->assertFalse($this->viewModel->canShowPrice());
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

        $this->assertTrue($this->viewModel->canShowCart($this->productMock));
    }
}