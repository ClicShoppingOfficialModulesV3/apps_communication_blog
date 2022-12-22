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

  class Blog
  {

    protected $blog_content_id;
    protected $description;
    protected $blog_short_description;
    protected $id;

    protected $db;
    protected $lang;


    Public function __construct()
    {

      $this->customer = Registry::get('Customer');
      $this->db = Registry::get('Db');
      $this->lang = Registry::get('Language');

      if (isset($_GET['blogContentId']))  {
        $blog_content_id = HTML::sanitize($_GET['blogContentId']);
      } else {
        $blog_content_id = null;
      }

      $this->blog_content_id = $blog_content_id;
      $this->checkBlogContentId();

      if ($this->customer->getCustomersGroupID() != 0 && !is_null($blog_content_id)) {
        $QblogContent = $this->db->prepare('select blog_content_id,
                                                    blog_content_status,
                                                    blog_content_archive
                                              where blog_content_id = :blog_content_id
                                              and blog_content_archive = 0
                                              and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                              and blog_content_status = 1
                                            ');
        $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
        $QblogContent->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());

      } else {
        $QblogContent = $this->db->prepare('select blog_content_id,
                                                    blog_content_status,
                                                    blog_content_archive
                                             from :table_blog_content
                                             where  blog_content_id = :blog_content_id
                                             and blog_content_archive = 0
                                             and (customers_group_id = 0 or customers_group_id = 99)
                                             and blog_content_status = 1
                                           ');
        $QblogContent->bindInt(':blog_content_id', (int)$blog_content_id);
      }

      $QblogContent->execute();
      $blogContent = $QblogContent->fetch();

      $this->data = $blogContent;
      $this->id = $QblogContent->valueInt('blog_content_id');
      $this->blogContentArchive = $QblogContent->value('blog_content_archive');
      $this->blogContentstatus = $QblogContent->value('blog_content_status');


    }

    protected function checkBlogContentId()
    {
      if ((!$this->blog_content_id) || (!is_numeric($this->blog_content_id))) {
        return false;
      }
    }

    public function getData()
    {
      return $this->data;
    }

// returns a single element of the data array
    public function get($obj)
    {
      return $this->data[$obj];
    }

    Public function getId()
    {
      return $this->id;
    }

    Public function getBlogArchive()
    {
      return $this->blogContentArchive;
    }

    Public function getStatus()
    {
      return $this->blogContentstatus;
    }

    /**
     * Number of blog content
     *
     * @param string
     * @return string $blog_check['total'], blog total
     * @access private
     */
    Private function setBlogCount()
    {
      $QblogCheck = $this->db->prepare('select count(*) as total
                                         from :table_blog_content bc,
                                              :table_blog_content_description bcd
                                         where bc.blog_content_status = :blog_content_status
                                         and bc.blog_content_id = :blog_content_id
                                         and bcd.blog_content_id = bc.blog_content_id
                                         and bcd.language_id = :language_id
                                      ');
      $QblogCheck->bindInt(':blog_content_id', (int)$this->id);
      $QblogCheck->bindInt(':language_id', (int)$this->lang->getId());
      $QblogCheck->bindValue(':blog_content_status', $this->blogContentstatus);
      $QblogCheck->execute();

      return $QblogCheck->valueInt('total');
    }

    /**
     * Number of blog
     *
     * @param string
     * @return string $blog_check['total'], blog total
     * @access public
     */
    Public function getBlogCount()
    {
      return $this->setBlogCount();
    }

    /**
     * Author of blog
     *
     * @param string
     * @return string $author, author
     * @access private
     */
    Private function setBlogContentAuthor($id)
    {

      if (is_null($id)) {
        $id = $this->id;
      }

      if ($this->customer->getCustomersGroupID() != 0) {
        $QblogContent = $this->db->prepare('select blog_content_author
                                             from :table_blog_content
                                             where  blog_content_id = :blog_content_id
                                             and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                           ');
        $QblogContent->bindInt(':blog_content_id', $id);
        $QblogContent->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());

      } else {

        $QblogContent = $this->db->prepare('select blog_content_author
                                             from :table_blog_content
                                             where  blog_content_id = :blog_content_id
                                             and (customers_group_id = 0 or customers_group_id = 99)
                                           ');
        $QblogContent->bindInt(':blog_content_id', $id);
      }

      $QblogContent->execute();

      if (!empty($QblogContent->value('blog_content_author'))) {
        $author = HTML::outputProtected($QblogContent->value('blog_content_author'));
      } else {
        $author = '';
      }

      return $author;

    }


    /**
     * Display Author of blog
     *
     * @param string
     * @return string $author, author
     * @access public
     */
    Public function getBlogContentAuthor($id = null)
    {
      return $this->setBlogContentAuthor($id);
    }

    /**
     * Description of blog
     *
     * @param string
     * @return string $author, author
     * @access private
     */
    Private function setBlogContentDescription()
    {

      if ($this->customer->getCustomersGroupID() != 0) {
// Mode b2b
        $QblogContent = $this->db->prepare('select bcd.blog_content_description
                                                 from :table_blog_content bc,
                                                      :table_blog_content_description bcd
                                                 where bc.blog_content_status = :blog_content_status
                                                 and bc.blog_content_id = :blog_content_id
                                                 and bcd.blog_content_id = bc.blog_content_id
                                                 and bcd.language_id = :language_id
                                                 and bc.blog_content_archive = :blog_content_archive
                                                 and (bc.customers_group_id = :customers_group_id or bc.customers_group_id = 99)
                                               ');
        $QblogContent->bindInt(':blog_content_id', (int)$this->id);
        $QblogContent->bindInt(':language_id', (int)$this->lang->getId());
        $QblogContent->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());
        $QblogContent->bindInt(':blog_content_status', $this->blogContentstatus);
        $QblogContent->bindInt(':blog_content_archive', $this->blogContentArchive);
      } else {
// Mode normal
        $QblogContent = $this->db->prepare('select  bcd.blog_content_description
                                                   from :table_blog_content bc,
                                                        :table_blog_content_description bcd
                                                   where bc.blog_content_status = :blog_content_status
                                                   and bc.blog_content_id = :blog_content_id
                                                   and bcd.blog_content_id = bc.blog_content_id
                                                   and bcd.language_id = :language_id
                                                   and bc.blog_content_archive = :blog_content_archive
                                                   and (bc.customers_group_id = 0 or bc.customers_group_id = 99)
                                                 ');
        $QblogContent->bindInt(':blog_content_id', (int)$this->id);
        $QblogContent->bindInt(':language_id', (int)$this->lang->getId());
        $QblogContent->bindInt(':blog_content_status', $this->blogContentstatus);
        $QblogContent->bindInt(':blog_content_archive', $this->blogContentArchive);
      }

      $QblogContent->execute();

      $blog_content_description = $QblogContent->value('blog_content_description');

      return $blog_content_description;
    }


    /**
     * Display description of blog
     *
     * @param string
     * @return string $blog_content_description, blog description
     * @access public
     */
    Public function getBlogContentDescription()
    {
      return $this->setBlogContentDescription();
    }


    /**
     * Name of blog
     *
     * @param string
     * @return string $blog_content_name, blog name
     * @access private
     */
    Private function setBlogContentName()
    {

      if ($this->customer->getCustomersGroupID() != 0) {
        $QblogContent = $this->db->prepare('select bcd.blog_content_name
                                             from :table_blog_content bc,
                                                  :table_blog_content_description bcd
                                               where bc.blog_content_status = :blog_content_status
                                               and bc.blog_content_id = :blog_content_id
                                               and bcd.blog_content_id = bc.blog_content_id
                                               and bcd.language_id = :language_id
                                               and bc.blog_content_archive = :blog_content_archive
                                               and (bc.customers_group_id = :customers_group_id or bc.customers_group_id = 99)
                                             ');
        $QblogContent->bindInt(':blog_content_id', (int)$this->id);
        $QblogContent->bindInt(':language_id', (int)$this->lang->getId());
        $QblogContent->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());
        $QblogContent->bindValue(':blog_content_status', $this->blogContentstatus);
        $QblogContent->bindValue(':blog_content_archive', $this->blogContentArchive);


      } else {

        $QblogContent = $this->db->prepare('select bcd.blog_content_name
                                             from :table_blog_content bc,
                                                  :table_blog_content_description bcd
                                             where bc.blog_content_status = :blog_content_status
                                             and bc.blog_content_id = :blog_content_id
                                             and bcd.blog_content_id = bc.blog_content_id
                                             and bc.blog_content_archive = :blog_content_archive
                                             and bcd.language_id = :language_id
                                             and (bc.customers_group_id = 0 or bc.customers_group_id = 99)
                                           ');
        $QblogContent->bindInt(':blog_content_id', (int)$this->id);
        $QblogContent->bindInt(':language_id', (int)$this->lang->getId());
        $QblogContent->bindValue(':blog_content_status', $this->blogContentstatus);
        $QblogContent->bindValue(':blog_content_archive', $this->blogContentArchive);
      }

      $QblogContent->execute();

      $blog_content_name = HTML::outputProtected($QblogContent->value('blog_content_name'));
      $blog_content_name = HTML::link(CLICSHOPPING::link(null, '&Blog&Content&blogContentId=' . (int)$_GET['blogContentId']) . '" itemprop="url" class="blogContentName" rel="author">', '<span itemprop="name">' . $blog_content_name . '</span>');

      return $blog_content_name;
    }


    /**
     * Display name of blog
     *
     * @param string
     * @return string $blog_content_name, blog name
     * @access public
     */
    Public function getBlogContentName()
    {
      return $this->setBlogContentName();
    }


    /**
     * Tag of blog
     *
     * @param string
     * @return string $tag, blog tag
     * @access private
     */
    Private function setBlogContentTag()
    {

      if ($this->customer->getCustomersGroupID() != 0) {

        $QblogContent = $this->db->prepare('select bcd.blog_content_head_tag_blog
                                               from :table_blog_content bc,
                                                    :table_blog_content_description bcd
                                               where bc.blog_content_status = :blog_content_status
                                               and bc.blog_content_id = :blog_content_id
                                               and bcd.blog_content_id = bc.blog_content_id
                                               and bcd.language_id = :language_id
                                               and bc.blog_content_archive = :blog_content_archive
                                               and (bc.customers_group_id = :customers_group_id or bc.customers_group_id = 99)
                                               ');
        $QblogContent->bindInt(':blog_content_id', (int)$this->id);
        $QblogContent->bindInt(':language_id', (int)$this->lang->getId());
        $QblogContent->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());
        $QblogContent->bindValue(':blog_content_status', $this->blogContentstatus);
        $QblogContent->bindValue(':blog_content_archive', $this->blogContentArchive);

      } else {

        $QblogContent = $this->db->prepare('select bcd.blog_content_head_tag_blog
                                             from :table_blog_content bc,
                                                  :table_blog_content_description bcd
                                             where bc.blog_content_status = :blog_content_status
                                             and bc.blog_content_id = :blog_content_id
                                             and bcd.blog_content_id = bc.blog_content_id
                                             and bc.blog_content_archive = :blog_content_archive
                                             and bcd.language_id = :language_id
                                             and (bc.customers_group_id = 0 or bc.customers_group_id = 99)
                                           ');
        $QblogContent->bindInt(':blog_content_id', (int)$this->id);
        $QblogContent->bindInt(':language_id', (int)$this->lang->getId());
        $QblogContent->bindValue(':blog_content_status', $this->blogContentstatus);
        $QblogContent->bindValue(':blog_content_archive', $this->blogContentArchive);
      }

      $QblogContent->execute();

      if (!empty($QblogContent->value('blog_content_head_tag_blog'))) {
        $blog_content_tag = HTML::outputProtected($QblogContent->value('blog_content_head_tag_blog'));
        $delimiter = ',';
        $blog_content_tag = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter, null) . ')\\s*|', $delimiter, $blog_content_tag));
        $tag = explode(",", $blog_content_tag);
      } else {
        $tag = '';
      }

      return $tag;
    }


    /**
     * Display tag of blog
     *
     * @param string
     * @return string $tag, blog tag
     * @access public
     */
    Public function getBlogContentTag()
    {
      return $this->setBlogContentTag();
    }


    /**
     * Tag of product for blog
     *
     * @param string
     * @return string $tag, blog tag
     * @access private
     */
    Private function setBlogContentTagProduct()
    {

      if ($this->customer->getCustomersGroupID() != 0) {

        $QblogContent = $this->db->prepare('select bcd.blog_content_head_tag_product
                                             from :table_blog_content bc,
                                                  :table_blog_content_description bcd
                                             where bc.blog_content_status = :blog_content_status
                                             and bc.blog_content_id = :blog_content_id
                                             and bcd.blog_content_id = bc.blog_content_id
                                             and bcd.language_id = :language_id
                                             and bc.blog_content_archive = :blog_content_archive
                                             and (bc.customers_group_id = :customers_group_id or bc.customers_group_id = 99)
                                           ');
        $QblogContent->bindInt(':blog_content_id', (int)$this->id);
        $QblogContent->bindInt(':language_id', (int)$this->lang->getId());
        $QblogContent->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());
        $QblogContent->bindValue(':blog_content_status', $this->blogContentstatus);
        $QblogContent->bindValue(':blog_content_archive', $this->blogContentArchive);

      } else {
        $QblogContent = $this->db->prepare('select bcd.blog_content_head_tag_product
                                             from :table_blog_content bc,
                                                  :table_blog_content_description bcd
                                             where bc.blog_content_status = :blog_content_status
                                             and bc.blog_content_id = :blog_content_id
                                             and bcd.blog_content_id = bc.blog_content_id
                                             and bc.blog_content_archive = :blog_content_archive
                                             and bcd.language_id = :language_id
                                             and (bc.customers_group_id = 0 or bc.customers_group_id = 99)
                                           ');
        $QblogContent->bindInt(':blog_content_id', (int)$this->id);
        $QblogContent->bindInt(':language_id', (int)$this->lang->getId());
        $QblogContent->bindValue(':blog_content_status', $this->blogContentstatus);
        $QblogContent->bindValue(':blog_content_archive', $this->blogContentArchive);
      }

      $QblogContent->execute();

      if (!empty($QblogContent->value('blog_content_head_tag_product'))) {

        $blog_content_tag = HTML::outputProtected($QblogContent->value('blog_content_head_tag_product'));
        $delimiter = ',';
        $blog_content_tag = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter, null) . ')\\s*|', $delimiter, $blog_content_tag));
        $tag = explode(",", $blog_content_tag);
      } else {
        $tag = '';
      }

      return $tag;
    }

    /**
     * Display tag of blog
     *
     * @param string
     * @return string $tag, blog tag
     * @access public
     */
    Public function getBlogContentTagProduct()
    {
      return $this->setBlogContentTagProduct();
    }

    /**
     * Display a short description for a product
     *
     * @param string $short_description , the short description of the product
     * @access public
     */
    public static function displayBlogShortDescription($description, $blog_short_description)
    {
      if ($blog_short_description > 0) {
        $short_description = html_entity_decode($description);
        $short_description = substr($short_description, 0, $blog_short_description);
        $short_description = HTML::breakString($short_description, $blog_short_description, '-<br />') . ((strlen($description) >= $blog_short_description - 1) ? ' ...' : '');
      }

      return $short_description;
    }
  }