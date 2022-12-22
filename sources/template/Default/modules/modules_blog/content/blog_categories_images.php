<div class="col-md-<?php echo  $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
  <div class="separator"></div>
  <div class="card-deck-wrapper">
    <div class="card-deck">
      <div class="card">
        <div class="card-block">
          <div class="separator"></div>
          <div class="card-img-top ModulesFrontPageBoostrapCategoriesImages">
<?php
  if (MODULE_BLOG_CATEGORIES_IMAGES_SHOW_IMAGE == 'True') {
?>
            <div class="text-center">
              <h3><?php echo $images; ?></h3>
            </div>
<?php
  }
  if (MODULE_BLOG_CATEGORIES_IMAGES_SHOW_NAME == 'True') {
?>
            <div class="moduleFrontPageCategoriesText">
              <h3><?php echo $link; ?></h3>
            </div>
<?php
  }
?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>