<?php
/**
 * The template for displaying hotel content within single
 *
 * This template can be overridden by copying it to yourtheme/opalhotel/single-hotel/title.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$tabs = opalhotel_single_hotel_tabs_filter();

if ( ! $tabs ) return;

?>

<div class="opalhotel-tabs opalhotel-single-hotel-tabs hotel-box">

	<ul class="tabs clearfix">
		<?php $i = 0; foreach ( $tabs as $key => $tab ) : $i++; ?>
			<li class="<?php echo esc_attr( $key ) ?><?php echo esc_attr( $i === 1 ? ' active' : '' ) ?>">
				<a href="#<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $tab['label'] ) ?></a>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php $i = 0; foreach ( $tabs as $key => $tab ) : ?>
		<?php if ( ! empty( $tab['callback'] ) ) : $i++; ?>
			<div class="panel entry-content<?php echo esc_attr( $i === 1 ? ' fade in' : '' ) ?>" id="<?php echo esc_attr( $key ) ?>">
				<?php call_user_func( $tab['callback'], $key, $tab ); ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>

</div>