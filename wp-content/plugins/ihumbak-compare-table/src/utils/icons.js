/**
 * Icons used by the editor UI. The check, minus and info shapes match the ones
 * the PHP renderer prints, so the preview looks like the frontend.
 */
const stroke = {
	xmlns: 'http://www.w3.org/2000/svg',
	viewBox: '0 0 24 24',
	width: 20,
	height: 20,
	fill: 'none',
	stroke: 'currentColor',
	strokeWidth: 2,
	strokeLinecap: 'round',
	strokeLinejoin: 'round',
	'aria-hidden': true,
	focusable: false,
};

export const checkIcon = (
	<svg { ...stroke } className="ihumbak-ct__icon ihumbak-ct__icon--check">
		<path d="M5 12.5l4.5 4.5L19 7.5" />
	</svg>
);

export const minusIcon = (
	<svg { ...stroke } className="ihumbak-ct__icon ihumbak-ct__icon--minus">
		<path d="M6 12h12" />
	</svg>
);

export const infoIcon = (
	<svg { ...stroke } className="ihumbak-ct__icon ihumbak-ct__icon--info">
		<circle cx="12" cy="12" r="9" />
		<path d="M12 11v5.5" />
		<path d="M12 7.5v.01" />
	</svg>
);

export const chevronIcon = (
	<svg { ...stroke } className="ihumbak-ct__icon ihumbak-ct__icon--chevron">
		<path d="M6 9l6 6 6-6" />
	</svg>
);

export const noIcon = (
	<svg { ...stroke }>
		<circle cx="12" cy="12" r="8" />
		<path d="M6.5 17.5l11-11" />
	</svg>
);

export const VALUE_ICONS = {
	check: checkIcon,
	minus: minusIcon,
};
