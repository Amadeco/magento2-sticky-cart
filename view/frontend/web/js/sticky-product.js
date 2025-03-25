/**
 * Amadeco StickyCart JavaScript component
 *
 * @category  Amadeco
 * @package   Amadeco_StickyCart
 * @copyright Ilan Parmentier
 */
define([
    'jquery',
    'underscore',
    'jquery-ui-modules/widget',
], function($, _) {
    'use strict';

    $.widget('amadeco.stickycart', {
        /**
         * Widget options
         * @type {Object}
         */
        options: {
            /**
             * Selector for sticky product container
             * @type {String}
             */
            container: '[data-role="sticky-product"]',

            /**
             * Selector for main add to cart button
             * @type {String}
             */
            addToCartBtn: "#product-addtocart-button",

            /**
             * Selector for elements after which the sticky cart should appear
             * @type {String}
             */
            stickyAfter: '.global.demo, .global.noscript',

            /**
             * Whether to show price in sticky cart
             * @type {Boolean}
             */
            showPrice: false,

            /**
             * Whether to show product tab summary in sticky cart
             * @type {Boolean}
             */
            showSummary: false,

            /**
             * Scroll offset (in pixels) after which sticky cart appears
             * @type {Number}
             */
            offset: 500,

            /**
             * Top margin for the sticky cart
             * @type {Number}
             */
            marginTop: 0
        },

        /**
         * Widget creation
         * Initialize sticky cart widget and bind events
         *
         * @private
         */
        _create: function() {
            this.marginTop = (this.element.outerWidth(true) - this.element.outerWidth());
            this.$addToCartBtn = $(this.options.addToCartBtn);
            this.$stickyAfter = $(this.options.stickyAfter);
            this.$stickyButton = this.element.find('button');

            this._bindEvents();
            this._bindAddToCartClick();

            if (this.options.showSummary) {
                this._createSummary();
            }
        },

        /**
         * Bind scroll and resize events
         * Uses throttle to improve performance
         *
         * @private
         */
        _bindEvents: function() {
            var widget = this,
                paused = false;

            $(window).on('scroll.stickyCart resize.stickyCart', _.throttle(function() {
                var scrollTop = $(window).scrollTop();

                if (widget.options.offset < scrollTop) {
                    if (!paused) {
                        paused = true;
                        widget.activate();
                    }
                } else {
                    if (paused) {
                        paused = false;
                        widget.deactivate();
                    }
                }
            }, 200));
        },

        /**
         * Bind click event to sticky cart button
         * Triggers click on the main add to cart button
         *
         * @private
         */
        _bindAddToCartClick: function() {
            var widget = this;

            this.$stickyButton.on('click.stickyCart', function() {
                widget.$addToCartBtn.trigger('click');
            });
        },

        /**
         * Activate sticky cart
         * Shows sticky cart and syncs with original add to cart button
         *
         * @public
         */
        activate: function() {
            // Handle price display if enabled
            if (this.options.showPrice) {
                this._syncPrice();
            }

            // Set margin based on elements that should be above the sticky cart
            if (this.$stickyAfter.length) {
                this.element.css('margin-top', this.marginTop + this.$stickyAfter.outerHeight(false));
            }

            // Observe changes to the original add to cart button
            this._observeAddToCartButton();

            // Show the sticky cart
            this.element.prop('aria-hidden', false).show();
        },

        /**
         * Synchronize price display between main product and sticky cart
         *
         * @private
         */
        _syncPrice: function() {
            var $priceBox = $('.product-info-price').find('.price-box'),
                $priceBoxWidget = this.element.find('.price-box');

            $priceBox.children().each(function() {
                $(this).parent().append($(this).clone());
                $priceBoxWidget.append($(this).detach());
            });
        },

        /**
         * Observe changes to add to cart button to sync with sticky button
         * Uses MutationObserver to watch for attribute changes
         *
         * @private
         */
        _observeAddToCartButton: function() {
            var widget = this;

            if (this.$addToCartBtn.length && !this.observer) {
                this.observer = new MutationObserver(function() {
                    widget.$stickyButton
                        .html(widget.$addToCartBtn.html())
                        .attr('class', widget.$addToCartBtn.attr('class'));
                });

                this.observer.observe(this.$addToCartBtn[0], {
                    attributes: true
                });
            }
        },

        /**
         * Deactivate sticky cart
         * Hides sticky cart and restores price elements if needed
         *
         * @public
         */
        deactivate: function() {
            // Handle price display if enabled
            if (this.options.showPrice) {
                this._restorePrice();
            }

            // Disconnect observer if it exists
            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }

            // Hide the sticky cart
            this.element.prop('aria-hidden', true).hide();
        },

        /**
         * Restore price elements to their original location
         *
         * @private
         */
        _restorePrice: function() {
            var $priceBox = $('.product-info-price').find('.price-box');
            $priceBox.children().remove();

            this.element.find('.price-box').children().each(function() {
                $priceBox.first().append($(this).detach());
            });
        },

        /**
         * Create product tabs summary in sticky cart
         *
         * @private
         */
        _createSummary: function() {
            var $tablist = $('.product.info.detailed[role=tablist]');

            if ($tablist.length) {
                var $summary = $('<div />', {'class': 'summary'});

                $('[data-role=switch]').each(function() {
                    var $anchor = $('<a />', {'href': $(this).prop('href')})
                        .html($(this).html())
                        .appendTo($summary);
                });

                this.element.children('.container').after($summary);
            }
        },

        /**
         * Clean up resources when widget is destroyed
         *
         * @private
         */
        _destroy: function() {
            // Remove event handlers
            $(window).off('scroll.stickyCart resize.stickyCart');
            this.$stickyButton.off('click.stickyCart');

            // Disconnect observer if it exists
            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }

            // Restore price if needed
            if (this.options.showPrice && this.element.is(':visible')) {
                this._restorePrice();
            }
        }
    });

    return $.amadeco.stickycart;
});