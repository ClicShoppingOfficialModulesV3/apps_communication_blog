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

  class BlogContent extends \ClicShopping\OM\Modules\HeaderTagsAbstract
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

      $this->app->loadDefinitions('Module/HeaderTags/blog_content');

      $this->title = $this->app->getDef('module_header_tags_blog_content_title');
      $this->description = $this->app->getDef('module_header_tags_blog_content_description');

      if (defined('MODULE_HEADER_TAGS_BLOG_CONTENT_STATUS')) {
        $this->sort_order = (int)MODULE_HEADER_TAGS_BLOG_CONTENT_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_BLOG_CONTENT_STATUS == 'True');
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

      if (isset($_GET['BlogContent'])) {
        $Qsubmit = $this->app->db->prepare('select submit_id,
                                                    language_id,
                                                    submit_defaut_language_title,
                                                    submit_defaut_language_keywords,
                                                    submit_defaut_language_description
                                             from :table_submit_description
                                             where submit_id = 1
                                             and language_id = :language_id
                                            ');

        $Qsubmit->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
        $Qsubmit->execute();

        $QblogInfo = $this->app->db->prepare('select pd.blog_content_name,
                                                     pd.blog_content_head_title_tag,
                                                     pd.blog_content_head_keywords_tag,
                                                     pd.blog_content_head_desc_tag
                                              from :table_blog_content p,
                                                   :table_blog_content_description  pd
                                              where p.blog_content_status = 1
                                              and p.blog_content_id = :blog_content_id
                                              and pd.blog_content_id = p.blog_content_id
                                              and pd.language_id = :language_id
                                             ');

        $QblogInfo->bindInt(':blog_content_id', (int)$_GET['blogContentId']);
        $QblogInfo->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
        $QblogInfo->execute();

        if (empty($QblogInfo->value('blog_content_head_title_tag'))) {
          $pages_title = HTML::sanitize($QblogInfo->value('blog_content_name'));
        } else {
          $head_title = HTML::sanitize($QblogInfo->value('blog_content_head_title_tag'));
          $pages_title_name = HTML::sanitize($QblogInfo->value('blog_content_name'));
          $pages_title = $head_title . ', ' . $pages_title_name;
        }

        $tags_array = [];

        if (empty($QblogInfo->value('blog_content_head_title_tag'))) {

          if (empty($Qsubmit->value('submit_defaut_language_title'))) {
            $tags_array['title'] = $pages_title . HTML::sanitize($Qsubmit->value('submit_defaut_language_title'));
          } else {
            $tags_array['title'] = $pages_title;
          }
        } else {
          $tags_array['title'] = HTML::sanitize($QblogInfo->value('blog_content_head_title_tag'));
        }

        if (empty($QblogInfo->value('blog_content_head_desc_tag'))) {
          if (empty($Qsubmit->value('submit_language_products_info_description'))) {
            $tags_array['desc'] = $pages_title . HTML::sanitize($Qsubmit->value('submit_defaut_language_description'));
          } else {
            $tags_array['desc'] = $pages_title . $QblogInfo->value('blog_content_head_desc_tag');
          }
        } else {
          $tags_array['desc'] = $pages_title . HTML::sanitize($QblogInfo->value('blog_content_head_desc_tag'));
        }

        if (empty($QblogInfo->value('blog_content_head_keywords_tag'))) {
          if (empty($Qsubmit->value('submit_language_products_info_keywords'))) {
            $tags_array['keywords'] = $pages_title . HTML::sanitize($Qsubmit->value('submit_defaut_language_keywords'));
          } else {
            $tags_array['keywords'] = $pages_title . $QblogInfo->value('blog_content_head_keywords_tag');
          }
        } else {
          $tags_array['keywords'] = $pages_title . HTML::sanitize($Qsubmit->value('blog_content_head_keywords_tag'));
        }

        $title = $CLICSHOPPING_Template->setTitle($tags_array['title'] . ' ,' . $CLICSHOPPING_Template->getTitle());
        $description = $CLICSHOPPING_Template->setDescription($tags_array['desc'] . ', ' . $CLICSHOPPING_Template->getDescription());
        $keywords = $CLICSHOPPING_Template->setKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());
        $new_keywords = $CLICSHOPPING_Template->setNewsKeywords($tags_array['keywords'] . ', ' . $CLICSHOPPING_Template->getKeywords());

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

    public function Install()
    {
      $this->app->db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_BLOG_CONTENT_STATUS',
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
          'configuration_key' => 'MODULE_HEADER_TAGS_BLOG_CONTENT_SORT_ORDER',
          'configuration_value' => '157',
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
      return ['MODULE_HEADER_TAGS_BLOG_CONTENT_STATUS',
        'MODULE_HEADER_TAGS_BLOG_CONTENT_SORT_ORDER'
      ];
    }
  }
