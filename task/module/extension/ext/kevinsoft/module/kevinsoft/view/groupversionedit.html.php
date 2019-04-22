<?php include '../../kevincom/view/header.html.php';?>
<form class='form' method='post' id='dataform'>
<div class='row-table'>
			<table width = '100%' cellpadding="5"> 
				<tr>
					<!--软件名称-->
					<th><?php echo $lang->kevinsoft->groupversion;?></th>
					<td><?php echo html::input('version', $groupversionInfo->version, "class='form-control'");?></td>
				</tr>
				<tr>
					<!--源文件-->
					<th><?php echo $lang->kevinsoft->subversion;?></th>
					<td><?php echo html::input('subversion', $groupversionInfo->subversion, "class='form-control'");?></td>
				</tr> 
				<tr>
					<!--删除标记-->
					<th><?php echo $lang->kevinsoft->deleted;?></th>
					<td><?php echo html::select('deleted', $lang->kevinsoft->deletedList, $lang->kevinsoft->deletedList[$groupversionInfo->deleted], 'class=form-control');?></td>
				</tr>			
			</table>
		<div class='actions text-right'>
			<?php echo html::submitButton($lang->save);?>
		</div>
</div>
</form>

