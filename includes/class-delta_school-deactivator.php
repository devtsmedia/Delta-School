<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    delta_school
 * @subpackage delta_school/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    delta_school
 * @subpackage delta_school/includes
 * @author     Your Name <email@example.com>
 */
class delta_school_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;

		// $tables_to_remove = array(
		// 	$wpdb->prefix . 'year_groups',
		// 	$wpdb->prefix . 'schools',
		// 	$wpdb->prefix . 'methods',
		// 	$wpdb->prefix . 'products',
		// 	$wpdb->prefix . 'school_configurations'
		// );

		// foreach ($tables_to_remove as $table) {
		// 	$wpdb->query("DROP TABLE IF EXISTS $table");
		// }

	}

}
