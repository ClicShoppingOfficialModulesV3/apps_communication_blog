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
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  use ClicShopping\Apps\Communication\Blog\Classes\ClicShoppingAdmin\BlogAdmin;

  $CLICSHOPPING_Blog = Registry::get('Blog');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_CategoriesAdmin = Registry::get('CategoriesAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');

  $action = $_GET['action'] ?? '';

  $CLICSHOPPING_Hooks->call('BlobCategories', 'PreAction');

  $customers_group = GroupsB2BAdmin::getAllGroups();
  $customers_group_name = '';

  foreach ($customers_group as $value) {
    $customers_group_name .= '<option value="' . $value['id'] . '">' . $value['text'] . '</option>';
  } // end empty action
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/blog.png', $CLICSHOPPING_Blog->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Blog->getDef('heading_title'); ?></span>
          <span class="col-md-2">
           <div class="form-group">
             <div class="controls">
<?php
  echo HTML::form('search', $CLICSHOPPING_Blog->link('BlogCategories'), 'post', null, ['session_id' => true]);
  echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_Blog->getDef('test_search') . '"');
?>
               </form>
             </div>
           </div>
          </span>

          <span class="col-md-3 text-center">
           <div class="form-group">
             <div class="controls">
<?php
  if (isset($_POST['cPath'])) {
    $current_category_id = HTML::sanitize($_POST['cPath']);
  } else {
    $current_category_id = 0;
  }

  echo HTML::form('goto', $CLICSHOPPING_Blog->link('BlogCategories'), 'post', null, ['session_id' => true]);
  echo HTML::selectMenu('cPath', BlogAdmin::getBlogCategoryTree(), $current_category_id, 'onchange="this.form.submit();"');
  echo '</form>';
?>
               </form>
             </div>
           </div>

          </span>
          <span class="col-md-4 text-end">
