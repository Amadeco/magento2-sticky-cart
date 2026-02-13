<?php
/**
 * @category  Amadeco
 * @package   Amadeco_StickyCart
 * @copyright Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\StickyCart\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * StickyCart Configuration Provider.
 *
 * This class provides access to module configuration values.
 * It strictly separates configuration retrieval from framework overhead.
 */
class Data
{
    /**
     * Config paths for sticky cart settings.
     */
    public const string XML_PATH_STICKYCART_ENABLED          = 'stickycart/general/enabled';
    public const string XML_PATH_STICKYCART_SHOW_IMAGE       = 'stickycart/general/show_image';
    public const string XML_PATH_STICKYCART_SHOW_NAME        = 'stickycart/general/show_name';
    public const string XML_PATH_STICKYCART_SHOW_PRICE       = 'stickycart/general/show_price';
    public const string XML_PATH_STICKYCART_SHOW_SKU         = 'stickycart/general/show_sku';
    public const string XML_PATH_STICKYCART_SHOW_AVAILABILITY = 'stickycart/general/show_availability';
    public const string XML_PATH_STICKYCART_SHOW_CART        = 'stickycart/general/show_cart';

    /**
     * Data constructor.
     *
     * Utilizes PHP 8.3 Constructor Property Promotion.
     * We inject ScopeConfigInterface directly, bypassing the heavy Context object.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * Check if sticky cart is enabled.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function isEnabled(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_STICKYCART_ENABLED, $scopeType, $scopeCode);
    }

    /**
     * Check if product image can be shown.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowImage(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_STICKYCART_SHOW_IMAGE, $scopeType, $scopeCode);
    }

    /**
     * Check if product name can be shown.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowName(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_STICKYCART_SHOW_NAME, $scopeType, $scopeCode);
    }

    /**
     * Check if product price can be shown.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowPrice(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_STICKYCART_SHOW_PRICE, $scopeType, $scopeCode);
    }

    /**
     * Check if product SKU can be shown.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowSku(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_STICKYCART_SHOW_SKU, $scopeType, $scopeCode);
    }

    /**
     * Check if product availability can be shown.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowAvailability(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->isSetFlag(self::XML_PATH_STICKYCART_SHOW_AVAILABILITY, $scopeType, $scopeCode);
    }

    /**
     * Check if add to cart button can be shown.
     *
     * Note: We type hint against Product (Model) because ProductInterface
     * does not strictly contain the `isSaleable` method.
     *
     * @param Product|ProductInterface $product
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowCart(
        ProductInterface $product,
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        // Safe check: ensure the object actually has the method before calling it
        // to prevent fatal errors if a DTO is passed.
        $isSaleable = method_exists($product, 'isSaleable') ? $product->isSaleable() : false;

        return $isSaleable && $this->isSetFlag(self::XML_PATH_STICKYCART_SHOW_CART, $scopeType, $scopeCode);
    }

    /**
     * Internal wrapper for flag retrieval to maintain DRY.
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
