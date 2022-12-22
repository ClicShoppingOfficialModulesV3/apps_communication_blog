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

  class bc_blog_content_facebook_comment {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_content_facebook_comment_title');
      $this->description = CLICSHOPPING::getDef('module_blog_content_facebook_content_description');

      if ( defined('MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_STATUS')) {
        $this->sort_order = MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_STATUS == 'True');

        $this->height = MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_HEIGHT;
        $this->width = MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_WIDTH;
      }
    }

    public function execute() {

      if ( isset($_GET['Blog']) &&  isset($_GET['Content']) && !empty($_GET['blog_content_id'])) {

        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Blog = Registry::get('Blog');

        if (!empty($_GET['blog_content_id'])) {

          $footer ='<script>';
          $footer .='(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; ';
          $footer .='if (d.getElementById(id)) return; ';
          $footer .='js = d.createElement(s); js.id = id; ';
          $footer .='js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId='. MODULE_BLOG_CONTENT_FACEBOOK_ID .'"; ';
          $footer .='fjs.parentNode.insertBefore(js, fjs); ';
          $footer .='}(document, \'script\', \'facebook-jssdk\')); ';
          $footer .='</script>' . "\n";

          $CLICSHOPPING_Template->addBlock($footer, 'footer_scripts');

          $blog_content_description_content ='<!--Facebook comment start-->'."\n";

          $link = urldecode(CLICSHOPPING::link(null, '&Blog&Content&blog_content_id=' . $CLICSHOPPING_Blog->getId()));

          ob_start();
          require_once($CLICSHOPPING_Template->getTemplateModules( $this->group . '/content/facebook_comment'));
          $blog_content_description_content .= ob_get_clean();

          $blog_content_description_content .='<!--Facebook comment end-->'."\n";

          $CLICSHOPPING_Template->addBlock($blog_content_description_content, $this->group);
        }
      }
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
      return defined('MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez insérer votre ID Facebook',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_ID',
          'configuration_value' => '',
          'configuration_description' => 'Affiche les recommandations des internautes de Facebook<br />Ce module ne sera fonctionnel que si vous mettez votre ID Facebook (voir sur Facebook)<br />https://developers.facebook.com/tools/explorer?method=GET',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le nombre de commentaires affich&eacutes',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_NUMBER_POST',
          'configuration_value' => '5',
          'configuration_description' => 'Veuillez indiquer le nombre de commentaires qui seront affichés',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer la hauteur du tableau',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_HEIGHT',
          'configuration_value' => '300',
          'configuration_description' => 'Veuillez entrer la hauteur du tableau',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer la largeur du tableau',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_WIDTH',
          'configuration_value' => '300',
          'configuration_description' => 'Veuillez entrer la largeur du tableau',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_SORT_ORDER',
          'configuration_value' => '60',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '6',
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
      return ['MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_STATUS',
             'MODULE_BLOG_CONTENT_FACEBOOK_ID',
             'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_NUMBER_POST',
             'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_HEIGHT',
             'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_WIDTH',
             'MODULE_BLOG_CONTENT_FACEBOOK_COMMENT_SORT_ORDER'
            ];
    }
  }

