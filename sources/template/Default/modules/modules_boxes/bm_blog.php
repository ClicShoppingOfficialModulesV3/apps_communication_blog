<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class bm_blog {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function  __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_boxes_blog_categories_title');
      $this->description = CLICSHOPPING::getDef('module_boxes_blog_categories_description');

      if ( defined('MODULE_BOXES_BLOG_CATEGORIES_STATUS')) {
        $this->sort_order = MODULE_BOXES_BLOG_CATEGORIES_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_BLOG_CATEGORIES_STATUS == 'True');
        $this->pages = MODULE_BOXES_BLOG_CATEGORIES_DISPLAY_PAGES;
        $this->group = ((MODULE_BOXES_BLOG_CATEGORIES_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    public function  execute() {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');

      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        $Qsubcategory = $CLICSHOPPING_Db->prepare('select c.blog_categories_id,
                                                          cd.blog_categories_name
                                                   from :table_blog_categories c,
                                                        :table_blog_categories_description cd
                                                   where c.blog_categories_id=cd.blog_categories_id
                                                   and cd.language_id = :language_id
                                                   and (c.customers_group_id = :customers_group_id or c.customers_group_id = 99)
                                                   order by c.sort_order, cd.blog_categories_name
                                                 ');
        $Qsubcategory->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
        $Qsubcategory->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
      } else {

        $Qsubcategory = $CLICSHOPPING_Db->prepare('select c.blog_categories_id,
                                                          cd.blog_categories_name
                                                   from :table_blog_categories c,
                                                        :table_blog_categories_description cd
                                                   where c.blog_categories_id = cd.blog_categories_id
                                                   and cd.language_id = :language_id
                                                   and (c.customers_group_id = 0 or c.customers_group_id = 99)
                                                   order by c.sort_order, cd.blog_categories_name
                                                 ');
        $Qsubcategory->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
       }

      $Qsubcategory->setCache('boxe-blog');
      $Qsubcategory->execute();

      if ($Qsubcategory->rowCount() > 0 ) {
        $blog_categories_banner = '';

        if ($CLICSHOPPING_Service->isStarted('Banner')) {
          if ($banner = $CLICSHOPPING_Banner->bannerExists('dynamic',  MODULE_BOXES_BLOG_CATEGORIES_BANNER_GROUP)) {
            $blog_categories_banner = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
          }
        }

        $data ='<!-- Boxe categories blog start -->' . "\n";
        $data .= '<section class="boxeBlogCategories" id="boxeBlogCategories">';
        $data .= '<div class="separator"></div>';
        $data .= '<div class="boxeBannerContentsBlogCategories">' . $blog_categories_banner .'</div>';
        $data .= '<div class="card boxeContainerBlogCategories">';
        $data .= '<div class="card-header boxeHeadingBlogCategories"><span class="card-title boxeTitleBlogCategories">' . CLICSHOPPING::getDef('module_boxes_blog_categories_box_title') . '</span></div>';
        $data .= '<div class="card-block boxeContentArroundBlogCategories">';
        $data .= '<div class="separator"></div>';

         while($Qsubcategory->fetch()) {

          $QblogContent = $CLICSHOPPING_Db->prepare('select p.blog_content_id,
                                                      p2c.blog_categories_id
                                               from :table_blog_content p,
                                                    :table_blog_content_description pd,
                                                    :table_blog_content_to_categories p2c
                                               where  p.blog_content_id = pd.blog_content_id
                                               and p.blog_content_status = 1
                                               and p.blog_content_archive = 0
                                               and (p.customers_group_id = 0 or p.customers_group_id = 99)
                                               and pd.language_id = :languages_id
                                               and p2c.blog_categories_id = :blog_categories_id
                                             ');
          $QblogContent->bindInt(':languages_id',(int)$CLICSHOPPING_Language->getId() );
          $QblogContent->bindInt(':blog_categories_id', (int)$Qsubcategory->valueInt('blog_categories_id'));

/*
          if (USE_CACHE == 'True') {
            $QblogContent->setCache('blog_tree-' . $CLICSHOPPING_Language->getId());
          }
*/
          $QblogContent->execute();

           if (is_numeric($QblogContent->valueInt('blog_categories_id'))) {
             $data .=  '<div class="card-text">
                          <ul class="list-inline">
                            <li class="list-inline-item boxeContentBlogCategories">' . HTML::link(CLICSHOPPING::link(null, 'Blog&Categories&current=' . (int)$QblogContent->valueInt('blog_categories_id')), $Qsubcategory->value('blog_categories_name')) . '</li>
                          </ul>
                        </div>
                      ';
           }
        }

        $data .= '</div>';
        $data .= '<div class="card-footer boxeBottomContentsBlogCategories"></div>';
        $data .= '</div>' . "\n";
        $data .= '</section>' . "\n";
        $data .='<!-- Boxe categories blog end -->' . "\n";

        $CLICSHOPPING_Template->addBlock($data, $this->group);
      }
    }

    public function  isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_BOXES_BLOG_CATEGORIES_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BOXES_BLOG_CATEGORIES_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please choose where the boxe must be displayed',
          'configuration_key' => 'MODULE_BOXES_BLOG_CATEGORIES_CONTENT_PLACEMENT',
          'configuration_value' => 'Right Column',
          'configuration_description' => 'Choose where the boxe must be displayed',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Left Column\', \'Right Column\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the group where the banner belongs',
          'configuration_key' => 'MODULE_BOXES_BLOG_CATEGORIES_BANNER_GROUP',
          'configuration_value' => SITE_THEMA . '_boxe_blog_categories',
          'configuration_description' => 'Please indicate the group where the banner belongs <br /> <br /> <strong> Note: </strong> <br /> <i> The group will be indicated when creating the banner in the Marketing section / Banner management </i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BOXES_BLOG_CATEGORIES_SORT_ORDER',
          'configuration_value' => '120',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate where boxing should be displayed',
          'configuration_key' => 'MODULE_BOXES_BLOG_CATEGORIES_DISPLAY_PAGES',
          'configuration_value' => 'all',
          'configuration_description' => 'Select the pages where boxing must be present.',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => 'clic_cfg_set_select_pages_list',
          'date_added' => 'now()'
        ]
      );
    }

    public function  remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function  keys() {
      return array('MODULE_BOXES_BLOG_CATEGORIES_STATUS',
                   'MODULE_BOXES_BLOG_CATEGORIES_CONTENT_PLACEMENT',
                   'MODULE_BOXES_BLOG_CATEGORIES_BANNER_GROUP',
                   'MODULE_BOXES_BLOG_CATEGORIES_SORT_ORDER',
                   'MODULE_BOXES_BLOG_CATEGORIES_DISPLAY_PAGES'
                  );
    }
  }
