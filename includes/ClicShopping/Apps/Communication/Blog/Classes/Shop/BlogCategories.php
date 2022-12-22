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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class BlogCategories
  {

    protected $blog_content_id;
    protected $description;
    protected $blog_short_description;
    protected $id;

    protected $db;
    protected $lang;

    Public function __construct($blog_content_id = '')
    {
    }

    public static function getKeywords()
    {
      if (isset($_GET['blog_keywords'])) {
        $blog_keywords = HTML::sanitize($_GET['blog_keywords']);
      } else {
        $blog_keywords = '';
      }

      return $blog_keywords;
    }

    public static function getData()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {
        if (isset($_GET['current']) && ($_GET['current'] != 0 || !empty($_GET['current']))) {
          $Qblog = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS bc.*,
                                                                         bcd.*
                                            from :table_blog_content bc,
                                                 :table_blog_content_description bcd,
                                                 :table_blog_content_to_categories p2c
                                            where bc.blog_content_status = 1
                                            and bcd.blog_content_id = bc.blog_content_id
                                            and bcd.language_id = :language_id
                                            and bc.blog_content_archive = 0
                                            and p2c.blog_categories_id = :blog_categories_id
                                            and p2c.blog_content_id = bc.blog_content_id
                                            and (bc.customers_group_id = :customers_group_id or bc.customers_group_id = 99)
                                            order by  bc.blog_content_sort_order,
                                                      bc.blog_content_date_added DESC
                                            limit :page_set_offset, :page_set_max_results
                                            ');
          $Qblog->bindInt(':blog_categories_id', $_GET['current']);
          $Qblog->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
          $Qblog->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());

        } elseif (isset($_GET['bPath']) && !empty($_GET['bPath'])) {
          $Qblog = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS bc.*,
                                                                           bcd.*
                                              from :table_blog_content bc,
                                                   :table_blog_content_description bcd,
                                                   :table_blog_content_to_categories p2c
                                              where bc.blog_content_status = 1
                                              and bcd.blog_content_id = bc.blog_content_id
                                              and bcd.language_id = :language_id
                                              and bc.blog_content_archive = 0
                                              and p2c.blog_categories_id = :blog_categories_id
                                              and p2c.blog_content_id = bc.blog_content_id
                                              and (bc.customers_group_id = :customers_group_id or bc.customers_group_id = 99)
                                              order by  bc.blog_content_sort_order,
                                                        bc.blog_content_date_added DESC
                                              limit :page_set_offset, :page_set_max_results
                                              ');
          $Qblog->bindInt(':blog_categories_id', $_GET['bPath']);
          $Qblog->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
          $Qblog->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());

        } else {

          $Qblog = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS bc.*,
                                                                         bcd.*
                                              from :table_blog_content bc,
                                                   :table_blog_content_description bcd,
                                                   :table_blog_content_to_categories p2c
                                              where bc.blog_content_status = 1
                                              and bcd.blog_content_id = bc.blog_content_id
                                              and bcd.language_id = :language_id
                                              and bc.blog_content_archive = 0
                                              and (bc.customers_group_id = :customers_group_id or bc.customers_group_id = 99)
                                              and (bc.blog_content_author  like :keywords
                                                   or bcd.blog_content_name like :keywords
                                                   or bcd.blog_content_head_tag_blog like :keywords
                                                  )
                                              order by  bc.blog_content_sort_order,
                                                        bc.blog_content_date_added DESC
                                              limit :page_set_offset, :page_set_max_results
                                              ');
          $Qblog->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
          $Qblog->bindInt(':customers_group_id', (int)$CLICSHOPPING_Customer->getCustomersGroupID());
          $Qblog->bindValue(':keywords', '%' . static::getKeywords() . '%');
        }
      } else {
// Mode normal
        if (isset($_GET['current']) && ($_GET['current'] != 0 || $_GET['current'] != '')) {
          $Qblog = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS bc.*,
                                                                           bcd.*
                                              from :table_blog_content bc,
                                                   :table_blog_content_description bcd,
                                                   :table_blog_content_to_categories p2c
                                              where bc.blog_content_status = 1
                                              and bcd.blog_content_id = bc.blog_content_id
                                              and bcd.language_id = :language_id
                                              and bc.blog_content_archive = 0
                                              and (bc.customers_group_id = 0 or bc.customers_group_id = 99)
                                              and p2c.blog_categories_id = :blog_categories_id
                                              and p2c.blog_content_id = bc.blog_content_id
                                              order by  bc.blog_content_sort_order,
                                              bc.blog_content_date_added DESC
                                              limit :page_set_offset, :page_set_max_results
                                              ');
          $Qblog->bindInt(':blog_categories_id', (int)$_GET['current']);
          $Qblog->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

        } elseif (isset($_GET['bPath']) && !empty($_GET['bPath'])) {

          $Qblog = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS bc.*,
                                                                           bcd.*
                                              from :table_blog_content bc,
                                                   :table_blog_content_description bcd,
                                                   :table_blog_content_to_categories p2c
                                              where bc.blog_content_status = 1
                                              and bcd.blog_content_id = bc.blog_content_id
                                              and bcd.language_id = :language_id
                                              and (bc.customers_group_id = 0 or bc.customers_group_id = 99)
                                              and bc.blog_content_archive = 0
                                              and p2c.blog_categories_id = :blog_categories_id
                                              and p2c.blog_content_id = bc.blog_content_id
                                              order by  bc.blog_content_sort_order,
                                                        bc.blog_content_date_added DESC
                                              limit :page_set_offset,
                                                    :page_set_max_results
                                              ');
          $Qblog->bindInt(':blog_categories_id', (int)$_GET['bPath']);
          $Qblog->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

        } else {

          $Qblog = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS bc.*,
                                                                        bcd.*
                                            from :table_blog_content bc,
                                                 :table_blog_content_description bcd,
                                                 :table_blog_content_to_categories b2c
                                            where bc.blog_content_status = 1
                                            and bcd.blog_content_id = bc.blog_content_id
                                            and bc.blog_content_id = b2c.blog_content_id
                                            and bcd.language_id = :language_id
                                            and bc.blog_content_archive = 0
                                            and (bc.customers_group_id = 0 or bc.customers_group_id = 99)
                                            and (bc.blog_content_author  like :keywords
                                                or bcd.blog_content_name like :keywords
                                                or bcd.blog_content_head_tag_blog like :keywords
                                                )
                                            order by  bc.blog_content_sort_order,
                                                      bc.blog_content_date_added DESC
                                            limit :page_set_offset,
                                                  :page_set_max_results
                                           ');
          $Qblog->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
          $Qblog->bindValue(':keywords', '%' . static::getKeywords() . '%');
        }
      }

      $Qblog->setPageSet(MODULE_BLOG_DISPLAY_SUMMARY_MAX_DISPLAY);

      $Qblog->execute();

      return $Qblog;
    }
  }