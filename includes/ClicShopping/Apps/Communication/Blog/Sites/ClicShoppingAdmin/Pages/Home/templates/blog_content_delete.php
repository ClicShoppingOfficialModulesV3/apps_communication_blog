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
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $action = $_GET['action'] ?? '';

  $cID = '';

  if (isset($_GET['cID'])) {
    $cID = HTML::sanitize($_GET['cID']);
  }

  $cPath = HTML::sanitize($current_category_id); //@todo

  $QblogContent = $CLICSHOPPING_Blog->db->prepare('select p.blog_content_id,
                                                         pd.blog_content_name
                                                   from :table_blog_content p,
                                                        :table_blog_content_description pd
                                                   where p.blog_content_id = pd.blog_content_id
                                                   and pd.language_id = :language_id
                                                   and p.blog_content_id = :blog_content_id
                                                 ');
  $QblogContent->bindInt(':blog_content_id', $cID);
  $QblogContent->bindInt(':language_id', $CLICSHOPPING_Language->getId());
  $QblogContent->execute();

  $pInfo = new ObjectInfo($QblogContent->toArray());
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
            class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Blog->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_Blog->getDef('text_info_heading_delete_product'); ?></strong></div>
  <?php echo HTML::form('products', $CLICSHOPPING_Blog->link('BlogContent&DeleteConfirm&cPath=' . $cPath)) . HTML::hiddenField('blog_content_id', $_GET['pID']); ?>
  <div class="adminformTitle">
    <div class="separator"></div>
    <div
      class="col-md-12"><?php echo $CLICSHOPPING_Blog->getDef('text_delete_product_intro') . '<br /><strong>' . $pInfo->blog_content_name . '</strong>'; ?>
      <br/><br/></div>
    <div class="separator"></div>
    <?php
      $blog_content_categories_string = '';
      $blog_content_categories = BlogAdmin::getGenerateBlogCategoryPath($_GET['pID'], 'product');

      for ($i = 0, $n = count($blog_content_categories); $i < $n; $i++) {
        $category_path = '';

        for ($j = 0, $k = count($blog_content_categories[$i]); $j < $k; $j++) {
          $category_path .= $blog_content_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
        }

        $category_path = substr($category_path, 0, -16);
        $blog_content_categories_string .= HTML::checkboxField('product_categories[]', $blog_content_categories[$i][count($blog_content_categories[$i]) - 1]['id'], true) . '&nbsp;' . $category_path . '<br />';
      }

      $blog_content_categories_string = substr($blog_content_categories_string, 0, -4);
    ?>
    <div class="col-md-12">
      <span class="col-md-3"><?php echo $blog_content_categories_string; ?></span>
    </div>
    <div class="separator"></div>
    <div
      class="col-md-12 text-center"><?php echo HTML::button($CLICSHOPPING_Blog->getDef('button_delete'), null, null, 'danger', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Blog->getDef('button_cancel'), null, $CLICSHOPPING_Blog->link('BlogContent&cPath=' . $cPath . '&cID=' . $cInfo->blog_categories_id), 'warning', null, 'sm'); ?></div>
  </div>
  </form>
</div>