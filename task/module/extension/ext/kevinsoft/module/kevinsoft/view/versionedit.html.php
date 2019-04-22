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
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id='titlebar'>
	<div class='heading'>
		<span class='prefix' title='id'><?php echo $lang->kevincom->kevin . " > " . $lang->kevinsoft->soft . " > " . $lang->kevinsoft->versionedit . " > "; ?> <strong><?php echo $VersionItem->id; ?></strong></span>
		<strong><?php echo $VersionItem->name; ?></strong>
		<span class='prefix' title='version'><strong><?php echo $VersionItem->version; ?></strong></span>
		<?php if ($VersionItem->deleted): ?>
			<span class='label label-danger'><?php echo  $lang->kevinsoft->deleted; ?></span>
		<?php endif; ?>
	</div>
</div>
<div class='row-table'>
	<form class='form' method='post' enctype='multipart/form-data' target='hiddenwin' id='dataform'>
		<table width = '100%' cellpadding="5"> 
			<tr>
				<th><strong><?php echo $lang->kevinsoft->softtype; ?></strong></th>
				<td><?php echo $lang->kevinsoft->softtypeList[$VersionItem->type]; ?></td>
			</tr>			
			<tr>
				<th><?php echo $lang->kevinsoft->versionvalid; ?></th>
				<td><?php echo html::select('valid', $lang->kevinsoft->versionvalidList, $VersionItem->valid, "class='form-control'"); ?></td>
			</tr> 
			<tr>
				<th><?php echo $lang->kevinsoft->replaceType; ?></th>
				<td><?php echo html::select('replaceType', $lang->kevinsoft->replaceTypeList, $VersionItem->type, "class='form-control'"); ?></td>
			</tr> 
			<tr>
				<th><?php echo $lang->kevinsoft->deleted; ?></th>
				<td><?php echo html::select('deleted', $lang->kevinsoft->deletedList, $VersionItem->deleted, 'class=form-control'); ?></td>
			</tr>	
			<?php if ($VersionItem->type != '1') { ?>
				<tr id='fileBox' class=''>
					<th><?php echo $lang->kevinsoft->files; ?></th>
					<td colspan='2'><?php echo $this->fetch('file', 'buildform'); ?></td>
				</tr>	
			<?php } ?>
		</table>
		<div class='actions text-right'>
			<?php echo html::submitButton($lang->save) . html::backButton(); ?>
		</div>
	</form>
</div>