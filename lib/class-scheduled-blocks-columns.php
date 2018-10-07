<?php

/**
 * Scheduled Blocks Columns: Scheduled_Blocks_Columns class
 *
 * @package scheduledblockscolumns
 * @since 0.1.0
 */

/**
 * Handles the columns blocks within Gutenberg.
 *
 * @since 0.1.0
 */

class Scheduled_Blocks_Columns {

	/**
	 * An array of html to remove AFTER gutenberg has parsed. This is mostly
	 * for reusable blocks.
	 *
	 * @var array
	 */
	public $remove_after = array();

	/**
	 * Our main initialization. Add our hooks.
	 *
	 * @return void
	 */
	public function init() {

		$this->add_hooks();

	}// end init()

	/**
	 * Add our actions and filters
	 *
	 * @return void
	 */
	public function add_hooks() {

		// Add 'columns' and 'column' to the parsed list of blocks
		add_filter( 'scheduled_blocks_valid_block_types', array( $this, 'scheduled_blocks_valid_block_types__add_columns_types' ) );

		add_filter( 'scheduled_blocks_get_usable_data_from_block_details_start', array( $this, 'scheduled_blocks_get_usable_data_from_block_details_start__handle_columns' ) );

		add_filter( 'the_content', array( $this, 'the_content__remove_scheduled_columns' ), 17 );

	}// end add_hooks()

	/**
	 * Add the column and columns types to the valid types so we're able to add schedules within
	 * Gutenberg.
	 *
	 * @param array $valid_core_types The current valid types
	 * @return array Adjusted types
	 */
	public function scheduled_blocks_valid_block_types__add_columns_types( $valid_core_types = array() ) {

		$valid_core_types[] = 'columns';
		$valid_core_types[] = 'column';

		return $valid_core_types;

	}//end scheduled_blocks_valid_block_types__add_columns_types()

	/**
	 * If we have innerBlocks AND (attrs['scheduledStart] or attrs['scheduledEnd'] then
	 * the user has added the schedule to the parent (think the columns block rather than
	 * the paragraph block inside a column). The innerHTML property for the parent block
	 * is simply the markup for the parent, it does not contain the markup for its children.
	 * We mark this as a special case and handle it differently when removing from the_content
	 *
	 * @param array $block_details The current block details
	 * @return void
	 */
	public function scheduled_blocks_get_usable_data_from_block_details_start__handle_columns( $block_details ) {

		if ( ! isset( $block_details['innerBlocks'] ) || ! is_array( $block_details['innerBlocks'] ) || empty( $block_details['innerBlocks'] ) ) {
			return $block_details;
		}

		if ( ! isset( $block_details['attrs']['scheduledStart'] ) && ! isset( $block_details['attrs']['scheduledEnd'] ) ) {
			return $block_details;
		}

		if ( ! Scheduled_Blocks::block_should_be_removed( $block_details ) ) {
			return $block_details;
		}

		// If this is a columns block, we need to get the starting tag, and strip the </div> so we know
		// where the column starts.
		$columns_names = array(
			'core/columns',
			'core/column',
		);

		if ( in_array( $block_details['blockName'], array_values( $columns_names ), true ) ) {

			if ( isset( $block_details['attrs']['scheduledStart'] ) ) {
				$this->remove_after[] = array(
					'scheduled' => 'start',
					'datetime'  => $block_details['attrs']['scheduledStart'],
				);
			} else {
				$this->remove_after[] = array(
					'scheduled' => 'end',
					'datetime'  => $block_details['attrs']['scheduledEnd'],
				);
			}
		}

		return $block_details;

	}// end scheduled_blocks_get_usable_data_from_block_details_start__handle_columns()

	public function the_content__remove_scheduled_columns( $content ) {

		if ( empty( $this->remove_after ) ) {
			return $content;
		}

		$dom = new IvoPetkov\HTML5DOMDocument();
		$dom->loadHTML( $content );

		// $this->removeAfter is an array of arrays. Each inner array has 2 keys:
		// 'scheduled' which is either 'Start' or 'End' and is whether we're searching for a scheduledstart or scheduledend
		// 'datetime' which is the timestamp for this scheduledStart/End
		// We're going to loop over each of these inner arrays and for a string such as div[scheduled{Start|End}={$datetime}]
		foreach ( $this->remove_after as $id => $remove_array ) {

			$start_or_end = $remove_array['scheduled'];
			$datetime     = $remove_array['datetime'];
			$string       = 'div[scheduled' . $start_or_end . '="' . $datetime . '"]';

			// Now we have our selector string, we need to get the outerHTML for this element.
			$outer_html = $dom->querySelector( $string )->outerHTML;

			// Now remove this from the content
			$content = str_replace( $outer_html, '', $content );

		}

		return $content;

	}// end the_content__remove_scheduled_columns()

}// end class Scheduled_Blocks_Columns()
