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

  namespace ClicShopping\Apps\Communication\Blog\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Blog = Registry::get('Blog');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Blog->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('BlogAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installBlogDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Blog->getDef('alert_module_install_success'), 'success', 'Blog');

      $CLICSHOPPING_Blog->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Blog = Registry::get('Blog');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_communication_blog']);

// Menu Top
      if ($Qcheck->fetch() === false) {

        $languages = $CLICSHOPPING_Language->getLanguages();

        $sql_data_array = ['sort_order' => 3,
          'link' => '',
          'image' => 'blog.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_communication_blog'
        ];

        $insert_sql_data = ['parent_id' => 6];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Blog->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        $Qid = $CLICSHOPPING_Db->get('administrator_menu', 'id', ['app_code' => 'app_communication_blog']);

// Categories
        $sql_data_array = ['sort_order' => 1,
          'link' => 'index.php?A&Communication\Blog&BlogCategories',
          'image' => 'blog.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_communication_blog'
        ];

        $insert_sql_data = ['parent_id' => $Qid->valueInt('id')];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Blog->getDef('title_menu_blog_categories')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }


// blog Content
        $sql_data_array = ['sort_order' => 2,
          'link' => 'index.php?A&Communication\Blog&BlogContent',
          'image' => 'blog.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_communication_blog'
        ];

        $insert_sql_data = ['parent_id' => $Qid->valueInt('id')];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Blog->getDef('title_menu_blog_content')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }


//*******************************************
// Modules
//*******************************************

        $sql_data_array = ['sort_order' => 5,
          'link' => 'index.php?A&Configuration\Modules&Modules&set=modules_blog',
          'image' => 'blog.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_communication_blog'
        ];

        $insert_sql_data = ['parent_id' => 122];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Blog->getDef('title_menu_blog_categories')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }


        $sql_data_array = ['sort_order' => 5,
          'link' => 'index.php?A&Configuration\Modules&Modules&set=modules_blog_content',
          'image' => 'blog.png',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_communication_blog'
        ];

        $insert_sql_data = ['parent_id' => 122];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Blog->getDef('title_menu_blog_content')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }


        Cache::clear('menu-administrator');
      }
    }

    private function installBlogDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_blog_content"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_blog_content (
  blog_content_id int(11) NOT NULL,
  blog_content_date_added datetime NOT NULL,
  blog_content_last_modified datetime DEFAULT NULL,
  blog_content_date_available datetime DEFAULT NULL,
  blog_content_status tinyint(1) NOT NULL DEFAULT 0,
  blog_content_archive tinyint(1) NOT NULL DEFAULT 0,
  admin_user_name varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  blog_content_sort_order int(3) DEFAULT NULL,
  blog_content_author varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  customers_group_id int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE :table_blog_content ADD PRIMARY KEY (blog_content_id), ADD KEY idx_blog_content_date_added (blog_content_date_added);
ALTER TABLE :table_blog_content MODIFY `blog_content_id` int(11) NOT NULL AUTO_INCREMENT;
EOD;

        $CLICSHOPPING_Db->exec($sql);
      }

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_blog_content_description"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_blog_content_description (
  blog_content_id int(11) NOT NULL DEFAULT 0,
  language_id int(11) NOT NULL DEFAULT 1,
  blog_content_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  blog_content_description text COLLATE utf8mb4_unicode_ci,
  blog_content_url varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  blog_content_viewed int(5) DEFAULT 0,
  blog_content_head_title_tag varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  blog_content_head_desc_tag varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  blog_content_head_keywords_tag varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  blog_content_head_tag_product varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  blog_content_head_tag_blog varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  blog_content_description_summary text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE :table_blog_content_description ADD PRIMARY KEY (blog_content_id, language_id), ADD KEY idx_blog_content_name` (blog_content_name);
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }


      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_blog_content_to_categories"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_blog_content_to_categories (
  blog_content_id int(11) NOT NULL DEFAULT 0,
  blog_categories_id int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE :table_blog_content_to_categories ADD PRIMARY KEY (blog_content_id, blog_categories_id);
EOD;

        $CLICSHOPPING_Db->exec($sql);
      }


      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_blog_categories"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_blog_categories (
  blog_categories_id int(11) NOT NULL,
  blog_categories_image varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  parent_id int(11) NOT NULL DEFAULT 0,
  sort_order int(3) DEFAULT NULL,
  date_added datetime DEFAULT NULL,
  last_modified datetime DEFAULT NULL,
  customers_group_id int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE :table_blog_categories ADD PRIMARY KEY (blog_categories_id), ADD KEY idx_blog_categories_parent_id (parent_id);

ALTER TABLE :table_blog_categories MODIFY blog_categories_id int(11) NOT NULL AUTO_INCREMENT;
EOD;

        $CLICSHOPPING_Db->exec($sql);
      }

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_blog_categories_description"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_blog_categories_description (
  blog_categories_id int(11) NOT NULL DEFAULT 0,
  language_id int(11) NOT NULL DEFAULT 1,
  blog_categories_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  blog_categories_description text COLLATE utf8mb4_unicode_ci,
  blog_categories_head_title_tag varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  blog_categories_head_desc_tag varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  blog_categories_head_keywords_tag varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE :table_blog_categories_description ADD PRIMARY KEY (blog_categories_id, language_id), ADD KEY idx_blog_categories_name (blog_categories_name);
EOD;

        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
