<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.goblincompute.com/
 * @since      1.0.0
 *
 * @package    Goblin
 * @subpackage Goblin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Goblin
 * @subpackage Goblin/includes
 * @author     Goblin <bsousaf@gmail.com>
 */
class Goblin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
			set_transient( 'vpt_flush', 1, 60 );
	}

}
