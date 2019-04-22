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
<div class='container mw-500px'>
  <div id='titlebar'>
    <div class='heading'>
      <span class='prefix' title='GROUP'><?php echo html::icon($lang->icons['group']); ?></span>
      <strong><small><?php echo html::icon($lang->icons['create']); ?></small> <?php echo $lang->kevindevice->groupcreate; ?></strong>
    </div>
  </div>
  <form class='form-condensed mw-500px pdb-20' method='post' target='hiddenwin' id='dataform'>
    <table align='center' class='table table-form'> 
      <tr>
        <th class='w-80px'><?php echo $lang->kevindevice->name; ?></th>
        <td><?php echo html::input('name', '', "class=form-control"); ?></td>
      </tr>  
	 <tr>
        <th class='w-80px'><?php echo $lang->kevindevice->type; ?></th>
	  	<td><?php echo html::select('type', $lang->kevindevice->GroupTypeList,'', "class='form-control'"); ?></td>
	</tr>  
      <tr>
        <th><?php echo $lang->kevindevice->desc; ?></th>
        <td><?php echo html::textarea('desc', '', "rows=5 class=form-control"); ?></td>
      </tr>  
      <tr><th></th><td><?php echo html::submitButton(); ?></td></tr>
    </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php'; ?>
