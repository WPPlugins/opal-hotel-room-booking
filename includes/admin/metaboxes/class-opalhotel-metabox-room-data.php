<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalhotel
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2016 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class OpalHotel_MetaBox_Room_Data {

	/* render */
	public static function render( $post ) {
		$opalhotel_room = OpalHotel_Room::instance( $post );
		/* filter */
		$tab_panels = apply_filters( 'opalhotel_room_data_tab', array(
				'general' => array(
					'label'  => __( 'General', 'opal-hotel-room-booking' ),
					'target' 	=> 'general_room_data',
					'icon'		=> 'fa fa-cog'
				),
				'discount' => array(
					'label'  	=> __( 'Discount', 'opal-hotel-room-booking' ),
					'target' 	=> 'discount_room_data',
					'icon'		=> 'fa fa-gift'
				),
				'extra_price' => array(
					'label'  	=> __( 'Extra Price', 'opal-hotel-room-booking' ),
					'target' 	=> 'extra_price_room_data',
					'icon'		=> 'fa fa-plug'
				),
				'package' => array(
					'label'  	=> __( 'Packages', 'opal-hotel-room-booking' ),
					'target' 	=> 'package_room_data',
					'icon'		=> 'fa fa-plane'
				),
				'amenities' => array(
					'label'  	=> __( 'Amenities', 'opal-hotel-room-booking' ),
					'target' 	=> 'amenities',
					'icon'		=> 'fa fa-gear'
				),
				'extra_amenities' => array(
					'label'  	=> __( 'Extra amenities', 'opal-hotel-room-booking' ),
					'target' 	=> 'extra_amenities',
					'icon'		=> 'fa fa-gear'
				),
				'description' => array(
					'label'  	=> __( 'Description', 'opal-hotel-room-booking' ),
					'target' 	=> 'pricing_description_data',
					'icon'		=> 'fa fa-file-text-o'
				),
		) );

		$amenities = opalhoteL_get_room_amenities();
		?>
		<div id="opalhotel_room_data_container" class="opalhotel_metabox_data_container">
		<?php wp_nonce_field( 'opalhotel_save_data', 'opalhotel_meta_nonce' ); ?>
			<ul class="opalhotel_metabox_data_tabs">
				<?php $i = 0; foreach ( $tab_panels as $key => $tab ) : ?>

					<li class="opalhotel_tab <?php echo esc_attr( $key ) ?>">
						<a href="#<?php echo esc_attr( $tab['target'] ) ?>" class="<?php echo $i === 0 ? ' active' : '' ?>">
							<?php if ( isset( $tab['icon'] ) ) : ?>
								<i class="<?php echo esc_attr( $tab['icon'] ) ?>"></i>
							<?php endif; ?>
							<?php echo esc_html( $tab['label'] ); ?>
						</a>
					</li>

				<?php $i++; endforeach; ?>
			</ul>

			<?php foreach ( $tab_panels as $key => $tab ) : ?>

				<?php do_action( 'opalhotel_room_data_before_tab_panel', $key, $tab ); ?>

				<?php if ( $key === 'general' ) : ?>

					<div id="general_room_data" class="opalhotel_room_data_panel active">

						<!-- quantity -->
						<?php if ( opalhotel_enable_hotel_mode() ) : ?>
							<div class="opalhotel_field_group">
								<?php
									$hotels = get_posts( array(
											'post_type'			=> OPALHOTEL_CPT_HOTEL,
											'posts_per_page'	=> -1,
											'post_status'		=> 'publish'
										) );

									$selected = get_post_meta( $post->ID, '_hotel', true );
									if ( $hotels ) : ?>
										<label for="hotels"><?php esc_html_e( 'Hotels', 'opal-hotel-room-booking' ); ?></label>

										<select name="_hotel">
											<?php foreach ( $hotels as $hotel ) : setup_postdata( $hotel ); ?>

												<option value="<?php echo esc_attr( $hotel->ID ) ?>"<?php selected( $selected, $hotel->ID ) ?>><?php echo esc_html( $hotel->post_title ); ?></option>

											<?php endforeach; wp_reset_postdata(); ?>
										</select>

									<?php endif; ?>
							</div>
						<?php endif; ?>
						<script type="text/javascript">
							(function($){

								$(document).ready(function(){
									// $('#hotels').select2();
								});

							})(jQuery);
						</script>

						<!-- quantity -->
						<div class="opalhotel_field_group">
							<label for="number_of_rooms"><?php esc_html_e( 'Number of room', 'opal-hotel-room-booking' ); ?></label>
							<input name="_qty" id="number_of_rooms" class="opalhotel_input_field" type="number" step="1" min="0" value="<?php echo esc_attr( absint( $opalhotel_room->qty ) ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'How many room?.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_qty' ); ?>
						</div>

						<!-- base price -->
						<div class="opalhotel_field_group">
							<label for="base_price"><?php printf( __( 'Base price (%s)', 'opal-hotel-room-booking' ), opalhotel_get_currency_symbol() ); ?></label>
							<input name="_base_price" id="base_price" class="opalhotel_input_field" type="number" step="any" min="0" value="<?php echo esc_attr( floatval( $opalhotel_room->base_price() ) ) ?>"/>
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Set base price of room.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_base_price' ); ?>
						</div>

						<!-- adults -->
						<div class="opalhotel_field_group">
							<label for="adults"><?php esc_html_e( 'Capacities', 'opal-hotel-room-booking' ); ?></label>
							<input name="_adults" id="adults" class="opalhotel_input_field" type="number" step="1" min="0" value="<?php echo esc_attr( absint( $opalhotel_room->adults ) ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Maximum adults allowed', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_adult' ); ?>
						</div>

						<!-- childrens -->
						<div class="opalhotel_field_group">
							<label for="childrens"><?php esc_html_e( 'Childrens', 'opal-hotel-room-booking' ); ?></label>
							<input name="_childrens" id="childrens" class="opalhotel_input_field" type="number" step="1" min="0" value="<?php echo esc_attr( absint( $opalhotel_room->childrens ) ) ?>" />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Maximum childrens allowed', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_children' ); ?>
						</div>

						<!-- Minimum night -->
						<div class="opalhotel_field_group">
							<label for="min_night"><?php esc_html_e( 'Minimum night', 'opal-hotel-room-booking' ); ?></label>
							<input name="_min_night" id="min_night" class="opalhotel_input_field" type="number" step="1" min="0"value="<?php echo esc_attr( absint( $opalhotel_room->min_night ) ) ?>"  />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Minimum night allowed, this will appear in search results.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_min_night' ); ?>
						</div>

						<!-- Bed -->
						<div class="opalhotel_field_group">
							<label for="bed"><?php esc_html_e( 'Bed', 'opal-hotel-room-booking' ); ?></label>
							<input name="_bed" id="bed" class="opalhotel_input_field" type="number" step="1" min="0" value="<?php echo esc_attr( absint( $opalhotel_room->bed ) ) ?>"  />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Number of bed.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_bed' ); ?>
						</div>

						<?php do_action( 'opalhotel_room_general_data', $opalhotel_room ) ?>
					</div>

					<div id="extra_amenities" class="extra_amenities opalhotel_room_data_panel">
						<table style="width:100%">
							<?php
							 	$labels   = (array)$opalhotel_room->extra_amenities_label;
							 	$contents = (array)$opalhotel_room->extra_amenities_content;
							?>
							<?php foreach( $labels as $key => $value ) : ?>
								<tr class="extra_amenities_ipts">
									<td>
										<div class="opalhotel_field_group">
											<label  ><?php esc_html_e( 'Label', 'opal-hotel-room-booking' ); ?></label>
											<input name="_extra_amenities_label[]"  class="opalhotel_input_field" type="text" value="<?php echo $value; ?>"  />
										</div>
									</td>
									<td>
										<div class="opalhotel_field_group">
											<label ><?php esc_html_e( 'Content', 'opal-hotel-room-booking' ); ?></label>
											<input name="_extra_amenities_content[]"  class="opalhotel_input_field" type="text" value="<?php echo isset($contents[$key])?($contents[$key]):"";?>"  />
										</div>
									</td>
									<td>
										<div class="opalhotel_field_group">
											<div class="btn-remove button" data-confirmed="<?php esc_html_e( 'Are you sure to delete?','opal-hotel-room-booking' ); ?>"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?></div>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>

							<tr class="extra_amenities_tmp">
								<td>
									<div class="opalhotel_field_group">
										<label><?php esc_html_e( 'Label', 'opal-hotel-room-booking' ); ?></label>
										<input name="_extra_amenities_label[]" class="opalhotel_input_field" type="text" value=""  />
										<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Room size.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
										<?php do_action( 'opalhotel_room_data_room_size' ); ?>
									</div>
								</td>
								<td>
									<div class="opalhotel_field_group">
										<label ><?php esc_html_e( 'Content', 'opal-hotel-room-booking' ); ?></label>
										<input name="_extra_amenities_content[]"  class="opalhotel_input_field" type="text" value=""  />
										<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Room size.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
										<?php do_action( 'opalhotel_room_data_room_size' ); ?>
									</div>
								</td>
								<td>
									<div class="opalhotel_field_group">
										<div class="btn-remove button" data-confirmed="<?php esc_html_e( 'Are you sure to delete?','opal-hotel-room-booking' ); ?>"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?></div>
									</div>
								</td>
							</tr>

						</table>
						<div class="extra_amenities_action">
							<div class="btn-action button button-primary button-large"><?php esc_html_e('Add'); ?></div>
						</div>
						<br>
					</div>

					<div class="opalhotel_room_data_panel" id="amenities">
							<!-- View -->
						<div class="opalhotel_field_group">
							<label for="room_size"><?php esc_html_e( 'Room Size', 'opal-hotel-room-booking' ); ?></label>
							<input name="_room_size" id="room_size" class="opalhotel_input_field" type="text" value="<?php echo esc_attr( $opalhotel_room->room_size ) ?>"  />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Room size.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_room_size' ); ?>
						</div>

						<div class="opalhotel_field_group">
							<label for="view"><?php esc_html_e( 'View', 'opal-hotel-room-booking' ); ?></label>
							<input name="_view" id="room_size" class="opalhotel_input_field" type="text" value="<?php echo esc_attr( $opalhotel_room->view ) ?>"  />
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'View.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<?php do_action( 'opalhotel_room_data_view' ); ?>
						</div>

						<?php if( $amenities ): ?>
							<?php 
							foreach( $amenities as $key => $amenity ): 
								$value  = isset($opalhotel_room->amenities[$key])?$opalhotel_room->amenities[$key]:"";
							?>
							<div class="opalhotel_field_group">
								<label for="view"><?php echo $amenity['label']; ?></label>
								<input name="_amenities[<?php echo $key; ?>]" id="label-<?php echo $key; ?>" class="opalhotel_input_field" type="text" value="<?php echo esc_attr( $value ) ?>"  />
								<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'View descriptions.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
								<?php do_action( 'opalhotel_room_data_view' ); ?>
							</div>
							<?php endforeach; ?>
						<?php endif; ?> 
					</div>
				<?php elseif ( $key === 'discount' ) : ?>

					<div id="discount_room_data" class="opalhotel_room_data_panel">
						<table class="opalhotel_metabox_table" id="opalhotel_discount_price">
							<thead>
								<tr>
									<th colspan="5"><?php esc_html_e( 'Set Discount Price', 'opal-hotel-room-booking' ); ?></th>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Type', 'opal-hotel-room-booking' ); ?></th>
									<th><?php esc_html_e( 'Unit', 'opal-hotel-room-booking' ); ?></th>
									<th><?php esc_html_e( 'Sale', 'opal-hotel-room-booking' ); ?></th>
									<th><?php esc_html_e( 'Discount', 'opal-hotel-room-booking' ); ?></th>
									<th>&nbsp;</th>
								</tr>
							</thead>

							<tbody>
								<?php if ( $discounts = $opalhotel_room->discounts ) : ?>

									<?php $types = opalhotel_room_discount_types(); // discount types ?>
									<?php $sale_types = opalhotel_room_discount_sale_types(); // sale types ?>
									<?php foreach ( $discounts as $k => $discount ) : ?>
										<tr>
											<td>
												<select name="_discounts[type][]">
													<?php foreach ( $types as $type => $label ) : ?>
														<option value="<?php echo esc_attr( $type ) ?>"<?php selected( $discount['type'], $type ) ?>>
															<?php printf( '%s', $label ) ?>
														</option>
													<?php endforeach; ?>
												</select>
											</td>
											<td>
												<input name="_discounts[unit][]" type="number" step="1" min="0" value="<?php echo esc_attr( $discount['unit'] ) ?>" />
											</td>
											<td>
												<select name="_discounts[sale_type][]">
													<?php foreach ( $sale_types as $type => $label ) : ?>
														<option value="<?php echo esc_attr( $type ) ?>"<?php selected( $discounts[$k]['sale_type'], $type ) ?>>
															<?php printf( '%s', $label ) ?>
														</option>
													<?php endforeach; ?>
												</select>
											</td>
											<td>
												<input name="_discounts[amount][]" type="number" step="any" min="0" value="<?php echo esc_attr( $discount['amount'] ) ?>" />
											</td>
											<td>
												<a href="#" class="button opalhotel_remove_extra"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?></a>
											</td>
										</tr>
									<?php endforeach; ?>

								<?php endif; ?>
							</tbody>

							<tfoot>
								<tr>
									<th colspan="5">
										<a href="#" class="button opalhotel_add_extra" id="opalhotel_add_discount" data-template="opalhotel-discount"><?php esc_html_e( 'Add new', 'opal-hotel-room-booking' ); ?></a>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>

				<?php elseif ( $key === 'extra_price' ) : ?>
					<!-- extra price tab content -->
					<div id="extra_price_room_data" class="opalhotel_room_data_panel">
						<!-- adults -->
						<table class="opalhotel_metabox_table" id="opalhotel_extra_adult_price">
							<thead>
								<tr>
									<th colspan="3"><?php esc_html_e( 'Adults', 'opal-hotel-room-booking' ); ?></th>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Qty', 'opal-hotel-room-booking' ); ?></th>
									<th><?php printf( __( 'Price (%s)', 'opal-hotel-room-booking' ), opalhotel_get_currency_symbol() ); ?></th>
									<th>&nbsp;</th>
								</tr>
							</thead>

							<tbody>
							<?php if ( $extra_adults = $opalhotel_room->extra_adults ) : ?>

									<?php foreach ( $extra_adults as $k => $extra ) : ?>

										<tr>
											<td>
												<input type="number" min="0" step="1" name="_extra_adults[qty][<?php echo esc_attr( $k ) ?>]" value="<?php echo esc_attr( $extra['qty'] ) ?>"  />
											</td>
											<td>
												<input type="number" min="0" step="any" name="_extra_adults[price][<?php echo esc_attr( $k ) ?>]" value="<?php echo esc_attr( $extra['price'] ) ?>"  />
											</td>
											<td>
												<a href="#" class="button opalhotel_remove_extra"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?></a>
											</td>
										</tr>

									<?php endforeach; ?>

							<?php endif; ?>
							</tbody>

							<tfoot>
								<tr>
									<th colspan="3">
										<a href="#" class="button opalhotel_add_extra" id="opalhotel_add_extra_adult_price" data-template="opalhotel-extra-price" data-name="_extra_adults">
											<?php esc_html_e( 'Add new', 'opal-hotel-room-booking' ); ?>
										</a>
									</th>
								</tr>
							</tfoot>
						</table>
						<!-- end adults -->
						<!-- childrens -->
						<table class="opalhotel_metabox_table" id="opalhotel_extra_child_price">
							<thead>
								<tr>
									<th colspan="3"><?php esc_html_e( 'Childrens', 'opal-hotel-room-booking' ); ?></th>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Qty', 'opal-hotel-room-booking' ); ?></th>
									<th><?php printf( __( 'Price (%s)', 'opal-hotel-room-booking' ), opalhotel_get_currency_symbol() ); ?></th>
									<th>&nbsp;</th>
								</tr>
							</thead>

							<tbody>
							<?php if ( $extra_childs = get_post_meta( $post->ID, '_extra_childs', true ) ) : ?>

									<?php foreach ( $extra_childs as $k => $extra ) : ?>

										<tr>
											<td>
												<input type="number" min="0" step="1" name="_extra_childs[qty][<?php echo esc_attr( $k ) ?>]" value="<?php echo esc_attr( $extra['qty'] ) ?>"  />
											</td>
											<td>
												<input type="number" min="0" step="any" name="_extra_childs[price][<?php echo esc_attr( $k ) ?>]" value="<?php echo esc_attr( $extra['price'] ) ?>"  />
											</td>
											<td>
												<a href="#" class="button opalhotel_remove_extra"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?></a>
											</td>
										</tr>

									<?php endforeach; ?>

							<?php endif; ?>
							</tbody>

							<tfoot>
								<tr>
									<th colspan="3">
										<a href="#" class="button opalhotel_add_extra" id="opalhotel_add_extra_child_price" data-template="opalhotel-extra-price"  data-name="_extra_childs">
											<?php esc_html_e( 'Add new', 'opal-hotel-room-booking' ); ?>
										</a>
									</th>
								</tr>
							</tfoot>
						</table>
						<!-- end childrens -->
					</div>

				<?php elseif ( $key === 'package' ) : ?>
					<!-- package tab content -->
					<div id="package_room_data" class="opalhotel_room_data_panel">

						<!-- quantity -->
						<div class="opalhotel_field_group">
							<label for="opalhotel_room_package"><?php esc_html_e( 'Package', 'opal-hotel-room-booking' ); ?></label>
							<span class="opalhotel_tiptip" data-tip="<?php esc_attr_e( 'Select packages. Sortable package will appear on your frontend.', 'opal-hotel-room-booking' ); ?>"><i class="fa fa-question-circle-o"></i></span>
							<select name="_search" id="opalhotel_room_package" class="opalhotel_input_field"></select>
							<a href="#" class="button" id="opalhotel_room_add_package"><?php esc_html_e( 'Add Package', 'opal-hotel-room-booking' ); ?></a>
						</div>

						<?php if ( $packages = $opalhotel_room->get_packages() ) : ?>

							<div class="opalhotel_field_group opalhotel_sortable_container" id="opalhotel-room-packages">

								<?php foreach ( $packages as $k => $package ) : ?>

									<div class="opalhotel_sortable">
										<div class="package_id">
											<a href="<?php echo esc_url( get_edit_post_link( $package->id ) ) ?>"><?php echo esc_html( opalhotel_format_id( $package->id ) ); ?></a>
										</div>
										<div class="package_name"><?php echo esc_html( $package->post_title ); ?></div>
										<div class="package_action">
											<a href="#" class="button remove_package"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ) ?></a>
										</div>
										<input type="hidden" name="_package_id[]" value="<?php echo esc_attr( $package->id ) ?>" />

										<?php do_action( 'opalhotel_room_package_data', $opalhotel_room, $package ) ?>
									</div>

								<?php endforeach; ?>

							</div>

						<?php endif; ?>

					</div>

				<?php elseif ( $key === 'description' ) : ?>

					<div id="pricing_description_data" class="opalhotel_room_data_panel">
						<?php wp_editor( get_the_excerpt(), 'excerpt' ); ?>
					</div>

				<?php else : ?>

					<?php do_action( 'opalhotel_room_data_tab_panel', $key, $tab ); ?>

				<?php endif; ?>

				<?php do_action( 'opalhotel_room_data_after_tab_panel', $key, $tab ); ?>

			<?php endforeach; ?>
		</div>

		<!-- underscrore template -->
		<!-- extra price table -->
		<script type="text/html" id="tmpl-opalhotel-extra-price">
			<tr>
				<td>
					<input type="number" min="0" step="1" name="{{ data.name }}[qty][{{ data.key }}]" value="{{ data.qty }}"  />
				</td>
				<td>
					<input type="number" min="0" step="any" name="{{ data.name }}[price][{{ data.key }}]" value="{{ data.price }}"  />
				</td>
				<td>
					<a href="#" class="button opalhotel_remove_extra"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?></a>
				</td>
			</tr>
		</script>
		<script type="text/html" id="tmpl-opalhotel-discount">
			<?php $types = opalhotel_room_discount_types(); // discount types ?>
			<?php $sale_types = opalhotel_room_discount_sale_types(); // sale types ?>
			<tr>
				<td>
					<select name="_discounts[type][]">
						<?php foreach ( $types as $type => $label ) : ?>
							<option value="<?php echo esc_attr( $type ) ?>">
								<?php printf( '%s', $label ) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<input name="_discounts[unit][]" type="number" step="1" min="0" value="" />
				</td>
				<td>
					<select name="_discounts[sale_type][]">
						<?php foreach ( $sale_types as $type => $label ) : ?>
							<option value="<?php echo esc_attr( $type ) ?>">
								<?php printf( '%s', $label ) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<input name="_discounts[amount][]" type="number" step="any" min="0" value="" />
				</td>
				<td>
					<a href="#" class="button opalhotel_remove_extra"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ); ?></a>
				</td>
			</tr>
		</script>
		<!-- opalhotel package -->
		<script type="text/html" id="tmpl-opalhotel-package">
			<div class="opalhotel_sortable">
				<div class="package_id">
					<a href="{{ data.edit_link }}">{{ data.id_format }}</a>
				</div>
				<div class="package_name">{{{ data.title }}}</div>
				<div class="package_action">
					<a href="#" class="button remove_package"><?php esc_html_e( 'Remove', 'opal-hotel-room-booking' ) ?></a>
				</div>
				<input type="hidden" name="_package_id[]" value="{{ data.id }}" />
			</div>
		</script>
		<!-- end extra price table -->
		<?php
	}

	/* save post meta*/
	public static function save( $post_id, $post ) {

		if ( $post->post_type !== OPALHOTEL_CPT_ROOM || empty( $_POST ) ) {
			return;
		}
		/* delete post meta */
		if ( ! empty( $_POST['_hotel'] ) ) {
			update_post_meta( $post_id, '_hotel', $_POST['_hotel'] );
		}
		/* set quantity */
		if ( ! isset( $_POST['_qty'] ) || ! is_numeric( $_POST['_qty'] ) ) {
			OpalHotel_Admin_MetaBoxes::add_error( __( 'Please set number of room.', 'opal-hotel-room-booking' ) );
		} else {
			update_post_meta( $post_id, '_qty', absint( $_POST['_qty'] ) );
		}

		/* set base price */
		if ( ! isset( $_POST['_base_price'] ) || ! is_numeric( $_POST['_base_price'] ) ) {
			OpalHotel_Admin_MetaBoxes::add_error( __( 'Please set base price of room.', 'opal-hotel-room-booking' ) );
		} else {
			update_post_meta( $post_id, '_base_price', floatval( $_POST['_base_price'] ) );
		}

		/* set adults */
		if ( ! isset( $_POST['_adults'] ) || ! is_numeric( $_POST['_adults'] ) ) {
			OpalHotel_Admin_MetaBoxes::add_error( __( 'Please set number of capacities.', 'opal-hotel-room-booking' ) );
		} else {
			update_post_meta( $post_id, '_adults', absint( $_POST['_adults'] ) );
		}

		/* set childrens */
		$childrens = isset( $_POST['_childrens'] ) ? absint( $_POST['_childrens'] ) : 0;
		update_post_meta( $post_id, '_childrens', $childrens );

		/* set childrens */
		$min_night = isset( $_POST['_min_night'] ) ? absint( $_POST['_min_night'] ) : 0;
		update_post_meta( $post_id, '_min_night', $min_night );

		/* optional */
		if ( ! empty( $_POST['_extra_adults'] ) ) {
			$adults = $_POST['_extra_adults'];
			if ( isset( $adults['price'], $adults['qty'] ) ) {
				$adults_extra = array();
				foreach ( $adults['price'] as $k => $price ) {
					$ex = array(
							'price'	=> $price,
							'qty'	=> isset( $adults['qty'][$k] ) ? $adults['qty'][$k] : 0
						);
					$adults_extra[] = $ex;
				}
				update_post_meta( $post_id, '_extra_adults', $adults_extra );
				// do action hook
				do_action( 'opalhotel_update_extra_adults', $adults_extra, $post_id, $post );
			}
		} else {
			update_post_meta( $post_id, '_extra_adults', array() );
		}
		/* childs */
		if ( ! empty( $_POST['_extra_childs'] ) ) {
			$childs = $_POST['_extra_childs'];
			if ( isset( $childs['price'], $childs['qty'] ) ) {
				$childs_extra = array();
				foreach ( $childs['price'] as $k => $price ) {
					$ex = array(
							'price'	=> $price,
							'qty'	=> isset( $childs['qty'][$k] ) ? $childs['qty'][$k] : 0
						);
					$childs_extra[] = $ex;
				}
				update_post_meta( $post_id, '_extra_childs', $childs_extra );
				// do action hook
				do_action( 'opalhotel_update_extra_childs', $childs_extra, $post_id, $post );
			}
		} else {
			update_post_meta( $post_id, '_extra_childs', array() );
		}

		if ( ! empty( $_POST['_discounts'] ) ) {
			$discounts = $_POST['_discounts'];
			$discounts_params = array();
			foreach ( $discounts['type'] as $k => $type ) {
				$param = array(
						'type'		=> $type,
						'unit'		=> isset( $discounts['unit'], $discounts['unit'][$k] ) ? absint( $discounts['unit'][$k] ) : 0,
						'sale_type'	=> isset( $discounts['sale_type'], $discounts['sale_type'][$k] ) ? $discounts['sale_type'][$k] : 'subtract',
						'amount'	=> isset( $discounts['amount'], $discounts['amount'][$k] ) ? absint( $discounts['amount'][$k] ) : 0
					);
				$discounts_params[] = $param;
			}
			update_post_meta( $post_id, '_discounts', $discounts_params );
			// do action hook
			do_action( 'opalhotel_update_discounts', $discounts_params, $post_id, $post );
		} else {
			update_post_meta( $post_id, '_discounts', array() );
		}

		/* delete ola meta */
		delete_post_meta( $post->ID, '_package_id' );
		if ( isset( $_POST['_package_id'] ) && ! empty( $_POST['_package_id'] ) ) {
			foreach ( $_POST['_package_id'] as $k => $id ) {
				add_post_meta( $post_id, '_package_id', $id );
			}
		}

		if ( isset( $_POST['_bed'] ) ) {
			update_post_meta( $post_id, '_bed', absint( $_POST['_bed'] ) );
			// do action hook
			do_action( 'opalhotel_update_bed', $_POST['_bed'], $post_id, $post );
		}

		if ( isset( $_POST['_view'] ) ) {
			update_post_meta( $post_id, '_view', ( $_POST['_view'] ) );
			// do action hook
			do_action( 'opalhotel_update_view', $_POST['_view'], $post_id, $post );
		}

		if ( isset( $_POST['_amenities'] ) ) {  
			update_post_meta( $post_id, '_amenities', $_POST['_amenities']  );
			// do action hook
			do_action( 'opalhotel_update_amenities', $_POST['_view'], $post_id, $post );
		}

		if ( isset( $_POST['_room_size'] ) ) {
			update_post_meta( $post_id, '_room_size', sanitize_text_field( $_POST['_room_size'] ) );
			// do action hook
			do_action( 'opalhotel_update_view', $_POST['_view'], $post_id, $post );
		}

		/// 
		if ( isset( $_POST['_extra_amenities_content'] ) ) {
			unset( $_POST['_extra_amenities_content'][count($_POST['_extra_amenities_content'])-1] );
			unset( $_POST['_extra_amenities_label'][count($_POST['_extra_amenities_label'])-1] );
			if( !empty($_POST['_extra_amenities_label']) ){  
				update_post_meta( $post_id, '_extra_amenities_label', $_POST['_extra_amenities_label'] );
				update_post_meta( $post_id, '_extra_amenities_content', $_POST['_extra_amenities_content'] ); 
			} else {
				update_post_meta( $post_id, '_extra_amenities_label', array() );
				update_post_meta( $post_id, '_extra_amenities_content', array() ); 
			}
		}
	}

}
