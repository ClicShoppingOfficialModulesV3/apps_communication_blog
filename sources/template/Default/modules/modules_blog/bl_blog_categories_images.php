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

  class bl_blog_categories_images {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_categories_images_title');
      $this->description = CLICSHOPPING::getDef('module_blog_categories_images_description');

      if (defined('MODULE_BLOG_CATEGORIES_IMAGES_STATUS')) {
        $this->sort_order = MODULE_BLOG_CATEGORIES_IMAGES_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_CATEGORIES_IMAGES_STATUS == 'True');
      }
    }

    public function execute() {
      global $bPath;

      if (isset($_GET['Blog']) && isset($_GET['Categories']) && empty($bPath)) {

        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Customer = Registry::get('Customer');
        $CLICSHOPPING_Db = Registry::get('Db');
        $CLICSHOPPING_Language = Registry::get('Language');

        $bootstrap_column = (int)MODULE_BLOG_CATEGORIES_IMAGES_BOX_COLUMNS;

        if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {

          $Qcategories = $CLICSHOPPING_Db->prepare('select c.blog_categories_id,
                                                           c.blog_categories_image,
                                                           cd.blog_categories_name
                                                   from :table_blog_categories c,
                                                        :table_blog_categories_description cd
                                                   where c.parent_id = 0
                                                   and c.blog_categories_id = cd.blog_categories_id
                                                   and cd.language_id = :language_id
                                                   and (c.customers_group_id = :customers_group_id or c.customers_group_id = 99)
                                                   order by c.sort_order,
                                                            cd.blog_categories_name
                                                 ');
          $Qcategories->bindValue(':language_id',  (int)$CLICSHOPPING_Language->getId() );
          $Qcategories->bindValue(':customers_group_id',(int)$CLICSHOPPING_Customer->getCustomersGroupID());

          $Qcategories->execute();

        } else {

          $Qcategories = $CLICSHOPPING_Db->prepare('select c.blog_categories_id,
                                                           c.blog_categories_image,
                                                           cd.blog_categories_name
                                                   from :table_blog_categories c,
                                                        :table_blog_categories_description cd
                                                   where c.parent_id = 0
                                                   and c.blog_categories_id = cd.blog_categories_id
                                                   and cd.language_id = :language_id
                                                   and (c.customers_group_id = 0 or c.customers_group_id = 99)
                                                   order by c.sort_order,
                                                            cd.blog_categories_name
                                                 ');
          $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

          $Qcategories->execute();
        }

        if ($Qcategories->rowCount() > 0) {
          while ($Qcategories->fetch() )  {

            $categories_id = $Qcategories->valueInt('blog_categories_id');

            $categories_data[$categories_id] = ['id' => $categories_id,
                                                'name' => $Qcategories->value('blog_categories_name'),
                                                'image' => $Qcategories->value('blog_categories_image')
                                               ];
          } //while ($categories

// Set up the box in the selected style
          if (count($categories_data) > 0) { // Show only if we have categories in the array

// Show the categories in a fixed grid (# of columns is set in Admin)
            $categories_content = '<!-- Categories blog Images start -->' . "\n";
            $categories_content .= '<div class="separator"></div>';

            $categories_content .= '<div class="text-center">';
            $categories_content .= '<div class="d-flex flex-wrap">';

            foreach ($categories_data as $category) {
              $images = HTML::link(CLICSHOPPING::link(null, 'Blog&Categories&current=' . $category['id']), HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . $category['image'], HTML::outputProtected($category['name']), (int)MODULE_BLOG_CATEGORIES_IMAGES_WIDTH, (int)MODULE_BLOG_CATEGORIES_IMAGES_HEIGHT, null, true));
              $link = HTML::link( CLICSHOPPING::link(null, 'Blog&Categories&current=' . $category['id']), $category['name']);

              ob_start();
              require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/blog_categories_images'));
              $categories_content .= ob_get_clean();
            } //foreach ($categories_data

           $categories_content .= '</div>';
           $categories_content .= '</div>' . "\n";
           $categories_content .= '<!-- Categories blog Images end -->' . "\n";

           $CLICSHOPPING_Template->addBlock($categories_content, $this->group);
          } // end count
        }
      }
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_BLOG_CATEGORIES_IMAGES_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_CATEGORIES_IMAGES_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Combien de colonnes souhaitez-vous afficher ?',
          'configuration_key' => 'MODULE_BLOG_CATEGORIES_IMAGES_BOX_COLUMNS',
          'configuration_value' => '6',
          'configuration_description' => '- Entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the width of the image',
          'configuration_key' => 'MODULE_BLOG_CATEGORIES_IMAGES_WIDTH',
          'configuration_value' => '',
          'configuration_description' => 'Displays a size delimited in width (resizing)',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please indicate the height of the image',
          'configuration_key' => 'MODULE_BLOG_CATEGORIES_IMAGES_HEIGHT',
          'configuration_value' => '',
          'configuration_description' => 'Displays a size delimited in height (resizing)',
          'configuration_group_id' => '6',
          'sort_order' => '6',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Would you like to display images ?',
          'configuration_key' => 'MODULE_BLOG_CATEGORIES_IMAGES_SHOW_IMAGE',
          'configuration_value' => 'True',
          'configuration_description' => 'Affiche les petites images des catégories du blog<br><br><i>(Valeur True = Oui - Valeur False = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous afficher les noms des catégories du blog ?',
          'configuration_key' => 'MODULE_BLOG_CATEGORIES_IMAGES_SHOW_NAME',
          'configuration_value' => 'True',
          'configuration_description' => 'Affiche le nom de la catégorie du blog<br><br><i>(Valeur True = Oui - Valeur False = Non)</i>',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_CATEGORIES_IMAGES_SORT_ORDER',
          'configuration_value' => '20',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '5',
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
        'MODULE_BLOG_CATEGORIES_IMAGES_STATUS',
        'MODULE_BLOG_CATEGORIES_IMAGES_BOX_COLUMNS',
        'MODULE_BLOG_CATEGORIES_IMAGES_WIDTH',
        'MODULE_BLOG_CATEGORIES_IMAGES_HEIGHT',
        'MODULE_BLOG_CATEGORIES_IMAGES_SHOW_IMAGE',
        'MODULE_BLOG_CATEGORIES_IMAGES_SHOW_NAME',
        'MODULE_BLOG_CATEGORIES_IMAGES_SORT_ORDER'
      );
    }
  }
