/**
 * Column commands and row reconciliation.
 *
 * Columns are matched by position: the header's compare-column children are the
 * source of truth and every row holds one compare-value per column. The header
 * and the rows are `templateLock: all`, so `insertBlock`/`moveBlocks`/
 * `removeBlock` are refused there; `replaceInnerBlocks` is not, which is what
 * makes these table-level commands the only way to change the column set.
 */
import { createBlock } from '@wordpress/blocks';
import { store as blockEditorStore } from '@wordpress/block-editor';

export const MIN_COLUMNS = 1;
export const MAX_COLUMNS = 3;

export const TABLE = 'ihumbak/compare-table';
export const HEADER = 'ihumbak/compare-header';
export const COLUMN = 'ihumbak/compare-column';
export const SECTION = 'ihumbak/compare-section';
export const ROW = 'ihumbak/compare-row';
export const VALUE = 'ihumbak/compare-value';

export const createColumn = () => createBlock( COLUMN );
export const createValue = () => createBlock( VALUE );

export const createRow = ( columnCount ) =>
	createBlock( ROW, {}, Array.from( { length: columnCount }, createValue ) );

export const createSection = ( columnCount ) =>
	createBlock( SECTION, {}, [ createRow( columnCount ) ] );

// Same header as the block template: exactly one, kept first.
export const createHeader = ( columnCount ) =>
	createBlock(
		HEADER,
		{ lock: { move: true, remove: true } },
		Array.from( { length: columnCount }, createColumn )
	);

/**
 * @param {Object[]} blocks Inner blocks of a compare-table.
 * @return {{header: ?Object, rows: Object[]}} The header and every row of every section.
 */
export function getTableParts( blocks ) {
	const header = blocks.find( ( block ) => block.name === HEADER ) ?? null;
	const rows = [];

	blocks.forEach( ( block ) => {
		if ( block.name !== SECTION ) {
			return;
		}
		block.innerBlocks.forEach( ( row ) => {
			if ( row.name === ROW ) {
				rows.push( row );
			}
		} );
	} );

	return { header, rows };
}

/**
 * Number of service columns of a table (0 when it has no header yet).
 * @param {Object} registry      Data registry.
 * @param {string} tableClientId Client ID of the compare-table.
 */
export function getColumnCount( registry, tableClientId ) {
	const { header } = getTableParts(
		registry.select( blockEditorStore ).getBlocks( tableClientId )
	);

	return header ? header.innerBlocks.length : 0;
}

/**
 * Pads with empty values or drops trailing cells so a row has `count` cells.
 * @param {Object[]} cells Cells of a row.
 * @param {number}   count Number of service columns.
 */
function fitCells( cells, count ) {
	if ( cells.length === count ) {
		return cells;
	}
	if ( cells.length > count ) {
		return cells.slice( 0, count );
	}

	return [
		...cells,
		...Array.from( { length: count - cells.length }, createValue ),
	];
}

/**
 * Applies one positional change to the header and to every row, as a single
 * store update and therefore a single undo step.
 *
 * @param {Object}   registry      Data registry.
 * @param {string}   tableClientId Client ID of the compare-table.
 * @param {Function} change        ( cells, createCell ) => cells.
 * @param {?string}  selectAfter   Client ID to select once the change is applied.
 */
function changeColumns( registry, tableClientId, change, selectAfter = null ) {
	const { getBlocks } = registry.select( blockEditorStore );
	const { replaceInnerBlocks, selectBlock } =
		registry.dispatch( blockEditorStore );
	const { header, rows } = getTableParts( getBlocks( tableClientId ) );

	if ( ! header ) {
		return;
	}

	const count = header.innerBlocks.length;

	registry.batch( () => {
		replaceInnerBlocks(
			header.clientId,
			change( header.innerBlocks, createColumn ),
			false
		);

		rows.forEach( ( row ) => {
			replaceInnerBlocks(
				row.clientId,
				change( fitCells( row.innerBlocks, count ), createValue ),
				false
			);
		} );

		if ( selectAfter ) {
			selectBlock( selectAfter );
		}
	} );
}

/**
 * Inserts an empty service column at `index`. Does nothing at the maximum.
 * @param {Object} registry      Data registry.
 * @param {string} tableClientId Client ID of the compare-table.
 * @param {number} index         Column position.
 */
export function insertColumn( registry, tableClientId, index ) {
	if ( getColumnCount( registry, tableClientId ) >= MAX_COLUMNS ) {
		return;
	}

	const column = createColumn();

	changeColumns(
		registry,
		tableClientId,
		( cells, createCell ) => [
			...cells.slice( 0, index ),
			createCell === createColumn ? column : createCell(),
			...cells.slice( index ),
		],
		column.clientId
	);
}

/**
 * Removes the service column at `index`. The last column cannot be removed.
 * @param {Object} registry      Data registry.
 * @param {string} tableClientId Client ID of the compare-table.
 * @param {number} index         Column position.
 */
