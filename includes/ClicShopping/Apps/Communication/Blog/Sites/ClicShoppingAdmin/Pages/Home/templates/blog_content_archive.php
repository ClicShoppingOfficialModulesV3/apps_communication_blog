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
  $CLICSHOPPING_Language = Registry::get('Language');

  $action = $_GET['action'] ?? '';

  $cID = '';

  if (isset($_GET['cID'])) {
    $cID = HTML::sanitize($_GET['cID']);
  }

  if (isset($_GET['cPath'])) {
    $cPath = HTML::sanitize($_GET['cPath']);
  } else {
    $cPath = 0;
  }

  if (isset($_GET['pID'])) $pID = HTML::sanitize($_GET['pID']);

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
  <div class="col-md-12 mainTitle">
    <strong><?php echo $CLICSHOPPING_Blog->getDef('text_info_heading_archive'); ?></strong></div>
  <?php echo HTML::form('archive', $CLICSHOPPING_Blog->link('BlogContent&ArchiveToConfirm&cPath=' . $cPath)) . HTML::hiddenField('blog_content_id', $_GET['pID']); ?>
  <div class="adminformTitle">
    <div class="separator"></div>
    <div
      class="col-md-12"><?php echo $CLICSHOPPING_Blog->getDef('text_info_archive_intro') . ' <strong>' . $pInfo->blog_content_name . '</strong>'; ?>
      <br/><br/></div>
    <div class="separator"></div>
    <div class="col-md-12">
      <span
        class="col-md-3"><?php echo $CLICSHOPPING_Blog->getDef('text_info_current_categories') . '<br /><strong>' . BlogAdmin::getOutputGeneratedBlogCategoryPath($_GET['pID'], 'product'); ?></span>
    </div>
    <div class="separator"></div>
    <div
      class="col-md-12 text-center"><?php echo HTML::button($CLICSHOPPING_Blog->getDef('button_archive'), null, null, 'primary', null, 'sm') . ' </span><span>' . HTML::button($CLICSHOPPING_Blog->getDef('button_cancel'), null, $CLICSHOPPING_Blog->link('BlogContent&cPath=' . $cPath . '&pID=' . $_GET['pID']), 'warning', null, 'sm'); ?>
    </div>
  </div>
  </form>
</div>