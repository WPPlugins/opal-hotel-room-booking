<?php
/**
 * The template for displaying room content within loops
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/reservation.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

do_action( 'opalhotel_before_reservation' );

/* reservation steps */
$steps = apply_filters( 'opalhotel_reservation_steps', array(
		1		=> array(
				'label'		=> __( 'Check Availability', 'opal-hotel-room-booking' ),
				'callback'	=> 'opalhotel_calendar_search_template_step'
			),
		2		=> array(
				'label'		=> __( 'Choose Room', 'opal-hotel-room-booking' ),
				'callback'	=> 'opalhotel_search_results_template_step'
			),
		3		=> array(
				'label'		=> __( 'Make A Reservation', 'opal-hotel-room-booking' ),
				'callback'	=> 'opalhotel_make_a_reservation_step'
			),
		4		=> array(
				'label'		=> __( 'Confirmation', 'opal-hotel-room-booking' ),
				'callback'	=> 'opalhotel_confirmation_step'
			),
	) );

$current_step = ! empty( $_REQUEST['step'] ) ? absint( $_REQUEST['step'] ) : ( isset( $atts['step'] ) ? absint( $atts['step'] ) : 1 );
$step_content = ! empty( $steps[$current_step] ) && ! empty( $steps[$current_step]['callback'] ) ? $steps[$current_step]['callback'] : '';

?>

<div class="opalhotel-reservation-container">
	<header class="opalhotel-reservation-process-steps">

		<?php do_action( 'opalhotel_before_process_step', $atts ); ?>

		<ul>
			<?php foreach ( $steps as $num => $step ) : ?>

				<li class="opalhotel-step<?php echo esc_attr( $current_step == $num ? ' active' : '' ) ?>">
					<span><?php printf( '%d', $num ) ?></span>
					<h4><?php echo esc_html( ! empty( $step['label'] ) ? $step['label'] : '' ); ?></h4>
				</li>

			<?php endforeach; ?>
		</ul>

	</header>

	<?php
		// print all notices
		opalhotel_print_notices();
	?>
	<div class="opalhotel-reservation-step-content">

		<?php if ( $step_content ) : ?>
			<?php call_user_func( $step_content, $current_step, $atts ); ?>
		<?php endif; ?>

	</div>

	<?php do_action( 'opalhotel_after_reservation' ); ?>

	<?php do_action( 'opalhotel_after_process_step', $atts ); ?>

</div>