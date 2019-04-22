<?php
/**
 * The browse group view file of kevindevice module
 *
 * @copyright   Kevin
 * @author      kevin<3301647@qq.com>
 * @package     kevindevice
 */
?>
<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<?php js::set('confirmDelete', $lang->kevindevice->confirmDelete . "'". $lang->kevindevice->group ) . "'?"; ?>

<?php include 'kevindevicebar.html.php'; ?>
<table align='center' class='table table-condensed table-hover table-striped  tablesorter table-fixed' id='groupList'>
  <thead>
    <tr>
      <th class='w-auto'><?php echo $lang->kevindevice->id; ?></th>
      <th class='w-auto'><?php echo $lang->kevindevice->name; ?></th>
      <th><?php echo $lang->kevindevice->desc; ?></th>
<!--      <th class='w-p60'><?php// echo $lang->kevindevice->devices; ?></th>-->
      <th class='w-auto {sorter:false}'><?php echo $lang->actions; ?></th>
    </tr>
  </thead>
  <tbody>
      <?php
      foreach ($groups as $group):
          $devices = implode(' ', $groupUsers[$group->id]);
          ?>
        <tr class='text-center'>
          <td class='strong'><?php echo $group->id; ?></td>
          <td class='text-center'><?php common::printLink('kevindevice', 'groupview', "groupid=$group->id", $group->name); ?></td>
          <td class='text-center'><?php echo $group->desc; ?></td>
<!--          <td class='text-left' title='<?php //echo $devices; ?>'><?php// echo $devices; ?></td>-->
          <td class='text-center'>
            <?php
            echo " ";
            common::printIcon('kevindevice', 'groupedit', "groupID=$group->id", '', 'edit', 'pencil', '', 'iframe', 'yes');
            if (common::hasPriv('kevindevice', 'groupdelete')) {
                echo " ";
                common::printIcon('kevindevice', 'groupdelete', "groupID=$group->id", '', 'edit', 'remove', '', 'iframe', 'yes');
            }
            ?>
          </td>
        </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include '../../common/view/footer.html.php'; ?>
