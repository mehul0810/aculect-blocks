<?php
/**
 * Lightweight test runner for Aculect Blocks.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

require __DIR__ . '/stubs.php';
require dirname( __DIR__ ) . '/vendor/autoload.php';

use Aculect\Blocks\Schema\SchemaOutput;
use Aculect\Blocks\Blocks\CoreBlockPatterns;
use Aculect\Blocks\Settings\SettingsRepository;
use Aculect\Blocks\StructuredData\AccordionFaqExtractor;
use Aculect\Blocks\StructuredData\BreadcrumbListBuilder;

$tests = array();

$tests['SchemaOutput detects nested types'] = static function (): void {
	$schema = new SchemaOutput();
	$nodes  = array(
		array(
			'@graph' => array(
				array(
					'@type' => array( 'WebPage', 'BreadcrumbList' ),
				),
			),
		),
	);

	test_assert( $schema->contains_type( $nodes, 'BreadcrumbList' ), 'Expected nested BreadcrumbList to be detected.' );
};

$tests['SchemaOutput appends missing node once'] = static function (): void {
	$schema = new SchemaOutput();
	$node   = array( '@type' => 'FAQPage' );

	$nodes = $schema->append_if_missing( array(), $node, 'FAQPage' );
	$nodes = $schema->append_if_missing( $nodes, $node, 'FAQPage' );

	test_assert_same( 1, count( $nodes ), 'Expected duplicate FAQPage node to be skipped.' );
};

$tests['BreadcrumbListBuilder normalizes labels and URLs'] = static function (): void {
	$builder = new BreadcrumbListBuilder();
	$node    = $builder->build(
		array(
			array(
				'label' => '<strong>Home</strong>',
				'url'   => 'https://example.com/',
			),
			array(
				'label' => 'Current Page',
			),
		),
		'https://example.com/current/#aculect-breadcrumb',
		'https://example.com/current/'
	);

	test_assert( is_array( $node ), 'Expected BreadcrumbList node.' );
	test_assert_same( 'BreadcrumbList', $node['@type'] ?? null, 'Expected BreadcrumbList type.' );
	test_assert_same( 'Home', $node['itemListElement'][0]['name'] ?? null, 'Expected HTML to be stripped from label.' );
	test_assert_same( 'https://example.com/current/', $node['itemListElement'][1]['item'] ?? null, 'Expected current URL fallback.' );
};

$tests['AccordionFaqExtractor extracts complete FAQ rows'] = static function (): void {
	$extractor = new AccordionFaqExtractor();
	$items     = $extractor->extract(
		array(
			array(
				'blockName'   => 'core/accordion-item',
				'innerBlocks' => array(
					array(
						'blockName' => 'core/accordion-heading',
						'attrs'     => array( 'title' => 'What is Aculect Blocks?' ),
					),
					array(
						'blockName'   => 'core/accordion-panel',
						'innerBlocks' => array(
							array(
								'blockName' => 'core/paragraph',
								'innerHTML' => '<p>It enhances core blocks.</p>',
							),
						),
					),
				),
			),
		)
	);

	test_assert_same( 1, count( $items ), 'Expected one FAQ row.' );
	test_assert_same( 'What is Aculect Blocks?', $items[0]['question'] ?? null, 'Expected extracted question.' );
	test_assert_same( '<p>It enhances core blocks.</p>', $items[0]['answer'] ?? null, 'Expected extracted answer HTML.' );
};

$tests['SettingsRepository keeps new defaults and sanitizes booleans'] = static function (): void {
	$GLOBALS['aculect_blocks_test_options'][ SettingsRepository::OPTION_NAME ] = array(
		'block_styles_enabled'      => '0',
		'editor_assets_enabled'     => '1',
		'patterns_enabled'          => false,
		'faq_schema_enabled'        => true,
		'breadcrumb_schema_enabled' => 'yes',
	);

	$settings = ( new SettingsRepository() )->all();

	test_assert_same( false, $settings['block_styles_enabled'], 'Expected block styles to sanitize false.' );
	test_assert_same( true, $settings['editor_assets_enabled'], 'Expected editor assets to sanitize true.' );
	test_assert_same( false, $settings['patterns_enabled'], 'Expected patterns to sanitize false.' );
	test_assert_same( true, $settings['breadcrumb_schema_enabled'], 'Expected breadcrumb schema to sanitize true.' );
};

$tests['Core patterns use registered styles and WordPress 7.0 blocks'] = static function (): void {
	$GLOBALS['aculect_blocks_test_options']            = array();
	$GLOBALS['aculect_blocks_test_pattern_categories'] = array();
	$GLOBALS['aculect_blocks_test_patterns']           = array();

	( new CoreBlockPatterns( new SettingsRepository() ) )->register_patterns();

	test_assert_same( 3, count( $GLOBALS['aculect_blocks_test_patterns'] ), 'Expected all public core-block patterns to register.' );

	$card_grid = $GLOBALS['aculect_blocks_test_patterns']['aculect-blocks/card-grid']['content'] ?? '';
	test_assert( is_string( $card_grid ), 'Expected card-grid pattern content.' );
	test_assert( str_contains( $card_grid, 'is-style-outline' ), 'Expected the card-grid pattern to use the core outline button style.' );
	test_assert( ! str_contains( $card_grid, 'aculect-secondary' ), 'Expected the card-grid pattern to avoid an unregistered theme-specific button style.' );

	$faq = $GLOBALS['aculect_blocks_test_patterns']['aculect-blocks/faq-accordion']['content'] ?? '';
	test_assert( is_string( $faq ) && str_contains( $faq, '<!-- wp:accordion -->' ), 'Expected the FAQ pattern to use the WordPress 7.0 Accordion block.' );

	$breadcrumb = $GLOBALS['aculect_blocks_test_patterns']['aculect-blocks/breadcrumb-header']['content'] ?? '';
	test_assert( is_string( $breadcrumb ) && str_contains( $breadcrumb, '<!-- wp:breadcrumbs /-->' ), 'Expected the breadcrumb pattern to use the WordPress 7.0 Breadcrumbs block.' );
};

$failures = 0;

foreach ( $tests as $name => $test ) {
	try {
		$test();
		echo 'PASS: ' . $name . PHP_EOL;
	} catch ( Throwable $throwable ) {
		++$failures;
		echo 'FAIL: ' . $name . PHP_EOL;
		echo '  ' . $throwable->getMessage() . PHP_EOL;
	}
}

if ( 0 !== $failures ) {
	exit( 1 );
}

echo 'All tests passed.' . PHP_EOL;
