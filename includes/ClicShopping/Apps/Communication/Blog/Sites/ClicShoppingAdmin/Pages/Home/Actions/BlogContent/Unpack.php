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

  class Unpack extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');

      if (isset($_GET['cPath'])) $cPath = HTML::sanitize($_GET['cPath']);

      if ($_GET['action'] == 'unpack' && isset($_GET['pID'])) {

        $blog_content_id = HTML::sanitize($_GET['pID']);

        $CLICSHOPPING_Blog->db->save('blog_content', [
          'blog_content_last_modified' => 'now()',
          'blog_content_archive' => '0'
        ], [
            'blog_content_id' => (int)$blog_content_id
          ]
        );
      } // end post

      Cache::clear('blog_tree');

      $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $cPath);
    }
  }