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

  class bc_blog_content_description {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_content_description_name');
      $this->description = CLICSHOPPING::getDef('module_blog_content_description_name_description');

      if (defined('MODULE_BLOG_CONTENT_DESCRIPTION_NAME_STATUS')) {
        $this->sort_order = MODULE_BLOG_CONTENT_DESCRIPTION_NAME_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_CONTENT_DESCRIPTION_NAME_STATUS == 'True');
      }
    }

    public function execute() {

      if ( isset($_GET['Blog']) &&  isset($_GET['Content']) && !empty($_GET['blog_content_id'])) {

        $content_width = (int)MODULE_BLOG_CONTENT_DESCRIPTION_NAME_CONTENT_WIDTH;

        $CLICSHOPPING_Blog = Registry::get('Blog');
        $CLICSHOPPING_Template = Registry::get('Template');

        $blog_content_description = $CLICSHOPPING_Blog->getBlogContentDescription();

        $blog_content_description_content = '<!-- Start blog_content_description  -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/blog_content_description'));
        $blog_content_description_content .= ob_get_clean();

        $blog_content_description_content .= '<!-- end blog_content_description_name -->' . "\n";

        $CLICSHOPPING_Template->addBlock($blog_content_description_content, $this->group);

      }
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_BLOG_CONTENT_DESCRIPTION_NAME_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_DESCRIPTION_NAME_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_DESCRIPTION_NAME_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_CONTENT_DESCRIPTION_NAME_SORT_ORDER',
          'configuration_value' => '60',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '2',
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
        'MODULE_BLOG_CONTENT_DESCRIPTION_NAME_STATUS',
        'MODULE_BLOG_CONTENT_DESCRIPTION_NAME_CONTENT_WIDTH',
        'MODULE_BLOG_CONTENT_DESCRIPTION_NAME_SORT_ORDER'
      );
    }
  }
