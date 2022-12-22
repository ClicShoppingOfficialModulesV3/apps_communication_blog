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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class bl_blog_wordpress {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_wordpress_title');
      $this->description = CLICSHOPPING::getDef('module_blog_wordpress_description');

      if ( defined('MODULES_BLOG_WORDPRESS_STATUS')) {
        $this->sort_order = (int)MODULES_BLOG_WORDPRESS_SORT_ORDER;
        $this->enabled = (MODULES_BLOG_WORDPRESS_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');

      $bootstrap_column = (int)MODULES_BLOG_WORDPRESS_CONTENT_WIDTH;

      if (isset($_GET['Blog']) && isset($_GET['Categories'])) {

        if (!empty(MODULES_BLOG_WORDPRESS_SITE)) {
          $json = file_get_contents(HTML::outputProtected(MODULES_BLOG_WORDPRESS_SITE) . '/wp-json/wp/v2/posts?per_page=' . (int)MODULES_BLOG_WORDPRESS_MAX_DISPLAY . '&_embed');
          $posts = json_decode($json, true);

          if (count($posts) > 0) {

            $template = '<!-- start blog worpress -->' . "\n";
            $template .= '<div class="d-flex flex-wrap">';

            require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/wordpress'));
            $template .= ob_get_clean();

            $template .= '</div>' . "\n";
            $template .= '<!-- end blog worpress-->' . "\n";
            $CLICSHOPPING_Template->addBlock($template, $this->group);
          }
        }
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULES_BLOG_WORDPRESS_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULES_BLOG_WORDPRESS_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_public function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MODULES_BLOG_WORDPRESS_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_public function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer l\'url du site Worpress',
          'configuration_key' => 'MODULES_BLOG_WORDPRESS_SITE',
          'configuration_value' => '',
          'configuration_description' => 'L\'url de votre site worpress : https://www.monblog<br><br><strong>Note :</strong><br>Avant d\'utiliser ce module, vous devez installer le module WordPress REST API (Version 2) sur votre blog Wordpress. Voir <a target="_blank" rel="noopener" href="https://wordpress.org/plugins/rest-api/">https://wordpress.org/plugins/rest-api/</a>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_public function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le nombre de billet à afficher',
          'configuration_key' => 'MODULES_BLOG_WORDPRESS_MAX_DISPLAY',
          'configuration_value' => '4',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_public function' => '',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULES_BLOG_WORDPRESS_SORT_ORDER',
          'configuration_value' => '120',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_public function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array('MODULES_BLOG_WORDPRESS_STATUS',
                    'MODULES_BLOG_WORDPRESS_SITE',
                    'MODULES_BLOG_WORDPRESS_CONTENT_WIDTH',
                    'MODULES_BLOG_WORDPRESS_MAX_DISPLAY',
                    'MODULES_BLOG_WORDPRESS_SORT_ORDER'
                   );
    }
  }
