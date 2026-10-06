=== Medical Averitas ===
Contributors: Medcomms Experts
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.1.1
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A clean and professional WordPress theme designed for the Medical Averitas Pharma project.

== Description ==
Medical Averitas is a private project theme for Medical Averitas Pharma. It includes custom blocks, post types, and responsive layouts built for the WordPress block editor.

== Installation ==
1. Upload the Medical Averitas theme to your WordPress site:
   - Go to Appearance > Themes > Add New > Upload Theme.
   - Choose the ZIP file of the theme and click "Install Now."
2. Activate the theme and configure it as needed via the WordPress Customizer.
3. From the theme directory, install PHP dependencies if vendor/ is missing:
   `composer install --no-dev --optimize-autoloader`

== Build ==
Frontend assets are compiled with Vite (Node 20+). Edit sources under `src/` and block folders, then rebuild before deploy.

1. `nvm use` (or install Node 20+)
2. `npm install`
3. `npm run dev` — watch mode while developing
4. `npm run build` — production build into `assets/` and block `.min` files
5. `composer install --no-dev --optimize-autoloader` — PHP packages into `vendor/`

Commit built `.min` CSS/JS when deploying to hosts without a build step (e.g. WP Engine).

== Features ==
- Customizable colors and typography.
- Responsive and mobile-friendly design.
- Compatible with the WordPress block editor (Gutenberg).

== Changelog ==
= 1.1.1 =
* Theme metadata alignment and WordPress header compliance.
* Composer dependency management and Vite build docs.

= 1.0 =
* Initial release.

== Notes ==
This theme is a private project created for Medical Averitas Pharma. For support or updates, contact Medcomms Experts.
https://www.medcomms-experts.com
