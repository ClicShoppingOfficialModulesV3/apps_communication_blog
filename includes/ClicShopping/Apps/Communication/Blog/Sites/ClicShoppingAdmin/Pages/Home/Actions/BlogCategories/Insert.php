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

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_POST['sort_order'])) $sort_order = HTML::sanitize($_POST['sort_order']);

      if (isset($_GET['cPath'])) {
        $current_category_id = HTML::sanitize($_GET['cPath']);
      } else {
        $current_category_id = 0;
      }

      $sql_data_array = ['sort_order' => (int)$sort_order];

      $insert_sql_data = ['parent_id' => (int)$current_category_id,
        'date_added' => 'now()'
      ];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Blog->db->save('blog_categories', $sql_data_array);

      $blog_categories_id = $CLICSHOPPING_Blog->db->lastInsertId();

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = count($languages); $i < $n; $i++) {

        if (isset($_POST['blog_categories_name'])) $blog_categories_name_array = HTML::sanitize($_POST['blog_categories_name']);

        $language_id = $languages[$i]['id'];

        $sql_data_array = ['blog_categories_name' => $blog_categories_name_array[$language_id],
          'blog_categories_description' => $_POST['blog_categories_description'][$language_id],
          'blog_categories_head_title_tag' => HTML::sanitize($_POST['blog_categories_head_title_tag'][$language_id]),
          'blog_categories_head_desc_tag' => HTML::sanitize($_POST['blog_categories_head_desc_tag'][$language_id]),
          'blog_categories_head_keywords_tag' => HTML::sanitize($_POST['blog_categories_head_keywords_tag'][$language_id])
        ];

        $insert_sql_data = ['blog_categories_id' => $blog_categories_id,
          'language_id' => $languages[$i]['id']
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Blog->db->save('blog_categories_description', $sql_data_array);
      }


// Ajoute ou efface l'image dans la base de donees
      if (isset($_POST['delete_image'])) {
        $blog_categories_image = '';

        $CLICSHOPPING_Blog->db->save('blog_categories', ['blog_categories_image' => $blog_categories_image],
          ['blog_categories_id' => (int)$blog_categories_id]
        );


      } elseif (isset($_POST['blog_categories_image']) && !is_null($_POST['blog_categories_image']) && ($_POST['blog_categories_image'] != 'none')) {
        $blog_categories_image = $_POST['blog_categories_image'];


// Insertion images des produits via l'editeur FCKeditor (fonctionne sur les nouveaux produits et editions produits)
        if (isset($_POST['blog_categories_image']) && !is_null($_POST['blog_categories_image']) && ($_POST['blog_categories_image'] != 'none')) {
          $blog_categories_image = HTMLOverrideAdmin::getCkeditorImageAlone($blog_categories_image);
        } else {
          $blog_categories_image = (isset($_POST['categories_previous_image']) ? $_POST['categories_previous_image'] : '');
        }

        $CLICSHOPPING_Blog->db->save('blog_categories', ['blog_categories_image' => $blog_categories_image],
          ['blog_categories_id' => (int)$blog_categories_id]
        );
      }

      $CLICSHOPPING_Hooks->call('BlogCategories', 'Insert');

      Cache::clear('blog_tree');
      Cache::clear('boxe_blog');

      $CLICSHOPPING_Blog->redirect('BlogCategories&cPath=' . $_GET['cPath'] . '&cID=' . $blog_categories_id);
    }
  }