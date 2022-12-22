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
  <div class="card boxeContainerCategories">
    <div class="card-header boxeHeadingCategories">
      <span class="card-title blogSummaryTitleColumn1">
        <h1><span class="col-md-9 blogSummaryTitleColumn1"><?php echo $blog_content_name; ?></span></h1>
        <h3><span class="col-md-3 blogSummaryDateColumn1"><?php echo $date; ?></span></h3>
      </span>
    </div>
    <div class="card-block boxeContentArroundCategories">
      <div class="separator"></div>
      <div class="card-text blogSummaryShortFollowColumn1"><?php echo $short_description; ?></div>
      <div class="separator"></div>
      <div class="text-end">
<?php
  echo HTML::link(CLICSHOPPING::link(null, 'Blog&Content&blog_content_id=' . (int)$blog_id), '<i class="fas fa-arrow-circle-right fa-1x"></i>') . '   ';
  echo HTML::link(CLICSHOPPING::link(null, 'Blog&Content&blog_content_id=' . (int)$blog_id), CLICSHOPPING::getDef('module_blog_display_summary_content'));
?>
      </div>
    </div>
    <div class="card-footer blogSummaryAuthorColumn1 text-end">
      <h6><i class="fas fa-user fa-1x blogSummaryAuthorColumn1"></i> <?php echo $author; ?></h6>
    </div>
  </div>
</div>
