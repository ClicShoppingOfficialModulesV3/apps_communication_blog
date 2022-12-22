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

  class bc_blog_content_linkedin {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_content_linkedin_title');
      $this->public_title = CLICSHOPPING::getDef('module_blog_content_linkedin_public_title');
      $this->description = CLICSHOPPING::getDef('module_blog_content_linkedin_description');

      if ( defined('MODULE_BLOG_CONTENT_LINKEDIN_STATUS')) {
        $this->sort_order = MODULE_BLOG_CONTENT_LINKEDIN_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_CONTENT_LINKEDIN_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');

      if ( isset($_GET['Blog']) &&  isset($_GET['Content']) && !empty($_GET['blog_content_id'])) {

      $CLICSHOPPING_Blog = Registry::get('Blog');

      $blog_content_linkedin_button =  '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . CLICSHOPPING::link(null, '&Blog&Content&blog_content_id=' . $CLICSHOPPING_Blog->getId()) . '" target="_blank" rel="noopener"><img src="' . $CLICSHOPPING_Template->getDirectoryTemplateImages() . 'icons/social_bookmarks/' . $this->icon . '" border="0" title="' . HTML::outputProtected($this->public_title) . '"  alt="' . HTML::outputProtected($this->public_title) . '"/></a>';

      $blog_content_description_content = '<!-- Start blog_content_linkedin -->' . "\n";

      ob_start();
      require_once($CLICSHOPPING_Template->getTemplateModules( $this->group . '/content/blog_content_linkedin'));
      $blog_content_description_content .= ob_get_clean();

      $blog_content_description_content .= '<!-- end blog_content_linkedin  -->' . "\n";

      $CLICSHOPPING_Template->addBlock($blog_content_description_content, $this->group);

      } // php_self
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function getIcon() {
      return $this->icon;
    }

    public function getPublicTitle() {
      return $this->public_title;
    }

    public function check() {
      return defined('MODULE_BLOG_CONTENT_LINKEDIN_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');



      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_LINKEDIN_STATUS',
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
          'configuration_key' => 'MODULE_BLOG_CONTENT_LINKEDIN_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Affiche le bouton à gauche ou à droite<br><br><i>(- Valeur None = Aucune <br>- Valeur Left = Gauche <br>- Valeur Right = Droite)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'none\', \'right\', \'left\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_CONTENT_LINKEDIN_SORT_ORDER',
          'configuration_value' => '270',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '3',
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
      return array('MODULE_BLOG_CONTENT_LINKEDIN_STATUS',
                   'MODULE_BLOG_CONTENT_LINKEDIN_POSITION',
                   'MODULE_BLOG_CONTENT_LINKEDIN_SORT_ORDER'
                   );
    }
  }
