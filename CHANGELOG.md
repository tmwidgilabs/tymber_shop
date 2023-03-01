# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.2.0]
### Added
- Integration with GitHub

## [3.1.1]
### Added
- Verify is REST API is enabled or not
- New security performances applied

## [3.1.0]
### Added
- New param to send the site URL on the API token verification

## [3.0.9]
### Changed
- Delete shop version values from DB if the API token isn't defined

## [3.0.8]
### Changed
- Custom sentry logs as warnings and not errors

## [3.0.7]
### Added
- Show error messages to the user when the API token isn't successfully activated
### Changed
- Improve the way of shop version is shown 

## [3.0.6]
### Added
- Show the version that is currently installed

## [3.0.5]
### Added
- Add helper text to API token field

## [3.0.4]
### Added
- Message to user when the Tymber Server isn't reachable
### Changed
- Improve the admin notices that are sent to the user

## [3.0.3]
### Added
- Tag with the site url to identify the event on sentry
- Improve logs on sentry
### Changed
- Update DSN url for sentry

## [3.0.2]
### Changed
- Update tymber server URL

### Fixed
- Fixed validate URL function before downloading zip

## [3.0.1]
### Changed
- All functions inside Wp_Tymber_Shop_Notices class are following Snake case

### Fixed
- Function name inside Wp_Tymber_Shop_Notices class was wrong addNotices -> add_notices

## [3.0.0]
### Added
- Auto update store system
- Created API that will integrate with Tymber Management Plugin
- Token system, plugin will just work if receive a valid token
- New notice function that will improve debug functions
- Function to verify if download link is a link or a path

### Fixed
- Some backoffice administration functions

## [2.1.0]
### Added
- Log System to apache using PHP error_log

## [2.0.2]
### Added
- Button in store administration options that takes you to the Shop Home Page

## [2.0.1] - Sept. 08, 2021
### Added
- Sitemap system with Yoast SEO

### Fixed
- Functions version

## [2.0.0] - Sept. 05, 2021
### Added
- Start Plugin Refactor
- CHANGELOG.md
- Adhere to Semantic Versioning
- Composer require oberonlai/wp-option
- Back Office Options >> Tymber Plugin
- Link Configure in plugins list
- Function recursive_rmdir in Wp_Tymber_Shop_Settings
- Git Tags With Release Version
- Composer require devaly/wordpress-routes
- Dynamic router system in includes/class-wp-tymber-shop-routes.php
- Dynamic get js and css by get_shops() and redirect
- option to save the shop names
- Dynamic edit base
- Dynamic Images Redirect

### Changed
- Change the "changelog" within the README to a CHANGELOG.md file
- README becomes a plugin simple documentation
- wp-tymber-shop.php versions to 1.1.0 >> 2.0.0
- Function plugin_dir_path( __FILE__ ) to the WP_TYMBER_SHOP_DIR predefined constant
- Function plugin_dir_url( __FILE__ ) to the WP_TYMBER_SHOP_URL predefined constant
- Add .obsidian to .gitignore
- Class name "Wp_Tymber_Shop_i18n" to "Wp_Tymber_Shop_I18n" in "/includes/class-wp-tymber-shop-i18n.php"
- Files within the "includes" and "admin" directories now follow WordPress Coding Standards
- Settings composer dependency "wp-user-manager/wp-optionskit" >>> "oberonlai/wp-option"
- Static router system with /menu now is Dynamic with "devaly/wordpress-routes" package

### Removed
- Section about "changelog" in README vs CHANGELOG.md
- /config.php (junk file)
- Composer require wp-user-manager/wp-optionskit
- Back Office Options >> Settings >> Tymber Plugin
- Static router system with /menu
- Removed favicons 404 console errors

## [1.1.0]
### Fixed
- Plugin Versions System
- Shop Fallbacks
- Shop Path in Website URL

## [1.0.16]
### Fixed
- Only match the url of the selected shop page.

## [1.0.15]
### Fixed
- Conflict with page URLs and listing of existing site pages.

## [1.0.14]
### Removed
- 'Shop subpath folder' setting field since it is no longer needed.

## [1.0.13]
### Added
- Support for Yoast SEO sitemap.
- Support for Rank Math SEO sitemap.
- Support for multi-store improved.
- Show shop sitemap in settings.
- Internal sitemap url.

## [1.0.9]
### Added
- Action to clear and refresh app dir.

### Removed
- Zip file in order to not pollute the media library.

## [1.0.6]
### Fixed
- Problem with zip file upload.

## [1.0.5]
### Changed
- We want to serve the app from a sub-folder.

## [1.0.4]
### Added
- Options page that supports multi-stores and tymber zip upload.

## [1.0.3]
### Changed
- Use file_get_contents instead of wp_redirect to avoid the user seeing the URL being rewritten.

## [1.0.2]
### Added
- Introduce a shop url dynamic var.

## [1.0.1]
### Added
- Use base html tag to specify plugin path.
