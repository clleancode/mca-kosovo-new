import { defineConfig } from 'vite';
import fs from 'node:fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const blocksDir = path.resolve(__dirname, 'template-parts/blocks');
const assetsDir = path.resolve(__dirname, 'assets');

/**
 * Discover block SCSS:
 *   template-parts/blocks/{name}/css/_{name}.scss | {name}.scss
 * → template-parts/blocks/{name}/css/{name}.min.css
 */
function getBlockCssEntries() {
	const entries = {};
	if (!fs.existsSync(blocksDir)) return entries;

	for (const blockName of fs.readdirSync(blocksDir)) {
		const blockPath = path.join(blocksDir, blockName);
		if (!fs.statSync(blockPath).isDirectory()) continue;

		const cssDir = path.join(blockPath, 'css');
		const scssFile = [
			path.join(cssDir, `_${blockName}.scss`),
			path.join(cssDir, `${blockName}.scss`),
		].find((file) => fs.existsSync(file));

		if (scssFile) {
			entries[blockName] = scssFile;
		}
	}

	return entries;
}

/**
 * Discover block JS:
 *   template-parts/blocks/{name}/js/{name}.js
 * → template-parts/blocks/{name}/js/{name}.min.js
 */
function getBlockJsEntries() {
	const entries = {};
	if (!fs.existsSync(blocksDir)) return entries;

	for (const blockName of fs.readdirSync(blocksDir)) {
		const blockPath = path.join(blocksDir, blockName);
		if (!fs.statSync(blockPath).isDirectory()) continue;

		const jsFile = path.join(blockPath, 'js', `${blockName}.js`);
		if (fs.existsSync(jsFile)) {
			entries[`js-${blockName}`] = jsFile;
		}
	}

	return entries;
}

const blockCssEntries = getBlockCssEntries();
const blockCssNames = new Set(Object.keys(blockCssEntries));
const blockJsEntries = getBlockJsEntries();
const blockJsNames = new Set(
	Object.keys(blockJsEntries).map((key) => key.replace(/^js-/, '')),
);

const cssEntries = {
	style: path.resolve(__dirname, 'src/css/style.scss'),
	...blockCssEntries,
};

/** Theme JS entries compiled into assets/js/*.min.js */
const themeJsEntries = {
	script: path.resolve(__dirname, 'src/js/scripts.js'),
};

function removeCssEntryJs() {
	return {
		name: 'remove-css-entry-js',
		generateBundle(_options, bundle) {
			const cssEntryNames = new Set(Object.keys(cssEntries));
			for (const [fileName, chunk] of Object.entries(bundle)) {
				if (
					chunk.type === 'chunk' &&
					chunk.isEntry &&
					cssEntryNames.has(chunk.name) &&
					fileName.endsWith('.js')
				) {
					delete bundle[fileName];
					// Sourcemap for the discarded CSS-entry stub (e.g. js/style.js.map).
					delete bundle[`${fileName}.map`];
				}
			}
		},
	};
}

