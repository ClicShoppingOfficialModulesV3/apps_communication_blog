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

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;
  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  $CLICSHOPPING_Blog = Registry::get('Blog');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $CLICSHOPPING_Hooks->call('BlobCategories', 'PreAction');

  $languages = $CLICSHOPPING_Language->getLanguages();

  if (isset($_GET['cPath'])) {
    $cPath = $_GET['cPath'];
  } else {
    $cPath = '';
  }

  if (isset($_GET['cID'])) {
    $cID = $_GET['cID'];
  } else {
    $cID = null;
  }

  if (isset($_GET['cID'])) {
    $Qcategories = $CLICSHOPPING_Blog->db->prepare('select c.*,
                                                            cd.*
                                                    from :table_blog_categories c,
                                                          :table_blog_categories_description cd
                                                    where c.blog_categories_id = :blog_categories_id
                                                    and c.blog_categories_id = cd.blog_categories_id
                                                    and cd.language_id = :language_id
                                                    order by c.sort_order,
                                                             cd.blog_categories_name
                                                    ');
    $Qcategories->bindInt(':blog_categories_id', (int)$cID);
    $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
    $Qcategories->execute();

    $cInfo = new ObjectInfo($Qcategories->toArray());
  } else {
    $cInfo = new ObjectInfo(array());
  }

  //  $form_action = (isset($cID)) ? 'Update' : 'Insert';

  $form_action = 'Insert';

  if (!is_null($cID)) {
    $form_action = 'Update';
  }

  echo HTML::form('new_category', $CLICSHOPPING_Blog->link('BlogCategories&' . $form_action . '&cPath=' . $cPath . '&cID=' . $cID), 'post', 'enctype="multipart/form-data"');

  echo HTMLOverrideAdmin::getCkeditor();
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/blog.png', $CLICSHOPPING_Blog->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Blog->getDef('table_heading_categories'); ?></span>
          <span class="col-md-6 text-end">
<?php
  echo HTML::hiddenField('categories_date_added', ($cInfo->date_added) ?? date('Y-m-d')) . HTML::hiddenField('parent_id', $cInfo->parent_id ?? 0) . HTML::button($CLICSHOPPING_Blog->getDef('button_update'), null, null, 'success') . ' ';
  echo HTML::button($CLICSHOPPING_Blog->getDef('button_cancel'), null, $CLICSHOPPING_Blog->link('BlogCategories&cPath=' . $cPath . '&cID=' . $cID), 'warning');
?>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="separator"></div>
<div id="blogCategoriesTabs" style="overflow: auto;">
  <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
    <li
      class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_Blog->getDef('tab_general') . '</a>'; ?></li>
    <li
      class="nav-item"><?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Blog->getDef('tab_description') . '</a>'; ?></li>
    <li
      class="nav-item"><?php echo '<a href="#tab3" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Blog->getDef('tab_ref') . '</a>'; ?></li>
    <li
      class="nav-item"><?php echo '<a href="#tab4" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Blog->getDef('tab_img') . '</a>'; ?></li>
  </ul>
  <div class="tabsClicShopping">
    <div class="tab-content" id="tab1BlogCategoriesContent">
      <?php
        // -------------------------------------------------------------------
        //          ONGLET General sur la description de la categorie
        // -------------------------------------------------------------------
      ?>
      <div class="tab-pane active" id="tab1">
        <div class="col-md-12 mainTitle">
          <div class="float-start"><?php echo $CLICSHOPPING_Blog->getDef('text_categories_name_title'); ?></div>
        </div>
        <div class="adminformTitle">
          <?php
            for ($i = 0, $n = count($languages); $i < $n; $i++) {
              ?>
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group row">
                    <label for="code"
                           class="col-2 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                    <div class="col-md-5">
                      <?php echo HTML::inputField('blog_categories_name[' . $languages[$i]['id'] . ']', BlogAdmin::getBlogCategoryName($cInfo->blog_categories_id ?? null, $languages[$i]['id']) ?? null, 'class="form-control" required aria-required="true"  id="categories_blog_name" placeholder="' . $CLICSHOPPING_Blog->getDef('text_categories_name') . '"', true) . '&nbsp;'; ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
          ?>
        </div>
        <div class="separator"></div>
        <div class="col-md-12 mainTitle"><?php echo $CLICSHOPPING_Blog->getDef('text_divers_title'); ?></div>
        <div class="adminformTitle">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group row">
                <label for="<?php echo $CLICSHOPPING_Blog->getDef('text_edit_sort_order'); ?>"
                       class="col-2 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_edit_sort_order'); ?></label>
                <div class="col-md-5">
                  <?php echo HTML::inputField('sort_order', $cInfo->sort_order ?? null, 'size="2"'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php echo $CLICSHOPPING_Hooks->output('BlogCategories', 'CustomerGroup', null, 'display'); ?>
      </div>
      <?php
        // ----------------------------------------------------------- //-->
        //          ONGLET sur la designation de la categorie          //-->
        // ----------------------------------------------------------- //-->
      ?>
      <div class="tab-pane" id="tab2">
        <div class="col-md-12 mainTitle">
          <span><?php echo $CLICSHOPPING_Blog->getDef('text_description_categories'); ?></span>
        </div>
        <div class="adminformTitle" id="tab2BlogCategoriesContent">
          <?php
            for ($i = 0, $n = count($languages); $i < $n; $i++) {
              ?>
              <div class="row">
                <div class="col-md-1">
                  <div class="form-group row">
                    <label for="Code"
                           class="col-1 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="lang" class="col-1 col-form-label"></label>
                    <div class="col-md-8">
                      <?php echo HTMLOverrideAdmin::textAreaCkeditor('blog_categories_description[' . $languages[$i]['id'] . ']', 'soft', '750', '300', (isset($blog_categories_description[$languages[$i]['id']]) ? str_replace('& ', '&amp; ', trim($blog_categories_description[$languages[$i]['id']])) : BlogAdmin::getBlogCategoryDescription($cInfo->blog_categories_id ?? null, $languages[$i]['id']))); ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
          ?>
        </div>
        <div class="alert alert-info" role="alert">
          <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Blog->getDef('title_help_description')) . ' ' . $CLICSHOPPING_Blog->getDef('title_help_description') ?></div>
          <div class="separator"></div>
          <div class="spaceRow"></div>
          <div class="row">
              <span class="col-md-12">
                 <?php echo $CLICSHOPPING_Blog->getDef('help_options'); ?>
                <blockquote><i><a data-toggle="modal"
                                  data-target="#myModalWysiwyg2"><?php echo $CLICSHOPPING_Blog->getDef('text_help_wysiwyg'); ?></a></i></blockquote>
                 <div class="modal fade" id="myModalWysiwyg2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                      aria-hidden="true">
                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal"><span
                             aria-hidden="true">&times;</span></button>
                         <h4 class="modal-title"
                             id="myModalLabel"><?php echo $CLICSHOPPING_Blog->getDef('text_help_wysiwyg'); ?></h4>
                       </div>
                       <div class="modal-body text-center">
                         <img class="img-fluid"
                              src="<?php echo $CLICSHOPPING_Template->getImageDirectory() . '/wysiwyg.png'; ?>">
                       </div>
                     </div>
                   </div>
                 </div>
              </span>
          </div>
        </div>
      </div>
      <?php
        // -----------------------------------------------------//-->
        //          ONGLET sur le référencement categories      //-->
        // ---------------------------------------------------- //-->
      ?>
       <div class="tab-pane" id="tab3">
        <div class="col-md-12 mainTitle" id="tab3BlogCategoriesContent">
          <span><?php echo $CLICSHOPPING_Blog->getDef('text_products_page_seo'); ?></span>
        </div>
        <div class="adminformTitle">
          <div class="spaceRow"></div>
          <div class="row">
            <div class="col-md-12 text-center">
              <span class="col-md-3"></span>
              <span class="col-md-3"><a href="https://www.google.fr/trends" target="_blank"
                                        rel="noreferrer"><?php echo $CLICSHOPPING_Blog->getDef('keywords_google_trend'); ?></a></span>
              
            </div>
          </div>
          <?php
            for ($i = 0, $n = count($languages); $i < $n; $i++) {
              ?>

              <div class="row">
                <div class="col-md-1">
                  <div class="form-group row">
                    <label for="Code"
                           class="col-1 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Blog->getDef('text_products_page_title'); ?>"
                           class="col-1 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_products_page_title'); ?></label>
                    <div class="col-md-8">
                      <?php echo HTMLOverrideAdmin::inputField('blog_categories_head_title_tag[' . $languages[$i]['id'] . ']', BlogAdmin::getBlogCategoryDescription($cInfo->blog_categories_id ?? null, $languages[$i]['id']), 'id="default_title_' . $i . '"'); ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Blog->getDef('text_products_header_description'); ?>"
                           class="col-1 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_products_header_description'); ?></label>
                    <div class="col-md-8">
                      <?php echo '&nbsp;' . HTML::textAreaField('blog_categories_head_desc_tag[' . $languages[$i]['id'] . ']', (isset($blog_categories_head_desc_tag[$languages[$i]['id']]) ? $blog_categories_head_desc_tag[$languages[$i]['id']] : BlogAdmin::getBlogCategoriesHeadDescTag($cInfo->blog_categories_id ?? null, $languages[$i]['id'])), '75', '2', 'id="default_description_' . $i . '"'); ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="<?php echo $CLICSHOPPING_Blog->getDef('text_products_keywords'); ?>"
                           class="col-1 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_products_keywords'); ?></label>
                    <div class="col-md-8">
                      <?php echo '&nbsp;' . HTML::inputField('blog_categories_head_keywords_tag[' . $languages[$i]['id'] . ']', BlogAdmin::getBlogCategoriesHeadKeywordsTag($cInfo->blog_categories_id ?? null, $languages[$i]['id'])); ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
          ?>
        </div>
        <div class="alert alert-info" role="alert">
          <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Blog->getDef('title_help_submit')) . ' ' . $CLICSHOPPING_Blog->getDef('title_help_submit') ?></div>
          <div class="separator"></div>
          <div><?php echo $CLICSHOPPING_Blog->getDef('help_submit'); ?></div>
        </div>
      </div>
      <?php
        // -----------------------------------------------------//-->
        //          ONGLET sur l'image de la categorie          //-->
        // ---------------------------------------------------- //-->
      ?>
      <div class="tab-pane" id="tab4">
        <div class="mainTitle">
          <span><?php echo $CLICSHOPPING_Blog->getDef('text_categories_image_title'); ?></span>
        </div>
        <div class="adminformTitle">
          <div class="row">
            <div class="col-md-12">
              <span
                class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'images_product.gif', $CLICSHOPPING_Blog->getDef('text_categories_image_vignette'), '40', '40'); ?></span>
              <span
                class="col-md-3 main"><?php echo $CLICSHOPPING_Blog->getDef('text_categories_image_vignette'); ?></span>
              <span
                class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'images_categories.gif', $CLICSHOPPING_Blog->getDef('text_categories_image_visuel'), '40', '40'); ?></span>
              <span
                class="col-md-7 main"><?php echo $CLICSHOPPING_Blog->getDef('text_categories_image_visuel'); ?></span>
            </div>
            <div class="col-md-12">
              <div class="adminformAide">
                <div class="row">
                  <span
                    class="col-md-4 text-center float-start"><?php echo HTMLOverrideAdmin::fileFieldImageCkEditor('blog_categories_image', null, '300', '300'); ?></span>
                  <span class="col-md-8 text-center float-end">
                      <div class="col-md-8">
                        <?php if (isset($cInfo->blog_categories_image)) echo $CLICSHOPPING_ProductsAdmin->getInfoImage($cInfo->blog_categories_image, $CLICSHOPPING_Blog->getDef('text_categories_image_vignette')); ?>
                      </div>
                      <div class="col-md-12 text-end">
                        <?php echo $CLICSHOPPING_Blog->getDef('text_categories_delete_image') . HTML::checkboxField('delete_image', 'yes', false); ?>
                      </div>
                    </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="separator"></div>
        <div class="alert alert-info" role="alert">
          <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Blog->getDef('title_help_general')) . ' ' . $CLICSHOPPING_Blog->getDef('title_help_general') ?></div>
          <div class="separator"></div>
          <div><?php echo $CLICSHOPPING_Blog->getDef('help_image_categories'); ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</form>
