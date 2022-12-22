<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_Blog = Registry::get('Blog');

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  if ( isset($_GET['blogContentId']) && is_numeric($_GET['blogContentId'])) {
    $id = (int)$CLICSHOPPING_Blog->getId();
  } else {
    CLICSHOPPING::redirect();
  }

  if ($CLICSHOPPING_Blog->getBlogCount() < 1 || (is_null($id))) {
?>
<section class="blog_content" id="blog_content">
  <div class="contentContainer">
    <div class="contentText">
      <div class="blogContent">
        <div class="separator"></div>
        <div class="alert alert-warning text-center" role="alert">
          <h3><?php echo CLICSHOPPING::getDef('text_blog_not_found'); ?></h3>
        </div>
        <div class="control-group">
          <div class="controls">
            <div class="buttonSet">
              <span class="text-end"><label for="buttonContinue"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(),'success'); ?></label></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- blog content not found  end -->
<?php
  } else {
// ------------------------------------------------------------
// ---- Display the blog content                           ----
// ------------------------------------------------------------
?>
  <div class="contentContainer">
    <div class="contentText">
      <div class="blogContent">
        <div itemscope itemtype="https://schema.org/Blog">
           <?php echo $CLICSHOPPING_Template->getBlocks('modules_blog_content'); ?>
        </div>
      </div>
    </div>
  </div>
<?php
  }
?>
</section>
