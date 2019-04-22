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
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div class="container">
	<form class='form' method='post' target='hiddenwin' id='dataform'>
	<div class='row-table'>
			<table width = '100%' cellpadding="5" class='table table-form'> 				 
				<tr>
					<th><?php echo $lang->kevinsoft->id;?></th>
					<td><?php echo $moduleInfo->id;?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->devicename;?></th>
					<td><?php echo html::select('device', $deviceArray, $moduleInfo->devicename,  "class='form-control chosen'");?></td>
				</tr> 
				<tr>
					<th><?php echo $lang->kevinsoft->applynum;?></th>
					<td><?php echo html::input('applynum',$moduleInfo->applynum, "class='form-control'");?></td>
				</tr> 
				<tr>
					<th><?php echo $lang->kevinsoft->softname;?></th>
					<td><?php echo html::select('software', $softArray, $moduleInfo->softname,  "class='form-control chosen'");?></td>
				</tr>	
				<tr>
							<th><?php echo $lang->kevinsoft->type;?></th>
							<td><?php echo html::radio('type', (array)$lang->kevinsoft->modulePrivList, $moduleInfo->type);?></td>		
				</tr>	
				<?php 	if(date('Y-m-d')<=$moduleInfo->endDate){
						$state='normal';
						$class='label label-success';
					}else{
						$state='exceed';
						$class='label label-warning';
					}?>
				<tr>
					<th><?php echo $lang->kevinsoft->status;?></th>
					<td><h5 class=<?php echo '"'.$class.'"';?>><?php echo $lang->kevinsoft->stateList[$state]; ?></h5></td>	
				</tr>	
				<tr>
					<th><?php echo $lang->kevinsoft->module;?></th>
					<td><?php echo html::input('module',$moduleInfo->module,  "class='form-control'");?></td>
				</tr>							
				<tr>
					<th><?php echo $lang->kevinsoft->count;?></th>
					<td><?php echo html::input('count', ($moduleInfo->count=='')?'1':$moduleInfo->count,  "class='form-control'");?></td>
				</tr>				
				<tr>
					<th><?php echo $lang->kevinsoft->startDate;?></th>
					<td><?php echo html::input("startDate",(string)$moduleInfo->startDate, "class='form-control form-date'"); ?></td>
				</tr>				
				<tr>
					<th><?php echo $lang->kevinsoft->endDate;?></th>
					<td><?php echo html::input('endDate', $moduleInfo->endDate,  "class='form-control form-date'");?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->deleted;?></th>
					<td><?php echo html::radio('deleted', (array)$lang->kevinsoft->deletedList, $moduleInfo->deleted);?></td>		
				</tr>
				<tr>
					<th><?php echo $lang->kevinsoft->notes;?></th>
					<td colspan = 4><?php echo html::textarea('notes', $moduleInfo->notes,  "rows='6' class='form-control' style=' height: 150px;'");?></td>
				</tr>				
			</table>
		<div class='actions text-center'>
			<?php echo html::submitButton($lang->save)."    ".html::backButton();?>
		</div>
	</div>
	</form>
</div>
<?php include '../../common/view/footer.html.php';?>
