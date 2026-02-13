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
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * StickyCart Configuration ViewModel.
 *
 * Serves as the bridge between Backend Configuration and Frontend Templates.
 * Implements ArgumentInterface to be injectable via layout XML.
 */
class Config implements ArgumentInterface
{
    /**
     * Config constructor.
     *
     * @param StickyCartHelper $helper
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly StickyCartHelper $helper,
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
        return $this->helper->isEnabled($scope, $scopeCode);
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
        return $this->helper->canShowImage($scope, $scopeCode);
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
        return $this->helper->canShowName($scope, $scopeCode);
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
        return $this->helper->canShowPrice($scope, $scopeCode);
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
        return $this->helper->canShowSku($scope, $scopeCode);
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
        return $this->helper->canShowAvailability($scope, $scopeCode);
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
        return $this->helper->canShowCart($product, $scope, $scopeCode);
    }
}
