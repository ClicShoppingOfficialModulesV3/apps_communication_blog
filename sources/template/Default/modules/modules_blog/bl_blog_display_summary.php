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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Communication\Blog\Classes\Shop\Blog;
  use ClicShopping\Apps\Communication\Blog\Classes\Shop\BlogCategories;

  class bl_blog_display_summary {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {

      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_blog_display_summary_title');
      $this->description = CLICSHOPPING::getDef('module_blog_display_summary_description');

      if (defined('MODULE_BLOG_DISPLAY_SUMMARY_STATUS')) {
        $this->sort_order = MODULE_BLOG_DISPLAY_SUMMARY_SORT_ORDER;
        $this->enabled = (MODULE_BLOG_DISPLAY_SUMMARY_STATUS == 'True');
      }
    }

    public function execute() {

      if (isset($_GET['Blog']) && isset($_GET['Categories'])) {

        $CLICSHOPPING_Template = Registry::get('Template');

        $blog_content = '<!-- Blog Summary start -->' . "\n";
        $blog_content .= '<div class="clearfix"></div>';
        $blog_content .= '<div class="separator"></div>';
        $blog_content .= '<div class="contentContainer">';
        $blog_content .= '<div class="contentText">';

        if (MODULE_BLOG_DISPLAY_SUMMARY_MAX_DISPLAY != 0) {

// nbr of column to display  boostrap
          $bootstrap_column = (int)MODULE_BLOG_DISPLAY_SUMMARY_COLUMNS;

          $Qblog = BlogCategories::getData();

          $listingTotalRow = $Qblog->getPageSetTotalRows();

          if ($listingTotalRow > 0) {

            if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
              $blog_content .= '<div>';
              $blog_content .= '<div class="col-md-6 pagenumber hidden-xs">';
              $blog_content .=  $Qblog->getPageSetLabel(CLICSHOPPING::getDef('text_display_number_of_items'));
              $blog_content .= '</div>';
              $blog_content .= '<div class="col-md-6">';
              $blog_content .= '<div class="float-end pagenav">'.  $Qblog->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y')), 'Shop') . '</div>';
              $blog_content .= '<div class="text-end">' . CLICSHOPPING::getDef('text_result_page') . '</div>';
              $blog_content .= '</div>';
              $blog_content .= '</div>';
              $blog_content .= '<div class="separator"></div>';
              $blog_content .= '<div class="clearfix"></div>';
            }

            $blog_content .= '<div class="modulesBlogContentsBlogDisplaySummary">';

            $blog_content .= '<div class="d-flex flex-wrap">';


// Template define
            $filename = $CLICSHOPPING_Template->getTemplateModulesFilename($this->group .'/template_html/' . MODULE_BLOG_DISPLAY_SUMMARY_TEMPLATE);

// display the short description for a product and attributes
             $blog_short_description = (int)MODULE_BLOG_DISPLAY_SUMMARY_SHORT_DESCRIPTION;

            while ($Qblog->fetch()) {
              $blog_id = (int)$Qblog->valueInt('blog_content_id');
              $description = $Qblog->value('blog_content_description_summary');
              $date = DateTime::toShort($Qblog->value('blog_content_date_added'));

              $author = '';
              $author_content = HTML::outputProtected($Qblog->value('blog_content_author'));

              if (!empty($author_content)) {
                $author = CLICSHOPPING::getDef('module_blog_display_summary_author') . ' ' . HTML::link(CLICSHOPPING::link(null, 'Blog&Categories&blog_keywords=' . $Qblog->value('blog_content_author')) . '" rel="author"', $author_content);
              }

              $short_description = Blog::displayBlogShortDescription($description, $blog_short_description);
              $blog_content_name = HTML::link(CLICSHOPPING::link(null, 'Blog&Content&blog_content_id=' . (int)$blog_id), $Qblog->value('blog_content_name'));

// 9- Template call
              if (is_file($filename)) {
                ob_start();
                require($filename);
                $blog_content .= ob_get_clean();
              } else {
                echo  '<div class="alert alert-warning text-center" role="alert">' . CLICSHOPPING::getDef('template_does_not_exist') . '</div>';
                exit;
              }
            } // end while

            $blog_content .= '</div>';
            $blog_content .= '</div>';

          } else {
            $blog_content .= '<div class="contentText" style="padding-top:20px;">';
            $blog_content .= '<div class="alert alert-info text-center" role="alert">';
            $blog_content .= '<div>' . CLICSHOPPING::getDef('text_no_blog') . '</div>';
            $blog_content .= '<div class="separator"></div>';
            $blog_content .= '</div>';
            $blog_content .= '</div>';
          }

          $blog_content .= '</div>';
          $blog_content .= '<div class="clearfix"></div>';
          $blog_content .= '<div class="contentText">';

          if (($listingTotalRow > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
            if ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) {
              $blog_content .= '<div class="clearfix"></div>';
              $blog_content .= '<div style="padding-top:10px;"></div>';
              $blog_content .= '<div>';
              $blog_content .= '<div class="col-md-6 pagenumber hidden-xs">';
              $blog_content .=  $Qblog->getPageSetLabel(CLICSHOPPING::getDef('text_display_number_of_items'));
              $blog_content .= '</div>';
              $blog_content .= '<div class="col-md-6 float-end">';
              $blog_content .= '<span class="float-end pagenav">'.  $Qblog->getPageSetLinks(CLICSHOPPING::getAllGET(array('page', 'info', 'x', 'y')), 'Shop') . '</span>';
              $blog_content .= '<span class="text-end">' . CLICSHOPPING::getDef('text_result_page') . '</span>';
              $blog_content .= '</div>';
              $blog_content .= '</div>';
              $blog_content .= '<div class="clearfix"></div>';
            }
          }

          $blog_content .= '</div>';

        } else {

          $blog_content .= '<div class="contentText">';
          $blog_content .= '<div class="alert alert-info" role="alert">' . CLICSHOPPING::getDef('text_no_blog') . '</div>';
          $blog_content .= '</div>';
        } // max display product

        $blog_content .= '</div>';

       $blog_content .= '<!-- Blog Summary End -->' . "\n";

       $CLICSHOPPING_Template->addBlock($blog_content, $this->group);

      } // php_self
    } // function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_BLOG_DISPLAY_SUMMARY_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_BLOG_DISPLAY_SUMMARY_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Quel type de template souhaitez-vous voir affiché concernant le blog ?',
          'configuration_key' => 'MODULE_BLOG_DISPLAY_SUMMARY_TEMPLATE',
          'configuration_value' => 'template_bootstrap_column_1.php',
          'configuration_description' => 'Veuillez indiquer le type de template que vous souhaitez voir affiché concernant les articles.<br /><br /><b>Note</b><br /> - Si vous avez opté pour une configuration en ligne, veuillez choisir un type de nom de template comme <u>template_line</u>.<br /><br /> - Si vous avez opté pour un affichage en colonne, veuillez choisir un type de nom de template comme <u>template_column</u> puis veuillez configurer le nombre de colonnes.<br />',          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => 'clic_cfg_set_multi_template_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Indiquer le nombre d\'articles à afficher',
          'configuration_key' => 'MODULE_BLOG_DISPLAY_SUMMARY_MAX_DISPLAY',
          'configuration_value' => '6',
          'configuration_description' => 'Veuillez indiquer le nombre maximum d\'article à afficher.',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le nombre de colonnes que vous souhaitez voir affiché pour les articles du  blog ?',
          'configuration_key' => 'MODULE_BLOG_DISPLAY_SUMMARY_COLUMNS',
          'configuration_value' => '6',
          'configuration_description' => 'Veuillez indiquer le nombre de colonnes de produit à afficher par ligne.<br><br><i>- Entre 1 et 12</i><br />',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez vous afficher une description courte des articles du blog ?',
          'configuration_key' => 'MODULE_BLOG_DISPLAY_SUMMARY_SHORT_DESCRIPTION',
          'configuration_value' => '0',
          'configuration_description' => 'Veuillez indiquer la longueur de la description.<br><br><i>- 0 pour aucune description<br>- 50 pour les 50 premiers caractères</i><br />',
          'configuration_group_id' => '6',
          'sort_order' => '5',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_BLOG_DISPLAY_SUMMARY_SORT_ORDER',
          'configuration_value' => '100',
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
      return array ('MODULE_BLOG_DISPLAY_SUMMARY_STATUS',
                    'MODULE_BLOG_DISPLAY_SUMMARY_TEMPLATE',
                    'MODULE_BLOG_DISPLAY_SUMMARY_MAX_DISPLAY',
                    'MODULE_BLOG_DISPLAY_SUMMARY_COLUMNS',
                    'MODULE_BLOG_DISPLAY_SUMMARY_SHORT_DESCRIPTION',
                    'MODULE_BLOG_DISPLAY_SUMMARY_SORT_ORDER'
                  );
    }
  }