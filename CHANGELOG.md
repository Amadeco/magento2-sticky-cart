# Changelog

All notable changes to the `Amadeco_StickyCart` Magento 2 module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to Semantic Versioning.

## [1.2.0] - 2026-03-21

### 🚀 Performance & UX Optimizations
* **IntersectionObserver Integration:** Replaced the expensive `window.scroll` event listener with a native `IntersectionObserver`. The widget now uses zero CPU while scrolling and precisely triggers visibility the exact millisecond the original "Add to Cart" button leaves the viewport.
* **Hardware-Accelerated CSS Transitions:** Removed jQuery `.show()` and `.hide()` methods (which trigger expensive CPU layout reflows via `display: none/block`). Visibility is now seamlessly managed via the `is-sticky` CSS class, utilizing GPU-accelerated `transform: translateY` and `opacity` transitions.
* **Event Throttling:** Applied `_.throttle` to the `resize` event and the fallback `scroll` event (for older browsers) to guarantee 60fps rendering without jank.
* **`requestAnimationFrame` Polyfill:** Synchronized DOM updates for the price box using a cross-browser `requestAnimationFrame` polyfill, preventing layout thrashing and screen tearing while preserving battery life.
* **DOM Query Caching:** Relocated all heavy jQuery DOM queries to the `_create` lifecycle phase, ensuring O(1) memory access during critical scroll and mutation events.

### 🏗️ Changed (Architecture & Extensibility)
* **Configurable Selectors (O/C Principle):** Extracted all hardcoded DOM selectors (e.g., `.product.info.detailed[role=tablist]`, `.price-box`) into the widget's `options` object. The module is now 100% theme-agnostic and can be customized via `x-magento-init` without overriding the core JS.
* **jQuery UI Native Methods:** Replaced standard `$(...).on()` event bindings with the Widget Factory's native `this._on()`. This provides automatic context binding (`this`) and guarantees automatic garbage collection upon widget destruction.
* **Deprecated Methods Removed:** Eliminated the use of the deprecated `$.proxy()` method and standard `$.bind()` in favor of native ES5 `Function.prototype.bind()`.
* **LESS/PHTML Modernization:** Removed inline `SecureHtmlRenderer` styles (`display: none`) from the `sticky.bar.phtml` template to allow the LESS file to handle visibility natively through state classes and transitions.

### 🐛 Fixed
* **Magento Swatch Compatibility (Critical):** Fixed a bug where detaching the original `.price-box` broke Magento's native `swatch-renderer.js`. The module now safely clones the HTML and listens to the native `updatePrice` event to sync prices perfectly without interfering with Swatches or Custom Options.
* **Margin Calculation Error:** Corrected the top-margin calculation in `_calculateLayout` which incorrectly subtracted horizontal `outerWidth()` instead of vertical `outerHeight()`.
* **DOM Duplicate IDs (Accessibility/WCAG):** Fixed an issue where cloning the product summary tabs and price boxes generated duplicate HTML `id` attributes. The cloning function now actively strips `id="..."` using RegEx to maintain strict W3C DOM validation and screen-reader compatibility.
* **Memory Leaks:** Refactored the `_destroy` method to strictly disconnect all `MutationObserver` and `IntersectionObserver` instances, unbind custom `updatePrice.stickycart` events, and cleanly remove dynamically injected summary containers, ensuring zero memory leaks during AJAX page loads.

---

## Initial Release

### Added
* **Sticky Header Component:** Added a sticky product information bar that appears when users scroll down the product page to improve Add to Cart conversion rates.
* **Dynamic Cart Synchronization:** The sticky "Add to Cart" button automatically syncs its state (enabled/disabled/text) with the main product form using `MutationObserver`.
* **Configurable Display Elements:** Added backend system configuration (`Stores > Configuration > Catalog > Sticky Cart`) to toggle the visibility of:
  * Product Image
  * Product Name
  * Product Price
  * Product SKU
  * Product Availability Status
* **Summary Tabs Integration:** Added capability to clone and display product info tabs (Details, More Information, Reviews) inside the sticky cart.
* **Responsive Design:** Integrated LESS mixins to automatically disable and hide the sticky cart on mobile/tablet viewports to preserve screen real estate.

***

## 💡 Developer Reflections & Architectural Notes

During the transition from `1.0.0` to `1.1.0`, we conducted a deep architectural review of the JavaScript widget to align it with Enterprise Magento 2 standards. Here are the key technical reflections:

### 1. The Danger of Detaching Magento DOM Nodes
In the initial release, the price synchronization relied on jQuery's `.detach()` to move the `.price-box` into the sticky header. 
* *Reflection:* While this works visually, it is lethal in Magento 2. Magento's Swatch Renderer and Custom Options scripts bind their updates to the `.price-box` located *specifically* inside the main product column. Moving it to a fixed header breaks the reference. 
* *Resolution:* We transitioned to a "Safe Clone & Listen" pattern. We leave the original price box completely untouched, securely clone its inner HTML (stripping IDs to prevent W3C validation errors), and use `this._on(this.$originalPriceWrapper, {'updatePrice': ...})` to let Magento's native event tell our widget when to update the clone.

### 2. Scroll Thrashing vs. IntersectionObserver
* *Reflection:* Binding `$(window).on('scroll')` to check an `offset: 500` threshold forces the browser's main thread to evaluate pixels constantly. Furthermore, 500px on a 4K monitor behaves differently than 500px on a laptop, often causing the sticky cart to appear while the original button is still visible.
* *Resolution:* We implemented the native `IntersectionObserver` API. This offloads visibility calculation to the browser's GPU/background processes (0 CPU cost) and dynamically triggers the sticky cart the exact millisecond the original "Add to Cart" button leaves the screen, ensuring flawless UX on any device height.

### 3. jQuery UI Widget Factory Best Practices
* *Reflection:* The initial code manually managed event bindings (`.on()`, `.off()`) and observer disconnections. 
* *Resolution:* By strictly adopting `this._on()`, we leveraged jQuery UI's automatic event namespacing and garbage collection. This ensures that if the widget is destroyed (e.g., via a Knockout/React router or AJAX reload), it leaves no memory leaks or orphaned event listeners behind.
