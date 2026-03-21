/**
 * Amadeco StickyCart JavaScript component
 *
 * @category    Amadeco
 * @package     Amadeco_StickyCart
 * @copyright   Copyright (c) Amadeco
 */
define([
    'jquery',
    'underscore',
    'jquery-ui-modules/widget'
], function ($, _) {
    'use strict';

    $.widget('amadeco.stickycart', {
        /**
         * Default widget options.
         * @type {Object}
         */
        options: {
            /** @type {String} Selector for the sticky product container. */
            container: '[data-role="sticky-product"]',
            /** @type {String} Selector for the main add to cart button. */
            addToCartBtn: '#product-addtocart-button',
            /** @type {String} Selector for elements after which the sticky cart should appear. */
            stickyAfter: '.global.demo, .global.noscript',
            /** @type {String} Selector for the product tabs container. */
            tabListSelector: '.product.info.detailed[role=tablist]',
            /** @type {String} Selector for individual tab switches. */
            tabSwitchSelector: '[data-role=switch]',
            /** @type {String} Selector for the original product price container on the page. */
            originalPriceSelector: '.product-info-price',
            /** @type {String} Selector for the specific price box element to be synchronized. */
            priceBoxSelector: '.price-box',
            /** @type {Boolean} Flag to determine if the price should be synchronized. */
            showPrice: false,
            /** @type {Boolean} Flag to determine if the product summary tabs should be shown. */
            showSummary: false,
            /** @type {Number} Scroll offset threshold in pixels to activate the sticky cart (Fallback for older browsers). */
            offset: 500,
            /** @type {Number} Base top margin for the sticky cart in pixels. */
            marginTop: 0,
            /** @type {String} CSS class added to the container when activated for hardware-accelerated transitions. */
            activeClass: 'is-sticky'
        },

        /**
         * Widget initialization lifecycle phase.
         * Caches DOM selectors, pre-calculates layout, and sets up high-performance observers.
         *
         * @private
         * @return {void}
         */
        _create: function () {
            this.$addToCartBtn = $(this.options.addToCartBtn);
            this.$stickyAfter = $(this.options.stickyAfter);
            this.$stickyButton = this.element.find('button');
            
            this.$originalPriceWrapper = $(this.options.originalPriceSelector).find(this.options.priceBoxSelector);
            this.$stickyPriceWrapper = this.element.find(this.options.priceBoxSelector);
            
            this.isActive = false;
            this.buttonObserver = null;
            this.visibilityObserver = null;
            this.$summaryContainer = null;
            
            this._calculateLayout();

            if (this.options.showSummary) {
                this._createSummary();
            }

            if (this.options.showPrice) {
                this._initPriceSync();
            }

            this._observeAddToCartButton();
            this._bindEvents();
            this._initIntersectionObserver();
        },

        /**
         * Initializes the IntersectionObserver for 0-CPU layout monitoring.
         * Triggers the sticky cart visibility exactly when the original button leaves the viewport.
         * Gracefully falls back to scroll events utilizing the offset option for older browsers.
         *
         * @private
         * @return {void}
         */
        _initIntersectionObserver: function () {
            var self = this;

            if (!this.$addToCartBtn.length || typeof window.IntersectionObserver === 'undefined') {
                this._on(this.window, {
                    'scroll': _.throttle(function () {
                        var scrollTop = self.window.scrollTop();

                        // Use the configured option instead of a hardcoded value
                        if (scrollTop > self.options.offset) {
                            self.activate();
                        } else {
                            self.deactivate();
                        }
                    }, 100)
                });

                return;
            }

            this.visibilityObserver = new IntersectionObserver(function (entries) {
                var entry = entries[0];
                
                if (!entry.isIntersecting && entry.boundingClientRect.top < 0) {
                    self.activate();
                } else {
                    self.deactivate();
                }
            }, {
                root: null,
                threshold: 0
            });

            this.visibilityObserver.observe(this.$addToCartBtn[0]);
        },

        /**
         * Safely clones the price HTML without detaching the live Magento widgets.
         * Listens to Magento's native `updatePrice` event to synchronize swatch and custom option changes.
         *
         * @private
         * @return {void}
         */
        _initPriceSync: function () {
            if (!this.$originalPriceWrapper.length || !this.$stickyPriceWrapper.length) {
                return;
            }

            // Initial clone
            this._clonePriceHtml();

            // PERFECTED: Using native _on() for automatic namespacing and garbage collection!
            this._on(this.$originalPriceWrapper, {
                'updatePrice': '_handlePriceUpdate'
            });
        },

        /**
         * Handler for Magento's native price update event.
         * Uses requestAnimationFrame polyfill to ensure 60fps rendering without jank.
         *
         * @private
         * @return {void}
         */
        _handlePriceUpdate: function () {
            // Polyfill: Use native rAF, vendor prefixes, or fallback to 60fps setTimeout
            var requestFrame = window.requestAnimationFrame || 
                               window.webkitRequestAnimationFrame || 
                               window.mozRequestAnimationFrame || 
                               function (callback) { window.setTimeout(callback, 1000 / 60); };

            requestFrame(function () {
                this._clonePriceHtml();
            }.bind(this)); // Safely bind 'this' to the widget instance inside the frame request
        },

        /**
         * Clones the price HTML and sanitizes ID attributes to maintain strict DOM validation.
         *
         * @private
         * @return {void}
         */
        _clonePriceHtml: function () {
            var safeHtml = String(this.$originalPriceWrapper.html()).replace(/id="[^"]*"/g, '');

            this.$stickyPriceWrapper.html(safeHtml);
        },

        /**
         * Calculates required dynamic margins based on elements situated above the cart.
         *
         * @private
         * @return {void}
         */
        _calculateLayout: function () {
            var marginOffset = this.options.marginTop + (this.element.outerHeight(true) - this.element.outerHeight());

            if (this.$stickyAfter.length) {
                marginOffset += this.$stickyAfter.outerHeight(false) || 0;
            }

            this.element.css('margin-top', marginOffset);
        },

        /**
         * Binds resize and click events using the native jQuery UI _on() method.
         *
         * @private
         * @return {void}
         */
        _bindEvents: function () {
            this._on(this.window, {
                'resize': _.throttle(this._calculateLayout, 100)
            });

            this._on(this.$stickyButton, {
                'click': '_handleAddToCartClick'
            });
        },

        /**
         * Handles the click event on the sticky "Add to Cart" button.
         *
         * @param {jQuery.Event} event - The jQuery click event object.
         * @private
         * @return {void}
         */
        _handleAddToCartClick: function (event) {
            event.preventDefault();
            this.$addToCartBtn.trigger('click');
        },

        /**
         * Observes DOM mutations on the original "Add to Cart" button.
         * Synchronizes CSS classes, HTML content, and disabled states.
         *
         * @private
         * @return {void}
         */
        _observeAddToCartButton: function () {
            var self = this;

            if (this.$addToCartBtn.length && !this.buttonObserver) {
                this.buttonObserver = new MutationObserver(function () {
                    self.$stickyButton
                        .html(self.$addToCartBtn.html())
                        .prop('disabled', self.$addToCartBtn.prop('disabled'))
                        .attr('class', self.$addToCartBtn.attr('class'));
                });

                this.buttonObserver.observe(this.$addToCartBtn[0], {
                    attributes: true,
                    childList: true,
                    subtree: true,
                    attributeFilter: ['class', 'disabled']
                });
            }
        },

        /**
         * Activates the sticky cart visibility via CSS classes.
         *
         * @public
         * @return {void}
         */
        activate: function () {
            this.isActive = true;
            this.element
                .addClass(this.options.activeClass)
                .prop('aria-hidden', false);
        },

        /**
         * Deactivates the sticky cart visibility via CSS classes.
         *
         * @public
         * @return {void}
         */
        deactivate: function () {
            this.isActive = false;
            this.element
                .removeClass(this.options.activeClass)
                .prop('aria-hidden', true);
        },

        /**
         * Creates the product tabs summary in the sticky cart.
         * Strips ID attributes from cloned HTML to prevent DOM duplicates.
         *
         * @private
         * @return {void}
         */
        _createSummary: function () {
            var $tablist = $(this.options.tabListSelector);

            if (!$tablist.length) {
                return;
            }

            this.$summaryContainer = $('<div />', { 'class': 'summary' });

            $tablist.find(this.options.tabSwitchSelector).each(function (index, element) {
                var $element = $(element),
                    safeHtml = String($element.html()).replace(/id="[^"]*"/g, ''),
                    $anchor = $('<a />', { 'href': $element.prop('href') }).html(safeHtml);
                
                this.$summaryContainer.append($anchor);
            }.bind(this));

            this.element.children('.container').after(this.$summaryContainer);
        },

        /**
         * Widget destruction lifecycle phase.
         * Cleans up dynamically injected DOM elements and disconnects all observers.
         *
         * @private
         * @return {void}
         */
        _destroy: function () {
            if (this.buttonObserver) {
                this.buttonObserver.disconnect();
                this.buttonObserver = null;
            }

            if (this.visibilityObserver) {
                this.visibilityObserver.disconnect();
                this.visibilityObserver = null;
            }

            if (this.$summaryContainer) {
                this.$summaryContainer.remove();
            }

            this._super();
        }
    });

    return $.amadeco.stickycart;
});
