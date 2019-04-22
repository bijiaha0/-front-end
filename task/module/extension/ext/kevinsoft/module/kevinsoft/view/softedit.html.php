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
<?php include '../../kevincom/view/header.html.php';?>
<div class="container">
	<form class='form' method='post' target='hiddenwin' id='dataform'>
	<div class='row-table'>
			<table width = '100%' cellpadding="5"> 				 
				<tr>
					<th><?php echo $lang->kevinsoft->id;?></th>
					<td><?php echo $softInfo->id;?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->softIID;?></th>
					<td><?php echo $softInfo->IID;?></td>
				</tr> 
				<tr>
					<th><?php echo $lang->kevinsoft->softtype;?></th>
					<td><?php echo $lang->kevinsoft->softtypeList[$softInfo->type];?></td>
				</tr>				
				<tr>
					<th><?php echo $lang->kevinsoft->softname;?></th>
					<td><?php echo html::input('name', $softInfo->name,  "class='form-control form-date'");?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->deleted;?></th>
					<td><?php echo html::select('deleted', $lang->kevinsoft->deletedList, $softInfo->deleted, 'class=form-control');?></td>
				</tr>				
			</table>
		<div class='actions text-center'>
			<?php echo html::submitButton($lang->save)."    ".html::backButton();?>
		</div>
	</div>
	</form>
</div>
<?php include '../../common/view/footer.html.php';?>
