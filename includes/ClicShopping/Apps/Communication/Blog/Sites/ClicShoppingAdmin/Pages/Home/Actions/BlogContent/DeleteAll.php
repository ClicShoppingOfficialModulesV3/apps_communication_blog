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

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  class DeleteAll extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_GET['cPath'])) $cPath = HTML::sanitize($_POST['cPath']);

      if (isset($_POST['selected'])) {
        foreach ($_POST['selected'] as $id) {
          $Qcheck = $CLICSHOPPING_Blog->db->prepare('select count(*) as total
                                                     from :table_blog_content_to_categories
                                                     where blog_content_id = :blog_content_id
                                                    ');
          $Qcheck->bindInt(':blog_content_id', (int)$id);

          $Qcheck->execute();

          if ($Qcheck->rowCount() > 0) {
            BlogAdmin::getRemoveBlogContent($id);
          }

          $CLICSHOPPING_Hooks->call('BlogContent', 'DeleteAll');
        } // end for each
      } // end post

      Cache::clear('blog_tree');

      $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $cPath);
    }
  }