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

  namespace ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;

  class BlogAdmin
  {
    protected $language_id;
    protected $id;
    protected $categories_array;
    protected $index;
    protected $from;
    protected $blog_content_id;

    /**
     * the blog category name
     *
     * @param string $category_id , $language_id
     * @return string $category['blog_categories_name'],  name of the blog categorie
     * @access public
     *
     */
    public static function getBlogCategoryName($blog_category_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QcategoryBlog = $CLICSHOPPING_Db->prepare('select blog_categories_name
                                                  from :table_blog_categories_description
                                                  where blog_categories_id = :blog_categories_id
                                                  and language_id = :language_id
                                                ');
      $QcategoryBlog->bindInt(':blog_categories_id', (int)$blog_category_id);
      $QcategoryBlog->bindInt(':language_id', (int)$language_id);

      $QcategoryBlog->execute();

      $blog_category = $QcategoryBlog->fetch();

      return $blog_category['blog_categories_name'];
    }

    /**
     * the category description
     *
     * @param string $blog_category_id , $language_id
     * @return string $category['blog_categories_name'],  description of the blog categorie
     * @access public
     *
     */
    public static function getBlogCategoryDescription($blog_category_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QcategoryBlog = $CLICSHOPPING_Db->prepare('select blog_categories_description
                                                  from :table_blog_categories_description
                                                  where blog_categories_id = :blog_categories_id
                                                  and language_id = :language_id
                                                ');
      $QcategoryBlog->bindInt(':blog_categories_id', (int)$blog_category_id);
      $QcategoryBlog->bindInt(':language_id', (int)$language_id);

      $QcategoryBlog->execute();

      $blog_category = $QcategoryBlog->fetch();

      return $blog_category['blog_categories_description'];
    }


    /**
     * the category meta title title
     *
     * @param string $blog_category_id , $language_id
     * @return string $category['categories_head_title_tag'],  meta tile of the blog categorie
     * @access public
     *
     */

    public static function getBlogCategoriesHeadTitleTag($blog_category_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QcategoryBlog = $CLICSHOPPING_Db->prepare('select blog_categories_head_title_tag
                                                    from :table_blog_categories_description
                                                    where blog_categories_id = :blog_categories_id
                                                    and language_id = :language_id
                                                  ');
      $QcategoryBlog->bindInt(':blog_categories_id', (int)$blog_category_id);
      $QcategoryBlog->bindInt(':language_id', (int)$language_id);

      $QcategoryBlog->execute();

      $blog_category = $QcategoryBlog->fetch();

      return $blog_category['blog_categories_head_title_tag'];
    }


    /**
     * the category meta description
     *
     * @param string $blog_category_id , $language_id
     * @return string $category['categories_head_title_tag'],  meta description of the blog categorie
     * @access public
     *
     */

    public static function getBlogCategoriesHeadDescTag($blog_category_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QcategoryBlog = $CLICSHOPPING_Db->prepare('select blog_categories_head_desc_tag
                                                  from :table_blog_categories_description
                                                  where blog_categories_id = :language_id
                                                  and language_id = :language_id
                                                ');
      $QcategoryBlog->bindInt(':blog_categories_id', (int)$blog_category_id);
      $QcategoryBlog->bindInt(':language_id', (int)$language_id);

      $QcategoryBlog->execute();

      return $QcategoryBlog->value('blog_categories_head_desc_tag');
    }

    /**
     * the category meta keywords title
     *
     * @param string $blog_category_id , $language_id
     * @return string $category['categories_head_title_tag'],  meta keywords of the blog categorie
     * @access public
     *
     */
    public static function getBlogCategoriesHeadKeywordsTag($blog_category_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QcategoryBlog = $CLICSHOPPING_Db->prepare('select blog_categories_head_keywords_tag
                                                  from :table_blog_categories_description
                                                  where blog_categories_id = :language_id
                                                  and language_id = :language_id
                                                ');
      $QcategoryBlog->bindInt(':blog_categories_id', (int)$blog_category_id);
      $QcategoryBlog->bindInt(':language_id', (int)$language_id);

      $QcategoryBlog->execute();

      return $QcategoryBlog->value('blog_categories_head_keywords_tag');
    }


    /**
     *
     * Count how many products exist in a category
     * @param string $blog_category_id , $include_deactivated (FALSE OR TRUE)
     * @return string $blog_content_countt, the Count how many products exist in a category
     * @access public
     *
     */
    public static function getBlogContentInCategoryCount($blog_category_id, $include_deactivated = false)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $blog_content_count = 0;

      if ($include_deactivated) {
        $QblogContent = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                    from :table_blog_content p,
                                                         :table_blog_content_to_categories p2c
                                                    where p.blog_content_id = p2c.blog_content_id
                                                    and p2c.blog_categories_id = :blog_categories_id
                                                  ');
        $QblogContent->bindInt(':blog_categories_id', (int)$blog_category_id);

      } else {
        $QblogContent = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                    from :table_blog_content p,
                                                         :table_blog_content_to_categories p2c
                                                    where p.blog_content_id = p2c.blog_content_id
                                                    and p.blog_content_status = :blog_content_status
                                                    and p2c.blog_categories_id = :blog_categories_id
                                               ');
        $QblogContent->bindInt(':blog_content_status', '1');
        $QblogContent->bindInt(':blog_categories_id', (int)$blog_category_id);

      }

      $QblogContent->execute();
      $blog_content = $QblogContent->fetch();

      $blog_content_count += $blog_content['total'];

      $Qcategories = $CLICSHOPPING_Db->prepare('select blog_categories_id
                                                from :table_blog_categories
                                                where parent_id = :parent_id
                                               ');

      $Qcategories->bindInt(':parent_id', (int)$blog_category_id);
      $Qcategories->execute();


      if ($Qcategories->fetch() !== false) {
        while ($childs = $Qcategories->fetch()) {
          $blog_content_count += static::getBlogContentInCategoryCount($childs['blog_categories_id'], $include_deactivated);
        }
      }

      return $blog_content_count;
    }


    /**
     *
     * Count how many subcategories exist in a category
     * @param string $blog_category_id
     * @return string $blog_categories_count, the tchilds_in_blog_category_count
     * @access public
     *
     */
    public static function getChildsInBlogCategoryCount($blog_category_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $blog_categories_count = 0;

      $Qcategories = $CLICSHOPPING_Db->prepare('select blog_categories_id
                                                from :table_blog_categories
                                                where parent_id = :parent_id
                                               ');

      $Qcategories->bindInt(':parent_id', (int)$blog_category_id);
      $Qcategories->execute();

      while ($categories = $Qcategories->fetch()) {
        $blog_categories_count++;
        $blog_categories_count += static::getChildsInBlogCategoryCount($categories['blog_categories_id']);
      }

      return $blog_categories_count;
    }


    /**
     * blog category tree
     *
     * @param string $parent_id , $spacing, $exclude, $category_tree_array , $include_itself
     * @return string $category_tree_array, the tree of category
     * @access public
     *
     */
    public static function getBlogCategoryTree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!is_array($category_tree_array)) $category_tree_array = [];
      if ((count($category_tree_array) < 1) && ($exclude != '0')) $category_tree_array[] = array('id' => '0', 'text' => CLICSHOPPING::getDef('text_top'));

      if ($include_itself) {

        $Qcategory = $CLICSHOPPING_Db->prepare('select blog_categories_name
                                                from :table_blog_categories_description
                                                where language_id = :language_id
                                                and blog_categories_id = :parent_id
                                               ');

        $Qcategory->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
        $Qcategory->bindInt(':parent_id', (int)$parent_id);
        $Qcategory->execute();

        $category = $Qcategory->fetch();

        $category_tree_array[] = array('id' => $parent_id, 'text' => $category['blog_categories_name']);
      }

      $Qcategory = $CLICSHOPPING_Db->prepare('select c.blog_categories_id,
                                                     cd.blog_categories_name,
                                                     c.parent_id
                                              from :table_blog_categories c,
                                                   :table_blog_categories_description cd
                                              where c.blog_categories_id = cd.blog_categories_id
                                              and cd.language_id = :language_id
                                              and c.parent_id = :parent_id
                                              order by c.sort_order,
                                                       cd.blog_categories_name
                                            ');

      $Qcategory->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
      $Qcategory->bindInt(':parent_id', (int)$parent_id);
      $Qcategory->execute();

      while ($categories = $Qcategory->fetch()) {

        if ($exclude != $categories['blog_categories_id']) $category_tree_array[] = array('id' => $categories['blog_categories_id'],
          'text' => $spacing . $categories['blog_categories_name']);
        $category_tree_array = static::getBlogCategoryTree($categories['blog_categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
      }

      return $category_tree_array;
    }

    /**
     * blog remove blog categories
     *
     * @param string $blog_categories_id
     * @return string
     * @access public
     *
     */
    public static function getRemoveBlogCategory($blog_categories_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');


      $QcategoryBlogImage = $CLICSHOPPING_Db->prepare('select blog_categories_image
                                                      from :table_blog_categories
                                                      where blog_categories_id = :blog_categories_id
                                                     ');
      $QcategoryBlogImage->bindInt(':blog_categories_id', (int)$blog_categories_id);
      $QcategoryBlogImage->execute();

      $category_blog_image = $QcategoryBlogImage->fetch();

// Controle si l'image est utilise sur une autre categorie du blog
      $QduplicateBlogImage = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                        from :table_blog_categories
                                                        where blog_categories_image = :blog_categories_image
                                                       ');
      $QduplicateBlogImage->bindValue(':blog_categories_image', $category_blog_image['blog_categories_image']);
      $QduplicateBlogImage->execute();

      $duplicate_blog_image = $QduplicateBlogImage->fetch();


// Controle si l'image est utilise sur les descriptions d'un blog
      $QduplicateImageBlogCategoriesDescription = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                                             from :table_blog_categories_description
                                                                             where blog_categories_description like :blog_categories_image
                                                                           ');
      $QduplicateImageBlogCategoriesDescription->bindValue(':blog_categories_image', '%' . $category_blog_image['blog_categories_image'] . '%');


      $QduplicateImageBlogCategoriesDescription->execute();

      $duplicate_image_blog_categories_description = $QduplicateImageBlogCategoriesDescription->fetch();


// Controle si l'image est utilise sur une autre categorie
      $QduplicateImageCategories = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                              from :table_categories
                                                              where categories_image = :categories_image
                                                             ');
      $QduplicateImageCategories->bindValue(':categories_image', $category_blog_image['blog_categories_image']);
      $QduplicateImageCategories->execute();

      $duplicate_image_categories = $QduplicateImageCategories->fetch();

// Controle si l'image est utilise sur les descriptions d'une catégorie

      $QduplicateImageCategoriesDescription = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                                          from :table_categories_description
                                                                          where categories_description like :categories_description
                                                                         ');
      $QduplicateImageCategoriesDescription->bindValue(':categories_description', '%' . $category_blog_image['blog_categories_image'] . '%');


      $QduplicateImageCategoriesDescription->execute();

      $duplicate_image_categories_description = $QduplicateImageCategoriesDescription->fetch();

// Controle si l'image est utilise le visuel d'un produit


      $QduplicateImageProduct = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                           from :table_products
                                                           where products_image = :products_image
                                                           or products_image_zoom = :products_image_zoom
                                                           or products_image_medium = :products_image_medium
                                                         ');
      $QduplicateImageProduct->bindValue(':products_image', $category_blog_image['blog_categories_image']);
      $QduplicateImageProduct->bindValue(':products_image_zoom', $category_blog_image['blog_categories_image']);
      $QduplicateImageProduct->bindValue(':products_image_medium', $category_blog_image['blog_categories_image']);
      $QduplicateImageProduct->execute();

      $duplicate_image_product = $QduplicateImageProduct->fetch();


// Controle si l'image est utilise sur les descriptions d'un produit
      $QduplicateImageProductDescription = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                                      from :table_products_description
                                                                      where products_description like :products_description
                                                                     ');
      $QduplicateImageProductDescription->bindValue(':products_description', '%' . $category_blog_image['blog_categories_image'] . '%');

      $QduplicateImageProductDescription->execute();

      $duplicate_image_product_description = $QduplicateImageProductDescription->fetch();

      // Controle si l'image est utilisee sur une banniere
      $QduplicateImageBanners = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                            from :table_banners
                                                            where banners_image = :banners_image
                                                           ');
      $QduplicateImageBanners->bindValue(':banners_image', $category_blog_image['blog_categories_image']);
      $QduplicateImageBanners->execute();

      $duplicate_image_banners = $QduplicateImageBanners->fetch();

// Controle si l'image est utilisee sur les fabricants
      $QduplicateImageManufacturers = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                                  from :table_manufacturers
                                                                  where manufacturers_image = :manufacturers_image
                                                                ');
      $QduplicateImageManufacturers->bindValue(':manufacturers_image', $category_blog_image['blog_categories_image']);
      $QduplicateImageManufacturers->execute();

      $duplicate_image_manufacturers = $QduplicateImageManufacturers->fetch();

// Controle si l'image est utilisee sur les fournisseurs
      $QduplicateImageSuppliers = $CLICSHOPPING_Db->prepare('select count(*) as total
                                                             from :table_suppliers
                                                             where suppliers_image = :suppliers_image
                                                           ');
      $QduplicateImageSuppliers->bindValue(':suppliers_image', $category_blog_image['blog_categories_image']);
      $QduplicateImageSuppliers->execute();

      $duplicate_image_suppliers = $QduplicateImageSuppliers->fetch();

      if (($duplicate_blog_image['total'] < 2) &&
        ($duplicate_image_blog_categories_description['total'] == 0) &&
        ($duplicate_image_categories['total'] == 0) &&
        ($duplicate_image_categories_description['total'] == 0) &&
        ($duplicate_image_product['total'] == 0) &&
        ($duplicate_image_product_description['total'] == 0) &&
        ($duplicate_image_banners['total'] == 0) &&
        ($duplicate_image_manufacturers['total'] == 0) &&
        ($duplicate_image_suppliers['total'] == 0)) {

// delete categorie image
        if (is_file($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $category_blog_image['blog_categories_image'])) {
          @unlink($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $category_blog_image['blog_categories_image']);
        }
      }

      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                            from :table_blog_categories
                                            where blog_categories_id = :blog_categories_id
                                          ');
      $Qdelete->bindInt(':blog_categories_id', (int)$blog_categories_id);
      $Qdelete->execute();

      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                            from :table_blog_categories_description
                                            where blog_categories_id = :blog_categories_id
                                          ');
      $Qdelete->bindInt(':blog_categories_id', (int)$blog_categories_id);
      $Qdelete->execute();

      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                            from :table_blog_content_to_categories
                                            where blog_categories_id = :blog_categories_id
                                          ');
      $Qdelete->bindInt(':blog_categories_id', (int)$blog_categories_id);
      $Qdelete->execute();

      Cache::clear('blog_tree');
    }


    /**
     * blog path on categories generated
     *
     * @param string $id , $categories_array, $from, $index
     * @return string $$categories_array, an array on categories
     * @access public
     *
     */
    public static function getGenerateBlogCategoryPath($id, $from = 'category', $categories_array = '', $index = 0)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!is_array($categories_array)) $categories_array = [];

      if ($from == 'product') {

        $Qcategories = $CLICSHOPPING_Db->prepare('select blog_categories_id
                                                  from :table_blog_content_to_categories
                                                  where blog_content_id = :blog_content_id
                                                  ');
        $Qcategories->bindInt(':blog_content_id', (int)$id);
        $Qcategories->execute();


        while ($categories = $Qcategories->fetch()) {

          if ($categories['blog_categories_id'] == '0') {
            $categories_array[$index][] = array('id' => '0', 'text' => CLICSHOPPING::getDef('text_top'));
          } else {

            $Qcategories = $CLICSHOPPING_Db->prepare('select cd.blog_categories_name,
                                                               c.parent_id
                                                        from :table_blog_categories c,
                                                             :table_blog_categories_description cd
                                                       where c.blog_categories_id = :blog_categories_id
                                                       and c.blog_categories_id = cd.blog_categories_id
                                                       and cd.language_id = :language_id
                                                    ');
            $Qcategories->bindInt(':blog_categories_id', (int)$categories['blog_categories_id']);
            $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

            $Qcategories->execute();

            $category = $Qcategories->fetch();

            $categories_array[$index][] = array('id' => $categories['blog_categories_id'], 'text' => $category['blog_categories_name']);

            if ((!empty($category['parent_id'])) && ($category['parent_id'] != '0')) $categories_array = static::getGenerateBlogCategoryPath($category['parent_id'], 'category', $categories_array, $index);
            $categories_array[$index] = array_reverse($categories_array[$index]);
          }
          $index++;
        }
      } elseif ($from == 'category') {

        $Qcategories = $CLICSHOPPING_Db->prepare('select cd.blog_categories_name,
                                                           c.parent_id
                                                    from :table_blog_categories c,
                                                         :table_blog_categories_description cd
                                                    where c.blog_categories_id = :blog_categories_id
                                                    and c.blog_categories_id = cd.blog_categories_id
                                                    and cd.language_id = :language_id
                                                   ');
        $Qcategories->bindInt(':blog_categories_id', (int)$id);
        $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

        $Qcategories->execute();

        $category = $Qcategories->fetch();

        $categories_array[$index][] = array('id' => $id, 'text' => $category['blog_categories_name']);
        if ((!empty($category['parent_id'])) && ($category['parent_id'] != '0')) $categories_array = static::getGenerateBlogCategoryPath($category['parent_id'], 'category', $categories_array, $index);
      }

      return $categories_array;
    }

    /**
     * output generated blog category path
     *
     * @param string $id , $from,
     * @return string $calculated_category_path_string
     * @access public
     *
     */
    public static function getOutputGeneratedBlogCategoryPath($id, $from = 'category')
    {
      $calculated_category_path_string = '';
      $calculated_category_path = static::getGenerateBlogCategoryPath($id, $from);
      for ($i = 0, $n = count($calculated_category_path); $i < $n; $i++) {
        for ($j = 0, $k = count($calculated_category_path[$i]); $j < $k; $j++) {
          $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
        }
        $calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br />';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -6);

      if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = CLICSHOPPING::getDef('text_top');

      return $calculated_category_path_string;
    }


    /**
     * get generated blog category path
     *
     * @param string $id , $from,
     * @return string $calculated_category_path_string
     * @access public
     *
     */


    public static function getGeneratedBlogCategoryPathIds($id, $from = 'category')
    {
      $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');

      $calculated_category_path_string = '';
      $calculated_category_path = $CLICSHOPPING_CategoriesAdmin->getGenerateCategoryPath($id, $from);

      for ($i = 0, $n = count($calculated_category_path); $i < $n; $i++) {
        for ($j = 0, $k = count($calculated_category_path[$i]); $j < $k; $j++) {
          $calculated_category_path_string .= $calculated_category_path[$i][$j]['id'] . '_';
        }
        $calculated_category_path_string = substr($calculated_category_path_string, 0, -1) . '<br />';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -6);

      if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = CLICSHOPPING::getDef('text_top');

      return $calculated_category_path_string;
    }




// *******************************************
// Blog Content
// *******************************************/


    /**
     * Name of the blog content
     *
     * @param string $blog_content_id , $language_id
     * @return string $blog_content['blog_content_name'], name of the blog_content
     * @access public
     *
     */
    public static function getBlogContentName($blog_content_id, $language_id = 0)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QblogContent = $CLICSHOPPING_Db->prepare('select blog_content_name
                                                from :table_blog_content_description
                                                where blog_content_id = :blog_content_id
                                                and language_id = :language_id
                                              ');
      $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
      $QblogContent->bindInt(':language_id', (int)$language_id);
      $QblogContent->execute();

      $blog_content = $QblogContent->fetch();

      return $blog_content['blog_content_name'];
    }


    /**
     * Description Name
     *
     * @param string $blog_content_id , $language_id
     * @return string $blog_content['products_description'], description name
     * @access public
     *
     */
    public static function getBlogContentDescription($blog_content_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QblogContent = $CLICSHOPPING_Db->prepare('select blog_content_description
                                                  from :table_blog_content_description
                                                  where blog_content_id = :blog_content_id
                                                  and language_id = :language_id
                                                ');
      $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
      $QblogContent->bindInt(':language_id', (int)$language_id);
      $QblogContent->execute();

      $blog_content = $QblogContent->fetch();

      return $blog_content['blog_content_description'];
    }


    /**
     * Title Name of the submit
     *
     * @param string $blog_content_id , $language_id
     * @return string product['products_head_title_tag'], description name
     * @access public
     *
     */
    public static function getBlogContentHeadTitleTag($blog_content_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QblogContent = $CLICSHOPPING_Db->prepare('select blog_content_head_title_tag
                                                  from :table_blog_content_description
                                                  where blog_content_id = :blog_content_id
                                                  and language_id = :language_id
                                                ');
      $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
      $QblogContent->bindInt(':language_id', (int)$language_id);
      $QblogContent->execute();

      $blog_content = $QblogContent->fetch();

      return $blog_content['blog_content_head_title_tag'];
    }

    /**
     * Description Name
     *
     * @param string $blog_content_id , $language_id
     * @return string $blog_content['products_head_desc_tag'], description name
     * @access public
     *
     */
    public static function getBlogContentHeadDescTag($blog_content_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QblogContent = $CLICSHOPPING_Db->prepare('select blog_content_head_desc_tag
                                                from :table_blog_content_description
                                                where blog_content_id = :blog_content_id
                                                and language_id = :language_id
                                              ');
      $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
      $QblogContent->bindInt(':language_id', (int)$language_id);
      $QblogContent->execute();

      $blog_content = $QblogContent->fetch();

      return $blog_content['blog_content_head_desc_tag'];
    }

    /**
     * keywords Name
     *
     * @param string $blog_content_id , $language_id
     * @return string $blog_content['products_head_keywords_tag'], keywords name
     * @access public
     *
     */
    public static function getBlogContentHeadKeywordsTag($blog_content_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QblogContent = $CLICSHOPPING_Db->prepare('select blog_content_head_keywords_tag
                                                  from :table_blog_content_description
                                                  where blog_content_id = :blog_content_id
                                                  and language_id = :language_id
                                                ');
      $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
      $QblogContent->bindInt(':language_id', (int)$language_id);
      $QblogContent->execute();

      $blog_content = $QblogContent->fetch();

      return $blog_content['blog_content_head_keywords_tag'];
    }


    /**
     * Product Tag Name
     *
     * @param string $blog_content_id , $language_id
     * @return string $blog_content['blog_content_head_tag_product'], keywords name
     * @access public
     *
     */
    public static function getBlogContentTagProduct($blog_content_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QblogContent = $CLICSHOPPING_Db->prepare('select blog_content_head_tag_product
                                                  from :table_blog_content_description
                                                  where blog_content_id = :blog_content_id
                                                  and language_id = :language_id
                                                ');
      $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
      $QblogContent->bindInt(':language_id', (int)$language_id);
      $QblogContent->execute();

      $blog_content = $QblogContent->fetch();

      return $blog_content['blog_content_head_tag_product'];
    }


    /**
     * blog Tag Name
     *
     * @param string $blog_content_id , $language_id
     * @return string $blog_content['blog_content_head_tag_blog'], keywords name
     * @access public
     *
     */
    public static function getBlogContentTagBlog($blog_content_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $QblogContent = $CLICSHOPPING_Db->prepare('select blog_content_head_tag_blog
                                                  from :table_blog_content_description
                                                  where blog_content_id = :blog_content_id
                                                  and language_id = :language_id
                                                ');
      $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
      $QblogContent->bindInt(':language_id', (int)$language_id);
      $QblogContent->execute();

      $blog_content = $QblogContent->fetch();

      return $blog_content['blog_content_head_tag_blog'];
    }


    /**
     * Status products - Sets the status of a product
     *
     * @param string products_id, status
     * @return string status on or off
     * @access public
     *
     *
     */
    public static function setBlogContentStatus($blog_content_id, $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {

        return $CLICSHOPPING_Db->save('blog_content', ['blog_content_status' => 1,
          'blog_content_last_modified' => 'now()',
        ],
          ['blog_content_id' => (int)$blog_content_id]
        );

        Cache::clear('blog_tree');
      } elseif ($status == 0) {

        return $CLICSHOPPING_Db->save('blog_content', ['blog_content_status' => 0,
          'blog_content_last_modified' => 'now()',
        ],
          ['blog_content_id' => (int)$blog_content_id]
        );

        Cache::clear('blog_tree');

      } else {
        return -1;
      }
    }


    /**
     * Blog content : remove blog
     *
     * @param string blog_content_id
     * @return
     * @access public
     *
     */

    public static function getRemoveBlogContent($blog_content_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                      from :table_blog_content
                                      where blog_content_id = :blog_content_id
                                    ');
      $Qdelete->bindInt(':blog_content_id', (int)$blog_content_id);
      $Qdelete->execute();

      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                      from :table_blog_content_to_categories
                                      where blog_content_id = :blog_content_id
                                    ');
      $Qdelete->bindInt(':blog_content_id', (int)$blog_content_id);
      $Qdelete->execute();

      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                      from :table_blog_content_description
                                      where blog_content_id = :blog_content_id
                                    ');
      $Qdelete->bindInt(':blog_content_id', (int)$blog_content_id);
      $Qdelete->execute();

      Cache::clear('blog_tree');
    }


    /**
     *  Blog content Description summary
     *
     * @param string $product_id , $language_id
     * @return string $product['products_description'], description name
     * @access public
     *
     */
    public static function getBlogContentDescriptionSummary($blog_content_id, $language_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qblog = $CLICSHOPPING_Db->prepare('select blog_content_description_summary
                                    from :table_blog_content_description
                                    where blog_content_id = :blog_content_id
                                    and language_id = :language_id
                                  ');
      $Qblog->bindInt(':blog_content_id', (int)$blog_content_id);
      $Qblog->bindInt(':language_id', (int)$language_id);
      $Qblog->execute();

      $blog = $Qblog->fetch();

      return $blog['blog_content_description_summary'];
    }
  }
