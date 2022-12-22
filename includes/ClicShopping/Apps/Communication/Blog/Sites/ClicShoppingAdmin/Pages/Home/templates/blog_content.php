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
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  $CLICSHOPPING_Blog = Registry::get('Blog');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $action = $_GET['action'] ?? '';

  if ($CLICSHOPPING_MessageStack->exists('main')) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }
?>
<div class="contentBody">

  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/blog.png', $CLICSHOPPING_Blog->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-2 pageHeading "><?php echo '&nbsp;' . $CLICSHOPPING_Blog->getDef('heading_title'); ?></span>
          <span class="col-md-2">
<?php
  echo HTML::form('search', $CLICSHOPPING_Blog->link('BlogContent'), 'post', null, ['session_id' => true]);
  echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_Blog->getDef('heading_title_search') . '"');
?>
            </form>
          </span>
          <span class="col-md-2">
            <div class="form-group">
<?php
  if (isset($_POST['cPath'])) {
    $current_category_id = HTML::sanitize($_POST['cPath']);
  } elseif (isset($_GET['cPath'])) {
    $current_category_id = HTML::sanitize($_GET['cPath']);
  } else {
    $current_category_id = $_POST['cPath'] = 0;
  }


  echo HTML::form('goto', $CLICSHOPPING_Blog->link('BlogContent'), 'post', null, ['session_id' => true]);
  echo HTML::selectMenu('cPath', BlogAdmin::getBlogCategoryTree(), $current_category_id, 'onchange="this.form.submit();"');
?>
               </form>
            </div>
          </span>
          <span class="col-md-5 text-end">
