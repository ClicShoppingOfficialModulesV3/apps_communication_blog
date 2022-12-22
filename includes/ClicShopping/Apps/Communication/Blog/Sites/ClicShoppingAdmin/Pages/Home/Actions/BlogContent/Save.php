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

  use ClicShopping\Sites\Common\TwitterClicShopping;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class Save extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Language = Registry::get('Language');
      $current_category_id = HTML::sanitize($_POST['move_to_category_id']);

      if (isset($_GET['cPath'])) $cPath = HTML::sanitize($_GET['cPath']);
      if (isset($_GET['pID'])) $blog_content_id = HTML::sanitize($_GET['pID']);

      if (isset($_POST['blog_content_date_available'])) {
        $blog_content_date_available = HTML::sanitize($_POST['blog_content_date_available']);
        $blog_content_date_available = (date('Y-m-d') < $blog_content_date_available) ? $blog_content_date_available : null;
      } else {
        $blog_content_date_available = null;
      }

      if (!isset($_GET['pID'])) {
//----------------------------------------------
// insert blog_content
//----------------------------------------------
        $sql_data_array = ['blog_content_date_available' => $blog_content_date_available,
          'blog_content_status' => (int)HTML::sanitize($_POST['blog_content_status']),
          'admin_user_name' => AdministratorAdmin::getUserAdmin(),
          'blog_content_sort_order' => (int)HTML::sanitize($_POST['blog_content_sort_order']),
          'blog_content_author' => HTML::sanitize($_POST['blog_content_author']),
          'blog_content_date_added' => 'now()'
        ];

        $CLICSHOPPING_Blog->db->save('blog_content', $sql_data_array);

        $blog_content_id = $CLICSHOPPING_Blog->db->lastInsertId();
        $_POST['blog_content_id'] = $blog_content_id;

        $CLICSHOPPING_Blog->db->save('blog_content_to_categories', ['blog_content_id' => (int)$blog_content_id,
            'blog_categories_id' => (int)$current_category_id
          ]
        );
      } else {
        $sql_data_array = ['blog_content_date_available' => $blog_content_date_available,
          'blog_content_status' => (int)HTML::sanitize($_POST['blog_content_status']),
          'admin_user_name' => AdministratorAdmin::getUserAdmin(),
          'blog_content_sort_order' => (int)HTML::sanitize($_POST['blog_content_sort_order']),
          'blog_content_author' => HTML::sanitize($_POST['blog_content_author'])
        ];

        $update_sql_data = ['blog_content_last_modified' => 'now()'];
        $sql_data_array = array_merge($sql_data_array, $update_sql_data);

        $CLICSHOPPING_Blog->db->save('blog_content', $sql_data_array, ['blog_content_id' => (int)$blog_content_id]);
      }

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];

        if (isset($_POST['blog_content_url'][$language_id])) {
          $blog_content_url = HTML::sanitize($_POST['blog_content_url'][$language_id]);
        } else {
          $blog_content_url = null;
        }


// Referencement
        $sql_data_array = ['blog_content_name' => HTML::sanitize($_POST['blog_content_name'][$language_id]),
          'blog_content_description' => $_POST['blog_content_description'][$language_id],
          'blog_content_head_title_tag' => HTML::sanitize($_POST['blog_content_head_title_tag'][$language_id]),
          'blog_content_head_desc_tag' => HTML::sanitize($_POST['blog_content_head_desc_tag'][$language_id]),
          'blog_content_head_keywords_tag' => HTML::sanitize($_POST['blog_content_head_keywords_tag'][$language_id]),
          'blog_content_url' => $blog_content_url,
          'blog_content_head_tag_product' => HTML::sanitize($_POST['blog_content_head_tag_product'][$language_id]),
          'blog_content_head_tag_blog' => HTML::sanitize($_POST['blog_content_head_tag_blog'][$language_id]),
          'blog_content_description_summary' => HTML::sanitize($_POST['blog_content_description_summary'][$language_id])
        ];

        if (!isset($_GET['pID'])) {
          $insert_sql_data = ['blog_content_id' => $blog_content_id,
            'language_id' => $language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Blog->db->save('blog_content_description', $sql_data_array);
        } else {
          $CLICSHOPPING_Blog->db->save('blog_content_description', $sql_data_array, ['blog_content_id' => (int)$blog_content_id,
              'language_id' => (int)$language_id
            ]
          );
        }
      } // end for

      if (!isset($_GET['pID'])) {
        $CLICSHOPPING_Hooks->call('BlogContent', 'Insert');

        $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $cPath . '&pID=' . $blog_content_id);
      } else {

        $CLICSHOPPING_Hooks->call('BlogContent', 'Update');
        $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $cPath . '&pID=' . $blog_content_id);
      }
    }
  }