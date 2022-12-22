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
<h3><?php echo sprintf(CLICSHOPPING::getDef('module_blog_wordpress_heading'), strftime('%B')); ?></h3>
<?php
  if (is_array($posts)) {
    foreach($posts as $key => $post) {
      $date = $post['date'];
      $fixed = date('j.m', strtotime(substr($date,0,10)));
      $post_image  = '<button class="btn btn-info btn-circle btn-xl">' . $fixed . '</button>';
?>
<div class="col-md-<?php echo  $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
  <div class="separator"></div>
  <div class="card-deck-wrapper wordpress-row">
    <div class="card-deck">
      <div class="card">
        <div class="card-block">
          <div class="separator"></div>
          <div class="wordpress-item">
            <div class="row">
              <div class="text-center"><?php echo $post_image; ?></div>
              <div class="caption">
                <p class="text-center"><?php echo HTML::link($post['link'], $post['title']['rendered']); ?>
                <hr>
                <p class="text-center"><strong><?php echo $post['_embedded']['author']['0']['name'] . ' ' . CLICSHOPPING::getDef('module_blog_wordpress_wrote')  . ':</strong> ' . $post['excerpt']['rendered']; ?></p>
                <div class="text-center">
                  <div class="btn-group">
                    <label for="buttonView"><?php echo HTML::link($post['link'] . '" class="btn btn-secondary" role="button""', CLICSHOPPING::getDef('module_blog_wordpress_button_view')); ?></label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
