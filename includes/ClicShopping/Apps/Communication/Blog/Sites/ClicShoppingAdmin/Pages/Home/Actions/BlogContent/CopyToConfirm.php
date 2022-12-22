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

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class CopyToConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Blog = Registry::get('Blog');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($_POST['blog_content_id']) && isset($_POST['blog_categories_id'])) {
        $blog_content_id = HTML::sanitize($_POST['blog_content_id']);
        $blog_categories_id = HTML::sanitize($_POST['blog_categories_id']);
        $current_category_id = HTML::sanitize($_GET['cPath']);

        if ($_POST['copy_as'] == 'link') {
          if ($blog_categories_id != $current_category_id) {

            $Qcheck = $CLICSHOPPING_Blog->db->prepare('select count(*) as total
                                                       from :table_blog_content_to_categories
                                                       where blog_content_id = :blog_content_id
                                                       and blog_categories_id = :blog_categories_id
                                                     ');
            $Qcheck->bindInt(':blog_content_id', (int)$blog_content_id);
            $Qcheck->bindInt(':blog_categories_id', (int)$blog_categories_id);
            $Qcheck->execute();

            $check = $Qcheck->fetch();

            if ($check['total'] < 1) {

              $CLICSHOPPING_Blog->db->save('blog_content_to_categories', [
                  'blog_content_id' => (int)$blog_content_id,
                  'blog_categories_id' => (int)$blog_categories_id
                ]
              );

            }
          } else {
            $CLICSHOPPING_MessageStack->add(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
          }
        } elseif ($_POST['copy_as'] == 'duplicate') {
//Duplication des champs ou copie de la table product

          $QblogContent = $CLICSHOPPING_Blog->db->prepare('select blog_content_date_available,
                                                         blog_content_status,
                                                         admin_user_name,
                                                         blog_content_sort_order,
                                                         blog_content_author,
                                                         customers_group_id
                                               from :table_blog_content
                                               where blog_content_id = :blog_content_id
                                              ');
          $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
          $QblogContent->execute();

          $blog_content = $QblogContent->fetch();

          $CLICSHOPPING_Blog->db->save('blog_content', [
              'blog_content_date_added' => 'now()',
              'blog_content_date_available' => (empty($blog_content['blog_content_date_available']) ? "null" : "'" . $blog_content['blog_content_date_available'] . "'"),
              'blog_content_status' => (int)$blog_content['blog_content_status'],
              'admin_user_name' => AdministratorAdmin::getUserAdmin(),
              'blog_content_sort_order' => (int)$blog_content['blog_content_sort_order'],
              'blog_content_author' => $blog_content['blog_content_author'],
              'customers_group_id' => (int)$blog_content['customers_group_id']
            ]
          );


          $dup_blog_content_id = $CLICSHOPPING_Blog->db->lastInsertId();

// ---------------------
// referencement
// ----------------------
          $Qdescription = $CLICSHOPPING_Blog->db->prepare('select language_id,
                                                            blog_content_name,
                                                            blog_content_description,
                                                            blog_content_head_title_tag,
                                                            blog_content_head_desc_tag,
                                                            blog_content_head_keywords_tag,
                                                            blog_content_url,
                                                            blog_content_head_tag_product,
                                                            blog_content_head_tag_blog,
                                                            blog_content_description_summary
                                                     from :table_blog_content_description
                                                     where blog_content_id = :blog_content_id
                                                   ');
          $Qdescription->bindint(':blog_content_id', (int)$blog_content_id);
          $Qdescription->execute();

          while ($description = $Qdescription->fetch()) {

            $CLICSHOPPING_Blog->db->save('blog_content_description', [
                'blog_content_id' => (int)$dup_blog_content_id,
                'language_id' => (int)$description['language_id'],
                'blog_content_name' => $description['blog_content_name'],
                'blog_content_description' => $description['blog_content_description'],
                'blog_content_head_title_tag' => $description['blog_content_head_title_tag'],
                'blog_content_head_desc_tag' => $description['blog_content_head_desc_tag'],
                'blog_content_head_keywords_tag' => $description['blog_content_head_keywords_tag'],
                'blog_content_url' => $description['blog_content_url'],
                'blog_content_head_tag_product' => $description['blog_content_head_tag_product'],
                'blog_content_head_tag_blog' => $description['blog_content_head_tag_blog'],
                'blog_content_description_summary' => $description['blog_content_description_summary']
              ]
            );
          }

          $CLICSHOPPING_Blog->db->save('blog_content_to_categories', ['blog_content_id' => (int)$dup_blog_content_id,
              'blog_categories_id' => (int)$blog_categories_id
            ]
          );

          $blog_content_id = $dup_blog_content_id;
        }

        Cache::clear('blog_tree');
      }

      $CLICSHOPPING_Blog->redirect('BlogContent&cPath=' . $blog_categories_id . '&pID=' . $blog_content_id);
    }
  }