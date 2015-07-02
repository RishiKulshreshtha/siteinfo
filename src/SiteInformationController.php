<?php

/**
 * @file
 * Contains \Drupal\siteinfo\SiteInformationController.
 */

namespace Drupal\siteinfo;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for Site Information.
 */
class SiteInformationController extends ControllerBase {

  /**
   * Render a list of entries in the database.
   */
  public function siteInformation() {
    $content = array();
    global $databases;
    $db_name = $databases['default']['default']['database'];
    $db_driver = $databases['default']['default']['driver'];
    $php_version = phpversion();
    $roles = user_roles(TRUE);
    $count_role = count($roles);
		$site_name = \Drupal::config('system.site')->get('name');
		$default_theme = \Drupal::config('system.theme')->get('default');
		$admin_theme = \Drupal::config('system.theme')->get('admin');
		$front_page = \Drupal::config('system.site')->get('page.front');
		$country = \Drupal::config('system.date')->get('country.default');
		$time_zone = \Drupal::config('system.date')->get('timezone.default');
		$language_code = \Drupal::config('system.site')->get('default_langcode');
		$file_system =  \Drupal::config('system.file')->get('default_scheme');
		$file_path =  \Drupal::config('system.file')->get('path.temporary');
		$install_date = \Drupal::service('date.formatter')->formatTimeDiffSince($installing_date);
		$cron_last = \Drupal::state()->get('system.cron_last');
		$cron_run = \Drupal::service('date.formatter')->formatTimeDiffSince($cron_last);
		$db_driver = \Drupal::database()->driver();
		$database_con = Database::getConnectionInfo();
		$db_name = $database_con['default']['database'];
		
    // Get list of enabled module.
    $query = db_select('users_field_data', 'u');
    $query->fields('u', array('name'));
    $query->condition('status', 1, '=');
    // Filter by active user.
    $user_name = $query->execute()->fetchAllKeyed(0, 0);
    $count_user = count($user_name);
    // Get list of content type.
    $cont_type = node_type_get_types();
    // Get list of enabled modules.
    $count_cont_type = count($cont_type);
		$mod_list = \Drupal::moduleHandler()->getModuleList();
    $count_mod = count($mod_list);
    $drupal_version = \Drupal::VERSION;
    // Set header.
    $header = array(array('data' => "Site Details", 'colspan' => 2));
    // Set rows.
    $rows['site_name'] = array(t("Site name"), $site_name);
    $rows['drupal_version'] = array(t("Drupal version"), $drupal_version);
    $rows['language_code'] = array(t("Default language code"), $language_code);
		$rows['country'] = array(t("Country"), $country);
		$rows['time_zone'] = array(t("Time Zone"), $time_zone);
		$rows['front_page'] = array(t("Front Page"), $front_page);
    $rows['cron_run'] = array(t("Last cron run"), $cron_run);
    $rows['file_system'] = array(t("File system"), $file_system);
		$rows['file_path'] = array(t("File path"), $file_path);
    $rows['php_version'] = array(t("Php version"), $php_version);
    $rows['db_driver'] = array(t("Database driver"), $db_driver);
    $rows['db_name'] = array(t("Database name"), $db_name);
    $rows['default_theme'] = array(t("Default theme"), $default_theme);
    $rows['admin_theme'] = array(t("Admin theme"), $admin_theme);
    $rows['count_role'] = array(t("Roles"), $count_role);
    $rows['count_user'] = array(t("Active users"), $count_user);
    $rows['count_cont_type'] = array(t("Content type"), $count_cont_type);
    $rows['count_mod'] = array(t("Enables module"), $count_mod);
		// Display in table format.
		$content['table_detail'] = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No entries available.'),
    );

    $lim = 0;
    // Iteration for content-type.
    foreach ($cont_type as $key => $value) {
      $name = $value->get('name');
			$row_col[$lim][0] = t("@cont_name", array('@cont_name' => $name));
      $query = \Drupal::entityQuery('node')
      // Filter by content type.
      ->condition('type', $key)
      // Filter by published.
      ->condition('status', 1)
      // Count.
      ->count();
      $result = $query->execute();
      $row_col[$lim][1] = t("@cout_node", array('@cout_node' => $result));
      $lim++;
    }
    $lim = 0;
    // Iteration for roles.
    foreach ($roles as $key => $value) {
      $role_name = $value->get('label');
			if (!isset($row_col[$lim][0])) {
        $row_col[$lim][0] = NULL;
      }
			if (!isset($row_col[$lim][1])) {
        $row_col[$lim][1] = NULL;
      }
			// Count number of user for specific role.
			$num_users = db_query("SELECT COUNT(*) FROM user__roles WHERE roles_target_id = :target_id", array(':target_id' => 'administrator'));
      $select = db_select('user__roles', 'usr');
      $select->fields('usr', array('entity_id'));
      $select->condition('roles_target_id', $key);
      $result = $select->execute()->fetchAllKeyed(0,0);
      $number_of_usr = count($result);
			// Store in rows.
      $row_col[$lim][2] = t("@role_name", array('@role_name' => $role_name));
			$row_col[$lim][3] = t("@usr_count", array('@usr_count' => $number_of_usr));
      $lim++;
    }
    $lim = 0;
    // Iteration for modules.
    foreach ($mod_list as $key => $value) {
      if (!isset($row_col[$lim][0])) {
        $row_col[$lim][0] = NULL;
      }
      if (!isset($row_col[$lim][1])) {
        $row_col[$lim][1] = NULL;
      }
      if (!isset($row_col[$lim][2])) {
        $row_col[$lim][2] = NULL;
      }
			if (!isset($row_col[$lim][3])) {
        $row_col[$lim][3] = NULL;
      }
      $row_col[$lim][4] = t("@mod_name", array('@mod_name' => $key));
      $lim++;
    }
    $lim = 0;
    foreach ($row_col as $key => $value) {
      if (!isset($row_col[$lim][4])) {
        $row_col[$lim][4] = NULL;
      }
      $lim++;
    }
    $header = array(t('Content Type'), t('Node'), t('Roles'), t('users'),
		  t('Modules'));
    $content['table_brief'] = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $row_col,
      '#empty' => t('No entries available.'),
    );
    return $content;
  }
}
