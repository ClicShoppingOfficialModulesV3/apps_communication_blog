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

  class bc_blog_content_twitter_button {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {

      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_content_twitter_button_title');
      $this->public_title = CLICSHOPPING::getDef('module_blog_content_twitter_button_public_title');
      $this->description = CLICSHOPPING::getDef('module_blog_content_tag_twitter_button_description');

      if ( defined('MODULE_BLOG_CONTENT_TWITTER_BUTTON_STATUS')) {
        $this->sort_order = MODULE_BLOG_CONTENT_TWITTER_BUTTON_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_CONTENT_TWITTER_BUTTON_STATUS == 'True');
      }
    }

    public function execute() {

      if ( isset($_GET['Blog']) &&  isset($_GET['Content']) && !empty($_GET['blog_content_id'])) {

        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Blog = Registry::get('Blog');

         $params = array('url=' . CLICSHOPPING::link(null, 'Blog&Content&blog_content_id=' . $CLICSHOPPING_Blog->getId()));
         $params[] = 'related=' . MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT . ((strlen(MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT_DESC) > 0) ? ':' . MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT_DESC : '');

        if ( strlen(MODULE_BLOG_CONTENT_TWITTER_BUTTON_ACCOUNT) > 0 ) {
          $params[] = 'via=' . urlencode(MODULE_BLOG_CONTENT_TWITTER_BUTTON_ACCOUNT);
        }

        if ( strlen(MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT) > 0 ) {
          $params[] = 'related=' . urlencode(MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT);
        }

        if ( MODULE_BLOG_CONTENT_TWITTER_BUTTON_COUNT_POSITION == 'Vertical' ) {
          $params[] = 'count=vertical';
        } elseif ( MODULE_BLOG_CONTENT_TWITTER_BUTTON_COUNT_POSITION == 'None' ) {
          $params[] = 'count=none';
        }

        $blog_content_twitter_button =  '<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script><a href="http://twitter.com/share?' . $params . '" target="_blank" rel="noopener" class="twitter-share-button">' . HTML::outputProtected($this->public_title) . '</a>';

        $blog_content_description_content = '<!-- Start blog_content_google button +1 -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules( $this->group . '/content/blog_content_twitter_button'));
        $blog_content_description_content .= ob_get_clean();

        $blog_content_description_content .= '<!-- end blog_content_content_google button +1  -->' . "\n";

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
      return defined('MODULE_BLOG_CONTENT_TWITTER_BUTTON_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TWITTER_BUTTON_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Propriétaire du compte Twitter',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TWITTER_BUTTON_ACCOUNT',
          'configuration_value' => '',
          'configuration_description' => 'Veuillez indiquez Le speudo du propriétaire du compte sur lequel le tweet sera envoyé',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Autre compte utilisateur recommandé à suivre',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT',
          'configuration_value' => '',
          'configuration_description' => 'Indiquez un autre compte utilisateur à suivre et recommandé au client',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Description concernant le compte Twitter',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT_DESC',
          'configuration_value' => '',
          'configuration_description' => 'indiquer une description concernant le compte Twitter',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Position du compteur de Tweet',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TWITTER_BUTTON_COUNT_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Veuillez indiquer la postion du compteur de tweet',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Horizontal\', \'Vertical\', \'None\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'A quel endroit souhaitez-vous afficher les tags en relation avec les produits ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TWITTER_BUTTON_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Affiche le bouton à gauche ou à droite<br><br><i>(- Valeur None = Aucune <br>- Valeur Left = Gauche <br>- Valeur Right = Droite)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'float-none\', \'float-end\', \'float-start\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_CONTENT_TWITTER_BUTTON_SORT_ORDER',
          'configuration_value' => '300',
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
      return array('MODULE_BLOG_CONTENT_TWITTER_BUTTON_STATUS',
                   'MODULE_BLOG_CONTENT_TWITTER_BUTTON_ACCOUNT',
                   'MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT',
                   'MODULE_BLOG_CONTENT_TWITTER_BUTTON_RELATED_ACCOUNT_DESC',
                   'MODULE_BLOG_CONTENT_TWITTER_BUTTON_COUNT_POSITION',
                   'MODULE_BLOG_CONTENT_TWITTER_BUTTON_POSITION',
                   'MODULE_BLOG_CONTENT_TWITTER_BUTTON_SORT_ORDER');
    }
  }
