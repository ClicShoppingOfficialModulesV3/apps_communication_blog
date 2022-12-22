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
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  $CLICSHOPPING_Blog = Registry::get('Blog');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $CLICSHOPPING_Hooks->call('BlogContent', 'PreAction');

  $parameters = ['blog_content_name' => '',
    'blog_content_description' => '',
    'blog_content_id' => '',
    'blog_content_date_added' => '',
    'blog_content_last_modified' => '',
    'blog_content_date_available' => '',
    'blog_content_status' => '',
    'blog_content_sort_order' => '',
    'blog_content_author' => '',
    'customers_group_id' => '',
    'blog_content_description_summary' => ''
  ];

  $pInfo = new ObjectInfo($parameters);

  if (isset($_GET['pID'])) {
    $QblogContent = $CLICSHOPPING_Blog->db->prepare('select pd.blog_content_name,
                                                             pd.blog_content_description,
                                                             pd.blog_content_head_title_tag,
                                                             pd.blog_content_head_desc_tag,
                                                             pd.blog_content_head_keywords_tag,
                                                             pd.blog_content_head_tag_product,
                                                             pd.blog_content_head_tag_blog,
                                                             p.blog_content_id,
                                                             p.blog_content_date_added,
                                                             p.blog_content_last_modified,
                                                             date_format(p.blog_content_date_available, "%d-%m-%Y") as blog_content_date_available,
                                                             p.blog_content_status,
                                                             p.blog_content_sort_order,
                                                             p.blog_content_author,
                                                             p.customers_group_id,
                                                            pd.blog_content_description_summary
                                                     from :table_blog_content p,
                                                          :table_blog_content_description pd
                                                     where p.blog_content_id = :blog_content_id
                                                     and p.blog_content_id = pd.blog_content_id
                                                     and pd.language_id = :language_id
                                                    ');
    $QblogContent->bindInt(':blog_content_id', (int)$_GET['pID']);
    $QblogContent->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
    $QblogContent->execute();

    $blog_content = $QblogContent->fetch();

    $pInfo->ObjectInfo($QblogContent->toArray());
  } else {
    $pInfo->ObjectInfo(array());

  }

  $languages = $CLICSHOPPING_Language->getLanguages();

  echo HTML::form('blog', $CLICSHOPPING_Blog->link('BlogContent&Save&cPath=' . $_GET['cPath'] . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')), 'post', 'enctype="multipart/form-data"');

  $current_category_id = HTML::sanitize($_GET['cPath']);

  echo HTMLOverrideAdmin::getCkeditor();
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/blog_edit.png', $CLICSHOPPING_Blog->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Blog->getDef('text_new_product', ['category_name' => BlogAdmin::getOutputGeneratedBlogCategoryPath($current_category_id)]); ?></span>
          <span class="col-md-6 text-end">
<?php
  echo HTML::hiddenField('blog_content_date_added', $pInfo->blog_content_date_added ?? date('Y-m-d'));

  if (isset($_GET['Edit'])) {
    echo HTML::button($CLICSHOPPING_Blog->getDef('button_update'), null, null, 'success') . ' ';
  } else {
    echo HTML::button($CLICSHOPPING_Blog->getDef('button_insert'), null, null, 'success') . ' ';
  }

  echo HTML::button($CLICSHOPPING_Blog->getDef('button_cancel'), null, $CLICSHOPPING_Blog->link('BlogContent&cPath=' . $_GET['cPath'] . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')), 'warning');
?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div id="blogContentTabs" style="overflow: auto;">
    <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
      <li
        class="nav-item"><?php echo '<a href="#tab1" role="tab" data-toggle="tab" class="nav-link active">' . $CLICSHOPPING_Blog->getDef('tab_general') . '</a>'; ?></li>
      <li
        class="nav-item"><?php echo '<a href="#tab2" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Blog->getDef('tab_description'); ?></a></li>
      <li
        class="nav-item"><?php echo '<a href="#tab3" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Blog->getDef('tab_ref'); ?></a></li>
    </ul>
    <div class="tabsClicShopping">
      <div class="tab-content">
        <?php
          // ---------------------------------------------------------------//-->
          //          ONGLET General sur les informations produits          //-->
          // -------------------------------------------------------------- //-->
        ?>
        <div class="tab-pane active" id="tab1">
          <div class="mainTitle">
            <span class="col-md-2 mainTitle"><?php echo $CLICSHOPPING_Blog->getDef('text_products_name'); ?></span>
            <span
              class="col-md-10 mainTitle text-end"><?php echo $CLICSHOPPING_Blog->getDef('text_user_name') . AdministratorAdmin::getUserAdmin(); ?></span>
          </div>
          <div class="adminformTitle" id="tab1Block1Content">

            <?php
              for ($i = 0, $n = count($languages); $i < $n; $i++) {
                ?>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="code"
                             class="col-2 col-form-label"><?php echo $CLICSHOPPING_Language->getImage($languages[$i]['code']); ?></label>
                      <div class="col-md-5">
                        <?php echo HTML::inputField('blog_content_name[' . $languages[$i]['id'] . ']', (isset($blog_content_name[$languages[$i]['id']]) ? $blog_content_name[$languages[$i]['id']] : BlogAdmin::getBlogContentName($pInfo->blog_content_id, $languages[$i]['id'])), 'class="form-control" required aria-required="true" required="" id="blog_name" placeholder="' . $CLICSHOPPING_Blog->getDef('text_products_name') . '"', true) . '&nbsp;'; ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              }
            ?>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="code"
                         class="col-2 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_categories_name'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::selectMenu('move_to_category_id', BlogAdmin::getBlogCategoryTree(), $current_category_id) . HTML::hiddenField('current_category_id', $current_category_id); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="separator"></div>
          <div class="mainTitle"><?php echo $CLICSHOPPING_Blog->getDef('text_products_presentation'); ?></div>
          <div class="adminformTitle" id="tab1Block2Content">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="code"
                         class="col-2 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_sort_order'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('blog_content_sort_order', $pInfo->blog_content_sort_order, 'size="2"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label for="code"
                         class="col-2 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_products_author'); ?></label>
                  <div class="col-md-5">
                    <?php echo HTML::inputField('blog_content_author', $pInfo->blog_content_author, 'size="30"'); ?>
                  </div>
                </div>
              </div>
            </div>

            <?php
              // status
              if (empty($blog_content['blog_content_status'])) {
                $blog_content_status = '1';
                echo HTML::hiddenField('blog_content_status', $blog_content_status);
              } else {
                echo HTML::hiddenField('blog_content_status', $blog_content['blog_content_status']);
              }

              echo $CLICSHOPPING_Hooks->output('BlogContent', 'PageTwitter', null, 'display');
            ?>
          </div>

          <?php
            echo $CLICSHOPPING_Hooks->output('BlogContent', 'CustomerGroup', null, 'display');
          ?>

        </div>
        <?php
          // ------------------------------------ //-->
          //          ONGLET Description          //-->
          // ------------------------------------ //-->
        ?>
        <div class="tab-pane" id="tab2">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Blog->getDef('text_products_header_description'); ?></div>
          <div class="adminformTitle" id="tab2Block1Content">
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
                        <?php echo HTMLOverrideAdmin::textAreaCkeditor('blog_content_description[' . $languages[$i]['id'] . ']', 'soft', '750', '300', (isset($blog_content_description[$languages[$i]['id']]) ? str_replace('& ', '&amp; ', trim($blog_content_description[$languages[$i]['id']])) : BlogAdmin::getBlogContentDescription($pInfo->blog_content_id, $languages[$i]['id']))); ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label for="code"
                             class="col-2 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_products_description_summary'); ?></label>
                      <div class="col-md-10">
                        <?php echo HTML::textAreaField('blog_content_description_summary[' . $languages[$i]['id'] . ']', (isset($blog_content_description_summary[$languages[$i]['id']]) ? str_replace('& ', '&amp; ', trim($blog_content_description_summary[$languages[$i]['id']])) : BlogAdmin::getBlogContentDescriptionSummary($pInfo->blog_content_id, $languages[$i]['id'])), '120', '3'); ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              }
            ?>
          </div>

          <div class="separator"></div>
          <div class="alert alert-info" role="alert">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Blog->getDef('title_help_description')) . ' ' . $CLICSHOPPING_Blog->getDef('title_help_description') ?></div>
            <div class="separator"></div>
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
                       <div class="modal-body text-md_center">
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
          // ----------------------------- //-->
          //          ONGLET Referencement //-->
          // ----------------------------- //-->
        ?>
        <div class="tab-pane" id="tab3">
          <div class="mainTitle"><?php echo $CLICSHOPPING_Blog->getDef('text_products_page_refefrencement'); ?></div>
          <div class="adminformTitle" id="tab3Block1Content">
            <div class="row">
              <div class="separator"></div>
              <div class="col-md-12 text-center">
                <span class="col-md-6 text-center"><a href="https://www.google.fr/trends" target="_blank"
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
                        <?php echo '&nbsp;' . HTML::inputField('blog_content_head_title_tag[' . $languages[$i]['id'] . ']', BlogAdmin::getBlogContentHeadTitleTag($pInfo->blog_content_id, $languages[$i]['id'] ?? null), 'maxlength="70" size="77" id="default_title_' . $i . '"', false); ?>
                        &nbsp;
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
                        <?php echo HTML::textAreaField('blog_content_head_desc_tag[' . $languages[$i]['id'] . ']', BlogAdmin::getBlogContentHeadDescTag($pInfo->blog_content_id, $languages[$i]['id']) ?? null, '75', '2', 'id="default_description_' . $i . '"'); ?>
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
                        <?php echo HTML::textAreaField('blog_content_head_keywords_tag[' . $languages[$i]['id'] . ']', BlogAdmin::getBlogContentHeadKeywordsTag($pInfo->blog_content_id, $languages[$i]['id']) ?? null, '75', '5'); ?>
                        &nbsp;
                      </div>
                    </div>
                  </div>
                </div>


                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Blog->getDef('text_products_tag_product'); ?>"
                             class="col-1 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_products_tag_product'); ?></label>
                      <div class="col-md-8">
                        <?php echo '&nbsp;' . HTML::inputField('blog_content_head_tag_product[' . $languages[$i]['id'] . ']', BlogAdmin::getBlogContentTagProduct($pInfo->blog_content_id, $languages[$i]['id']) ?? null, 'maxlength="100" size="77" id="default_tag_product_' . $i . '"', false); ?>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group row">
                      <label for="<?php echo $CLICSHOPPING_Blog->getDef('text_products_tag_blog'); ?>"
                             class="col-1 col-form-label"><?php echo $CLICSHOPPING_Blog->getDef('text_products_tag_blog'); ?></label>
                      <div class="col-md-8">
                        <?php echo '&nbsp;' . HTML::inputField('blog_content_head_tag_blog[' . $languages[$i]['id'] . ']', BlogAdmin::getBlogContentTagBlog($pInfo->blog_content_id, $languages[$i]['id'] ?? null), 'maxlength="100" size="77" id="default_tag_blog_' . $i . '"', false); ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              }
            ?>
          </div>
          <div class="separator"></div>
          <div class="alert alert-info" role="alert">
            <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Blog->getDef('title_help_submit')) . ' ' . $CLICSHOPPING_Blog->getDef('title_help_submit') ?></div>
            <div class="separator"></div>
            <div><?php echo $CLICSHOPPING_Blog->getDef('help_submit'); ?></div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
      <?php
        //***********************************
        // extension
        //***********************************
        echo $CLICSHOPPING_Hooks->output('BlogContent', 'Page', null, 'display');
      ?>
      <!-- fin produit -->
    </div>
  </div>
</div>
</form>
