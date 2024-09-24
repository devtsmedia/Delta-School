<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    delta_school
 * @subpackage delta_school/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    delta_school
 * @subpackage delta_school/includes
 * @author     Your Name <email@example.com>
 */
class delta_school_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$year_groups_table_name = $wpdb->prefix . 'year_groups';
		$schools_table_name = $wpdb->prefix . 'schools';
		$methods_table_name = $wpdb->prefix . 'methods';
		$products_table_name = $wpdb->prefix . 'products';
		$school_configuration_table_name = $wpdb->prefix . 'school_configurations';

		$charset_collate = $wpdb->get_charset_collate();

		$year_groups = "CREATE TABLE $year_groups_table_name (
		id INT NOT NULL AUTO_INCREMENT,
		name VARCHAR(100) NOT NULL,
		sort_order INT NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

		$schools = "CREATE TABLE IF NOT EXISTS $schools_table_name (
			id INT NOT NULL AUTO_INCREMENT,
			brin_number VARCHAR(255) NOT NULL,
			basispoort_id INT UNIQUE,
			name VARCHAR(255) NOT NULL,
			address VARCHAR(255),
			postal_code VARCHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci,
			city VARCHAR(255),
			municipal_number VARCHAR(255),
			municipal_name VARCHAR(255),
			name_contact VARCHAR(255),
			invoice_email_address VARCHAR(255),
			denomination VARCHAR(255),
			phone VARCHAR(255) DEFAULT '0' COMMENT 'Add a 0 before telefoonnummer',
			website VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_ci,
			PRIMARY KEY (id),
			UNIQUE KEY brin_number_unique (brin_number),
			UNIQUE KEY basispoort_id_unique (basispoort_id)
		) $charset_collate;";

		$methods = "CREATE TABLE IF NOT EXISTS $methods_table_name (
			id INT AUTO_INCREMENT PRIMARY KEY,
			-- product_id INT NOT NULL,
			code VARCHAR(255) NOT NULL UNIQUE,
			name VARCHAR(255) NOT NULL,
			url VARCHAR(255) NOT NULL,
			icon_url VARCHAR(255) NOT NULL
		)$charset_collate;";

		$products = "CREATE TABLE IF NOT EXISTS $products_table_name (
			id INT AUTO_INCREMENT PRIMARY KEY,
			code VARCHAR(255) NOT NULL UNIQUE,
			name VARCHAR(255) NOT NULL,
			is_physical_product BOOLEAN NOT NULL,
			Price INT NOT NULL,
			vat_percentage INT NOT NULL,
			url VARCHAR(255) NOT NULL,
			icon_url VARCHAR(255) NOT NULL
		)$charset_collate;";

		$school_configuration = "CREATE TABLE $school_configuration_table_name (
			id INT AUTO_INCREMENT PRIMARY KEY,
			school_id INT NOT NULL,
			school_year_id INT NOT NULL,
			product_id INT NOT NULL,
			books BOOLEAN NOT NULL,
			num_students INT NOT NULL,
			comments TEXT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		)$charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($year_groups);
		dbDelta($schools);
		dbDelta($methods);
		dbDelta($products);
		dbDelta($school_configuration);
	}

}
