<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: icon
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'KPTCP_Field_icon' ) ) {
  class KPTCP_Field_icon extends KPTCP_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'button_title' => esc_html__( 'Add Icon', 'kptcp' ),
        'remove_title' => esc_html__( 'Remove Icon', 'kptcp' ),
      ) );

      echo $this->field_before();

      $nonce  = wp_create_nonce( 'kptcp_icon_nonce' );
      $hidden = ( empty( $this->value ) ) ? ' hidden' : '';

      echo '<div class="kptcp-icon-select">';
      echo '<span class="kptcp-icon-preview'. esc_attr( $hidden ) .'"><i class="'. esc_attr( $this->value ) .'"></i></span>';
      echo '<a href="#" class="button button-primary kptcp-icon-add" data-nonce="'. esc_attr( $nonce ) .'">'. $args['button_title'] .'</a>';
      echo '<a href="#" class="button kptcp-warning-primary kptcp-icon-remove'. esc_attr( $hidden ) .'">'. $args['remove_title'] .'</a>';
      echo '<input type="hidden" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="kptcp-icon-value"'. $this->field_attributes() .' />';
      echo '</div>';

      echo $this->field_after();

    }

    public function enqueue() {
      add_action( 'admin_footer', array( 'KPTCP_Field_icon', 'add_footer_modal_icon' ) );
      add_action( 'customize_controls_print_footer_scripts', array( 'KPTCP_Field_icon', 'add_footer_modal_icon' ) );
    }

    public static function add_footer_modal_icon() {
    ?>
      <div id="kptcp-modal-icon" class="kptcp-modal kptcp-modal-icon hidden">
        <div class="kptcp-modal-table">
          <div class="kptcp-modal-table-cell">
            <div class="kptcp-modal-overlay"></div>
            <div class="kptcp-modal-inner">
              <div class="kptcp-modal-title">
                <?php esc_html_e( 'Add Icon', 'kptcp' ); ?>
                <div class="kptcp-modal-close kptcp-icon-close"></div>
              </div>
              <div class="kptcp-modal-header">
                <input type="text" placeholder="<?php esc_html_e( 'Search...', 'kptcp' ); ?>" class="kptcp-icon-search" />
              </div>
              <div class="kptcp-modal-content">
                <div class="kptcp-modal-loading"><div class="kptcp-loading"></div></div>
                <div class="kptcp-modal-load"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

  }
}
