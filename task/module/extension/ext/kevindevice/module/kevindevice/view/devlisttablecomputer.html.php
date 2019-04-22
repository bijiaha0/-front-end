<table class='table setTable table-condensed table-hover table-striped tablesorter ' id='deviceList'　 style="table-layout:fixed;">
	<thead>
		<tr>
			<?php $vars	 = "param=$param&type=$type&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; ?>
			<th class='w-50px'><?php common::printorderlink('id', $orderBy, $vars, $lang->idAB); ?></th>
			<th class='w-60px'><?php echo $lang->actions; ?></th>
			<th class='w-120px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->kevindevice->name); ?></th>
			<th class='w-120px'><?php common::printOrderLink('label', $orderBy, $vars, $lang->kevindevice->label); ?></th>
			<th class='w-80px'><?php common::printOrderLink('group', $orderBy, $vars, $lang->kevindevice->group); ?></th>
			<th class='w-80px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->kevindevice->type); ?></th>
			<th class='w-50px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->kevindevice->status); ?></th>
			<th class='w-100px'><?php common::printOrderLink('user', $orderBy, $vars, $lang->kevindevice->user); ?></th>
			<th class='w-100px'><?php common::printOrderLink('charge', $orderBy, $vars, $lang->kevindevice->charge); ?></th>
			<th class='w-80px'><?php common::printOrderLink('dept', $orderBy, $vars, $lang->kevindevice->dept); ?></th>
			<th class='w-100px'><?php common::printOrderLink('tcpip', $orderBy, $vars, $lang->kevindevice->tcpip); ?></th>
			<th class='w-120px'><?php common::printOrderLink('manageip', $orderBy, $vars, $lang->kevindevice->manageip); ?></th>
			<th class='w-80px'><?php common::printOrderLink('count', $orderBy, $vars, $lang->kevindevice->count); ?></th>
			<th class='w-100px'><?php common::printOrderLink('cpuID', $orderBy, $vars, $lang->kevindevice->cpuID); ?></th>
			<th class='w-120px'><?php common::printOrderLink('deviceSN', $orderBy, $vars, $lang->kevindevice->deviceSN); ?></th>
			<th class='w-120px'><?php common::printOrderLink('version', $orderBy, $vars, $lang->kevindevice->version); ?></th>
			<th class='w-100px'><?php common::printOrderLink('monitorSN', $orderBy, $vars, $lang->kevindevice->monitorSN); ?></th>
			<th class='w-100px'><?php common::printOrderLink('monitorVersion', $orderBy, $vars, $lang->kevindevice->monitorVersion); ?></th>
			<th class='w-120px'><?php common::printOrderLink('assetNumber', $orderBy, $vars, $lang->kevindevice->assetNumber); ?></th>
			<th class='w-120px'><?php common::printOrderLink('vidioCard', $orderBy, $vars, $lang->kevindevice->vidioCard); ?></th>
			<th class='w-80px'><?php common::printOrderLink('discCapacity', $orderBy, $vars, $lang->kevindevice->discCapacity); ?></th>
			<th class='w-150px'><?php common::printOrderLink('memoryCapacity', $orderBy, $vars, $lang->kevindevice->memoryCapacity); ?></th>
			<th class='w-80px'><?php common::printOrderLink('system', $orderBy, $vars, $lang->kevindevice->system); ?></th>
			<th class='w-150px'><?php common::printOrderLink('mac', $orderBy, $vars, $lang->kevindevice->mac); ?></th>
			<th class='w-120px'><?php common::printOrderLink('purpose', $orderBy, $vars, $lang->kevindevice->purpose); ?></th>
			<th class='w-200px'><?php common::printOrderLink('loginaddr', $orderBy, $vars, $lang->kevindevice->loginaddr); ?></th>
			<th class='w-80px'><?php common::printOrderLink('join', $orderBy, $vars, $lang->kevindevice->join); ?></th>
			<th class='w-80px'><?php common::printOrderLink('dieDate', $orderBy, $vars, $lang->kevindevice->dieDate); ?></th>
			<th class='w-150px'><?php common::printOrderLink('description', $orderBy, $vars, $lang->kevindevice->desc); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		$canBatchEdit		 = false; //common::hasPriv('user', 'batchEdit');
		$canManageContacts	 = false; //common::hasPriv('user', 'manageContacts');
		$candevdelete		 = common::hasPriv('kevindevice', 'devdelete');
		$canUserEdit		 = common::hasPriv('kevindevice', 'devedit');
		?>
		<?php foreach ($devices as $device): ?>
			<tr class='text-center'>
				<td>
					<?php
					if ($canBatchEdit or $canManageContacts) echo "<input type='checkbox' name='devices[]' value='$device->name'> ";
					printf('%03d', $device->id);
					?>
				</td>
				<td class='text-left'>
					<?php
					if ($canUserEdit) {
						common::printIcon('kevindevice', 'devedit', "userID=$device->id", '', '', 'pencil');
						// echo html::a($this->createLink('kevindevice', 'devedit', "userID=$device->id"), '<i class="icon-common-edit icon-pencil"></i>', '', "title='{$lang->kevindevice->edit}' class='btn-icon iframe'");
					}
					if ($candevdelete) {
						common::printIcon('kevindevice', 'devdelete', "userID=$device->id", '', '', 'remove', '', 'iframe', true, "data-width='550'");
					}
					?>
				</td>
				<td><?php if (!common::printLink('kevindevice', 'devview', "id=$device->id", $device->name)) echo $device->name; ?></td>
				<td><?php echo $device->label; ?></td>
				<td><?php echo $device->groupName; ?></td>
				<td><?php echo $lang->kevindevice->DevTypeList[$device->type]; ?></td>
				<td><?php echo $lang->kevindevice->StatusList[$device->status]; ?></td>
				<td><?php  if($device->user){$enuser=str_split($device->user);echo strtoupper($enuser[0]).':'.$device->user; }?></td>
				<td><?php echo $users[$device->charge]; ?></td>
				<td><?php echo $device->deptName; ?></td>
				<td><?php echo $device->tcpip; ?></td>
				<td><?php echo $device->manageip; ?></td>
				<td><?php echo ($device->count==0)?'':$device->count; ?></td>
				<td><?php echo $device->cpuID; ?></td>
				<td><?php echo $device->deviceSN; ?></td>
				<td><?php echo $device->version; ?></td>
				<td><?php echo $device->monitorSN; ?></td>
				<td><?php echo $device->monitorVersion; ?></td>
				<td><?php echo $device->assetNumber; ?></td>
				<td><?php echo $device->vidioCard; ?></td>
				<td><?php echo $device->discCapacity; ?></td>
				<td><?php echo $device->memoryCapacity; ?></td>
				<td><?php echo $device->system; ?></td>
				<td><?php echo $device->mac; ?></td>
				<td><?php echo $device->purpose; ?></td>
				<td><?php echo $device->loginaddr; ?></td>
				<td><?php echo ($device->join == "0000-00-00")? "":$device->join; ?></td>
				<td><?php echo ($device->dieDate == "0000-00-00")? "":$device->dieDate; ?></td>
				<td class='w-250px'><?php echo nl2br($device->description); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='22' class = 'text-left'>
				<div  style="width:400px;">
				<?php echo $pager->show(); ?></div>
			</td>
		</tr>
	</tfoot>
</table>