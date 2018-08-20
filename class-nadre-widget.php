<?php
/**
Plugin Name: NADRE Widget
Plugin URI: http://wordpress.org/extend/plugins/#
Description: NADRE plugin
Author: PI4
Version: 1.0.0
**/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'vendor/autoload.php';
include_once 'invenio_client/class-inveniosearchclient.php';

/**
 * @package NADRE_Widget
 * @version 1.0
 */
class NADRE_Widget extends WP_Widget {

	var $client;
	/**
	 * Sets up the widgets name etc
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/__construct/
	 * @see https://developer.wordpress.org/reference/functions/wp_register_sidebar_widget/
	 *
	 */
	function __construct() {
		$widget_ops = array(
			'classname'   => 'nadre_widget',
			'description' => 'NADRE Widget',
		);
		parent::__construct( 'nadre_widget', 'NADRE Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget on front-end
	 *
	 * @param array $args Widget arguments
	 * @param array $instance
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/widget/
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		if ( ! empty( $instance['endpoint'] ) && filter_var( $instance['endpoint'], FILTER_VALIDATE_URL ) ) {
			$this->client = ( $this->client ) ? $this->client : new InvenioSearchClient( $instance['endpoint'] );
			$rg           = $instance['records'];
			if ( $rg > 0 ) {
				$query    = [
					'query' => [
						'rg' => (int) $instance['records'],
						'sf' => 'recid',
						'so' => 'd',
						'ot' => 'abstract,title,authors,creation_date,recid',
						'of' => 'recjson',
					],
				];
				$response = $this->client->search( $query );
				if ( is_array( $response ) ) {
					foreach ( $response as $record ) {
						$rec_title   = $record['title'];
						$rec_authors = $record['authors'];
						$rec_url     = $this->client->get_uri() . '/record/' . $record['recid'];
						?>
						<p>
							<a href="<?php echo esc_url( $rec_url ); ?>" target="_blank">
								<strong><?php echo esc_html( $rec_title['title'] ); ?></strong>
							</a>
							<ul>
								<?php foreach ( $rec_authors as $author ) { ?>
									<li><?php echo esc_html( $author['full_name'] ); ?></li>
								<?php } ?>
							</ul>
							<hr />
						</p>
							<?php
					}
				} else {
					?>
					<p> <?php esc_html( "$response", 'text_domain' ); ?> </p>
					<?php
				}
			} else {
				?>
				<p> <?php esc_html__( 'No valid repository url specified!', 'text_domain' ); ?> </p>
				<?php
			}
		} else {
			?>
			<p> <?php esc_html__( 'No valid repository url specified!', 'text_domain' ); ?> </p>
			<?php
		}
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/form/
	 */
	public function form( $instance ) {
		?>
		<script type='text/javascript' src='<?php echo esc_url( plugins_url( '/assets/js/main.js', __FILE__ ) ); ?>'></script>
		<?php
		$title    = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Title', 'text_domain' );
		$endpoint = ! empty( $instance['endpoint'] ) ? $instance['endpoint'] : esc_html__( 'Invenio endpoint', 'text_domain' );
		$records  = ! empty( $instance['records'] ) ? $instance['records'] : 5;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'endpoint' ) ); ?>">
				<?php esc_attr_e( 'Invenio endpoint:', 'text_domain' ); ?>
			</label>
			<input class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'endpoint' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'endpoint' ) ); ?>"
				type="text" value="<?php echo esc_html( $endpoint ); ?>"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'records' ) ); ?>">
				<?php esc_attr_e( 'Returned records:', 'text_domain' ); ?>
			</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'rec_slider' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'rec_slider' ) ); ?>"
				type="range" min="1" max="10" value="<?php echo esc_html( $records ); ?>"
				onchange="setSelected(this.value, '<?php echo esc_attr( $this->get_field_id( 'records' ) ); ?>')" />
			<input id="<?php echo esc_attr( $this->get_field_id( 'records' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'records' ) ); ?>"
				type="text" value="<?php echo esc_html( $records ); ?>" readonly/>
		</p>
		<?php

	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/update/
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['endpoint'] = ( ! empty( $new_instance['endpoint'] ) ) ? strip_tags( $new_instance['endpoint'] ) : '';
		$instance['records']  = ( ! empty( $new_instance['records'] ) ) ? strip_tags( $new_instance['records'] ) : '';
		return $instance;
	}
}

// register NADRE_Widget
add_action( 'widgets_init', function() {
	register_widget( 'NADRE_Widget' );
} );
