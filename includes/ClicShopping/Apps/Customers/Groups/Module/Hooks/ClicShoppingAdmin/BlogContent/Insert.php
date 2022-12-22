<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\BlogContent;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

  class Insert implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Groups')) {
        Registry::set('Groups', new GroupsApp());
      }

      $this->app = Registry::get('Groups');
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_BLOG_BL_STATUS') || CLICSHOPPING_APP_BLOG_BL_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['Insert'])) {
        if (isset($_POST['customers_group_id'])) {
          $customers_group_id = HTML::sanitize($_POST['customers_group_id']);

          $QCustomersGroup = $this->app->db->prepare('select blog_content_id
                                                       from :table_blog_content
                                                       order by blog_content_id desc
                                                       limit 1
                                                      ');
          $QCustomersGroup->execute();

          $sql_data_array = ['customers_group_id' => (int)$customers_group_id];

          $this->app->db->save('blog_content', $sql_data_array, ['blog_content_id' => (int)$QCustomersGroup->valueInt('blog_content_id')]);
        }
      }
    }
  }