<?php
  $cPath_back = null;

  if (isset($cPath_array) && count($cPath_array) > 0) {
    for ($i = 0, $n = count($cPath_array) - 1; $i < $n; $i++) {
      if (empty($cPath_back)) {
        $cPath_back .= $cPath_array[$i];
      } else {
        $cPath_back .= '_' . $cPath_array[$i];
      }
    }
  } else {
    $cPath_back = $current_category_id;
  }

  $cPath_back = (!is_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';

  echo HTML::button($CLICSHOPPING_Blog->getDef('button_back'), null, $CLICSHOPPING_Blog->link('BlogContent&' . $cPath_back . 'cID=' . $current_category_id), 'primary') . '&nbsp;';

  if (!isset($_GET['search'])) {
    echo HTML::button($CLICSHOPPING_Blog->getDef('button_new_category'), null, $CLICSHOPPING_Blog->link('BlogCategoriesEdit&cPath=' . $current_category_id), 'info') . '&nbsp;';
    echo HTML::button($CLICSHOPPING_Blog->getDef('button_new_ticket'), null, $CLICSHOPPING_Blog->link('BlogContentEdit&cPath=' . $current_category_id), 'success');
  }

  if (!isset($_GET['av'])) {
    echo ' ' . HTML::button($CLICSHOPPING_Blog->getDef('button_archive'), null, $CLICSHOPPING_Blog->link('BlogContentArchiveView'), 'warning');
  }

  echo HTML::form('delete_all', $CLICSHOPPING_Blog->link('BlogContent&DeleteAll&cPath=' . $current_category_id));
?>
                <a onClick="$('delete').prop('action', ''); $('form').submit();"
                   class="button"><?php echo HTML::button($CLICSHOPPING_Blog->getDef('button_delete'), null, null, 'danger'); ?></a>&nbsp;

          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <!-- // select all the product to delete -->
          <th width="1" class="text-center"><input type="checkbox"
                                                      onClick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
          </th>
          <th colspan="4">&nbsp;</th>
          <th><?php echo $CLICSHOPPING_Blog->getDef('table_heading_categories_products'); ?></th>
          <?php
            if (MODE_B2B_B2C == 'true') {
              ?>
              <th><?php echo $CLICSHOPPING_Blog->getDef('table_heading_customers_groups'); ?></th>
              <?php
            }
          ?>
          <th class="text-center"><?php echo $CLICSHOPPING_Blog->getDef('table_heading_status'); ?></th>
          <th></th>
          <th class="text-center"><?php echo $CLICSHOPPING_Blog->getDef('table_heading_last_modified'); ?>&nbsp;</th>
          <th><?php echo $CLICSHOPPING_Blog->getDef('table_heading_created'); ?>&nbsp;</th>
          <th class="text-center"><?php echo $CLICSHOPPING_Blog->getDef('table_heading_sort_order'); ?>&nbsp;</th>
          <th class="text-end"><?php echo $CLICSHOPPING_Blog->getDef('table_heading_action'); ?>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
          // ################################################################################################################ -->
          //                                         LISTING PRODUITS                                          -->
          //################################################################################################################ -->

          $blog_content_count = 0;

          // Recherche des produits
          if (isset($_POST['search'])) {
            $QblogContent = $CLICSHOPPING_Blog->db->prepare('select p.blog_content_id,
                                                             pd.blog_content_name,
                                                             p.blog_content_date_added,
                                                             p.blog_content_last_modified,
                                                             p.blog_content_date_available,
                                                             p.blog_content_status,
                                                             p.admin_user_name,
                                                             p2c.blog_categories_id,
                                                             p.blog_content_sort_order,
                                                             p.blog_content_author
                                                     from :table_blog_content p,
                                                          :table_blog_content_description pd,
                                                          :table_blog_content_to_categories p2c
                                                     where p.blog_content_id = pd.blog_content_id
                                                     and pd.language_id = :language_id
                                                     and p.blog_content_id = p2c.blog_content_id
                                                     and p.blog_content_archive = 0
                                                     and pd.blog_content_name like :search
                                                     order by pd.blog_content_name
                                            ');

            $QblogContent->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $QblogContent->bindValue(':search', '%' . $search . '%');
            $QblogContent->execute();
// Archive
          } else {
            $QblogContent = $CLICSHOPPING_Blog->db->prepare('select p.blog_content_id,
                                                             pd.blog_content_name,
                                                             p.blog_content_date_added,
                                                             p.blog_content_last_modified,
                                                             p.blog_content_date_available,
                                                             p.blog_content_status,
                                                             p.admin_user_name,
                                                             p.blog_content_sort_order,
                                                             p.blog_content_author,
                                                             p.customers_group_id
                                                     from :table_blog_content p,
                                                          :table_blog_content_description pd,
                                                          :table_blog_content_to_categories p2c
                                                     where p.blog_content_id = pd.blog_content_id
                                                     and pd.language_id = :language_id
                                                     and p.blog_content_id = p2c.blog_content_id
                                                     and p2c.blog_categories_id = :blog_categories_id
                                                     and p.blog_content_archive = 0
                                                     order by pd.blog_content_name
                                                  ');
            $QblogContent->bindInt(':blog_categories_id', $current_category_id);
            $QblogContent->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $QblogContent->execute();
          }

          $blog_categories_count = 0;

          while ($QblogContent->fetch()) {

            $blog_categories_count++;

// Permettre l'affichage des groupes en mode B2B
            if (MODE_B2B_B2C == 'true') {
              if ($QblogContent->valueInt('customers_group_id') != 0 && $QblogContent->valueInt('customers_group_id') != 99) {
                $customers_group['customers_group_name'] = GroupsB2BAdmin::getCustomersGroupName($QblogContent->valueInt('customers_group_id'));
              } elseif ($QblogContent->valueInt('customers_group_id') == 99) {
                $customers_group['customers_group_name'] = $CLICSHOPPING_Blog->getDef('text_all_groups');
              } else {
                $customers_group['customers_group_name'] = $CLICSHOPPING_Blog->getDef('normal_customer');
              }
            }


// Get blog_categories_id for product if search
            if (isset($_POST['search'])) $current_category_id = $QblogContent->valueInt('blog_categories_id');
            ?>
            <td>
              <?php // select all the product to delete
                if (isset($_POST['selected'])) {
                  ?>
                  <input type="checkbox" name="selected[]"
                         value="<?php echo $QblogContent->valueInt('blog_content_id'); ?>" checked="checked"/>
                  <?php
                } else {
                  ?>
                  <input type="checkbox" name="selected[]"
                         value="<?php echo $QblogContent->valueInt('blog_content_id'); ?>"/>
                  <?php
                }
              ?>
            </td>
            <?php
            if ($QblogContent->valueInt('blog_content_status') == 1) {
              ?>
              <td
                width="20px;"><?php echo '<a href="' . HTTP::getShopUrlDomain() . 'index.php?&Blog&Content&blog_content_id=' . $QblogContent->valueInt('blog_content_id') . '" target="_blank" rel="noreferrer">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/preview_catalog.png', $CLICSHOPPING_Blog->getDef('icon_preview')) . '</a>'; ?></td>
              <?php
            } else {
              ?>
              <td></td>
              <?php
            }
            ?>
            <td></td>
            <td><?php echo $QblogContent->value('blog_content_name'); ?></td>
            <td></td>
            <td></td>
            <?php
// Permettre l'affichage des groupes en mode B2B
            if (MODE_B2B_B2C == 'true') {
              ?>
              <td><?php echo $customers_group['customers_group_name']; ?></td>
              <?php
            }
            ?>
            <td class="text-center">
              <?php
                if ($QblogContent->valueInt('blog_content_status') == 1) {
                  echo '<a href="' . $CLICSHOPPING_Blog->link('BlogContent&SetFlag&flag=0&pID=' . $QblogContent->valueInt('blog_content_id') . '&cPath=' . $current_category_id) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                } else {
                  echo '<a href="' . $CLICSHOPPING_Blog->link('BlogContent&SetFlag&flag=1&pID=' . $QblogContent->valueInt('blog_content_id') . '&cPath=' . $current_category_id) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                }
              ?>
            </td>
            <td class="text-center"></td>
            <?php
            if (!is_null($QblogContent->value('last_modified'))) {
              echo '<td class="text-center">' . DateTime::toShort($QblogContent->value('last_modified')) . '</td>';
            } else {
              echo '<td class="text-center"></td>';
            }
            ?>
            <td class="text-center"><?php echo $QblogContent->value('admin_user_name'); ?></td>

            <td class="text-center"><?php echo $QblogContent->value('blog_content_sort_order'); ?></td>
            <td class="text-end">
              <?php
                echo '<a href="' . $CLICSHOPPING_Blog->link('BlogContentEdit&Edit&cPath=' . $current_category_id . '&pID=' . $QblogContent->valueInt('blog_content_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Blog->getDef('image_edit')) . '</a>';
                echo '&nbsp;';
                echo '<a href="' . $CLICSHOPPING_Blog->link('BlogContentMove&cPath=' . $current_category_id . '&pID=' . $QblogContent->valueInt('blog_content_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/move.gif', $CLICSHOPPING_Blog->getDef('image_move')) . '</a>';
                echo '&nbsp;';
                echo '<a href="' . $CLICSHOPPING_Blog->link('BlogContentCopy&cPath=' . $current_category_id . '&pID=' . $QblogContent->valueInt('blog_content_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/copy.gif', $CLICSHOPPING_Blog->getDef('image_copy_to')) . '</a>';
                echo '&nbsp;';

                if (!isset($_GET['av'])) {
                  echo '<a href="' . $CLICSHOPPING_Blog->link('BlogContentArchive&pID=' . $QblogContent->valueInt('blog_content_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/archive.gif', $CLICSHOPPING_Blog->getDef('image_archive_to')) . '</a>';
                } else {
                  echo '<a href="' . $CLICSHOPPING_Blog->link('BlogContent&Unpack&cPath=' . $current_category_id . '&pID=' . $QblogContent->valueInt('blog_content_id') . '&action=unpack') . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/unpack.gif', $CLICSHOPPING_Blog->getDef('image_unpack')) . '</a>';
                }

                echo '&nbsp;';
                echo '<a href="' . $CLICSHOPPING_Blog->link('BlogContentDelete&cPath=' . $current_category_id . '&pID=' . $QblogContent->valueInt('blog_content_id')) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Blog->getDef('image_delete')) . '</a>';
                echo '&nbsp;';
              ?>
            </td>
            </tr>
            <?php
          }
        ?>
        </tbody>
      </table>
    </td>
    </form>
    </tr>
  </table>
  <div><?php echo $CLICSHOPPING_Blog->getDef('text_categories_name') . '&nbsp;' . $blog_categories_count . '<br />' . $CLICSHOPPING_Blog->getDef('text_products') . '&nbsp;' . $blog_content_count; ?></div>
</div>