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

  namespace ClicShopping\Apps\Communication\Blog\Module\Hooks\ClicShoppingAdmin\Categories;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Communication\Blog\Blog as BlogApp;

  class DeleteConfirm implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $template;

    public function __construct()
    {
      if (!Registry::exists('Blog')) {
        Registry::set('Blog', new BlogApp());
      }

      $this->app = Registry::get('Blog');
      $this->template = Registry::get('TemplateAdmin');
    }

    private function delete($category_id)
    {

      $QcategoriesImage = $this->db->prepare('select categories_image
                                               from :table_categories
                                               where categories_id = :categories_id
                                             ');
      $QcategoriesImage->bindInt(':categories_id', (int)$category_id);

      $QcategoriesImage->execute();

// Controle si l'image est utilise sur une autre categorie
      $QduplicateImage = $this->db->prepare('select count(*) as total
                                             from :table_categories
                                             where categories_image = :categories_image
                                             ');
      $QduplicateImage->bindValue(':categories_image', $QcategoriesImage->value('categories_image'));

      $QduplicateImage->execute();

// Controle si l'image est utilise sur une autre categorie du blog
      $QduplicateBlogImage = $this->db->prepare('select count(*) as total
                                                  from :table_blog_categories
                                                  where blog_categories_image = :blog_categories_image
                                                ');
      $QduplicateBlogImage->bindValue(':blog_categories_image', $QcategoriesImage->value('categories_image'));

      $QduplicateBlogImage->execute();

// Controle si l'image est utilise sur les descriptions d'un blog
      $QduplicateImageBlogCategoriesDescription = $this->db->prepare('select count(*) as total
                                                                     from :table_blog_categories_description
                                                                     where blog_categories_description like :blog_categories_description
                                                                    ');
      $QduplicateImageBlogCategoriesDescription->bindValue(':blog_categories_description', '%' . $QcategoriesImage->value('categories_image') . '%');

      $QduplicateImageBlogCategoriesDescription->execute();

// Controle si l'image est utilise le visuel d'un produit
      $QduplicateImageCatalog = $this->db->prepare('select count(*) as total
                                                    from :table_products
                                                    where products_image = :products_image
                                                    or products_image_zoom = :products_image_zoom
                                                   ');
      $QduplicateImageCatalog->bindValue(':products_image', $QcategoriesImage->value('categories_image'));
      $QduplicateImageCatalog->bindValue(':products_image_zoom', $QcategoriesImage->value('categories_image'));

      $QduplicateImageCatalog->execute();

// Controle si l'image est utilise sur les descriptions d'un produit
      $QduplicateImageProductDescription = $this->db->prepare('select count(*) as total
                                                               from :table_products_description
                                                               where products_description like :blog_categories_description
                                                              ');
      $QduplicateImageProductDescription->bindValue(':blog_categories_description', '%' . $QcategoriesImage->value('categories_image') . '%');

      $QduplicateImageProductDescription->execute();


      if (($QduplicateImage->valueInt('total') < 2) &&
        ($QduplicateBlogImage->valueInt('total') == 0) &&
        ($QduplicateImageBlogCategoriesDescription->valueInt('total') == 0) &&
        ($QduplicateImageCatalog->valueInt('total') == 0) &&
        ($QduplicateImageProductDescription->valueInt('total') == 0) &&
        ($QduplicateImageBanners->valueInt('total') == 0) &&
        ($QduplicateImageManufacturers->valueInt('total') == 0) &&
        ($QduplicateImageSuppliers->valueInt('total') == 0)) {

// delete categorie image
        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $QcategoriesImage->value('categories_image'))) {
          @unlink($this->template->getDirectoryPathTemplateShopImages() . $QcategoriesImage->value('categories_image'));
        }
      }
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_BLOG_BL_STATUS') || CLICSHOPPING_APP_BLOG_BL_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['DeleteConfirm'])) {
        if (isset($_GET['categories_id'])) {
          $category_id = HTML::sanitize($_GET['categories_id']);
          $this->delete($category_id);
        }
      }
    }
  }