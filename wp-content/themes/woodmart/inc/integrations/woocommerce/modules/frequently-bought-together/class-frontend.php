<?php
/**
 * Frequently bought together class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Frequently_Bought_Together;

use WP_Query;
use XTS\Singleton;

/**
 * Frontend class.
 */
class Frontend extends Singleton {

	/**
	 * Frequently bought together products.
	 *
	 * @var array
	 */
	protected $wfbt_products = array();

	/**
	 * Frequently bought together main product id.
	 *
	 * @var string
	 */
	protected $main_product_id = '';

	/**
	 * Bundle ID.
	 *
	 * @var string
	 */
	protected $bundle_id = '';

	/**
	 * Subtotal bundle products price.
	 *
	 * @var array
	 */
	protected $subtotal_products_price = array();

	/**
	 * Init.
	 */
	public function init() {
		add_action( 'woodmart_after_product_tabs', array( $this, 'get_bought_together_products' ) );

		add_action( 'wp_ajax_woodmart_update_frequently_bought_price', array( $this, 'update_frequently_bought_price' ) );
		add_action( 'wp_ajax_nopriv_woodmart_update_frequently_bought_price', array( $this, 'update_frequently_bought_price' ) );

		add_filter( 'woodmart_localized_string_array', array( $this, 'update_localized_string' ) );
	}

	/**
	 * Update localized settings
	 *
	 * @param array $settings Settings.
	 * @return array
	 */
	public function update_localized_string( $settings ) {
		$settings['frequently_bought'] = wp_create_nonce( 'wd-frequently-bought-together' );

		return $settings;
	}

	/**
	 * Update ajax frequently bought price.
	 *
	 * @return void
	 */
	public function update_frequently_bought_price() {
		check_ajax_referer( 'wd-frequently-bought-together', 'key' );

		if ( empty( $_POST['main_product'] ) || empty( $_POST['products_id'] ) || empty( $_POST['bundle_id'] ) ) {
			return;
		}

		$bundle_id    = sanitize_text_field( wp_unslash( $_POST['bundle_id'] ) );
		$main_product = sanitize_text_field( wp_unslash( $_POST['main_product'] ) );
		$products_id  = woodmart_clean( $_POST['products_id'] ); //phpcs:ignore
		$fbt_products = get_post_meta( $bundle_id, '_woodmart_fbt_products', true );
		$fragments    = array();

		$this->subtotal_products_price = array();

		if ( ! $fbt_products ) {
			return;
		}

		foreach ( $fbt_products as $fbt_product ) {
			$this->wfbt_products[ $fbt_product['id'] ] = $fbt_product;
		}

		$this->main_product_id = (int) $main_product;
		$this->bundle_id       = $bundle_id;

		if ( $products_id ) {
			foreach ( $products_id as $id => $variation_id ) {
				if ( ! isset( $this->wfbt_products[ $id ] ) && $id !== (int) $main_product && $variation_id !== (int) $main_product ) {
					continue;
				}

				if ( $variation_id ) {
					$variation_product = wc_get_product( $variation_id );

					$fragments[ 'div.wd-fbt-bundle-' . $this->bundle_id . ' .wd-product-' . $id . ' .price' ] = '<span class="price">' . $this->update_product_price( $variation_product->get_price_html(), $variation_product ) . '</span>';
				} else {
					$current_product = wc_get_product( $id );
					$this->update_product_price( $current_product->get_price_html(), $current_product );
				}
			}
		}

		$fbt_count = count( $this->subtotal_products_price );

		$fragments[ 'div.wd-fbt-bundle-' . $this->bundle_id . ' .wd-fbt-purchase .price' ]       = '<span class="price">' . $this->get_subtotal_bundle_price() . '</span>';
		$fragments[ 'div.wd-fbt-bundle-' . $this->bundle_id . ' .wd-fbt-purchase .wd-fbt-desc' ] = '<div class="wd-fbt-desc">' . sprintf( _n( 'For %s item', 'For %s items', $fbt_count, 'woodmart' ), $fbt_count ) . '</div>';

		wp_send_json(
			array(
				'fragments' => $fragments,
			)
		);
	}

