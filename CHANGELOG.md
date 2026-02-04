# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-02-05

### Added
- Initial release of modern eBay PHP/Laravel package
- PHP 8.1+ support with strict typing
- Native PHP 8.1 Enums for all eBay CodeTypes (Site, Currency, ListingStatus, OrderStatus, PaymentStatus, ListingType)
- OAuth 2.0 authentication with automatic token refresh
- Trading API (XML) client with support for:
  - GetOrders
  - GetCategories
  - GetCategoryFeatures
  - GetItem
  - AddFixedPriceItem
  - GetMyEbaySelling
- Commerce API (REST) client with support for:
  - Taxonomy API
  - Translation API
  - Inventory API
  - Fulfillment API
- Type-safe DTOs for Order and Item resources
- Custom exception hierarchy (EbayApiException, AuthenticationException, InvalidConfigurationException)
- Laravel Service Provider and Facade
- Comprehensive configuration file
- Fluent interface for API interactions
- Multi-site support (US, UK, DE, FR, AU, CA, and more)
- Automatic token management and refresh
- PSR-4, PSR-7, PSR-12 compliance
- Extensive documentation with links to official eBay API docs
- PHPUnit test structure

### Changed
- Modernized architecture from original library
- Replaced static Lists classes with PHP 8.1 Enums
- Improved error handling with custom exceptions
- Enhanced type safety with strict typing throughout

### Deprecated
- N/A (Initial release)

### Removed
- N/A (Initial release)

### Fixed
- N/A (Initial release)

### Security
- Secure OAuth 2.0 token handling
- Environment-based credential management
