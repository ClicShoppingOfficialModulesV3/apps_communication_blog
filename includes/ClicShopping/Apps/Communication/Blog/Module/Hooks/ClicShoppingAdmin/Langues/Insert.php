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

  namespace ClicShopping\Apps\Communication\Blog\Module\Hooks\ClicShoppingAdmin\Langues;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Communication\Blog\Blog as BlogApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $insert_language_id;

    public function __construct()
    {
      global $insert_id;

      if (!Registry::exists('Blog')) {
        Registry::set('Blog', new BlogApp());
      }

      $this->app = Registry::get('Blog');
      $this->insert_language_id = HTML::sanitize($insert_id);
      $this->lang = Registry::get('Language');
    }

    private function insert()
    {
      if (isset($this->insert_language_id)) {

// create additional blog_categories_description records
        $QblogCategories = $this->app->db->prepare('select c.blog_categories_id as orig_blog_categories_id,
                                                           cd.*
                                                    from :table_blog_categories c left join :table_blog_categories_description cd on c.blog_categories_id = cd.blog_categories_id
                                                    where cd.language_id = :language_id
                                                  ');

        $QblogCategories->bindInt(':language_id', (int)$this->lang->getId());
        $QblogCategories->execute();

        while ($QblogCategories->fetch()) {
          $cols = $QblogCategories->toArray();

          $cols['blog_categories_id'] = $cols['orig_blog_categories_id'];
          $cols['language_id'] = $this->insert_language_id;

          unset($cols['orig_blog_categories_id']);

          $this->app->db->save('blog_categories_description', $cols);
        }

// create additional blog_content_description records
        $QblogCategories = $this->app->db->prepare('select p.blog_content_id as orig_blog_content_id,
                                                           pd.*
                                                    from :table_blog_content p left join :table_blog_content_description pd on p.blog_content_id = pd.blog_content_id
                                                    where pd.language_id = :language_id
                                                  ');

        $QblogCategories->bindInt(':language_id', (int)$this->lang->getId());
        $QblogCategories->execute();

        while ($QblogCategories->fetch()) {
          $cols = $QblogCategories->toArray();

          $cols['blog_content_id'] = $cols['orig_blog_content_id'];
          $cols['language_id'] = $this->insert_language_id;

          unset($cols['orig_blog_content_id']);

          $this->app->db->save('blog_content_description', $cols);
        }
      }
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_BLOG_BL_STATUS') || CLICSHOPPING_APP_BLOG_BL_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Insert'])) {
        $this->insert();
      }
    }
  }