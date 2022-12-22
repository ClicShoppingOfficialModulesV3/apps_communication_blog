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


  namespace ClicShopping\Apps\Communication\Blog\Sites\ClicShoppingAdmin\Pages\Home\Actions\BlogCategories;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');

      if ($_GET['flag'] == 0 || $_GET['flag'] == 1) {
        if (isset($_GET['pID'])) {
          BlogAdmin::setBlogContentStatus($_GET['pID'], $_GET['flag']);
        }

        Cache::clear('blog_tree');
        Cache::clear('boxe_blog');
      }

      $CLICSHOPPING_Blog->redirect('BlogCategories&cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID']);
    }
  }