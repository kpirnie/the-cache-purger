<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: backup
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'KPTCP_Field_backup' ) ) {
  class KPTCP_Field_backup extends KPTCP_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $unique = $this->unique;
      $nonce  = wp_create_nonce( 'kptcp_backup_nonce' );
      $export = add_query_arg( array( 'action' => 'kptcp-export', 'unique' => $unique, 'nonce' => $nonce ), admin_url( 'admin-ajax.php' ) );

      echo $this->field_before();

      echo '<textarea name="kptcp_import_data" class="kptcp-import-data"></textarea>';
      echo '<button type="submit" class="button button-primary kptcp-confirm kptcp-import" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Import', 'kptcp' ) .'</button>';
      echo '<hr />';
      echo '<textarea readonly="readonly" class="kptcp-export-data">'. esc_attr( json_encode( get_option( $unique ) ) ) .'</textarea>';
      echo '<a href="'. esc_url( $export ) .'" class="button button-primary kptcp-export" target="_blank">'. esc_html__( 'Export & Download', 'kptcp' ) .'</a>';
      echo '<hr />';
      echo '<button type="submit" name="kptcp_transient[reset]" value="reset" class="button kptcp-warning-primary kptcp-confirm kptcp-reset" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Reset', 'kptcp' ) .'</button>';

      echo $this->field_after();

    }

  }
}
