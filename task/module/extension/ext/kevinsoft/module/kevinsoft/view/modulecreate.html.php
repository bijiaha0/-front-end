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
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div class='main'>
	<div id='titlebar'>
		<div class='heading'>
			<strong><small class='text-muted'><?php echo html::icon($lang->icons['create']);?></small> <?php echo $lang->kevinsoft->modulecreate;?></strong>
		</div>
	</div>
<form class='form' method='post' target='hiddenwin' id='dataform'>
<div class='row-table'>
			<table width = '100%' cellpadding="5"> 	
				<tr>
					<th><?php echo $lang->kevinsoft->devicename;?></th>
					<td><?php echo html::select('device', $deviceArray, '',  "class='form-control chosen'");?></td>
				</tr> 
				<tr>
					<th><?php echo $lang->kevinsoft->applynum;?></th>
					<td><?php echo html::input('applynum','', "class='form-control'");?></td>
				</tr> 
				<tr>
					<th><?php echo $lang->kevinsoft->softname;?></th>
					<td><?php echo html::select('software', $softArray, '',  "class='form-control chosen'");?></td>
				</tr>	
				<tr>
					<th><?php echo $lang->kevinsoft->type;?></th>
					<td><?php echo html::radio('type', (array)$lang->kevinsoft->modulePrivList,'');?></td>		
				</tr>			
				<tr>
					<th><?php echo $lang->kevinsoft->module;?></th>
					<td><?php echo html::input('module','',  "class='form-control'");?></td>
				</tr>						
				<tr>
					<th><?php echo $lang->kevinsoft->count;?></th>
					<td><?php echo html::input('count', 1,  "class='form-control'");?></td>
				</tr>				
				<tr>
					<th><?php echo $lang->kevinsoft->startDate;?></th>
					<td><?php echo html::input("startDate", '', "class='form-control form-date'"); ?></td>
				</tr>		
				<tr>
					<th><?php echo $lang->kevinsoft->endDate;?></th>
					<td><?php echo html::input('endDate', '',  "class='form-control form-date'");?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->notes;?></th>
					<td colspan = 4><?php echo html::textarea('notes', '',  "rows='6' class='form-control' style=' height: 150px;'");?></td>
				</tr>	
			</table>
		<div class='actions text-right'>
			<?php echo html::submitButton($lang->save);?>
		</div>
</div>
</form>

</div>

