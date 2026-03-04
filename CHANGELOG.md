# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2024-03-04

### Added
- **Message API (REST) client** with full support for eBay Commerce Message API v1.0
  - `getConversations()` - Retrieve conversations with filtering and pagination
  - `getConversation()` - Get all messages in a specific conversation
  - `sendMessage()` - Send new messages or reply to existing conversations
  - `updateConversation()` - Mark conversations as read, archive, or delete
  - `bulkUpdateConversation()` - Update up to 10 conversations in a single request
- **Three new Enums** for Message API:
  - `ConversationStatus` (ACTIVE, ARCHIVED, DELETED, READ, UNREAD)
  - `ConversationType` (FROM_EBAY, FROM_MEMBERS)
  - `MessageMediaType` (IMAGE, PDF, DOC, TXT)
- **Three new DTOs** for type-safe message handling:
  - `MessageMedia` - Represents message attachments
  - `Message` - Represents individual messages with sender, recipient, body, and media
  - `Conversation` - Represents conversation threads with metadata
- Added `commerce.message` OAuth scope to default configuration
- Comprehensive test suite with fixtures for all Message API endpoints
- Extensive documentation and usage examples in EXAMPLES.md
- Laravel Job example for automated message processing

### Changed
- Updated `Ebay` main class to initialize and expose `MessageClient`
- Updated Facade with `@method` annotation for IDE autocompletion
- Enhanced token propagation to include MessageClient

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
