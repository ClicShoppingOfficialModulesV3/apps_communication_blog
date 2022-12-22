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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_GET['cPath'])) $cPath = HTML::sanitize($_POST['cPath']);

      if (isset($_POST['blog_content_id']) && isset($_POST['product_categories']) && is_array($_POST['product_categories'])) {
        $blog_content_id = HTML::sanitize($_POST['blog_content_id']);

        $blog_content_categories = $_POST['product_categories'];

        for ($i = 0, $n = count($blog_content_categories); $i < $n; $i++) {

// delete product of categorie
          $Qdelete = $CLICSHOPPING_Blog->db->prepare('delete
                                                      from :table_blog_content_to_categories
                                                      where blog_content_id = :blog_content_id
                                                      and blog_categories_id = :blog_categories_id
                                                     ');
          $Qdelete->bindInt(':blog_content_id', $blog_content_id);
          $Qdelete->bindInt(':blog_categories_id', $blog_content_categories[$i]);
          $Qdelete->execute();
        }

        $QblogContentCategories = $CLICSHOPPING_Blog->db->prepare('select count(*)
                                                                  from :table_blog_content_to_categories
                                                                  where blog_content_id = :blog_content_id
                                                                ');
        $QblogContentCategories->bindInt(':blog_content_id', $blog_content_id);
        $QblogContentCategories->execute();

        if ($QblogContentCategories->rowCount() > 0) {
          BlogAdmin::getRemoveBlogContent($blog_content_id);
        }

        $CLICSHOPPING_Hooks->call('BlogContent', 'Delete');
      }

      Cache::clear('blog_tree');

      $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $cPath);
    }
  }