export function removeColumn( registry, tableClientId, index ) {
	const { header } = getTableParts(
		registry.select( blockEditorStore ).getBlocks( tableClientId )
	);

	if ( ! header || header.innerBlocks.length <= MIN_COLUMNS ) {
		return;
	}

	const remaining = header.innerBlocks.filter( ( _, i ) => i !== index );
	const neighbour = remaining[ Math.min( index, remaining.length - 1 ) ];

	changeColumns(
		registry,
		tableClientId,
		( cells ) => cells.filter( ( _, i ) => i !== index ),
		neighbour.clientId
	);
}

/**
 * Moves the service column at `from` to position `to`.
 * @param {Object} registry      Data registry.
 * @param {string} tableClientId Client ID of the compare-table.
 * @param {number} from          Current column position.
 * @param {number} to            Target column position.
 */
export function moveColumn( registry, tableClientId, from, to ) {
	const { header } = getTableParts(
		registry.select( blockEditorStore ).getBlocks( tableClientId )
	);

	if (
		! header ||
		from === to ||
		to < 0 ||
		to >= header.innerBlocks.length ||
		! header.innerBlocks[ from ]
	) {
		return;
	}

	changeColumns(
		registry,
		tableClientId,
		( cells ) => {
			const next = [ ...cells ];
			const [ moved ] = next.splice( from, 1 );
			next.splice( to, 0, moved );
			return next;
		},
		header.innerBlocks[ from ].clientId
	);
}

/**
 * Describes what is out of sync in a table, as a string that only changes when
 * there is something new to fix (safe to use as an effect dependency).
 *
 * @param {Object[]} blocks Inner blocks of a compare-table.
 * @return {string} Empty when the table is consistent.
 */
export function getInconsistency( blocks ) {
	const { header, rows } = getTableParts( blocks );

	if ( ! header ) {
		// An empty table is about to receive its template; one that has
		// sections but no header was pasted or typed in the code editor.
		return blocks.length ? 'no-header' : '';
	}

	const issues = [];

	if ( blocks[ 0 ] !== header ) {
		issues.push( 'header-position' );
	}

	if ( header.innerBlocks.length > MAX_COLUMNS ) {
		issues.push( `header-columns:${ header.innerBlocks.length }` );
	}

	const count = Math.min( header.innerBlocks.length, MAX_COLUMNS );

	// A header without columns is not a state to propagate: emptying every
	// row would destroy content that the header alone cannot bring back.
	if ( count >= MIN_COLUMNS ) {
		rows.forEach( ( row ) => {
			if ( row.innerBlocks.length !== count ) {
				issues.push( `${ row.clientId }:${ row.innerBlocks.length }` );
			}
		} );
	}

	return issues.join( ',' );
}

/**
 * Brings a table back in sync after a change the column commands did not make
 * (duplicated or pasted rows, a row added from the inserter, a section moved
 * above the header, a header with too many columns or no header at all coming
 * from the code editor). The fix is a consequence of the user's change, not a
 * change of its own, so it must not create an undo step.
 *
 * Every fix removes the issue it was made for, and the caller only runs this
 * when the description of the issues changes, so it cannot loop.
 * @param {Object} registry      Data registry.
 * @param {string} tableClientId Client ID of the compare-table.
 */
export function reconcileTable( registry, tableClientId ) {
	const { getBlocks } = registry.select( blockEditorStore );
	const actions = registry.dispatch( blockEditorStore );
	const { replaceInnerBlocks } = actions;
	// Unstable API: without it the fixes still apply, they just become
	// regular undo steps.
	const markNotPersistent = () => {
		if (
			typeof actions.__unstableMarkNextChangeAsNotPersistent ===
			'function'
		) {
			actions.__unstableMarkNextChangeAsNotPersistent();
		}
	};
	const blocks = getBlocks( tableClientId );
	const { header, rows } = getTableParts( blocks );

	if ( ! header ) {
		if ( ! blocks.length ) {
			return;
		}

		// As many columns as the widest row, so no value is dropped that the
		// limit would not drop anyway. The rows are fitted on the next pass.
		const widest = rows.reduce(
			( max, row ) => Math.max( max, row.innerBlocks.length ),
			MIN_COLUMNS
		);

		registry.batch( () => {
			markNotPersistent();
			replaceInnerBlocks(
				tableClientId,
				[ createHeader( Math.min( widest, MAX_COLUMNS ) ), ...blocks ],
				false
			);
		} );
		return;
	}

	const count = Math.min( header.innerBlocks.length, MAX_COLUMNS );

	registry.batch( () => {
		if ( blocks[ 0 ] !== header ) {
			markNotPersistent();
			replaceInnerBlocks(
				tableClientId,
				[ header, ...blocks.filter( ( block ) => block !== header ) ],
				false
			);
		}

		if ( header.innerBlocks.length > MAX_COLUMNS ) {
			markNotPersistent();
			replaceInnerBlocks(
				header.clientId,
				header.innerBlocks.slice( 0, MAX_COLUMNS ),
				false
			);
		}

		if ( count < MIN_COLUMNS ) {
			return;
		}

		rows.forEach( ( row ) => {
			if ( row.innerBlocks.length === count ) {
				return;
			}
			markNotPersistent();
			replaceInnerBlocks(
				row.clientId,
				fitCells( row.innerBlocks, count ),
				false
			);
		} );
	} );
}
