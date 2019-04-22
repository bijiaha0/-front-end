<?php include '../../kevincom/view/header.html.php'; ?>
<?php
$sessionString = $config->requestType == 'PATH_INFO' ? '?' : '&';
$sessionString .= session_name() . '=' . session_id();
?>
<table class='table setTable table-hover table-striped tablesorter table-fixed' id='KevinValueList' style="table-layout:fixed;" style ="width:100px">
	<thead>
		<tr  height=35px>
			<th class='text-center  w-id'><?php echo $lang->kevinsoft->id; ?></th>
			<th class='text-left w-auto'><?php echo $lang->kevinsoft->filename; ?></th>
			<th class='text-right w-100px'><?php echo $lang->kevinsoft->filesize; ?></th>
			<th class='text-center w-50px'><?php echo $lang->kevinsoft->versionID; ?></th>
			<th class='text-center w-80px'><?php echo $lang->kevinsoft->addedBy; ?></th>
			<th class='text-center w-100px'><?php echo $lang->kevinsoft->addedDate; ?></th>
			<th class='text-center  w-100px {sorter:false}'><?php echo $lang->actions; ?></th>
		</tr>
	</thead>
	<?php
	foreach ($files as $file):
		$canDelete	 = common::hasPriv('kevinsoft', 'filedelete');
		$canEdit	 = common::hasPriv('kevinsoft', 'filedelete');
		?>
		<tr>
			<td class='text-center'><?php printf('%03d', $file->id); ?></td>
			<td class='text-left'><?php
	$fileTitle	 = "<i class='icon-file-text text-muted icon'></i> &nbsp;" . $file->title . '.' . $file->extension;
	echo html::a($this->createLink('kevinsoft', 'filedownload', "id=$file->id") . $sessionString, $fileTitle, '_blank', "onclick='return downloadFile($file->id)'");
		?></td>
			<td class='text-right'><?php echo $file->size; ?></td>
			<td class='text-center'><?php echo $file->objectID; ?></td>
			<td class='text-center'><?php isset($users[$file->addedBy]) ? print($users[$file->addedBy]) : print($file->addedBy); ?></td>
			<td class='text-center'><?php echo date("y-m-d", strtotime($file->addedDate)); ?></td>
			<td class='text-center'>
				<?php
				if ($canDelete) common::printIcon('kevinsoft', 'filedelete', "id=" . $file->id, '', 'list', 'remove', 'hiddenwin');
				if ($canEdit) common::printIcon('kevinsoft', 'fileedit', "id=" . $file->id, '', 'list', 'pencil', '', 'iframe', true);
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	<tfoot>
		<tr><td colspan=7 align='right'><?php $pager->show(); ?></td></tr>
	</tfoot>
</table>			
<?php include '../../common/view/syntaxhighlighter.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>

<script language='Javascript'>
	/* Download a file, append the mouse to the link. Thus we call decide to open the file in browser no download it. */
	function downloadFile(fileID)
	{
		if (!fileID)
			return;
		var sessionString = '<?php echo $sessionString; ?>';
		var url = createLink('kevinsoft', 'filedownload', 'fileID=' + fileID + '&mouse=left') + sessionString;
		window.open(url, '_blank');
		return false;
	}
</script>