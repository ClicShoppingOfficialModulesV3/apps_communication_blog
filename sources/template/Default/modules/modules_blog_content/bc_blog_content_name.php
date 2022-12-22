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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class bc_blog_content_name {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_content_name');
      $this->description = CLICSHOPPING::getDef('module_blog_content_name_description');

      if (defined('MODULE_BLOG_CONTENT_NAME_STATUS')) {
        $this->sort_order = MODULE_BLOG_CONTENT_NAME_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_CONTENT_NAME_STATUS == 'True');
      }
    }

    public function execute() {

      if ( isset($_GET['Blog']) &&  isset($_GET['Content']) && !empty($_GET['blog_content_id'])) {

        $CLICSHOPPING_Customer = Registry::get('Customer');
        $CLICSHOPPING_Db = Registry::get('Db');
        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Language = Registry::get('Language');

        if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
// Mode b2b
         $QblogContent = $CLICSHOPPING_Db->prepare('select bcd.blog_content_name
                                                     from :table_blog_content bc,
                                                        :table_blog_content_description bcd
                                                     where bc.blog_content_status = 1
                                                     and bc.blog_content_id = :blog_content_id
                                                     and bcd.blog_content_id = bc.blog_content_id
                                                     and bcd.language_id = :language_id
                                                     and bc.blog_content_archive = 0
                                                     and bc.customers_group_id = :customers_group_id
                                                   ');
          $QblogContent->bindInt(':blog_content_id', (int)$_GET['blog_content_id'] );
          $QblogContent->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId() );
          $QblogContent->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID() );

        } else {

          $QblogContent = $CLICSHOPPING_Db->prepare('select bcd.blog_content_name
                                                     from :table_blog_content bc,
                                                          :table_blog_content_description bcd
                                                     where bc.blog_content_status = 1
                                                     and bc.blog_content_id = :blog_content_id
                                                     and bcd.blog_content_id = bc.blog_content_id
                                                     and bc.blog_content_archive = 0
                                                     and bcd.language_id = :language_id
                                                     and bc.customers_group_id = 0
                                                   ');
          $QblogContent->bindInt(':blog_content_id', (int)$_GET['blog_content_id'] );
          $QblogContent->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId() );
        }

        $QblogContent->execute();
        $blog_content = $QblogContent->fetch();

        $blog_content_name =  HTML::outputProtected($blog_content['blog_content_name']);
        $blog_content_name = '<h1><a href="' . CLICSHOPPING::link(null, '&Blog&Content&blog_content_id=' . (int)$_GET['blog_content_id']) . '" itemprop="url" class="blogContentName"><span itemprop="title">' . $blog_content_name . '</span></a></h1>';

        $blog_content_name_content = '<!-- Start blog_content_name -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/blog_content_name'));
        $blog_content_name_content .= ob_get_clean();

        $blog_content_name_content .= '<!-- blog_content_name -->' . "\n";

        $CLICSHOPPING_Template->addBlock($blog_content_name_content, $this->group);
      }
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_BLOG_CONTENT_NAME_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_NAME_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'A quel endroit souhaitez-vous afficher le nom de l\'article ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_NAME_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Affiche le nom de l\'article à gauche ou à droite<br><br><i>(Valeur None =  Aucune - Valeur Left = Gauche - Valeur Right = Droite)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'none\', \'right\', \'left\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_CONTENT_NAME_SORT_ORDER',
          'configuration_value' => '10',
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
        'MODULE_BLOG_CONTENT_NAME_STATUS',
        'MODULE_BLOG_CONTENT_NAME_POSITION',
        'MODULE_BLOG_CONTENT_NAME_SORT_ORDER'
      );
    }
  }
