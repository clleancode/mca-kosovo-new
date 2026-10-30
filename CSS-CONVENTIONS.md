# CSS conventions

Run `npm ci --include=dev` first. The checker imports Sass and PostCSS directly.

```sh
npm run lint:css
node scripts/check-css-conventions.mjs --self-test
npm run build
```

## Naming

- Component classes use `a-` for atoms, `m-` for molecules and `o-` for organisms.
  Use lowercase kebab-case, an optional `__element` and an optional `--modifier`:
  `.m-card__title--large`, `.a-btn` and `.o-header`.
- Shared helpers use `h-`; responsive spacing uses `s-`, such as `.s-d-t-xl`.
- Files under `src/css/__components/{atoms,molecules,organisms}/` use the matching
  prefix: `_a-button.scss`, `_m-card.scss` and `_o-header.scss`. `_index.scss`
  aggregates components.
- Use classes for new CSS selectors. IDs are reserved for existing reviewed
  exceptions and semantic relationships, such as labels and ARIA controls.
- Third-party and WordPress selectors are accepted by the checker's explicit
  prefix rules. Do not rename vendor classes to fit theme naming.

## Existing exceptions

`scripts/css-conventions.exceptions.json` lists the existing legacy/global
selectors retained by the theme. Add exceptions only after review; do not
regenerate the list to hide a lint failure. Animation classes such as `e-reveal`
are retained through this reviewed list.

The checker compiles the global Sass entry and any discovered block Sass entry
in memory, checks class/ID selectors and component filenames, and leaves the
production assets untouched. Use the Vite build to regenerate shipped assets.