	/**
	 * Get bought together products content.
	 *
	 * @param array $element_settings Settings.
	 *
	 * @return void
	 */
	public function get_bought_together_products( $element_settings = array() ) {
		global $product;

		$settings = array(
			'title'                   => '',
			'slides_per_view'         => woodmart_get_opt( 'bought_together_column', 3 ),
			'slides_per_view_tablet'  => woodmart_get_opt( 'bought_together_column_tablet', 'auto' ),
			'slides_per_view_mobile'  => woodmart_get_opt( 'bought_together_column_mobile', 'auto' ),
			'hide_pagination_control' => '',
			'hide_prev_next_buttons'  => '',
			'form_width'              => woodmart_get_opt( 'bought_together_form_width' ),
			'is_builder'              => false,
		);

		if ( $element_settings ) {
			$settings = array_merge( $settings, $element_settings );
		}

		$main_product          = $product->get_id();
		$this->main_product_id = $main_product;
		$bundles_data          = array();

		$bundles_id = get_post_meta( $main_product, 'woodmart_fbt_bundles_id', true );

		if ( ! $bundles_id ) {
			return;
		}

		foreach ( $bundles_id as $bundle_id ) {
			$bundle        = get_post( $bundle_id );
			$wfbt_products = get_post_meta( $bundle->ID, '_woodmart_fbt_products', true );

			if ( ! $wfbt_products ) {
				continue;
			}

			$bundles_data[ $bundle->ID ] = $wfbt_products;
		}

		if ( ! $bundles_data ) {
			return;
		}

		woodmart_enqueue_inline_style( 'woo-opt-fbt' );
		woodmart_enqueue_js_script( 'frequently-bought-together' );

		add_filter( 'woocommerce_get_price_html', array( $this, 'update_product_price' ), 10, 2 );
		add_filter( 'woodmart_product_label_output', array( $this, 'added_sale_label' ) );
		remove_action( 'woodmart_add_loop_btn', 'woocommerce_template_loop_add_to_cart', 10 );

		if ( ! $settings['is_builder'] ) {
			echo '<div class="container wd-fbt-wrap">';
		}

		if ( ! $settings['is_builder'] || $settings['title'] ) {
			$this->get_heading( $settings['title'], $settings['is_builder'] );
		}

		foreach ( $bundles_data as $bundle_id => $wfbt_products ) {
			$this->bundle_id               = $bundle_id;
			$this->wfbt_products           = array();
			$this->subtotal_products_price = array();

			foreach ( $wfbt_products as $wfbt_product ) {
				if ( $this->main_product_id === (int) $wfbt_product['id'] ) {
					continue;
				}

				$current_product = wc_get_product( $wfbt_product['id'] );

				if ( 'variation' === $current_product->get_type() && $current_product->get_parent_id() && $this->main_product_id === $current_product->get_parent_id() ) {
					continue;
				}

				$this->wfbt_products[ $wfbt_product['id'] ] = $wfbt_product;
			}

			$this->get_form_content( $settings );
		}

		if ( ! $settings['is_builder'] ) {
			echo '</div>';
		}

		remove_filter( 'woocommerce_get_price_html', array( $this, 'update_product_price' ), 10, 2 );
		remove_filter( 'woodmart_product_label_output', array( $this, 'added_sale_label' ) );

		if ( woodmart_get_opt( 'catalog_mode' ) || ! is_user_logged_in() && woodmart_get_opt( 'login_prices' ) ) {
			return;
		}

		add_action( 'woodmart_add_loop_btn', 'woocommerce_template_loop_add_to_cart', 10 );
	}

	/**
	 * Get heading content.
	 *
	 * @param string $title Title.
	 * @param bool   $is_builder Is builder.
	 *
	 * @return void
	 */
	protected function get_heading( $title = '', $is_builder = false ) {
		$class = '';

		if ( $is_builder ) {
			$class .= ' element-title';
		} else {
			$class .= ' slider-title';
		}

		?>
		<h4 class="title<?php echo esc_attr( $class ); ?>">
			<?php if ( $title ) : ?>
				<?php echo esc_html( $title ); ?>
			<?php else : ?>
				<?php esc_html_e( 'Frequently bought together', 'woodmart' ); ?>
			<?php endif; ?>
		</h4>
		<?php
	}

	/**
	 * Get form content.
	 *
	 * @param array $settings Settings.
	 *
	 * @return void
	 */
	public function get_form_content( $settings ) {
		global $product;

		$atts = array(
			'query_post_type'         => array( 'product', 'product_variation' ),
			'post_type'               => 'ids',
			'include'                 => array_column( $this->wfbt_products, 'id' ),
			'layout'                  => 'carousel',
			'orderby'                 => 'post__in',
			'slides_per_view'         => $settings['slides_per_view'],
			'slides_per_view_tablet'  => $settings['slides_per_view_tablet'],
			'slides_per_view_mobile'  => $settings['slides_per_view_mobile'],
			'hide_pagination_control' => $settings['hide_pagination_control'],
			'hide_prev_next_buttons'  => $settings['hide_prev_next_buttons'],
			'spacing'                 => 30,
		);

		array_unshift( $atts['include'], $product->get_id() );

		?>
			<div class="wd-fbt wd-design-side wd-fbt-bundle-<?php echo esc_attr( $this->bundle_id ); ?>">
				<?php
				if ( 'elementor' === woodmart_get_current_page_builder() ) {
					echo woodmart_elementor_products_template( $atts ); //phpcs:ignore
				} else {
					$atts['include'] = implode( ',', $atts['include'] );

					echo woodmart_shortcode_products( $atts ); //phpcs:ignore
				}

				$this->get_products_purchase();
				?>
			</div>
		<?php
	}

