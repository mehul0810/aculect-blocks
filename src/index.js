/**
 * Editor entry for core block variations and styles.
 */
wp.domReady( () => {
	const { registerBlockVariation } = wp.blocks;
	const { __ } = wp.i18n;

	registerBlockVariation( 'core/group', {
		name: 'aculect-clickable-card',
		title: __( 'Aculect Clickable Card', 'aculect-blocks' ),
		description: __(
			'A card composition with one stretched core Button link.',
			'aculect-blocks'
		),
		scope: [ 'inserter' ],
		attributes: {
			className: 'is-style-aculect-clickable-card',
			layout: {
				type: 'constrained',
			},
		},
		innerBlocks: [
			[
				'core/heading',
				{
					level: 3,
					content: __( 'Card title', 'aculect-blocks' ),
				},
			],
			[
				'core/paragraph',
				{
					content: __(
						'Describe the destination this card links to.',
						'aculect-blocks'
					),
				},
			],
			[
				'core/buttons',
				{},
				[
					[
						'core/button',
						{
							className: 'aculect-card__link',
							text: __( 'Learn more', 'aculect-blocks' ),
						},
					],
				],
			],
		],
	} );
} );
