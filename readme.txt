=== MCA Kosovo ===
Contributors: Clleancode
Requires at least: 6.0
Requires PHP: 8.0
Stable tag: 1.0.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mca

WordPress theme for MCA Kosovo, maintained by Clleancode.

== Requirements ==
WordPress 6.0+ is the declared baseline. Local smoke checks used WordPress 7.1.3.
The theme requires PHP 8.0 or newer.
Installed Composer dependencies require PHP 8.0+ at runtime; use PHP 8.0+ with
this dependency set. The declaration does not establish PHP 7.4 compatibility.
ACF Pro supplies blocks/fields; configured CF7 forms require Contact Form 7.
Build tools: Composer 2 and Node.js 20.19+ or 22.12+.

== Installation and build ==
Install the theme and configure blocks, menus and ACF options.
Run composer install --no-dev --optimize-autoloader and npm ci.
Use npm run dev for watch mode; npm run build for production assets.
Deploy compiled assets and locked dependencies, then clear caches.
See README.md for contributor and release checks.

== Translations ==
Theme translation domain: mca, loaded from languages/ by inc/setup.php.
Regenerate older-domain catalogs before verifying translated theme strings.

== Maintenance ==
The nonfunctional header chat placeholder was removed; no chat service is connected.
Support: https://www.clleancode.com/
