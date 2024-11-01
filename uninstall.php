<?php

// Deny direct access
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Remove main registered option when plugin is deleted
delete_option('wmpfc_fields');
delete_option('wmpfc_installed_date');