	/**
	 * Get purchase content.
	 *
	 * @return void
	 */
	protected function get_products_purchase() {
		global $product;

		if ( ! $product ) {
			$product = wc_get_product( $this->main_product_id );
		}

		$fbt_count     = count( $this->subtotal_products_price );
		$fbt_products  = array_column( $this->wfbt_products, 'id' );
		$show_checkbox = get_post_meta( $this->bundle_id, '_woodmart_show_checkbox', true );
		$classes       = '';

		array_unshift( $fbt_products, $product->get_id() );

		if ( ! empty( $show_checkbox ) ) {
			$classes .= ' xts-checkbox-on';
		}

		?>
		<form class="wd-fbt-form<?php echo esc_attr( $classes ); ?>" method="post">
			<input type="hidden" name="wd-fbt-bundle-id" value="<?php echo esc_attr( $this->bundle_id ); ?>">
			<input type="hidden" name="wd-fbt-main-product" value="<?php echo esc_attr( $product->get_id() ); ?>">

			<div class="wd-fbt-products">
				<?php foreach ( $fbt_products as $id ) : ?>
					<?php
					$current_product = wc_get_product( $id );
					$product_id      = $current_product->get_id();
					$variation       = '';

					if ( 'variable' === $current_product->get_type() && $current_product->get_children() ) {
						$variation = wc_get_product( $this->get_default_variation_product_id( $current_product ) );
					}

					?>
					<div class="wd-fbt-product wd-product-<?php echo esc_attr( $product_id ); ?>" data-id="<?php echo esc_attr( $product_id ); ?>">
						<div class="wd-fbt-product-heading" for="wd-fbt-product-<?php echo esc_attr( $product_id ); ?>">
							<?php if ( ! empty( $show_checkbox ) ) : ?>
								<input type="checkbox" id="wd-fbt-product-<?php echo esc_attr( $product_id ); ?>" data-id="<?php echo esc_attr( $product_id ); ?>" checked<?php echo esc_attr( $product_id === $product->get_id() ? ' disabled' : '' ); ?>>
							<?php endif; ?>
							<label for="wd-fbt-product-<?php echo esc_attr( $product_id ); ?>">
								<span class="wd-entities-title title">
									<?php echo esc_html( $current_product->get_name() ); ?>
								</span>
							</label>
							<span class="price">
								<?php if ( $variation ) : ?>
									<?php echo wp_kses( $variation->get_price_html(), true ); ?>
								<?php else : ?>
									<?php echo wp_kses( $current_product->get_price_html(), true ); ?>
								<?php endif; ?>
							</span>
						</div>
						<?php if ( $variation ) : ?>
							<div class="wd-fbt-product-variation">
								<select>
									<?php foreach ( $current_product->get_children() as $variation_id ) : ?>
										<?php $variation_product = wc_get_product( $variation_id ); ?>
										<option value="<?php echo esc_attr( $variation_product->get_id() ); ?>"<?php echo esc_attr( $variation->get_id() === $variation_product->get_id() ? ' selected="selected"' : '' ); ?>>
											<?php echo esc_html( wc_get_formatted_variation( $variation_product, true, false, false ) ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="wd-fbt-purchase">
				<div class="price">
					<?php echo wp_kses( $this->get_subtotal_bundle_price(), true ); ?>
				</div>
				<div class="wd-fbt-desc">
					<?php
					echo wp_kses(
						sprintf( _n( 'For %s item', 'For %s items', $fbt_count, 'woodmart' ), $fbt_count ),
						true
					);
					?>
				</div>
				<button class="wd-fbt-purchase-btn single_add_to_cart_button button" type="submit">
					<?php esc_html_e( 'Add to cart', 'woodmart' ); ?>
				</button>
			</div>
			<div class="wd-loader-overlay wd-fill"></div>
		</form>
		<?php
	}

	/**
	 * Get subtotal products price in bundle.
	 *
	 * @return string
	 */
	private function get_subtotal_bundle_price() {
		global $product;

		if ( ! $product ) {
			$product = wc_get_product( $this->main_product_id );
		}

		$old_price = array_sum( array_column( $this->subtotal_products_price, 'old' ) );
		$new_price = array_sum( array_column( $this->subtotal_products_price, 'new' ) );

		if ( $old_price <= $new_price ) {
			return wc_price( $new_price ) . $product->get_price_suffix();
		}

		return wc_format_sale_price( $old_price, $new_price ) . $product->get_price_suffix();
	}

	/**
	 * Update product price.
	 *
	 * @param string $price Product price HTML.
	 * @param object $product Product data.
	 *
	 * @return string
	 */
	public function update_product_price( $price, $product ) {
		$product_id = $product->get_ID();

		if ( 'variation' === $product->get_type() && ! isset( $this->wfbt_products[ $product_id ] ) ) {
			$product_parent = wc_get_product( $product->get_parent_id() );
			$product_id     = $product_parent->get_ID();
		}

		$discount = $this->get_discount_product_bundle( $product_id );

		if ( 'variable' === $product->get_type() ) {
			$old_price = (float) $product->get_variation_regular_price();
		} else {
			$old_price = (float) $product->get_regular_price();
		}

		if ( ( ! $discount || 100 <= $discount ) && $product->is_on_sale() ) {
			$old_price = (float) $product->get_price();
		}

		$this->subtotal_products_price[ $product_id ]['old'] = $old_price;

		if ( ! $discount || 100 <= $discount ) {
			$this->subtotal_products_price[ $product_id ]['new'] = $old_price;

			return $price;
		}

		$new_price = $old_price - ( ( $old_price / 100 ) * $discount );

		$this->subtotal_products_price[ $product_id ]['new'] = $new_price;

		if ( 'variable' === $product->get_type() ) {
			$prices = $product->get_variation_prices( true );

			if ( empty( $prices['price'] ) ) {
				return $price;
			} else {
				$min_reg_price = (float) current( $prices['regular_price'] );
				$max_reg_price = (float) end( $prices['regular_price'] );

				$min_reg_price = $min_reg_price - ( ( $min_reg_price / 100 ) * $discount );
				$max_reg_price = $max_reg_price - ( ( $max_reg_price / 100 ) * $discount );

				if ( $min_reg_price !== $max_reg_price ) {
					$price = wc_format_price_range( $min_reg_price, $max_reg_price );
				} else {
					$price = wc_format_sale_price( wc_price( end( $prices['regular_price'] ) ), wc_price( $min_reg_price ) );
				}

				return $price . $product->get_price_suffix();
			}
		}

		return wc_format_sale_price( $old_price, $new_price ) . $product->get_price_suffix();
	}

	/**
	 * Added product sale label.
	 *
	 * @param array $content Labels.
	 *
	 * @return array
	 */
	public function added_sale_label( $content ) {
		global $product;

		$product_id = $product->get_ID();

		if ( 'variation' === $product->get_type() && ! isset( $this->wfbt_products[ $product_id ] ) ) {
			$product_parent = wc_get_product( $product->get_parent_id() );
			$product_id     = $product_parent->get_ID();
		}

		$discount = (int) $this->get_discount_product_bundle( $product_id );

		if ( ! $discount || 100 <= $discount ) {
			return $content;
		}

		$label = '<span class="onsale product-label wd-fbt-sale-label">' . sprintf( _x( '-%d%%', 'sale percentage', 'woodmart' ), $discount ) . '</span>';

		array_unshift( $content, $label );

		return $content;
	}

	/**
	 * Get discount product price.
	 *
	 * @param integer $product_id Product ID.
	 *
	 * @return false|float
	 */
	private function get_discount_product_bundle( $product_id ) {
		if ( $this->main_product_id === $product_id ) {
			$discount = (float) get_post_meta( $this->bundle_id, '_woodmart_main_products_discount', true );
		} elseif ( isset( $this->wfbt_products[ $product_id ] ) ) {
			$discount = (float) $this->wfbt_products[ $product_id ]['discount'];
		} else {
			return false;
		}

		return $discount;
	}

	/**
	 * Get default variation product id.
	 *
	 * @param object $product Product data.
	 *
	 * @return false|mixed
	 */
	private function get_default_variation_product_id( $product ) {
		if ( $product->get_default_attributes() ) {
			$is_default_variation = false;

			foreach ( $product->get_available_variations() as $variation_values ) {
				foreach ( $variation_values['attributes'] as $key => $attribute_value ) {
					$attribute_name = str_replace( 'attribute_', '', $key );
					$default_value  = $product->get_variation_default_attribute( $attribute_name );

					if ( $default_value === $attribute_value ) {
						$is_default_variation = true;
					} else {
						$is_default_variation = false;
					}
				}

				if ( $is_default_variation ) {
					return $variation_values['variation_id'];
				}
			}
		}

		return current( $product->get_children() );
	}
}

Frontend::get_instance();
