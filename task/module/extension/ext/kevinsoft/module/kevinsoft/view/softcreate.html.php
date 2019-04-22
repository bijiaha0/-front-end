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
<?php include '../../common/view/kindeditor.html.php';?>
<div class='main'>
	<div id='titlebar'>
		<div class='heading'>
			<strong><small class='text-muted'><?php echo html::icon($lang->icons['create']);?></small> <?php echo $lang->kevinsoft->softcreate;?></strong>
		</div>
	</div>
<form class='form' method='post' target='hiddenwin' id='dataform'>
<div class='row-table'>
			<table width = '100%' cellpadding="5"> 				 
				<tr>
					<th><?php echo $lang->kevinsoft->softIID;?></th>
					<td><?php echo html::input('IID', '', "class='form-control'");?></td>
				</tr> 
				<tr>
					<th><?php echo $lang->kevinsoft->softname;?></th>
                    <td><?php echo html::input('name', '',  "class='form-control form-date'");?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->softvalid;?></th>
                    <td><?php echo html::select('valid', $lang->kevinsoft->softvalidList, '',  "class='form-control'");?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->softtype;?></th>
                    <td><?php echo html::select('type', $lang->kevinsoft->softtypeList,'',  "class='form-control'");?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->addedBy;?></th>
					<td><?php echo html::input('addedBy', $this->app->user->account,  "class='form-control' ");?></td>
				</tr> 
				<tr>
					<th><?php echo $lang->kevinsoft->addedDate;?></th>
					<td><?php echo html::input('addedDate', date('Y-m-d h:m:s'),  "class='form-control form-date' ");?></td>
				</tr> 
			</table>
		<div class='actions text-right'>
			<?php echo html::submitButton($lang->save);?>
		</div>
</div>
</form>

</div>

