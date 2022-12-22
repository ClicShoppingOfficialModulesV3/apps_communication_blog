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

  namespace ClicShopping\Apps\Communication\Blog\Module\HeaderTags;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Communication\Blog\Blog as BlogApp;

  class BlogCategory extends \ClicShopping\OM\Modules\HeaderTagsAbstract
  {

    protected $db;
    protected $lang;
    protected $app;
    public $group;

    protected function init()
    {
      if (!Registry::exists('BlogApp')) {
        Registry::set('BlogApp', new BlogApp());
      }

      $this->app = Registry::get('BlogApp');
      $this->lang = Registry::get('Language');
      $this->group = 'header_tags'; // could be header_tags or footer_scripts

      $this->app->loadDefinitions('Module/HeaderTags/blog_category');

      $this->title = $this->app->getDef('module_header_tags_blog_category_title');
      $this->description = $this->app->getDef('module_header_tags_blog_category_description');

      if (defined('MODULE_HEADER_TAGS_BLOG_CATEGORY_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_BLOG_CATEGORY_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_BLOG_CATEGORY_STATUS == 'True');
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function getOutput()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!defined('CLICSHOPPING_APP_BLOG_BL_STATUS') || CLICSHOPPING_APP_BLOG_BL_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Blog'])) {
        if (isset($_GET['current']) && $_GET['current'] > 0) {
          $Qsubmit = $this->app->db->prepare('select submit_id,
                                                  language_id,
                                                  submit_defaut_language_title,
                                                  submit_defaut_language_keywords,
                                                  submit_defaut_language_description
                                            from :table_submit_description
                                            where submit_id = :submit_id
                                            and language_id = :language_id
                                          ');
          $Qsubmit->bindInt(':submit_id', '1');
          $Qsubmit->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
          $Qsubmit->execute();

// Definition de la variable de gestion des colonnes
          $tags_array = [];

          $Qcategories = $this->app->db->prepare('select blog_categories_name,
                                                         blog_categories_head_title_tag,
                                                         blog_categories_head_desc_tag,
                                                         blog_categories_head_keywords_tag
                                                  from :table_blog_categories_description
                                                  where blog_categories_id = :blog_categories_id
                                                  and language_id = :language_id
                                                  limit 1
                                                 ');
          $Qcategories->bindInt(':blog_categories_id', (int)$_GET['current']);
          $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
          $Qcategories->execute();

          if ($Qcategories->rowCount() > 0) {
            $categories_name_clean = HTML::sanitize($Qcategories->value('blog_categories_name'));

            if (empty($Qcategories->value('blog_categories_head_title_tag'))) {
              if (empty($Qsubmit->value('submit_defaut_language_title'))) {
                $tags_array['title'] = $categories_name_clean;
              } else {
                $tags_array['title'] = $categories_name_clean . ',  ' . HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
              }
            } else {
              $tags_array['title'] = HTML::sanitize($Qcategories->value('blog_categories_head_title_tag')) . ', ' . $categories_name_clean;
            }

            if (empty($Qcategories->value('blog_categories_head_desc_tag'))) {
              if (empty($Qsubmit->value('submit_defaut_language_description'))) {
                $tags_array['desc'] = $categories_name_clean;
              } else {
                $tags_array['desc'] = $categories_name_clean . ', ' . HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
              }
            } else {
              $tags_array['desc'] = HTML::sanitize($Qcategories->value('blog_categories_head_desc_tag')) . ', ' . $categories_name_clean;
            }

            if (empty($Qcategories->value('blog_categories_head_keywords_tag'))) {
              if (empty($Qsubmit->value('submit_defaut_language_keywords'))) {
                $tags_array['keywords'] = $categories_name_clean;
              } else {
                $tags_array['keywords'] = $categories_name_clean . ', ' . HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
              }
            } else {
              $tags_array['keywords'] = $Qcategories->value('blog_categories_head_keywords_tag') . ', ' . $categories_name_clean;
            }

            $title = $CLICSHOPPING_Template->setTitle($tags_array['title'] . ' ,' . $CLICSHOPPING_Template->getTitle());
            $description = $CLICSHOPPING_Template->setDescription($tags_array['desc'] . ', ' . $CLICSHOPPING_Template->getDescription());
            $keywords = $CLICSHOPPING_Template->setKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());
            $new_keywords = $CLICSHOPPING_Template->setNewsKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());
          }

          $output =
            <<<EOD
{$title}
{$description}
{$keywords}
{$new_keywords}
EOD;

          return $output;
        }
      }
    }

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_BLOG_CATEGORY_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to install this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $this->app->db->save('configuration', [
          'configuration_title' => 'Display sort order',
          'configuration_key' => 'MODULE_HEADER_TAGS_BLOG_CATEGORY_SORT_ORDER',
          'configuration_value' => '155',
          'configuration_description' => 'Display sort order (The lower is displayd in first)',
          'configuration_group_id' => '6',
          'sort_order' => '160',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function keys()
    {
      return ['MODULE_HEADER_TAGS_BLOG_CATEGORY_STATUS',
        'MODULE_HEADER_TAGS_BLOG_CATEGORY_SORT_ORDER'
      ];
    }
  }
