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


  namespace ClicShopping\Apps\Communication\Blog\Sites\ClicShoppingAdmin\Pages\Home\Actions\BlogContent;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');

      if (isset($_GET['cPath'])) $cPath = HTML::sanitize($_GET['cPath']);
      if (isset($_GET['pID'])) $pID = HTML::sanitize($_GET['pID']);

      if (isset($_GET['flag'])) {
        if (isset($_GET['pID'])) {
          BlogAdmin::setBlogContentStatus($_GET['pID'], $_GET['flag']);
        }

        Cache::clear('blog_tree');
      }

      $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $cPath . '&pID=' . $pID);
    }
  }