# MCA Kosovo

WordPress theme for MCA Kosovo, maintained by Clleancode.
Version: 1.0.0. Translation domain: `mca`. License: GPL-2.0-or-later.
The installation directory remains `mca-kosovo-new`.

## Requirements

- WordPress 6.0+ is the declared baseline. Local smoke checks used WordPress 7.1.3;
  this is not a claim that every intermediate version was tested.
- The retained PHP declaration permits `^7.4 || ^8.0`. PHP 7.4 is unverified.
  Installed Composer dependencies currently require PHP 8.0+ at runtime. Use PHP
  8.0+ with this dependency set; the declaration does not establish 7.4 compatibility.
- ACF Pro supplies theme blocks and fields. Configured CF7 forms require Contact
  Form 7. Multilingual switching requires a separately configured plugin.
- Build tools: Composer 2 and Node.js 20.19+ or 22.12+ (Vite 7 requirement).
  Node is not required by the production WordPress server.

## Contributor workflow

1. Run `composer install --no-dev --optimize-autoloader`.
2. Run `npm ci`.
3. Edit PHP templates and sources in `src/` and `template-parts/blocks/`.
4. Run `npm run dev` for build/watch, or `npm run build` for production assets.
5. Theme-owned translation calls use `mca`, loaded by `inc/setup.php` from
   `languages/`. Regenerate any catalogs using older domains before verifying
   translated strings. Theme activation does not translate page content.

## Release checks

- Keep versions in style.css, readme.txt, package.json and package-lock.json aligned.
- Deploy changed PHP files, style.css, compiled assets in assets/ and block
  folders, and Composer dependencies installed from composer.lock.
- Clear site/CDN caches; check pages, Search, 404, configured forms and PHP logs.
- Test keyboard navigation, validation, success and failure responses.
- The header Open Chat placeholder is intentionally retained. It currently links
  to `#` and has no chat handler. Connect it only to an approved destination/service
  with tested keyboard and focus behavior.
- Changing metadata does not rename the installed theme directory.

Support and maintenance: https://www.clleancode.com/
