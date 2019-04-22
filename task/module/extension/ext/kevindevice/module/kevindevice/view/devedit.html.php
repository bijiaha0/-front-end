<?php
/**
 * The devedit view of kevindevice module
 *
 * @copyright   Kevin
 * @author      kevin<3301647@qq.com>
 * @package     kevindevice
 */
?>
<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php include 'kevindevicebar.html.php'; ?>

<div id='devhead'>
    <div class='heading'>
		<span class='prefix' title='LIST'><?php echo html::icon($lang->icons['edit']); ?> <strong><?php echo $title . " > "; ?></strong></span>
		<strong><?php
			if (!common::printLink('kevindevice', 'devview', "id=$device->id", $device->name)) echo $device->name;
			?> (<small><?php echo $lang->kevindevice->DevTypeList[$device->type]; ?></small>)</strong>
    </div>
</div>

<div class='container mw-800px'>
	<form class='form-condensed' method='post' target='hiddenwin' id='dataform'>
		<table align='center' class='table table-form'>
			<caption class='text-left text-muted'><?php echo $lang->kevindevice->basicInfo; ?></caption>
			<tr>
				<th class='w-90px'><?php echo $lang->kevindevice->name; ?></th>
				<td class='w-p40'><?php echo html::input('name', $device->name, "class='form-control' autocomplete='off'"); ?></td>
				<th><?php echo $lang->kevindevice->label; ?></th>
				<td><?php echo html::input('label', $device->label, "class='form-control' autocomplete='off'"); ?></td>	
			</tr>
			<tr>
				<th class='w-80px'><?php echo $lang->kevindevice->id; ?></th>
				<td class='w-auto'><?php echo $device->id; ?></td>
				<th class='w-90px'><?php echo $lang->kevindevice->type; ?></th>
				<td><?php echo html::select('type', $lang->kevindevice->DevTypeList, $device->type, "class='form-control'"); ?></td>
			</tr>    
			<tr>
				<th><?php echo $lang->kevindevice->status; ?></th>
				<td><?php echo html::select('status', $lang->kevindevice->StatusList, $device->status, "class='form-control'"); ?></td>
				<th><?php echo $lang->kevindevice->group; ?></th>
				<td><?php echo html::select('group', $groups, $deviceGroups, 'size=3 class="form-control chosen"'); ?></td>
			</tr>
		</table>
		<table align='center' class='table table-form'>
			<caption class='text-left text-muted'><?php echo $lang->kevindevice->UserVisitInfor; ?></caption>
			<tr>
				<th class='w-90px'><?php echo $lang->kevindevice->dept; ?></th>
				<td class='w-p40'><?php echo html::select('dept', $depts, $device->dept, "class='form-control chosen'"); ?></td>
			</tr>
			<tr>
				<th class='w-90px'><?php echo $lang->kevindevice->user; ?></th>
				<td class='w-p40'><?php echo html::select('user', $users, $device->user, "class='form-control chosen'"); ?></td>
				<th><?php echo $lang->kevindevice->charge; ?></th>
				<td><?php echo html::select('charge', $users, $device->charge, "class='form-control chosen'"); ?></td>
			</tr>
			<tr>
				<th><?php echo $lang->kevindevice->join; ?></th>
				<td><?php echo html::input('join', $device->join, "class='form-control form-date'"); ?></td>
				<th><?php echo $lang->kevindevice->dieDate; ?></th>
				<td><?php echo html::input('dieDate', $device->dieDate, "class='form-control form-date'"); ?></td>
			</tr>

		</table>
		<table align='center' class='table table-form'>
			<caption class='text-left text-muted'><?php echo $lang->kevindevice->computerInfor; ?></caption>
			<tr>
				<th class='w-80px'><?php echo $lang->kevindevice->tcpip; ?></th>
				<td><?php echo html::input('tcpip', $device->tcpip, "class='form-control' autocomplete='off'"); ?></td>
				<th class='w-80px'><?php echo $lang->kevindevice->manageip; ?></th>
				<td><?php echo html::input('manageip', $device->manageip, "class='form-control' autocomplete='off'"); ?></td>
			</tr>  
			<tr>
				<th class='w-80px'><?php echo $lang->kevindevice->count; ?></th>
				<td><?php echo html::input('count',($device->count==0)?'':$device->count, "class='form-control' autocomplete='off'"); ?></td>
				<th><?php echo $lang->kevindevice->cpuID; ?></th>
				<td><?php echo html::input('cpuID', $device->cpuID, "class='form-control' autocomplete='off'"); ?></td>	
			</tr>
			<tr>
				<th><?php echo $lang->kevindevice->deviceSN; ?></th>
				<td><?php echo html::input('deviceSN', $device->deviceSN, "class='form-control' autocomplete='off'"); ?></td>
				<th><?php echo $lang->kevindevice->version; ?></th>
				<td><?php echo html::input('version', $device->version, "class='form-control' autocomplete='off'"); ?></td>
				
			</tr>
			<tr>
				<th><?php echo $lang->kevindevice->monitorSN; ?></th>
				<td><?php echo html::input('monitorSN', $device->monitorSN, "class='form-control' autocomplete='off'"); ?></td>
				<th><?php echo $lang->kevindevice->monitorVersion; ?></th>
				<td><?php echo html::input('monitorVersion', $device->monitorVersion, "class='form-control' autocomplete='off'"); ?></td>
			</tr>
			<tr>
				<th><?php echo $lang->kevindevice->assetNumber; ?></th>
				<td><?php echo html::input('assetNumber', $device->assetNumber, "class='form-control' autocomplete='off'"); ?></td>
				<th><?php echo $lang->kevindevice->mac; ?></th>
				<td><?php echo html::input('mac', $device->mac, "class='form-control' autocomplete='off'"); ?></td>
			</tr>
			<tr>
				<th><?php echo $lang->kevindevice->vidioCard; ?></th>
				<td><?php echo html::input('vidioCard', $device->vidioCard, "class='form-control' autocomplete='off'"); ?></td>	
				<th><?php echo $lang->kevindevice->purpose; ?></th>
				<td><?php echo html::input('purpose', $device->purpose, "class='form-control' autocomplete='off'"); ?></td>
			</tr>
			<tr>
				<th><?php echo $lang->kevindevice->loginaddr; ?></th>
				<td><?php echo html::input('loginaddr', $device->loginaddr, "class='form-control' autocomplete='off'"); ?></td>
				<th><?php echo $lang->kevindevice->discCapacity; ?></th>
				<td><?php echo html::input('discCapacity', $device->discCapacity, "class='form-control' autocomplete='off'"); ?></td>
			</tr>
			<tr>
				<th><?php echo $lang->kevindevice->memoryCapacity; ?></th>
				<td><?php echo html::input('memoryCapacity', $device->memoryCapacity, "class='form-control' autocomplete='off'"); ?></td>	
				<th><?php echo $lang->kevindevice->system; ?></th>
				<td><?php echo html::input('system', $device->system, "class='form-control' autocomplete='off'"); ?></td>
			</tr>
		</table>
		<fieldset>
			<legend><?php echo $lang->kevindevice->desc; ?></legend>
			<div class='article-content'><?php echo html::textarea('description', htmlspecialchars($device->description), "'rows='5' class='form-control autosize' style='height: 108px;'"); ?></div>
		</fieldset>
		<table align='center' class='table table-form'>
			<tr><td colspan='2' class='text-center'><?php echo html::submitButton() . html::backButton(); ?></td></tr>
		</table>
	</form>
</div>
<?php include '../../common/view/footer.html.php'; ?>
