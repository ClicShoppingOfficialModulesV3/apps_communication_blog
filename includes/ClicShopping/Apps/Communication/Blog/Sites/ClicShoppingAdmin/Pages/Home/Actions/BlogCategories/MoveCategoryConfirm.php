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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  class MoveCategoryConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_POST['blog_categories_id']) && ($_POST['blog_categories_id'] != $_POST['move_to_category_id'])) {
        $blog_categories_id = HTML::sanitize($_POST['blog_categories_id']);
        $new_parent_id = HTML::sanitize($_POST['move_to_category_id']);

        $path = explode('_', BlogAdmin::getGeneratedBlogCategoryPathIds($new_parent_id));

        if (in_array($blog_categories_id, $path)) {
          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Blog->getDef('error_cannot_move_category_to_parent'), 'error');

          $CLICSHOPPING_Blog->redirect('BlogCategories.php', 'cPath=' . $_GET['cPath'] . '&cID=' . $blog_categories_id);

        } else {

          $Qupdate = $CLICSHOPPING_Blog->db->prepare('update :table_blog_categories
                                                set parent_id = :parent_id,
                                                last_modified = now()
                                                where blog_categories_id = :blog_categories_id
                                              ');
          $Qupdate->bindInt(':parent_id', (int)$new_parent_id);
          $Qupdate->bindInt(':blog_categories_id', (int)$blog_categories_id);
          $Qupdate->execute();

          Cache::clear('blog_tree');
          Cache::clear('boxe_blog');

          $CLICSHOPPING_Blog->redirect('BlogCategories&cPath=' . $new_parent_id . '&cID=' . $blog_categories_id);
        }
      }
    }
  }