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
<?php include 'configfeaturebar.html.php'; ?>
<?php //echo css::internal($keTableCSS);    ?>
<div id='titlebar'>
	<div class='heading'>
		<span class='prefix' title='MODULE'><?php echo html::icon($lang->icons['app']); ?> <strong><?php echo $module->id; ?></strong></span>
		<strong><?php echo $module->module; ?></strong>
		<?php if ($module->deleted): ?>
			<span class='label label-danger'><?php echo "已删除"; ?></span>
		<?php endif; ?>
	</div>
	<div class='actions'>
		<?php
		$browseLink	 = $this->createLink('kevinsoft', 'modulelist', "");
		$params		 = "moduleID=$module->id";

		ob_start();
		echo "<div class='btn-group'>";
		common::printIcon('kevinsoft', 'moduledit', $params, $module, 'button', 'pencil', '', 'iframe', true);
		if (!$module->deleted) common::printIcon('kevinsoft', 'moduledelete', $params, '', 'button', 'remove', 'hiddenwin');

		echo '</div>';
		echo "<div class='btn-group'>";
		common::printRPN($browseLink, $preAndNext);
		echo '</div>';
		$actionLinks = ob_get_contents();
		ob_end_clean();
		echo $actionLinks;
		?>
	</div>
</div>
<div class='row-table'>
	<div class='col-side'>
		<div class='main main-side'>
			<fieldset>
				<legend><?php echo $lang->kevinsoft->basicInfo; ?></legend>
				<table class='table table-data table-condensed table-borderless'>
					<tr>
						<th class='w-80px'><strong><?php echo $lang->kevinsoft->id; ?></strong></th>
						<td class='w-600px'><?php echo $module->id; ?></td>
						<th><strong><?php echo $lang->kevinsoft->devicename; ?></strong></th>
						<td><?php echo $module->devicename; ?></td>
					</tr>
					<tr>
						<?php 	if(date('Y-m-d')<=$module->endDate){
							$state='normal';
							$class='label label-success';
						}else{
							$state='exceed';
							$class='label label-warning';
						}?>
						<th><strong><?php echo $lang->kevinsoft->applynum;?></strong></th>
						<td><?php echo $module->applynum;?></td>
						<th><strong><?php echo $lang->kevinsoft->status;?></strong></th>
						<td><h5 class=<?php echo '"'.$class.'"';?>><?php echo $lang->kevinsoft->stateList[$state]; ?></h5></td>	
					</tr> 
					<tr>
						<th><strong><?php echo $lang->kevinsoft->softname; ?></strong></th>
						<td><?php echo $module->softname; ?></td>
						<th><strong><?php echo $lang->kevinsoft->type; ?></strong></th>
						<td><?php echo $lang->kevinsoft->modulePrivList[$module->type]; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->module; ?></strong></th>
						<td><?php echo $module->module; ?></td>
						<th><strong><?php echo $lang->kevinsoft->notes; ?></strong></th>
						<td><?php echo $module->notes; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->count; ?></strong></th>
						<td><?php echo $module->count; ?></td>
						<th><strong><?php echo $lang->kevinsoft->startDate; ?></strong></th>
						<td><?php echo $module->startDate; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->endDate; ?></strong></th>
						<td><?php echo $module->endDate; ?></td>
						<th><strong><?php echo $lang->kevinsoft->deleted; ?></strong></th>
						<td><?php echo $lang->kevinsoft->deletedList[$module->deleted]; ?></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo $lang->kevinsoft->deviceInfo; ?></legend>
				<table class='table table-data table-condensed table-borderless'>
					<tr>
						<th class='w-80px'><strong><?php echo $lang->kevinsoft->id; ?></strong></th>
						<td class='w-600px'><?php echo $deviceInfo->id; ?></td>
						<th><strong><?php echo $lang->kevinsoft->devicename; ?></strong></th>
						<td><?php echo $deviceInfo->name; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->purpose; ?></strong></th>
						<td><?php echo $deviceInfo->purpose; ?></td>
						<th><strong><?php echo $lang->kevinsoft->dept; ?></strong></th>
						<td><?php echo $deviceInfo->deptName; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->devicetype; ?></strong></th>
						<td><?php echo $lang->kevinsoft->DevTypeList[$deviceInfo->type]; ?></td>
						<th><strong><?php echo $lang->kevinsoft->status; ?></strong></th>
						<td><?php echo $lang->kevinsoft->StatusList[$deviceInfo->status]; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->charge; ?></strong></th>
						<td><?php echo $users[$deviceInfo->charge]; ?></td>
						<th><strong><?php echo $lang->kevinsoft->tcpip; ?></strong></th>
						<td><?php echo $deviceInfo->tcpip; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->desc; ?></strong></th>
						<td><?php echo $deviceInfo->description; ?></td>
						<th><strong><?php echo $lang->kevinsoft->join; ?></strong></th>
						<td><?php echo ($deviceInfo->join == "0000-00-00")? "":$deviceInfo->join;?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->dieDate; ?></strong></th>
						<td><?php echo ($deviceInfo->dieDate == "0000-00-00")? "":$deviceInfo->dieDate;?></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo $lang->kevinsoft->softInfo; ?></legend>
				<table class='table table-data table-condensed table-borderless'>
					<tr>
						<th class='w-80px'><strong><?php echo $lang->kevinsoft->id; ?></strong></th>
						<td class='w-200px'><?php echo $softInfo->id; ?></td>
						<th class='w-800px'><strong><?php echo $lang->kevinsoft->softIID; ?></strong></th>
						<td class='w-900px'><?php echo $softInfo->IID; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->softname; ?></strong></th>
						<td><?php echo $softInfo->name; ?></td>
						<th><strong><?php echo $lang->kevinsoft->softvalid; ?></strong></th>
						<td><?php echo $lang->kevinsoft->softvalidList[$softInfo->valid]; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->softtype; ?></strong></th>
						<td><?php echo $lang->kevinsoft->softtypeList[$softInfo->type]; ?></td>
						<th><strong><?php echo $lang->kevinsoft->addedBy; ?></strong></th>
						<td><?php echo $softInfo->addedBy; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->addedDate; ?></strong></th>
						<td><?php echo $softInfo->addedDate; ?></td>
						<th><strong><?php echo $lang->kevinsoft->deleted; ?></strong></th>
						<td><?php echo $lang->kevinsoft->deletedList[$softInfo->deleted]; ?></td>
					</tr>
				</table>
			</fieldset>
			<?php //include '../../common/view/action.html.php'; ?>
			<div class='actions left'><?php if (!$module->deleted) echo $actionLinks; ?>

			</div>
		</div>
	</div>
</div>
<?php include '../../common/view/syntaxhighlighter.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>