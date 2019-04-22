<?php
/**
 * The devcreate view of kevindevice module
 *
 * @copyright   Kevin
 * @author      kevin<3301647@qq.com>
 * @package     kevindevice
 */
?>
<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('holders', $lang->user->placeholder); ?>
<?php js::set('typeGroup', $typeGroup); ?>
<div class='container mw-800px'>
  <div id='titlebar'>
    <div class='heading'>
      <span class='prefix'><?php echo html::icon($lang->icons['user']); ?></span>
      <strong><small class='text-muted'><?php echo html::icon($lang->icons['create']); ?></small> <?php echo $lang->user->create; ?></strong>
    </div>
  </div>
  <form class='form-condensed mw-800px' method='post' target='hiddenwin' id='dataform'>
    <table align='center' class='table table-form'> 
      <tr>
        <th class='w-120px'><?php echo $lang->kevindevice->name; ?></th>
        <td class='w-auto'><?php echo html::input('name', '', "class='form-control' autocomplete='off'"); ?></td>
      </tr>
      <tr>
        <th><?php echo $lang->kevindevice->type; ?></th>
        <td><?php echo html::select('type', $lang->kevindevice->DevTypeList, '', "class='form-control'"); ?></td>
      </tr>
      <tr>    
        <th><?php echo $lang->kevindevice->status; ?></th>
        <td><?php echo html::select('status', $lang->kevindevice->StatusList, '', "class='form-control'"); ?></td>      </tr>    
      </tr>
      <tr>
        <th><?php echo $lang->kevindevice->dept; ?></th>
        <td><?php echo html::select('dept', $depts, $deptID, "class='form-control chosen'"); ?></td>
      </tr>
      <tr>
        <th><?php echo $lang->kevindevice->group; ?></th>
        <td><?php echo html::select('group', $groupList, 0, "class='form-control chosen'"); ?></td>
      </tr>
      <tr>
        <th><?php echo $lang->kevindevice->join; ?></th>
        <td><?php echo html::input('join', '', "class='form-control form-date'"); ?></td>
      </tr>     
      <tr>
        <th><?php echo $lang->kevindevice->desc; ?></th>
        <td><?php echo html::textarea('description', "", "rows='5' class='form-control autosize' style='height: 108px;'"); ?></td>
      </tr>            
      <tr><th></th><td><?php echo html::submitButton() . html::backButton(); ?></td></tr>
    </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php'; ?>
