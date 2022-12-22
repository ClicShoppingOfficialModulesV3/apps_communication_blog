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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  $CLICSHOPPING_Blog = Registry::get('Blog');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Language = Registry::get('Language');

  $action = $_GET['action'] ?? '';

  $CLICSHOPPING_Hooks->call('BlobCategories', 'PreAction');

  $cID = '';

  if (isset($_GET['cID'])) {
    $cID = HTML::sanitize($_GET['cID']);
  }

  $Qcategories = $CLICSHOPPING_Blog->db->prepare('select c.blog_categories_id,
                                                         cd.blog_categories_name,
                                                         c.parent_id
                                                  from :table_blog_categories c,
                                                       :table_blog_categories_description cd
                                                  where c.blog_categories_id = cd.blog_categories_id
                                                  and cd.language_id = :language_id
                                                  and c.blog_categories_id = :blog_categories_id
                                                ');
  $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());
  $Qcategories->bindInt(':blog_categories_id', $cID);

  $Qcategories->execute();

  $category_childs = array('childs_count' => BlogAdmin::getChildsInBlogCategoryCount($Qcategories->valueInt('blog_categories_id')));
  $category_blog_content = array('blog_content_count' => BlogAdmin::getBlogContentInCategoryCount($Qcategories->valueInt('blog_categories_id')));

  $cInfo_array = array_merge($Qcategories->toArray(), $category_childs, $category_blog_content);
  $cInfo = new ObjectInfo($cInfo_array);

  if (isset($_POST['cPath'])) {
    $current_category_id = HTML::sanitize($_POST['cPath']);
  } else {
    $current_category_id = 0;
  }
?>
<div class="contentBody">

  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/blog.png', $CLICSHOPPING_Blog->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-6 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Blog->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_Blog->getDef('text_info_heading_move_category'); ?></strong></div>
  <?php echo HTML::form('categories', $CLICSHOPPING_Blog->link('BlogCategories&MoveCategoryConfirm&cPath=' . $current_category_id)) . HTML::hiddenField('blog_categories_id', $cInfo->blog_categories_id); ?>
  <div class="adminformTitle">
    <div class="row">
      <div class="separator"></div>
      <div
        class="col-md-12"><?php echo $CLICSHOPPING_Blog->getDef('text_move_categories_intro', ['categorie_intro' => $cInfo->blog_categories_name]); ?>
        <br/><br/></div>
      <div class="separator"></div>
      <div class="col-md-12">
        <span
          class="col-md-3"><?php echo $CLICSHOPPING_Blog->getDef('text_move', ['categorie_name' => $cInfo->blog_categories_name]) . '<br />' . HTML::selectMenu('move_to_category_id', BlogAdmin::getBlogCategoryTree(), $current_category_id); ?></span>
      </div>
      <div class="separator"></div>
      <div class="col-md-12 text-center">
        <span><br/><?php echo HTML::button($CLICSHOPPING_Blog->getDef('button_move'), null, null, 'primary', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Blog->getDef('button_cancel'), null, $CLICSHOPPING_Blog->link('BlogCategories&cPath=' . $current_category_id . '&cID=' . $cInfo->blog_categories_id), 'warning', null, 'sm'); ?></span>
      </div>
    </div>
  </div>
  </form>
</div>