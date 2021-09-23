<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: icon
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'KPF_Field_icon' ) ) {
  class KPF_Field_icon extends KPF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'button_title' => esc_html__( 'Add Icon', 'kpf' ),
        'remove_title' => esc_html__( 'Remove Icon', 'kpf' ),
      ) );

      echo $this->field_before();

      $nonce  = wp_create_nonce( 'kpf_icon_nonce' );
      $hidden = ( empty( $this->value ) ) ? ' hidden' : '';

      echo '<div class="kpf-icon-select">';
      echo '<span class="kpf-icon-preview'. esc_attr( $hidden ) .'"><i class="'. esc_attr( $this->value ) .'"></i></span>';
      echo '<a href="#" class="button button-primary kpf-icon-add" data-nonce="'. esc_attr( $nonce ) .'">'. $args['button_title'] .'</a>';
      echo '<a href="#" class="button kpf-warning-primary kpf-icon-remove'. esc_attr( $hidden ) .'">'. $args['remove_title'] .'</a>';
      echo '<input type="hidden" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="kpf-icon-value"'. $this->field_attributes() .' />';
      echo '</div>';

      echo $this->field_after();

    }

    public function enqueue() {
      add_action( 'admin_footer', array( 'KPF_Field_icon', 'add_footer_modal_icon' ) );
      add_action( 'customize_controls_print_footer_scripts', array( 'KPF_Field_icon', 'add_footer_modal_icon' ) );
    }

    public static function add_footer_modal_icon() {
    ?>
      <div id="kpf-modal-icon" class="kpf-modal kpf-modal-icon hidden">
        <div class="kpf-modal-table">
          <div class="kpf-modal-table-cell">
            <div class="kpf-modal-overlay"></div>
            <div class="kpf-modal-inner">
              <div class="kpf-modal-title">
                <?php esc_html_e( 'Add Icon', 'kpf' ); ?>
                <div class="kpf-modal-close kpf-icon-close"></div>
              </div>
              <div class="kpf-modal-header">
                <input type="text" placeholder="<?php esc_html_e( 'Search...', 'kpf' ); ?>" class="kpf-icon-search" />
              </div>
              <div class="kpf-modal-content">
                <div class="kpf-modal-loading"><div class="kpf-loading"></div></div>
                <div class="kpf-modal-load"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

  }
}
