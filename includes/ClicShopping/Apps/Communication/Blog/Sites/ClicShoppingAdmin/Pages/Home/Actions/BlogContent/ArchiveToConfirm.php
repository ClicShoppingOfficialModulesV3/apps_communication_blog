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

  class ArchiveToConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');

      if (isset($_GET['cPath'])) $cPath = HTML::sanitize($_GET['cPath']);

      $blog_content_id = HTML::sanitize($_POST['blog_content_id']);

      $Qupdate = $CLICSHOPPING_Blog->db->prepare('update :table_blog_content
                                                  set blog_content_archive = 1
                                                  where blog_content_id = :blog_content_id
                                                ');

      $Qupdate->bindInt(':blog_content_id', (int)$blog_content_id);

      $Qupdate->execute();

// Mise a zero des stats
      $Qupdate = $CLICSHOPPING_Blog->db->prepare('update :table_blog_content_description
                                                  set blog_content_viewed = 0
                                                  where blog_content_id = :blog_content_id
                                                ');

      $Qupdate->bindInt(':blog_content_id', (int)$blog_content_id);

      $Qupdate->execute();

      Cache::clear('blog_tree');

      $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $cPath . '&pID=' . $blog_content_id);
    }
  }