function fixCssAssetPaths() {
	return {
		name: 'fix-css-asset-paths',
		generateBundle(_options, bundle) {
			for (const chunk of Object.values(bundle)) {
				if (chunk.type !== 'asset' || !chunk.fileName.endsWith('.css')) continue;

				const base = path.basename(chunk.fileName, '.min.css');
				const isBlockCss = blockCssNames.has(base);
				const fontsPath = isBlockCss ? '../../../../assets/fonts/' : '../fonts/';
				const iconsPath = isBlockCss ? '../../../../assets/icons/' : '../icons/';

				chunk.source = chunk.source
					.toString()
					.replace(/url\((['"]?)(?:\.\.\/)?fonts\//g, `url($1${fontsPath}`)
					.replace(/url\((['"]?)(?:\.\.\/)?icons\//g, `url($1${iconsPath}`);
			}
		},
	};
}

/**
 * Vite build.sourcemap covers JS, but does not emit CSS maps for SCSS rollup
 * inputs. Recompile each CSS entry with Sass source maps in development so
 * DevTools can jump from a rule back to its partial.
 */
function attachCssSourcemaps(enabled) {
	return {
		name: 'attach-css-sourcemaps',
		async generateBundle(_options, bundle) {
			if (!enabled) return;

			const sass = await import('sass');

			for (const [fileName, chunk] of Object.entries(bundle)) {
				if (chunk.type !== 'asset' || !fileName.endsWith('.css')) continue;

				const base = path.basename(fileName, '.min.css');
				const entry = cssEntries[base];
				if (!entry) continue;

				const result = sass.compile(entry, {
					style: 'expanded',
					sourceMap: true,
					sourceMapIncludeSources: true,
				});

				const isBlockCss = blockCssNames.has(base);
				const fontsPath = isBlockCss ? '../../../../assets/fonts/' : '../fonts/';
				const iconsPath = isBlockCss ? '../../../../assets/icons/' : '../icons/';

				let css = result.css
					.replace(/url\((['"]?)(?:\.\.\/)?fonts\//g, `url($1${fontsPath}`)
					.replace(/url\((['"]?)(?:\.\.\/)?icons\//g, `url($1${iconsPath}`);

				const mapFileName = `${fileName}.map`;
				const map = result.sourceMap;
				map.file = path.basename(fileName);
				map.sourceRoot = '';
				// Prefer theme-relative paths over absolute file:// URLs.
				map.sources = map.sources.map((source) => {
					const decoded = decodeURIComponent(
						source.replace(/^file:\/\//, ''),
					);
					return path.relative(__dirname, decoded).split(path.sep).join('/');
				});

				css += `\n/*# sourceMappingURL=${path.basename(mapFileName)} */`;
				chunk.source = css;

				this.emitFile({
					type: 'asset',
					fileName: mapFileName,
					source: JSON.stringify(map),
				});
			}
		},
	};
}

function unlinkIfExists(filePath) {
	if (fs.existsSync(filePath)) fs.unlinkSync(filePath);
}

/** Move a compiled file and its sibling .map (when sourcemaps are on). */
function relocateCompiledFile(src, dest) {
	if (!fs.existsSync(src)) return;
	fs.mkdirSync(path.dirname(dest), { recursive: true });
	fs.renameSync(src, dest);

	const mapSrc = `${src}.map`;
	if (fs.existsSync(mapSrc)) {
		fs.renameSync(mapSrc, `${dest}.map`);
	}
}

/**
 * emptyOutDir is false (fonts/vendors/images live under assets/), and
 * relocateBlockAssets only moves outputs forward. Without this, renamed or
 * deleted block sources leave orphaned .min.css/.min.js behind — both under
 * assets/ (failed/partial relocate) and permanently in old block folders.
 */
function cleanupStaleCompiledAssets() {
	return {
		name: 'cleanup-stale-compiled-assets',
		buildStart() {
			const themeJsBases = new Set(Object.keys(themeJsEntries));

			const assetsCssDir = path.join(assetsDir, 'css');
			if (fs.existsSync(assetsCssDir)) {
				for (const file of fs.readdirSync(assetsCssDir)) {
					const full = path.join(assetsCssDir, file);
					// Drop previous sourcemaps; development builds regenerate them.
					if (file.endsWith('.map')) {
						fs.unlinkSync(full);
						continue;
					}
					if (!file.endsWith('.min.css')) continue;
					const base = file.slice(0, -'.min.css'.length);
					// Theme global CSS is rebuilt every run; anything else here is a relocate leftover.
					if (base === 'style') continue;
					fs.unlinkSync(full);
				}
			}

			const assetsJsDir = path.join(assetsDir, 'js');
			if (fs.existsSync(assetsJsDir)) {
				for (const file of fs.readdirSync(assetsJsDir)) {
					const full = path.join(assetsJsDir, file);
					if (file.endsWith('.map')) {
						fs.unlinkSync(full);
						continue;
					}
					if (!file.endsWith('.min.js')) continue;
					const base = file.slice(0, -'.min.js'.length);
					if (themeJsBases.has(base)) continue;
					fs.unlinkSync(full);
				}
			}

			if (!fs.existsSync(blocksDir)) return;

			for (const blockName of fs.readdirSync(blocksDir)) {
				const blockPath = path.join(blocksDir, blockName);
				if (!fs.statSync(blockPath).isDirectory()) continue;

				const cssMin = path.join(blockPath, 'css', `${blockName}.min.css`);
				const jsMin = path.join(blockPath, 'js', `${blockName}.min.js`);

				// Always clear prior maps so production builds don't leave watch leftovers.
				unlinkIfExists(`${cssMin}.map`);
				unlinkIfExists(`${jsMin}.map`);

				if (fs.existsSync(cssMin) && !blockCssNames.has(blockName)) {
					fs.unlinkSync(cssMin);
				}

				if (fs.existsSync(jsMin) && !blockJsNames.has(blockName)) {
					fs.unlinkSync(jsMin);
				}
			}
		},
	};
}

function relocateBlockAssets() {
	return {
		name: 'relocate-block-assets',
		writeBundle(_options, bundle) {
			for (const chunk of Object.values(bundle)) {
				if (chunk.type === 'asset' && chunk.fileName.endsWith('.css')) {
					const base = path.basename(chunk.fileName, '.min.css');
					if (!blockCssNames.has(base)) continue;

					relocateCompiledFile(
						path.join(assetsDir, chunk.fileName),
						path.join(blocksDir, base, 'css', `${base}.min.css`),
					);
					continue;
				}

				if (
					chunk.type === 'chunk' &&
					chunk.isEntry &&
					chunk.name.startsWith('js-')
				) {
					const blockName = chunk.name.slice(3);
					if (!blockJsNames.has(blockName)) continue;

					relocateCompiledFile(
						path.join(assetsDir, chunk.fileName),
						path.join(
							blocksDir,
							blockName,
							'js',
							`${blockName}.min.js`,
						),
					);
				}
			}
		},
	};
}

function cssAssetFileName(assetInfo) {
	const raw = assetInfo.names?.[0] ?? assetInfo.name ?? '';
	if (!raw.endsWith('.css')) return '[name][extname]';

	const base = raw.replace(/\.css$/, '').replace(/^_/, '');
	if (base === 'style') return 'css/style.min.css';
	return `css/${base}.min.css`;
}

function jsEntryFileName(chunkInfo) {
	if (chunkInfo.name === 'script') return 'js/script.min.js';
	if (chunkInfo.name.startsWith('js-')) {
		return `js/${chunkInfo.name.slice(3)}.min.js`;
	}
	return 'js/[name].js';
}

export default defineConfig(({ mode }) => {
	const isDev = mode === 'development';

	return {
		base: './',
		plugins: [
			cleanupStaleCompiledAssets(),
			removeCssEntryJs(),
			fixCssAssetPaths(),
			attachCssSourcemaps(isDev),
			relocateBlockAssets(),
		],
		build: {
			outDir: 'assets',
			emptyOutDir: false,
			// Local `npm run dev` only — production builds stay map-free.
			sourcemap: isDev,
			cssCodeSplit: true,
			// Expanded CSS in watch mode pairs with Sass sourcemaps for DevTools.
			cssMinify: !isDev,
			minify: true,
			rollupOptions: {
				input: {
					...themeJsEntries,
					...cssEntries,
					...blockJsEntries,
				},
				output: {
					entryFileNames: jsEntryFileName,
					chunkFileNames: 'js/[name].js',
					assetFileNames: cssAssetFileName,
				},
			},
		},
	};
});
