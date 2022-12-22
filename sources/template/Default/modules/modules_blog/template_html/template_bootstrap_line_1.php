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

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
?>
<div class="col-md-<?php echo $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
  <div style="float:left;">
    <div class="blogSummaryTitleLine1">
      <span class="BlogSummaryTitleLine1"><h2><?php echo $blog_content_name; ?></h2></span>
    </div>
    <div class="blogSummaryDateLine1">
      <span class="BlogSummaryDateLine1"><h3><?php echo $date; ?></h3></span>
    </div>
    <div class="blogSummaryShortDescriptionLine1"><h3><?php echo $short_description; ?></h3></div>
    <div>
      <div class="col-md-3 text-center blogSummaryAuthorLine1">
        <h3>
<?php
  echo HTML::link(CLICSHOPPING::link(null, 'Blog&Content&blog_content_id=' . (int)$blog_id), '<i class="fas fa-arrow-circle-right fa-1x"></i>');
  echo HTML::link(CLICSHOPPING::link(null, 'Blog&Content&blog_content_id=' . (int)$blog_id), CLICSHOPPING::getDef('module_blog_display_summary_content'));
?>
        </h3>
      </div>
      <div class="col-md-3 blogSummaryAuthorLine1">
        <h3><i class="fas fa-user fa-2x blogSummaryAuthorLine1"></i> <?php echo $author; ?></h3>
      </div>
    </div>
    <div><hr /></div>
    <div class="separator"></div>
  </div>
</div>