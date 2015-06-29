<?php

/**
 * @file
 * Contains \Drupal\siteinfo\SiteInformationController.
 */

namespace Drupal\siteinfo;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\NodeType;
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
 
    // Access site variables.
   
		//$site_name = variable_get('site_name');
    /*$default_theme = variable_get('theme_default');
    $admin_theme = variable_get('admin_theme');
    $file_temporary_path = variable_get('file_temporary_path');
    $install_time = variable_get('install_time');
    $cron_last_run = variable_get('cron_last');*/
    //$clean_url = \Drupal::config('system.module')->get('instal_time');
		$site_name = \Drupal::config('system.site')->get('name');
		$default_theme = \Drupal::config('system.theme')->get('default');
		$admin_theme = \Drupal::config('system.theme')->get('admin');
		$front_page = \Drupal::config('system.site')->get('front');
		$language_code = \Drupal::config('system.site')->get('default_langcode');
		$file_temporary_path =  \Drupal::config('system.file')->get('temporary');
		$install_date = \Drupal::config('system.module')->get('instal_time');
		$cron_run = \Drupal::config('system.cron')->get('threshold');
		$db_driver = \Drupal::database()->driver();
		$db_name = \Drupal::config('node.type')->get('name');
    // Format date.
    //$install_date = format_date($install_time, 'dateonly');
    //$cron_run = format_date($cron_last_run, 'dateonly');
    $cln_url = isset($clean_url) ? "Yes" : "No";
    // Get list of enabled module.
    $query = db_select('users_field_data', 'u');
    $query->fields('u', array('name'));
    $query->condition('status', 1, '=');
    // Filter by active user.
    $user_name = $query->execute()->fetchAllKeyed(0, 0);
    $count_user = count($user_name);

    // Get list of content type.
    $cont_type = node_type_get_types();
    $count_cont_type = count($cont_type);

    // Get list of enabled module.
    $mod_list = 10;
		// $mod_list = module_list();
    $count_mod = count($mod_list);
    $drupal_version = \Drupal::VERSION;

    $header = array(array('data' => "Site Details", 'colspan' => 2));

    $rows['site_name'] = array(t("Site name"), $site_name);
    $rows['drupal_version'] = array(t("Drupal version"), $drupal_version);
    $rows['language_code'] = array(t("Default language code"), $language_code);
		$rows['front_page'] = array(t("Front Page"), $front_page);
		$rows['install_date'] = array(t("Install date"), $install_date);
    $rows['cron_run'] = array(t("Last cron run"), $cron_run);
    $rows['cln_url'] = array(t("Clean url"), $cln_url);
    $rows['file_temporary_path'] = array(t("File temporary path"), $file_temporary_path);
    $rows['php_version'] = array(t("Php version"), $php_version);
    $rows['db_driver'] = array(t("Database driver"), $db_driver);
    $rows['db_name'] = array(t("Database name"), $db_name);
    $rows['default_theme'] = array(t("Default theme"), $default_theme);
    $rows['admin_theme'] = array(t("Admin theme"), $admin_theme);
    $rows['count_role'] = array(t("Roles"), $count_role);
    $rows['count_user'] = array(t("Active users"), $count_user);
    $rows['count_cont_type'] = array(t("Content type"), $count_cont_type);
    $rows['count_mod'] = array(t("Enables module"), $count_mod);
/*
    $output = theme('table', array('header' => $header, 'rows' => $rows));
*/
    $lim = 0;
		//echo "<pre>"; print_r($cont_type); 

    // Iteration for content-type.
    foreach ($cont_type as $key => $value) {
		  //$data1 = NodeType::load($value->bundle());
			//echo "<pre>"; print_r($key); 
			
			//echo "ddd $key";
		  //$da =  \Drupal::config('node.schema.article')->get('name');
      $name = NodeType::load->getName($value->name);
			//$row_col[$lim][0] = t("@cont_name", array('@cont_name' => $value->name));
			/*
      $query = new EntityFieldQuery();
      // Grab nodes.
      $query->entityCondition('entity_type', 'node')
      // Filter by content type.
      ->entityCondition('bundle', $key)
      // Filter by published.
      ->propertyCondition('status', 1)
      // Count.
      ->count();
      $result = $query->execute();
      $row_col[$lim][1] = t("@cout_node", array('@cout_node' => $result));
      $lim++;*/
    }
		//exit;
/*
    $lim = 0;
    // Iteration for roles.
    foreach ($roles as $key) {
      if (!isset($row_col[$lim][0])) {
        $row_col[$lim][1] = "";
      }
      $row_col[$lim][2] = t("@role_name", array('@role_name' => $key));
      $lim++;
    }

    $lim = 0;
    // Iteration for modules.
    foreach ($mod_list as $key) {
      if (!isset($row_col[$lim][0])) {
        $row_col[$lim][0] = "";
      }
      if (!isset($row_col[$lim][1])) {
        $row_col[$lim][1] = "";
      }
      if (!isset($row_col[$lim][2])) {
        $row_col[$lim][2] = "";
      }
      $row_col[$lim][3] = t("@mod_name", array('@mod_name' => $key));
      $lim++;
    }

    $lim = 0;
    // Iteration for users.
    foreach ($user_name as $key => $value) {
      if (!isset($row_col[$lim][0])) {
        $row_col[$lim][0] = "";
      }
      if (!isset($row_col[$lim][1])) {
        $row_col[$lim][1] = "";
      }
      if (!isset($row_col[$lim][2])) {
        $row_col[$lim][2] = "";
      }
      if (!isset($row_col[$lim][3])) {
        $row_col[$lim][3] = "";
      }

      $row_col[$lim][4] = t("@usr_name", array('@usr_name' => $value));
      $lim++;
    }

    $lim = 0;
    foreach ($row_col as $key => $value) {
      if (!isset($row_col[$lim][4])) {
        $row_col[$lim][4] = "";
      }
      $lim++;
    }
  /*  
    $per_page = 10;
  // Initialize the pager.
  $current_page = pager_default_initialize(count($row_col), $per_page);
  // Split list into page sized chunks.
  $chunks = array_chunk($row_col, $per_page, TRUE);
  $header = array(t('Content Type'), t('Node'), t('Roles'), t('Modules'),
    t('Active users'));
  $output .= theme('table', array('header' => $header, 'rows' => $chunks[$current_page]));
  // Show the pager.
  $output .= theme('pager', array('quantity', count($row_col)));
  return $output;
  */
    $content['table'] = array(
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => t('No entries available.'),
    );

		
	/*$table = array(
  '#type' => 'table',
  '#header' => $header,
  '#rows' => $rows,
  '#attributes' => array(
    'id' => 'my-module-table',
    ),
  );
  $markup = drupal_render($table);
  // Pager is not an element type, use #theme directly.
  $pager = array('#theme' => 'pager');
  $markup .= drupal_render($pager);*/
  return $content;
  }
}
