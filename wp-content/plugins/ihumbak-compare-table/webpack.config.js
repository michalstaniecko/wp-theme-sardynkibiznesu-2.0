/**
 * Extends the default @wordpress/scripts config with explicit entries.
 *
 * Output (build/ is committed, the plugin is deployed by copying it as-is):
 *   build/blocks/<block>/index.js + index.asset.php + block.json   editor scripts
 *   build/style.css    frontend structural CSS   (handle: ihumbak-compare-table)
 *   build/editor.css   editor-only overrides     (handle: ihumbak-compare-table-editor)
 *   build/view.js      frontend behaviour        (handle: ihumbak-compare-table-view)
 */
const fs = require( 'fs' );
const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const BLOCKS = [
	'compare-table',
	'compare-header',
	'compare-column',
	'compare-section',
	'compare-row',
	'compare-value',
];

const src = ( file ) => path.resolve( __dirname, 'src', file );

const entry = {};

BLOCKS.forEach( ( block ) => {
	entry[ `blocks/${ block }/index` ] = src( `blocks/${ block }/index.js` );
} );

// Entries that may be missing in a partial checkout are skipped, not stubbed.
[
	[ 'style', 'style.scss' ],
	[ 'editor', 'editor.scss' ],
	[ 'view', 'view.js' ],
].forEach( ( [ name, file ] ) => {
	if ( fs.existsSync( src( file ) ) ) {
		entry[ name ] = src( file );
	} else {
		// eslint-disable-next-line no-console
		console.warn(
			`[ihumbak-compare-table] src/${ file } not found, skipped.`
		);
	}
} );

// The default config moves every file called style.(s)css into a separate
// `style-<entry>.css` chunk. Here the stylesheets are entries of their own, so
// that split would only rename build/style.css to build/style-style.css.
const { style: _style, ...cacheGroups } =
	defaultConfig.optimization?.splitChunks?.cacheGroups ?? {};

// A stylesheet entry also emits an empty script and its asset file; nothing
// loads them, so they are removed to keep the committed build/ clean.
class RemoveStyleEntryScripts {
	apply( compiler ) {
		compiler.hooks.afterEmit.tap( 'RemoveStyleEntryScripts', () => {
			[ 'style', 'editor' ].forEach( ( name ) => {
				[ '.js', '.js.map', '.asset.php' ].forEach( ( ext ) => {
					fs.rmSync( path.join( compiler.outputPath, name + ext ), {
						force: true,
					} );
				} );
			} );
		} );
	}
}

module.exports = {
	...defaultConfig,
	entry,
	plugins: [ ...defaultConfig.plugins, new RemoveStyleEntryScripts() ],
	optimization: {
		...defaultConfig.optimization,
		splitChunks: {
			...defaultConfig.optimization?.splitChunks,
			cacheGroups,
		},
	},
};
