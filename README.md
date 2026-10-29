# MCA Kosovo

WordPress theme for MCA Kosovo, maintained by Clleancode.
Version: 1.0.0. Translation domain: `mca`. License: GPL-2.0-or-later.
The installation directory remains `mca-kosovo-new`.

## Requirements

- WordPress 6.0+ is the declared baseline. Local smoke checks used WordPress 7.1.3;
  this is not a claim that every intermediate version was tested.
- PHP 8.0+ is required. Composer resolves dependencies against PHP 8.0.0 so a
  dependency requiring a higher PHP version cannot silently raise the minimum.
- ACF Pro supplies theme blocks and fields. Configured CF7 forms require Contact
  Form 7. Multilingual switching requires a separately configured plugin.
- Build tools: Composer 2 and Node.js 20.19+ or 22.12+ (Vite 7 requirement).
  Node is not required by the production WordPress server.

## Contributor workflow

1. Run `composer install --no-dev --optimize-autoloader`.
2. Use npm and the committed `package-lock.json`; run `npm ci --include=dev`.
   Builds and CSS linting require development tools such as Vite and Sass, even
   when building production assets. `npm ci --omit=dev` is not a build install.
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
- The former header Open Chat placeholder was removed. Only add a new chat action
  for an approved destination/service with tested keyboard and focus behavior.
- Changing metadata does not rename the installed theme directory.

Support and maintenance: https://www.clleancode.com/

## Build and deployment policy

- npm is the package manager; do not add another package manager's lockfile.
- Run `npm run lint:css` before `npm run build`. The GitHub Actions workflow
  performs both checks after installing all build dependencies.
- CSS conventions and the reviewed selector exceptions are documented in
  [CSS-CONVENTIONS.md](CSS-CONVENTIONS.md).
- `vendor/` is intentionally committed for WordPress deployments that install
  the theme without running Composer. After dependency changes, commit
  `composer.json`, `composer.lock` and the regenerated Composer files together.
- `node_modules/` is intentionally ignored. Deploy the compiled assets rather
  than Node dependencies; the WordPress production server does not need npm.

## ACF Local JSON

- `acf-json/` is the theme's Local JSON save/load directory. Commit exported
  field groups with their stable ACF keys after reviewing changes.
- Field groups registered by `inc/fields/` and `inc/options.php` already live in
  version-controlled PHP. Keep those PHP definitions as their source of truth.
- Database-defined groups must be exported from the running site's ACF Tools
  before deployment. Importing or syncing on the destination is a separate
  admin action; saving JSON does not migrate existing field values or content.
