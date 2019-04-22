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
<div id='titlebar'>
	<div class='heading'>
		<span class='prefix' title='soft id'><?php echo $lang->kevincom->kevin . " > " . $lang->kevinsoft->soft . " > " . $lang->kevinsoft->versioncreate. " > " ; ?> <strong><?php echo $softID; ?></strong></span>
	</div>
</div>
<div class='main'>
	<form class='form' method='post' enctype='multipart/form-data'  target='hiddenwin' id='dataform'>
		<table class='table table-form' width = '100%' cellpadding="5"> 
			<tr>
				<th  class='w-120px'><?php echo $lang->kevinsoft->versionsoft; ?></th>
				<td><?php echo $softfilePairs[$softID];?></td>
			</tr> 
			<tr>
				<th><?php echo $lang->kevinsoft->version; ?></th>
				<td><?php echo html::input('version', '', "class='form-control'"); ?></td>
			</tr> 
			<tr>
				<th><?php echo $lang->kevinsoft->versionvalid; ?></th>
				<td><?php echo html::select('valid', $lang->kevinsoft->versionvalidList, '', "class='form-control'"); ?></td>
			</tr> 
			<tr>
				<th><?php echo $lang->kevinsoft->replaceType; ?></th>
				<td><?php echo html::select('type', $lang->kevinsoft->replaceTypeList, '', "class='form-control'"); ?></td>
			</tr> 
		</table>
		<div class='actions text-right'>
<?php echo html::submitButton($lang->save); ?>
		</div>
	</form>
</div>

