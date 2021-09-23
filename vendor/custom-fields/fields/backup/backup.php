<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: backup
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'KPF_Field_backup' ) ) {
  class KPF_Field_backup extends KPF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $unique = $this->unique;
      $nonce  = wp_create_nonce( 'kpf_backup_nonce' );
      $export = add_query_arg( array( 'action' => 'kpf-export', 'unique' => $unique, 'nonce' => $nonce ), admin_url( 'admin-ajax.php' ) );

      echo $this->field_before();

      echo '<textarea name="kpf_import_data" class="kpf-import-data"></textarea>';
      echo '<button type="submit" class="button button-primary kpf-confirm kpf-import" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Import', 'kpf' ) .'</button>';
      echo '<hr />';
      echo '<textarea readonly="readonly" class="kpf-export-data">'. esc_attr( json_encode( get_option( $unique ) ) ) .'</textarea>';
      echo '<a href="'. esc_url( $export ) .'" class="button button-primary kpf-export" target="_blank">'. esc_html__( 'Export & Download', 'kpf' ) .'</a>';
      echo '<hr />';
      echo '<button type="submit" name="kpf_transient[reset]" value="reset" class="button kpf-warning-primary kpf-confirm kpf-reset" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Reset', 'kpf' ) .'</button>';

      echo $this->field_after();

    }

  }
}
