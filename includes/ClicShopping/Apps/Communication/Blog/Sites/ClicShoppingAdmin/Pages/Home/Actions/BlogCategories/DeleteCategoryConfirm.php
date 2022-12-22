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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  class DeleteCategoryConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');

      if (isset($_POST['blog_categories_id'])) {
        $blog_categories_id = HTML::sanitize($_POST['blog_categories_id']);
        $categories = BlogAdmin::getBlogCategoryTree($blog_categories_id, '', '0', '', true);

        $blog_content = [];
        $blog_content_delete = [];

        for ($i = 0, $n = count($categories); $i < $n; $i++) {

          $QcustomersGroup = $CLICSHOPPING_Blog->db->prepare('select blog_content_id
                                                        from :table_blog_content_to_categories
                                                        where blog_categories_id = :blog_categories_id
                                                      ');
          $QcustomersGroup->bindint(':blog_categories_id', (int)$categories[$i]['id']);
          $QcustomersGroup->execute();

          while ($blog_content_ids = $QcustomersGroup->fetch()) {
            $blog_content[$blog_content_ids['blog_content_id']]['categories'][] = $categories[$i]['id'];
          }
        }

        foreach ($blog_content as $key => $value) {
          $category_ids = '';

          for ($i = 0, $n = count($value['categories']); $i < $n; $i++) {
            $category_ids .= "'" . (int)$value['categories'][$i] . "', ";
          }

          $category_ids = substr($category_ids, 0, -2);

          $Qcheck = $CLICSHOPPING_Blog->db->prepare('select count(*) as total
                                               from :table_blog_content_to_categories
                                               where blog_content_id = :blog_content_id
                                               and blog_categories_id not in (:blog_categories_id)
                                              ');
          $Qcheck->bindInt(':blog_content_id', (int)$key);
          $Qcheck->bindInt(':blog_categories_id', $category_ids);
          $Qcheck->execute();

          if ($Qcheck->value('total') < '1') {
            $blog_content_delete[$key] = $key;
          }
        }

// removing categories can be a lengthy process
        for ($i = 0, $n = count($categories); $i < $n; $i++) {
          BlogAdmin::getRemoveBlogCategory($categories[$i]['id']);
        }

        foreach (array_keys($blog_content_delete) as $key) {
          BlogAdmin::getRemoveBlogContent($key);
        }
      }

      Cache::clear('blog_tree');
      Cache::clear('boxe_blog');

      $CLICSHOPPING_Blog->redirect('BlogCategories&cPath=' . $cPath);
    }
  }