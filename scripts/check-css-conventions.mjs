import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import * as sass from 'sass';
import postcss from 'postcss';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const exceptions = JSON.parse(fs.readFileSync(path.join(root, 'scripts/css-conventions.exceptions.json'), 'utf8'));
const word = '[a-z][a-z0-9]*(?:-[a-z0-9]+)*';
const component = new RegExp(`^[amo]-${word}(?:__${word})?(?:--${word})?$`);
const external = /^(?:swiper|fancybox|carousel|wpcf7|aos|gm|wp|icon|is|has|can)(?:-|__)/;
const utility = /^[hs]-[a-z0-9]+(?:-[a-z0-9]+)*$/;
const allowedClass = name => component.test(name) || utility.test(name) || external.test(name) || exceptions.classes.includes(name);

function inspect(css, source, issues) {
    postcss.parse(css, { from: source }).walkRules(rule => {
        // Attribute values are not class or ID names.
        const selector = rule.selector.replace(/"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'/g, '');
        for (const match of selector.matchAll(/\.((?:\\.|[\w-])+)/g)) {
            if (!allowedClass(match[1])) issues.add(`${source}: unexpected class .${match[1]}`);
        }
        for (const match of selector.matchAll(/#([a-zA-Z_][\w-]*)/g)) {
            if (!exceptions.ids.includes(match[1])) issues.add(`${source}: new ID selector #${match[1]} (prefer a component class)`);
        }
    });
}

if (process.argv.includes('--self-test')) {
    const issues = new Set();
    inspect('.m-card__title--large, .a-btn, .s-d-t-xl, .swiper-slide { color: red } .CardOops, #qa-new-id { color: red }', 'fixture', issues);
    if (issues.size !== 2) throw new Error('Convention checker failed its positive/negative fixture');
    console.log('PASS: valid conventions accepted; invalid class and new ID rejected.');
    process.exit(0);
}

const issues = new Set();
for (const [folder, prefix] of Object.entries({ atoms: 'a', molecules: 'm', organisms: 'o' })) {
    for (const file of fs.readdirSync(path.join(root, 'src/css/__components', folder))) {
        if (file.endsWith('.scss') && file !== '_index.scss' && !new RegExp(`^_${prefix}-[a-z0-9-]+\\.scss$`).test(file)) {
            issues.add(`${folder}/${file}: expected _${prefix}-component.scss`);
        }
    }
}

// Compile in memory only; production assets are never rewritten.
const entries = ['src/css/style.scss'];
for (const block of fs.readdirSync(path.join(root, 'template-parts/blocks'))) {
    for (const filename of [`_${block}.scss`, `${block}.scss`]) {
        const entry = `template-parts/blocks/${block}/css/${filename}`;
        if (fs.existsSync(path.join(root, entry))) { entries.push(entry); break; }
    }
}
for (const entry of entries) {
    inspect(sass.compile(path.join(root, entry), { logger: sass.Logger.silent }).css, entry, issues);
}
if (issues.size) {
    console.error([...issues].sort().join('\n'));
    process.exitCode = 1;
} else {
    console.log(`PASS: ${entries.length} Sass entry point(s), component filenames and compiled class/ID selectors. Existing exceptions retained.`);
}
