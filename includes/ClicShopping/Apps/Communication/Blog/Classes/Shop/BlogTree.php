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

  namespace ClicShopping\Apps\Communication\Blog\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class BlogTree
  {

    /**
     * Flag to control if the total number of products in a category should be calculated
     *
     * @var boolean
     * @access protected
     */

    protected $_show_total_products = false;

    /**
     * Array containing the category structure relationship data
     *
     * @var array
     * @access protected
     */

    protected $_data = array();

    protected $root_category_id = 0;
    protected $max_level = 0;
    protected $root_start_string = '';
    protected $root_end_string = '';
    protected $parent_start_string = '';
    protected $parent_end_string = '';
    protected $parent_group_start_string = '<ul>';
    protected $parent_group_end_string = '</ul>';
    protected $parent_group_apply_to_root = false;
    protected $child_start_string = '<li>';
    protected $child_end_string = '</li>';
    protected $breadcrumb_separator = '_';
    protected $breadcrumb_usage = true;
    protected $spacer_string = '';
    protected $spacer_multiplier = 1;
    protected $follow_bpath = false;
    protected $bpath_array = array();
    protected $bpath_start_string = '';
    protected $bpath_end_string = '';
    protected $category_product_count_start_string = '&nbsp;(';
    protected $category_product_count_end_string = ')';

    /**
     * Constructor; load the category structure relationship data from the database
     *
     * @access public
     */

    public function __construct()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        $Qcategories = $CLICSHOPPING_Db->prepare('select c.blog_categories_id,
                                                         cd.blog_categories_name,
                                                         cd.blog_categories_description,
                                                         c.blog_categories_image
                                                  from :blog_categories c,
                                                       :blog_categories_description cd
                                                  where c.blog_categories_id = cd.blog_categories_id
                                                  and cd.language_id = :language_id
                                                  and (c.customers_group_id = :customers_group_id or c.customers_group_id = 99)
                                                  order by c.sort_order,
                                                           cd.blog_categories_name
                                                  ');
        $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());
        $Qcategories->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());

        $Qcategories->execute();
      } else {
        $Qcategories = $CLICSHOPPING_Db->prepare('select c.blog_categories_id,
                                                         cd.blog_categories_name,
                                                         cd.blog_categories_description,
                                                         c.blog_categories_image
                                                  from :blog_categories c,
                                                       :blog_categories_description cd
                                                  where c.blog_categories_id = cd.blog_categories_id
                                                  and cd.language_id = :language_id
                                                  and (c.customers_group_id = 0 or c.customers_group_id = 99)
                                                  order by c.sort_order,
                                                           cd.blog_categories_name
                                                  ');
        $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());

        $Qcategories->execute();
      }

      while ($Qcategories->fetch()) {
        $this->_data[$Qcategories->valueInt('parent_id')][$Qcategories->valueInt('categories_id')] = array('name' => $Qcategories->value('blog_categories_name'),
          'image' => $Qcategories->value('blog_categories_image'),
          'count' => 0);
      }
    }

    function reset()
    {
      $this->root_category_id = 0;
      $this->max_level = 0;
      $this->root_start_string = '';
      $this->root_end_string = '';
      $this->parent_start_string = '';
      $this->parent_end_string = '';
      $this->parent_group_start_string = '<ul>';
      $this->parent_group_end_string = '</ul>';
      $this->child_start_string = '<li>';
      $this->child_end_string = '</li>';
      $this->breadcrumb_separator = '_';
      $this->breadcrumb_usage = true;
      $this->spacer_string = '';
      $this->spacer_multiplier = 1;
      $this->follow_bpath = false;
      $this->bpath_array = array();
      $this->bpath_start_string = '';
      $this->bpath_end_string = '';
//      $this->_show_total_products = (SERVICES_CATEGORY_PATH_CALCULATE_PRODUCT_COUNT == '1') ? true : false;
      $this->category_product_count_start_string = '&nbsp;(';
      $this->category_product_count_end_string = ')';
    }

    /**
     * Return a formated string representation of a category and its subcategories
     *
     * @param int $parent_id The parent ID of the category to build from
     * @param int $level Internal flag to note the depth of the category structure
     * @access protected
     * @return string
     */
    protected function _buildBranch($parent_id, $level = 0)
    {

      $result = ((($level === 0) && ($this->parent_group_apply_to_root === true)) || ($level > 0)) ? $this->parent_group_start_string : null;

      if (isset($this->_data[$parent_id])) {
        foreach ($this->_data[$parent_id] as $category_id => $category) {
          if ($this->breadcrumb_usage === true) {
            $category_link = $this->buildBreadcrumb($category_id);
          } else {
            $category_link = $category_id;
          }

          $result .= $this->child_start_string;

          if (isset($this->_data[$category_id])) {
            $result .= $this->parent_start_string;
          }

          if ($level === 0) {
            $result .= $this->root_start_string;
          }

          if (($this->follow_bpath === true) && in_array($category_id, $this->bpath_array)) {
            $link_title = $this->bpath_start_string . $category['name'] . $this->bpath_end_string;
          } else {
            $link_title = $category['name'];
          }

          $result .= str_repeat($this->spacer_string, $this->spacer_multiplier * $level);
          $result .= HTML::link(CLICSHOPPING::link(null, 'bPath=' . $category_link), $link_title);

          if ($this->_show_total_products === true) {
            $result .= $this->category_product_count_start_string . $category['count'] . $this->category_product_count_end_string;
          }

          if ($level === 0) {
            $result .= $this->root_end_string;
          }

          if (isset($this->_data[$category_id])) {
            $result .= $this->parent_end_string;
          }

          if (isset($this->_data[$category_id]) && (($this->max_level == '0') || ($this->max_level > $level + 1))) {
            if ($this->follow_bpath === true) {
              if (in_array($category_id, $this->bpath_array)) {
                $result .= $this->_buildBranch($category_id, $level + 1);
              }
            } else {
              $result .= $this->_buildBranch($category_id, $level + 1);
            }
          }

          $result .= $this->child_end_string;
        }
      }

      $result .= ((($level === 0) && ($this->parent_group_apply_to_root === true)) || ($level > 0)) ? $this->parent_group_end_string : null;

      return $result;
    }

    public function buildBranchArray($parent_id, $level = 0, $result = array())
    {
      if (empty($result)) {
        $result = array();
      }

      if (isset($this->_data[$parent_id])) {
        foreach ($this->_data[$parent_id] as $category_id => $category) {
          if ($this->breadcrumb_usage === true) {
            $category_link = $this->buildBreadcrumb($category_id);
          } else {
            $category_link = $category_id;
          }

          $result[] = array('id' => $category_link,
            'title' => str_repeat($this->spacer_string, $this->spacer_multiplier * $level) . $category['name']);

          if (isset($this->_data[$category_id]) && (($this->max_level == '0') || ($this->max_level > $level + 1))) {
            if ($this->follow_bpath === true) {
              if (in_array($category_id, $this->bpath_array)) {
                $result = $this->buildBranchArray($category_id, $level + 1, $result);
              }
            } else {
              $result = $this->buildBranchArray($category_id, $level + 1, $result);
            }
          }
        }
      }

      return $result;
    }

    public function buildBreadcrumb($category_id, $level = 0)
    {
      $breadcrumb = '';

      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $id => $info) {
          if ($id == $category_id) {
            if ($level < 1) {
              $breadcrumb = $id;
            } else {
              $breadcrumb = $id . $this->breadcrumb_separator . $breadcrumb;
            }

            if ($parent != $this->root_category_id) {
              $breadcrumb = $this->buildBreadcrumb($parent, $level + 1) . $breadcrumb;
            }
          }
        }
      }

      return $breadcrumb;
    }

    /**
     * Return a formated string representation of the category structure relationship data
     *
     * @access public
     * @return string
     */

    public function getTree()
    {
      return $this->_buildBranch($this->root_category_id);
    }

    /**
     * Magic function; return a formated string representation of the category structure relationship data
     *
     * This is used when echoing the class object, eg:
     *
     * echo $osC_CategoryTree;
     *
     * @access public
     * @return string
     */

    public function __toString()
    {
      return $this->getTree();
    }

    public function getArray($parent_id = '')
    {
      return $this->buildBranchArray((empty($parent_id) ? $this->root_category_id : $parent_id));
    }

    public function exists($id)
    {
      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $category_id => $info) {
          if ($id == $category_id) {
            return true;
          }
        }
      }

      return false;
    }

    public function getChildren($category_id, &$array = array())
    {
      foreach ($this->_data as $parent => $categories) {
        if ($parent == $category_id) {
          foreach ($categories as $id => $info) {
            $array[] = $id;
            $this->getChildren($id, $array);
          }
        }
      }

      return $array;
    }

    /**
     * Return category information
     *
     * @param int $id The category ID to return information of
     * @param string $key The key information to return (since v3.0.2)
     * @return mixed
     * @since v3.0.0
     */

    public function getData($id, $key = null)
    {
      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $category_id => $info) {
          if ($id == $category_id) {
            $data = array('id' => $id,
              'name' => $info['name'],
              'parent_id' => $parent,
              'image' => $info['image'],
              'count' => $info['count']);

            return (isset($key) ? $data[$key] : $data);
          }
        }
      }

      return false;
    }

    /**
     * Return the parent ID of a category
     *
     * @param int $id The category ID to return the parent ID of
     * @return int
     * @since v3.0.2
     */

    public function getParentID($id)
    {
      return $this->getData($id, 'parent_id');
    }

    /**
     * Calculate the number of products in each category
     *
     * @access protected
     */

    protected function _calculateProductTotals($filter_active = true)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $totals = array();

      $sql_query = 'select p2c.categories_id, count(*) as total
                    from :table_products p,
                        :table_products_to_categories p2c
                    where p2c.products_id = p.products_id';

      if ($filter_active === true) {
        $sql_query .= ' and p.products_status = :products_status';
      }

      $sql_query .= ' group by p2c.categories_id';

      if ($filter_active === true) {
        $Qtotals = $CLICSHOPPING_Db->prepare($sql_query);
        $Qtotals->bindInt(':products_status', 1);
      } else {
        $Qtotals = $CLICSHOPPING_Db->query($sql_query);
      }

      $Qtotals->execute();

      while ($Qtotals->fetch()) {
        $totals[$Qtotals->valueInt('categories_id')] = $Qtotals->valueInt('total');
      }

      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $id => $info) {
          if (isset($totals[$id]) && ($totals[$id] > 0)) {
            $this->_data[$parent][$id]['count'] = $totals[$id];

            $parent_category = $parent;

            while ($parent_category != $this->root_category_id) {
              foreach ($this->_data as $parent_parent => $parent_categories) {
                foreach ($parent_categories as $parent_category_id => $parent_category_info) {
                  if ($parent_category_id == $parent_category) {
                    $this->_data[$parent_parent][$parent_category_id]['count'] += $this->_data[$parent][$id]['count'];

                    $parent_category = $parent_parent;

                    break 2;
                  }
                }
              }
            }
          }
        }
      }
    }

    public function getNumberOfProducts($id)
    {
      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $category_id => $info) {
          if ($id == $category_id) {
            return $info['count'];
          }
        }
      }

      return false;
    }

    public function setRootCategoryID($root_category_id)
    {
      $this->root_category_id = $root_category_id;
    }

    public function setMaximumLevel($max_level)
    {
      $this->max_level = $max_level;
    }

    public function setRootString($root_start_string, $root_end_string)
    {
      $this->root_start_string = $root_start_string;
      $this->root_end_string = $root_end_string;
    }

    public function setParentString($parent_start_string, $parent_end_string)
    {
      $this->parent_start_string = $parent_start_string;
      $this->parent_end_string = $parent_end_string;
    }

    public function setParentGroupString($parent_group_start_string, $parent_group_end_string, $apply_to_root = false)
    {
      $this->parent_group_start_string = $parent_group_start_string;
      $this->parent_group_end_string = $parent_group_end_string;
      $this->parent_group_apply_to_root = $apply_to_root;
    }

    public function setChildString($child_start_string, $child_end_string)
    {
      $this->child_start_string = $child_start_string;
      $this->child_end_string = $child_end_string;
    }

    public function setBreadcrumbSeparator($breadcrumb_separator)
    {
      $this->breadcrumb_separator = $breadcrumb_separator;
    }

    public function setBreadcrumbUsage($breadcrumb_usage)
    {
      if ($breadcrumb_usage === true) {
        $this->breadcrumb_usage = true;
      } else {
        $this->breadcrumb_usage = false;
      }
    }

    public function setSpacerString($spacer_string, $spacer_multiplier = 2)
    {
      $this->spacer_string = $spacer_string;
      $this->spacer_multiplier = $spacer_multiplier;
    }

    public function setCategoryPath($bpath, $bpath_start_string = '', $bpath_end_string = '')
    {
      $this->follow_bpath = true;
      $this->bpath_array = explode($this->breadcrumb_separator, $bpath);
      $this->bpath_start_string = $bpath_start_string;
      $this->bpath_end_string = $bpath_end_string;
    }

    public function setFollowCategoryPath($follow_bpath)
    {
      if ($follow_bpath === true) {
        $this->follow_bpath = true;
      } else {
        $this->follow_bpath = false;
      }
    }

    public function setCategoryPathString($bpath_start_string, $bpath_end_string)
    {
      $this->bpath_start_string = $bpath_start_string;
      $this->bpath_end_string = $bpath_end_string;
    }

    public function setShowCategoryProductCount($show_category_product_count)
    {
      if ($show_category_product_count === true) {
        $this->_show_total_products = true;
      } else {
        $this->_show_total_products = false;
      }
    }

    public function setCategoryProductCountString($category_product_count_start_string, $category_product_count_end_string)
    {
      $this->category_product_count_start_string = $category_product_count_start_string;
      $this->category_product_count_end_string = $category_product_count_end_string;
    }
  }
