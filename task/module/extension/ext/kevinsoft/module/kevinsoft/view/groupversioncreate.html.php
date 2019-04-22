<?php
/**
 * The view file
 *
 * @copyright   Kevin
 * @charge: free
 * @license: ZPL (http://zpl.pub/v1)
 * @author      Kevin <3301647@qq.com>
 * @package     kevinsoft
 * @link        http://www.zentao.net
 */
?>
<?php include '../../kevincom/view/header.html.php'; ?>

<div id='titlebar'>
	<div class='heading'>
		<span class='prefix' title='SOFT'><?php echo $lang->kevincom->kevin . " > " . $lang->kevinsoft->soft . " > " . $lang->kevinsoft->groupversioncreate . " > " . html::icon($lang->icons['app']); ?> <strong><?php echo $groupitem->id; ?></strong></span>
		<strong><?php echo $groupitem->name; ?></strong>
	</div>
</div>
<form class='form-condensed' method='post' target='hiddenwin' id='dataform'>
	<div class='row-table'>
		<table width = '100%' cellpadding="5"> 
			<tr>
				<!--灯具名称-->
				<th><?php echo $lang->kevinsoft->soft; ?></th>
				<td><?php echo $groupitem->name; ?></td>
			</tr> 
			<tr>
				<!--删除标记-->
				<th><?php echo $lang->kevinsoft->version; ?></th>
				<td><?php echo html::input('version', '', "class='form-control'"); ?></td>
			</tr>
		</table>
		<div class='actions text-right'>
			<?php echo html::submitButton($lang->save); ?>
		</div>
	</div>
</form>