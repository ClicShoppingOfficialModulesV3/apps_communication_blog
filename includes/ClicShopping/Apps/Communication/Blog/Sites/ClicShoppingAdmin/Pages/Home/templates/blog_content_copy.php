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

  if (isset($_GET['pID'])) $pID = HTML::sanitize($_GET['pID']);

  if (isset($_GET['cPath'])) {
    $cPath = HTML::sanitize($_GET['cPath']);
  } else {
    $cPath = 0;
  }

  $QblogContent = $CLICSHOPPING_Blog->db->prepare('select p.blog_content_id,
                                                   pd.blog_content_name
                                           from :table_blog_content p,
                                                :table_blog_content_description pd
                                           where p.blog_content_id = pd.blog_content_id
                                           and pd.language_id = :language_id
                                           and p.blog_content_id = :blog_content_id
                                         ');
  $QblogContent->bindInt(':blog_content_id', $pID);
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
    <strong><?php echo $CLICSHOPPING_Blog->getDef('text_info_heading_copy_to'); ?></strong></div>
  <?php echo HTML::form('copy_to', $CLICSHOPPING_Blog->link('BlogContent&CopyToConfirm&cPath=' . $cPath)) . HTML::hiddenField('blog_content_id', $_GET['pID']); ?>
  <div class="adminformTitle">
    <div class="separator"></div>
    <div class="col-md-12">
      <br/><?php echo $CLICSHOPPING_Blog->getDef('text_info_copy_to_intro') . ' <strong>' . $pInfo->blog_content_name . '</strong>'; ?>
      <br/></div>
    <div class="separator"></div>
    <div class="col-md-12">
      <span
        class="col-md-3"><br/><?php echo $CLICSHOPPING_Blog->getDef('text_info_current_categories') . '<br /><strong>' . BlogAdmin::getOutputGeneratedBlogCategoryPath($_GET['pID'], 'product'); ?></span>
    </div>
    <div class="separator"></div>
    <div class="col-md-12">
      <span
        class="col-md-3"><br/><?php echo $CLICSHOPPING_Blog->getDef('text_categories_name') . '<br />' . HTML::selectMenu('blog_categories_id', BlogAdmin::getBlogCategoryTree(), $cPath); ?></span>
    </div>
    <div class="separator"></div>
    <div class="col-md-12">
      <div><?php echo $CLICSHOPPING_Blog->getDef('text_how_to_copy'); ?></div>
      <div class="custom-control custom-radio custom-control-inline">
        <?php echo HTML::radioField('copy_as', 'link', true, 'class="custom-control-input" id="copy_as_link" name="copy_as_link"'); ?>
        <label class="custom-control-label" for="copy_as_link"><?php echo $CLICSHOPPING_Products->getDef('text_copy_as_link'); ?></label>
      </div>

      <div class="custom-control custom-radio custom-control-inline">
        <?php echo HTML::radioField('copy_as', 'duplicate', null, 'class="custom-control-input" id="copy_as_duplicate" name="copy_as_duplicate"'); ?>
        <label class="custom-control-label" for="copy_as_duplicate"><?php echo $CLICSHOPPING_Products->getDef('text_copy_as_duplicate'); ?></label>
      </div>
    </div>
    <div class="separator"></div>
    <div
      class="col-md-12 text-center"><?php echo HTML::button($CLICSHOPPING_Blog->getDef('button_copy'), null, null, 'primary', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Blog->getDef('button_cancel'), null, $CLICSHOPPING_Blog->link('BlogContent&cPath=' . $cPath . '&pID=' . $_GET['pID']), 'warning', null, 'sm'); ?></div>
  </div>
  </form>
</div>