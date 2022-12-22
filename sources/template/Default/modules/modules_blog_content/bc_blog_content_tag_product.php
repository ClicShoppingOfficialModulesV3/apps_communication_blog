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

  use ClicShopping\Service\Shop\WhosOnline;

  class bc_blog_content_tag_product {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_content_tag_product_title');
      $this->description = CLICSHOPPING::getDef('module_blog_content_tag_product_description');

      if (defined('MODULE_BLOG_CONTENT_TAG_PRODUCT_STATUS')) {
        $this->sort_order = MODULE_BLOG_CONTENT_TAG_PRODUCT_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_CONTENT_TAG_PRODUCT_STATUS == 'True');
      }
    }

    public function execute() {
      $spider_flag = WhosOnline::getResultSpiderFlag();

      if ( isset($_GET['Blog']) &&  isset($_GET['Content']) && !empty($_GET['blog_content_id'])) {

        $CLICSHOPPING_Blog = Registry::get('Blog');
        $CLICSHOPPING_Template = Registry::get('Template');

        $product_tag = $CLICSHOPPING_Blog->getBlogContentTag();

        if (!empty($product_tag) && $spider_flag === false) {

          $tag_content = '<!-- Start tag_content_product -->' . "\n";
          $tag_content .= '<div class="clearfix"></div>';
          $tag_content .= '<div class="separator"></div>';
          $tag_content .= '<div class="contentText" itemprop="tag" style="float: '.MODULE_BLOG_CONTENT_TAG_PRODUCT_POSITION .';">';
          $tag_content .= '<div class="moduleBlogContentTagProductText">' . CLICSHOPPING::getDef('module_text_tag_product');

          foreach ($product_tag  as $value) {
            $tag_content .= '<span class="label label-default moduleBlogContentTagProduct"><a href="' . CLICSHOPPING::link(null, 'Searh&AdvancedSearch&keywords=' . $value, 'rel="nofollow, tag"') . '">'.$value.'</a><span> ';
          }

          $tag_content .= '</div>';
          $tag_content .= '</div>' . "\n";
          $tag_content .= '<!-- end tag_content_product  -->' . "\n";

          $CLICSHOPPING_Template->addBlock($tag_content, $this->group);
         }
      } // php_self
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_BLOG_CONTENT_TAG_PRODUCT_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TAG_PRODUCT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'A quel endroit souhaitez-vous afficher les tags en relation avec les produits ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TAG_PRODUCT_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Affiche les tags de l\'article à gauche ou à droite<br><br><i>(- Valeur None = Aucune <br>- Valeur Left = Gauche <br>- Valeur Right = Droite)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'none\', \'right\', \'left\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TAG_PRODUCT_SORT_ORDER',
          'configuration_value' => '60',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_BLOG_CONTENT_TAG_PRODUCT_STATUS',
        'MODULE_BLOG_CONTENT_TAG_PRODUCT_POSITION',
        'MODULE_BLOG_CONTENT_TAG_PRODUCT_SORT_ORDER'
      );
    }
  }
