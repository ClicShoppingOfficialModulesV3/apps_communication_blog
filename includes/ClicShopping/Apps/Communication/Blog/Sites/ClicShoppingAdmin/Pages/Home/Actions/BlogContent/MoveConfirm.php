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

  class MoveConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');

      if (isset($_POST['blog_content_id'])) $blog_content_id = HTML::sanitize($_POST['blog_content_id']);
      if (isset($_POST['move_to_category_id'])) $new_parent_id = HTML::sanitize($_POST['move_to_category_id']);
      if (isset($_GET['current_category_id'])) $current_category_id = HTML::sanitize($_GET['current_category_id']);

      $QduplicateCheck = $CLICSHOPPING_Blog->db->prepare('select count(*)
                                                           from :table_blog_content_to_categories
                                                           where blog_content_id = :blog_content_id
                                                           and blog_categories_id  not in ( :blog_categories_id )
                                                        ');
      $QduplicateCheck->bindInt(':blog_content_id', $blog_content_id);
      $QduplicateCheck->bindInt(':blog_categories_id', $new_parent_id);
      $QduplicateCheck->execute();

      if ($QduplicateCheck->rowCount() < 1.01) {

        $Qupdate = $CLICSHOPPING_Blog->db->prepare('update :table_blog_content_to_categories
                                                    set blog_categories_id = :blog_categories_id
                                                    where blog_content_id = :blog_content_id
                                                    and blog_categories_id = :blog_categories_id1
                                                  ');
        $Qupdate->bindInt(':blog_categories_id', (int)$new_parent_id);
        $Qupdate->bindInt(':blog_content_id', (int)$blog_content_id);
        $Qupdate->bindInt(':blog_categories_id1', (int)$current_category_id);

        $Qupdate->execute();
      }

      Cache::clear('blog_tree');

      $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $new_parent_id . '&pID=' . $blog_content_id);
    }
  }