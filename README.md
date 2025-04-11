# Amadeco StickyCart Module for Magento 2

[![Latest Stable Version](https://img.shields.io/github/v/release/Amadeco/magento2-sticky-cart)](https://github.com/Amadeco/magento2-sticky-cart/releases)
[![Magento 2](https://img.shields.io/badge/Magento-2.4.x-brightgreen.svg)](https://magento.com)
[![PHP](https://img.shields.io/badge/PHP-8.1|8.2|8.3-blue.svg)](https://www.php.net)
[![License](https://img.shields.io/github/license/Amadeco/magento2-sticky-cart)](https://github.com/Amadeco/magento2-sticky-cart/blob/main/LICENSE.txt)

[SPONSOR: Amadeco](https://www.amadeco.fr)

## Overview

The StickyCart module adds a sticky product information bar with an Add to Cart button to your Magento store, which is displayed as the user scrolls down the product page. This increases conversion rates by keeping the Add to Cart button always accessible.

## Features

- Sticky header that appears when scrolling down product pages
- Configurable display of product information:
  - Product image
  - Product name
  - Product price
  - Product SKU
  - Product availability status
- Synchronization with the main Add to Cart button
- Mobile-friendly design (automatically disabled on small screens)
- Easy configuration through Magento admin

## Installation

### Composer Installation

```bash
composer require amadeco/module-sticky-cart
bin/magento module:enable Amadeco_StickyCart
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
```

### Manual Installation

1. Create directory `app/code/Amadeco/StickyCart` in your Magento installation
2. Clone or download this repository into that directory
3. Enable the module and update the database:
```bash
bin/magento module:enable Amadeco_StickyCart
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
```

## Theme Configuration

### Important: Image Size Configuration

To ensure proper display of the sticky cart product image, you must add the following image definition to your theme's `view.xml` file (located at `app/design/frontend/[Vendor]/[theme]/etc/view.xml`):

```xml
<image id="sticky_product_thumbnail" type="thumbnail">
    <width>60</width>
    <height>40</height>
</image>
```

This configuration is theme-dependent, so you'll need to add it to each theme where you want the StickyCart module to be used.

## Configuration

1. Go to Admin > Stores > Configuration > Catalog > Sticky Cart
2. Set "Enable" to "Yes" to activate the module
3. Configure which product information elements should be displayed in the sticky cart

## Compatibility

- Magento 2.4.x
- PHP 8.1, 8.2, 8.3

## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md).

## Support

For issues or feature requests, please create an issue on our GitHub repository.

## License

This module is licensed under the MIT License (MIT). See the [LICENSE](LICENSE) file for details.
