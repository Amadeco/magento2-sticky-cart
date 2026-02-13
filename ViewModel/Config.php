<?php
/**
 * Amadeco StickyCart module
 *
 * @category  Amadeco
 * @package   Amadeco_StickyCart
 * @copyright Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\StickyCart\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * StickyCart Configuration ViewModel.
 *
 * Serves as the bridge between Backend Configuration and Frontend Templates.
 * Now handles configuration retrieval directly, removing the need for a Helper.
 */
class Config implements ArgumentInterface
{
    /**
     * Config paths for sticky cart settings.
     */
    private const string XML_PATH_ENABLED           = 'stickycart/general/enabled';
    private const string XML_PATH_SHOW_IMAGE        = 'stickycart/general/show_image';
    private const string XML_PATH_SHOW_NAME         = 'stickycart/general/show_name';
    private const string XML_PATH_SHOW_PRICE        = 'stickycart/general/show_price';
    private const string XML_PATH_SHOW_SKU          = 'stickycart/general/show_sku';
    private const string XML_PATH_SHOW_AVAILABILITY = 'stickycart/general/show_availability';
    private const string XML_PATH_SHOW_CART         = 'stickycart/general/show_cart';

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * Check if sticky cart is enabled.
     *
     * @param string $scope
     * @param string|null $scopeCode
     * @return bool
     */
    public function isEnabled(
        string $scope = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_ENABLED, $scope, $scopeCode);
    }

    /**
     * Check if product image can be shown.
     *
     * @param string $scope
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowImage(
        string $scope = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_SHOW_IMAGE, $scope, $scopeCode);
    }

    /**
     * Check if product name can be shown.
     *
     * @param string $scope
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowName(
        string $scope = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_SHOW_NAME, $scope, $scopeCode);
    }

    /**
     * Check if product price can be shown.
     *
     * @param string $scope
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowPrice(
        string $scope = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_SHOW_PRICE, $scope, $scopeCode);
    }

    /**
     * Check if product SKU can be shown.
     *
     * @param string $scope
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowSku(
        string $scope = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_SHOW_SKU, $scope, $scopeCode);
    }

    /**
     * Check if product availability can be shown.
     *
     * @param string $scope
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowAvailability(
        string $scope = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_SHOW_AVAILABILITY, $scope, $scopeCode);
    }

    /**
     * Check if add to cart button can be shown.
     *
     * @param ProductInterface $product
     * @param string $scope
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowCart(
        ProductInterface $product,
        string $scope = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        // Strict check for saleable capability
        $isSaleable = method_exists($product, 'isSaleable') ? $product->isSaleable() : false;

        return $isSaleable && $this->isSetFlag(self::XML_PATH_SHOW_CART, $scope, $scopeCode);
    }

    /**
     * Internal wrapper for flag retrieval.
     *
     * @param string $path
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    private function isSetFlag(string $path, string $scopeType, ?string $scopeCode): bool
    {
        return $this->scopeConfig->isSetFlag($path, $scopeType, $scopeCode);
    }
}
