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

  namespace ClicShopping\Apps\Communication\Blog\Sites\Shop\Pages\Blog\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Content extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Blog = Registry::get('Blog');

// templates
      $this->page->setFile('content.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('blog_content');
//language
      $CLICSHOPPING_Language->loadDefinitions('blog_content');

      $CLICSHOPPING_Breadcrumb->add($CLICSHOPPING_Blog->getBlogContentName(), CLICSHOPPING::link(null, 'Blog&Content&blogContentId=' . (int)$_GET['blogContentId']));
    }
  }
