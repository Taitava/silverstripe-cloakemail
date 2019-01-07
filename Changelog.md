# Changelog

## 2.0.0
 - Compatibility with SilverStripe 4. If you use SS3, use the 1.x release line.
 - `purge_mailto_links` configuration option is now `true` by default. (Breaks backwards compatibility, so set it to false if you are upgrading from 1.x or test your project well to make sure it doesn't cause problems with mailto-links created in the CMS HTML editor).

## 1.1.0
 - Create an option to remove mailto links from HTML before cloaking the email addresses.

## 1.0.1
 - Fix installer-name in composer.json.

## 1.0.0
 - Initial release