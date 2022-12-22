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

  namespace ClicShopping\Apps\Communication\Blog\Sites\Shop\Pages\Blog;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Communication\Blog\Classes\Shop\Blog as BlogApp;

  class Blog extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      global $CLICSHOPPING_Blog;

      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_template = Registry::get('Template');

      $CLICSHOPPING_Blog = new BlogApp();
      Registry::set('Blog', $CLICSHOPPING_Blog);

      $CLICSHOPPING_Blog = Registry::get('Blog');

      $this->app = $CLICSHOPPING_Blog;

//      $this->app->loadDefinitions('Sites/Shop/main');
    }
  }
