<?php
/**
 * @category  Amadeco
 * @package   Amadeco_StickyCart
 * @copyright Ilan Parmentier
 */
declare(strict_types=1);

namespace Amadeco\StickyCart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * StickyCart Helper Data
 *
 * Helper class to access module configuration values and determine
 * if various elements of the sticky cart should be displayed
 */
class Data extends AbstractHelper
{
    /**
     * Config paths for sticky cart settings
     */
    public const XML_PATH_STICKYCART_ENABLED = 'stickycart/general/enabled';
    public const XML_PATH_STICKYCART_CAN_SHOW_IMAGE = 'stickycart/general/show_image';
    public const XML_PATH_STICKYCART_CAN_SHOW_NAME = 'stickycart/general/show_name';
    public const XML_PATH_STICKYCART_CAN_SHOW_PRICE = 'stickycart/general/show_price';
    public const XML_PATH_STICKYCART_CAN_SHOW_SKU = 'stickycart/general/show_sku';
    public const XML_PATH_STICKYCART_CAN_SHOW_AVAILABILITY = 'stickycart/general/show_availability';
    public const XML_PATH_STICKYCART_CAN_SHOW_CART = 'stickycart/general/show_cart';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Check if sticky cart is enabled
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function isEnabled(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STICKYCART_ENABLED,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Check if product image can be shown
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowImage(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STICKYCART_CAN_SHOW_IMAGE,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Check if product name can be shown
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowName(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STICKYCART_CAN_SHOW_NAME,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Check if product price can be shown
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowPrice(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STICKYCART_CAN_SHOW_PRICE,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Check if product SKU can be shown
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowSku(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STICKYCART_CAN_SHOW_SKU,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Check if product availability can be shown
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowAvailability(
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STICKYCART_CAN_SHOW_AVAILABILITY,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * Check if add to cart button can be shown
     *
     * @param ProductInterface $product
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function canShowCart(
        ProductInterface $product,
        string $scopeType = ScopeInterface::SCOPE_STORE,
        ?string $scopeCode = null
    ): bool {
        return $product->isSaleable() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_STICKYCART_CAN_SHOW_CART,
            $scopeType,
            $scopeCode
        );
    }
}