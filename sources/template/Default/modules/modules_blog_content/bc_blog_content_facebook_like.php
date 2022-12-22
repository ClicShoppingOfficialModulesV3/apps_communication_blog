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

  class bc_blog_content_facebook_like {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_content_facebook_like_title');
      $this->public_title = CLICSHOPPING::getDef('module_blog_content_facebook_like_public_title');
      $this->description = CLICSHOPPING::getDef('module_blog_content_facebook_like_description');

      if ( defined('MODULE_BLOG_CONTENT_FACEBOOK_LIKE_STATUS')) {
        $this->sort_order = MODULE_BLOG_CONTENT_FACEBOOK_LIKE_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_CONTENT_FACEBOOK_LIKE_STATUS == 'True');
      }
    }

    public function execute() {

      $CLICSHOPPING_Template = Registry::get('Template');

      if ( isset($_GET['Blog']) && isset($_GET['Content']) && !empty($_GET['blog_content_id'])) {

        $CLICSHOPPING_Blog = Registry::get('Blog');

        $style = (MODULE_BLOG_CONTENT_FACEBOOK_LIKE_STYLE == 'Standard') ? 'standard' : 'button_count';
        $faces = (MODULE_BLOG_CONTENT_FACEBOOK_LIKE_FACES == 'True') ? 'true' : 'false';
        $width = MODULE_BLOG_CONTENT_FACEBOOK_LIKE_WIDTH;
        $height =	MODULE_BLOG_CONTENT_FACEBOOK_LIKE_HEIGHT;
        $action = (MODULE_BLOG_CONTENT_FACEBOOK_LIKE_VERB == 'Like') ? 'like' : 'recommend';
        $scheme = (MODULE_BLOG_CONTENT_FACEBOOK_LIKE_SCHEME == 'Light') ? 'light' : 'dark';

        $blog_content_facebook_like_button =  '<iframe align="left" src="http://www.facebook.com/plugins/like.php?href=' . CLICSHOPPING::link(null, '&Blog&Content&blog_content_id=' . $CLICSHOPPING_Blog->getId()) . '&amp;layout=' . $style . '&amp;show_faces=' . $faces . '&amp;width=' . $width . '&amp;action=' . $action . '&amp;colorscheme=' . $scheme . '&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . $width . 'px; height:' . $height . 'px;" allowTransparency="true"></iframe>';

        $blog_content_description_content = '<!-- Start blog_content_facebook like -->' . "\n";

        ob_start();
        require_once($CLICSHOPPING_Template->getTemplateModules( $this->group . '/content/facebook_like'));
        $blog_content_description_content .= ob_get_clean();

        $blog_content_description_content .= '<!-- end Start blog_content_facebook like  -->' . "\n";

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
      return defined('MODULE_BLOG_CONTENT_FACEBOOK_LIKE_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Style du layout',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_STYLE',
          'configuration_value' => 'Standard',
          'configuration_description' => 'Détermine la taille et le nombre  à coté du bouton',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Standard\', \'Button Count\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Montrer les visages ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_FACES',
          'configuration_value' => 'False',
          'configuration_description' => 'Montre les profils en desssous du bouton ',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Taille en pixel',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_WIDTH',
          'configuration_value' => '200',
          'configuration_description' => 'la taille de la iframe en pixel',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Hauteur en pixel',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_HEIGHT',
          'configuration_value' => '50',
          'configuration_description' => 'la hauteur de la iframe en pixel.',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Verbe à afficher',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_VERB',
          'configuration_value' => 'Like',
          'configuration_description' => 'le verbe qui sera afficher dans le bouton',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Like\', \'Recommend\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Couleur du bouton',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_SCHEME',
          'configuration_value' => 'Light',
          'configuration_description' => 'La couleur du bouton',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'Light\', \'Dark\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'A quel endroit souhaitez-vous afficher les tags en relation avec les produits ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Affiche le bouton à gauche ou à droite<br><br><i>(- Valeur None = Aucune <br>- Valeur Left = Gauche <br>- Valeur Right = Droite)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '7',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'none\', \'right\', \'left\'))',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_SORT_ORDER',
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
      return ['MODULE_BLOG_CONTENT_FACEBOOK_LIKE_STATUS',
              'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_STYLE',
              'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_FACES',
              'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_WIDTH',
              'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_HEIGHT',
              'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_VERB',
              'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_SCHEME',
              'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_POSITION',
              'MODULE_BLOG_CONTENT_FACEBOOK_LIKE_SORT_ORDER'
             ];
    }
  }
