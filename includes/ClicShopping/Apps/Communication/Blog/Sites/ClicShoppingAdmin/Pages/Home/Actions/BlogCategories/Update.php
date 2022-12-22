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

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_POST['blog_categories_id'])) $blog_categories_id = HTML::sanitize($_POST['blog_categories_id']);

      if (empty($blog_categories_id)) {
        $blog_categories_id = HTML::sanitize($_GET['cID']);
      }

      $sort_order = HTML::sanitize($_POST['sort_order']);

      $sql_data_array = ['sort_order' => (int)$sort_order];

      $update_sql_data = ['last_modified' => 'now()'];

      $sql_data_array = array_merge($sql_data_array, $update_sql_data);

      $CLICSHOPPING_Blog->db->save('blog_categories', $sql_data_array, ['blog_categories_id' => (int)$blog_categories_id]);

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = count($languages); $i < $n; $i++) {

        $blog_categories_name_array = $_POST['blog_categories_name'];

        $language_id = $languages[$i]['id'];

        $sql_data_array = ['blog_categories_name' => HTML::sanitize($blog_categories_name_array[$language_id]),
          'blog_categories_description' => $_POST['blog_categories_description'][$language_id],
          'blog_categories_head_title_tag' => HTML::sanitize($_POST['blog_categories_head_title_tag'][$language_id]),
          'blog_categories_head_desc_tag' => HTML::sanitize($_POST['blog_categories_head_desc_tag'][$language_id]),
          'blog_categories_head_keywords_tag' => HTML::sanitize($_POST['blog_categories_head_keywords_tag'][$language_id])
        ];

        $CLICSHOPPING_Blog->db->save('blog_categories_description', $sql_data_array, ['blog_categories_id' => (int)$blog_categories_id,
            'language_id' => (int)$languages[$i]['id']
          ]
        );
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

      $CLICSHOPPING_Hooks->call('BlogCategories', 'Update');

      Cache::clear('blog_tree');
      Cache::clear('boxe_blog');

      $CLICSHOPPING_Blog->redirect('BlogCategories&cPath=' . $_GET['cPath'] . '&cID=' . $blog_categories_id);
    }
  }