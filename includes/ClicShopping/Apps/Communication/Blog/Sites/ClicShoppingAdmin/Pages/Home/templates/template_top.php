<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Blog = Registry::get('Blog');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  if ($CLICSHOPPING_MessageStack->exists('main')) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }

  // check if the catalog image directory exists
  if (is_dir($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages())) {
    if (!FileSystem::isWritable($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages())) $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_catalog_image_directory_not_writeable'), 'error');
  } else {
    $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_catalog_image_directory_does_not_exist'), 'error');
  }
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/products_options.gif', $CLICSHOPPING_Blog->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Blog->getDef('heading_title'); ?></span>
          <span
            class="col-md-7 text-end"><?php echo HTML::button($CLICSHOPPING_Blog->getDef('button_back'), null, $CLICSHOPPING_Blog->link(null, 'A&Communication\Blog'), 'primary'); ?>
        </div>
      </div>
    </div>
  </div>

