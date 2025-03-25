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

use Amadeco\StickyCart\Helper\Data as StickyCartHelper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * StickyCart configuration view model
 *
 * Provides configuration values and visibility logic
 * for sticky cart elements in the frontend
 */
class Config implements ArgumentInterface
{
    /**
     * @var StickyCartHelper
     */
    private StickyCartHelper $helper;

    /**
     * Config constructor.
     *
     * @param StickyCartHelper $helper
     */
    public function __construct(
        StickyCartHelper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Check if sticky cart is enabled
     *
     * @param string $scope
     * @param mixed $scopeCode
     * @return bool
     */
    public function isEnabled(
        string $scope = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return $this->helper->isEnabled($scope, $scopeCode);
    }

    /**
     * Check if product image can be shown
     *
     * @param string $scope
     * @param mixed $scopeCode
     * @return bool
     */
    public function canShowImage(
        string $scope = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return $this->helper->canShowImage($scope, $scopeCode);
    }

    /**
     * Check if product name can be shown
     *
     * @param string $scope
     * @param mixed $scopeCode
     * @return bool
     */
    public function canShowName(
        string $scope = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return $this->helper->canShowName($scope, $scopeCode);
    }

    /**
     * Check if product price can be shown
     *
     * @param string $scope
     * @param mixed $scopeCode
     * @return bool
     */
    public function canShowPrice(
        string $scope = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return $this->helper->canShowPrice($scope, $scopeCode);
    }

    /**
     * Check if product SKU can be shown
     *
     * @param string $scope
     * @param mixed $scopeCode
     * @return bool
     */
    public function canShowSku(
        string $scope = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return $this->helper->canShowSku($scope, $scopeCode);
    }

    /**
     * Check if product availability can be shown
     *
     * @param string $scope
     * @param mixed $scopeCode
     * @return bool
     */
    public function canShowAvailability(
        string $scope = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return $this->helper->canShowAvailability($scope, $scopeCode);
    }

    /**
     * Check if add to cart button can be shown
     *
     * @param ProductInterface $product
     * @param string $scope
     * @param mixed $scopeCode
     * @return bool
     */
    public function canShowCart(
        ProductInterface $product,
        string $scope = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        return $this->helper->canShowCart($product, $scope, $scopeCode);
    }
}