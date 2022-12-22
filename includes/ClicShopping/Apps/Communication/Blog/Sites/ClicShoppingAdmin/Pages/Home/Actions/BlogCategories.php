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

  namespace ClicShopping\Apps\Communication\Blog\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Categories\Classes\ClicShoppingAdmin\CategoriesAdmin;

  class BlogCategories extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');

      $this->page->setFile('blog_categories.php');
      $this->page->data['action'] = 'BlogCategories';

      $CLICSHOPPING_Blog->loadDefinitions('Sites/ClicShoppingAdmin/BlogCategories');
    }
  }