<?php
  $cPath_back = null;

  $cPath_array = $CLICSHOPPING_CategoriesAdmin->getPathArray();

  if (isset($cPath_array) && count($cPath_array) > 0) {
    for ($i = 0, $n = count($cPath_array) - 1; $i < $n; $i++) {
      if (empty($cPath_back)) {
        $cPath_back .= $cPath_array[$i];
      } else {
        $cPath_back .= '_' . $cPath_array[$i];
      }
    }
  }

  $cPath_back = (!is_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';

  echo HTML::button($CLICSHOPPING_Blog->getDef('button_back'), null, $CLICSHOPPING_Blog->link('BlogCategories&' . $cPath_back . 'cID=' . $current_category_id), 'primary') . '&nbsp;';

  if (!isset($_POST['search'])) {
    echo HTML::button($CLICSHOPPING_Blog->getDef('button_new_category'), null, $CLICSHOPPING_Blog->link('BlogCategoriesEdit&cPath=' . $current_category_id), 'info') . '&nbsp;';
    echo HTML::button($CLICSHOPPING_Blog->getDef('button_new_ticket'), null, $CLICSHOPPING_Blog->link('BlogContentEdit&cPath=' . $current_category_id), 'success');
  }
?>
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
            // Permettre l'affichage des groupes en mode B2B
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
          $blog_categories_count = 0;

          if (isset($_POST['search'])) {
            $search = HTML::sanitize($_POST['search']);

            $Qcategories = $CLICSHOPPING_Blog->db->prepare('select SQL_CALC_FOUND_ROWS c.blog_categories_id,
                                                                                 cd.blog_categories_name,
                                                                                 c.blog_categories_image,
                                                                                 c.parent_id,
                                                                                 c.sort_order,
                                                                                 c.date_added,
                                                                                 c.last_modified,
                                                                                 c.customers_group_id
                                                      from :table_blog_categories c,
                                                           :table_blog_categories_description cd
                                                      where c.blog_categories_id = cd.blog_categories_id
                                                      and cd.language_id = :language_id
                                                      and cd.blog_categories_name like :search
                                                      order by c.sort_order,
                                                               cd.blog_categories_name
                                                      limit :page_set_offset,
                                                            :page_set_max_results
                                                      ');
            $Qcategories->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
            $Qcategories->bindValue(':search', '%' . $search . '%');
          } else {
            $Qcategories = $CLICSHOPPING_Blog->db->prepare('select SQL_CALC_FOUND_ROWS c.blog_categories_id,
                                                                                 cd.blog_categories_name,
                                                                                 c.blog_categories_image,
                                                                                 c.parent_id,
                                                                                 c.sort_order,
                                                                                 c.date_added,
                                                                                 c.last_modified,
                                                                                 c.customers_group_id
                                                      from :table_blog_categories c,
                                                           :table_blog_categories_description cd
                                                      where c.parent_id = :parent_id
                                                      and c.blog_categories_id = cd.blog_categories_id
                                                      and cd.language_id = :language_id
                                                      order by c.sort_order,
                                                               cd.blog_categories_name
                                                ');

            $Qcategories->bindInt(':parent_id', $current_category_id);
            $Qcategories->bindInt(':language_id', $CLICSHOPPING_Language->getId());
            $Qcategories->execute();
          }

          while ($Qcategories->fetch()) {
            $blog_categories_count++;

// Get parent_id for subcategories if search
            if (isset($_POST['search'])) $current_category_id = $Qcategories->valueInt('parent_id');

            if ((!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ((int)$_GET['cID'] === $Qcategories->valueInt('blog_categories_id')))) && !isset($cInfo)) {
              $category_childs = ['childs_count' => BlogAdmin::getChildsInBlogCategoryCount($Qcategories->valueInt('blog_categories_id'))];
              $category_blog_content = ['blog_content_count' => BlogAdmin::getBlogContentInCategoryCount($Qcategories->valueInt('blog_categories_id'))];

              $cInfo_array = array_merge($Qcategories->toArray(), $category_childs, $category_blog_content);
              $cInfo = new ObjectInfo($cInfo_array);
            }

// Permettre l'affichage des groupes en mode B2B
            if (MODE_B2B_B2C == 'true') {
              if ($Qcategories->valueInt('customers_group_id') != 0 && $Qcategories->valueInt('customers_group_id') != 99) {
                $customers_group['customers_group_name'] = GroupsB2BAdmin::getCustomersGroupName($Qcategories->valueInt('customers_group_id'));
              } elseif ($Qcategories->valueInt('customers_group_id') == 99) {
                $customers_group['customers_group_name'] = $CLICSHOPPING_Blog->getDef('text_all_groups');
              } else {
                $customers_group['customers_group_name'] = $CLICSHOPPING_Blog->getDef('visitor_name');
              }
            }
            ?>
            <th scope="row" class="text-center">&nbsp;</th>
            <td><?php echo HTML::link($CLICSHOPPING_Blog->link('BlogCategories&' . $CLICSHOPPING_CategoriesAdmin->getPath($Qcategories->valueInt('blog_categories_id')))); ?><span class="text-primary"><i class="fas fa-folder fa-1x primary"></i></span></td>
            <td colspan="3">&nbsp;</td>
            <td
              class="text-start"><?php echo '<strong>' . $Qcategories->value('blog_categories_name') . '</strong>'; ?></td>
            <?php
// Permettre l'affichage des groupes en mode B2B
            if (MODE_B2B_B2C == 'true') {
              ?>
              <td class="text-start"><?php echo $customers_group['customers_group_name']; ?></td>
              <?php
            }
            ?>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php
            if (!is_null($Qcategories->value('last_modified'))) {
              echo '<td class="text-center">' . DateTime::toShort($Qcategories->value('last_modified')) . '</td>';
            } else {
              echo '<td class="text-center"></td>';
            }
            ?>
            <td class="text-center">&nbsp;</td>
            <td class="text-center"><?php echo $Qcategories->valueInt('sort_order'); ?></td>
            <td class="text-end">
              <?php
                echo HTML::link($CLICSHOPPING_Blog->link('BlogCategoriesEdit&cPath=' . $current_category_id . '&cID=' . $Qcategories->valueInt('blog_categories_id') . '&action=edit_category'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Blog->getDef('image_edit'))) . '</a>';
                echo '&nbsp;';
                echo HTML::link($CLICSHOPPING_Blog->link('BlogCategoriesMove&cPath=' . $current_category_id . '&cID=' . $Qcategories->valueInt('blog_categories_id') . '&action=move_category'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/move.gif', $CLICSHOPPING_Blog->getDef('image_move'))) . '</a>';
                echo '&nbsp;';
                echo HTML::link($CLICSHOPPING_Blog->link('BlogCategoriesDelete&cPath=' . $current_category_id . '&cID=' . $Qcategories->valueInt('blog_categories_id') . '&action=delete_category'), HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Blog->getDef('image_delete'))) . '</a>';
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
  </table>
  </form>
  <div><?php echo $CLICSHOPPING_Blog->getDef('text_categories') . '&nbsp;' . $blog_categories_count; ?></div>
</div>