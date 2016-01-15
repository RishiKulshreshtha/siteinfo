<?php

/**
 * @file
 * Contains \Drupal\siteinfo\Plugin\Block\SiteInformationBlock.
 */

namespace Drupal\siteinfo\Plugin\Block;

use Drupal\block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Annotation\Translation;
use Drupal\siteinfo\SiteInformationController;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Site Information' block.
 *
 * @Block(
 *   id = "siteinfo_block",
 *   admin_label = @Translation("Site Information")
 * )
 */
class SiteInformationBlock extends BlockBase {
  
	/**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access site information');
  }
	
  /**
   * {@inheritdoc}
   */
  public function build() {
    $siteinfo = \Drupal\siteinfo\Controller\SiteInformationController::siteInformation();
		$content = drupal_render($siteinfo);
		return array(
      '#type' => 'markup',
      '#markup' => $content,
    );
  }
}
