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
<div id='titlebar'>
	<div class='heading'>
		<span class='prefix' title='version'><?php echo html::icon($lang->icons['app']); ?> <strong><?php echo $VersionItem->id; ?></strong></span>
		<strong><?php echo $VersionItem->name; ?></strong>
		<?php if ($VersionItem->deleted): ?>
			<span class='label label-danger'><?php echo "已删除"; ?></span>
		<?php endif; ?>
	</div>
	<div class='actions'>
		<?php
		$browseLink	 = $this->createLink('kevinsoft', 'versionlist', "");
		$params		 = "versionID=$VersionItem->id";
		ob_start();
		echo "<div class='btn-group'>";
		common::printIcon('kevinsoft', 'versionedit', $params, '', 'list', 'pencil', '', 'iframe', true);
		if (!$VersionItem->deleted) common::printIcon('kevinsoft', 'versiondelete', $params, '', 'list', 'remove', 'hiddenwin');
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
<div class='main main-side'>
	<fieldset>
		<legend><?php echo $lang->kevinsoft->basicInfo; ?></legend>
		<table class='table table-data table-condensed table-borderless'>
			<tr>
				<th class='w-120px'><strong><?php echo $lang->kevinsoft->name; ?></strong></th>
				<td><?php echo $VersionItem->name; ?></td>
				<th class='w-120px'><strong><?php echo $lang->kevinsoft->id; ?></strong></th>
				<td><?php echo $VersionItem->id; ?></td>
			</tr>
			<tr>
				<th class='w-80px'><strong><?php echo $lang->kevinsoft->versionsoft; ?></strong></th>
				<td><?php if ($soft) echo $soft->name; ?></td>
				<th><strong><?php echo $lang->kevinsoft->versionversion; ?></strong></th>
				<td><?php echo $VersionItem->version; ?></td>
			</tr>
			<tr>
				<th><strong><?php echo $lang->kevinsoft->versiondownloads; ?></strong></th>
				<td><?php echo $VersionItem->downloads; ?></td>
				<th><strong><?php echo $lang->kevinsoft->versionvalid; ?></strong></th>
				<td><?php echo $lang->kevinsoft->versionvalidList[$VersionItem->valid]; ?></td>
			</tr>
			<tr>
				<th><strong><?php echo $lang->kevinsoft->softtype; ?></strong></th>
				<td><?php echo  $lang->kevinsoft->softtypeList[$VersionItem->type]; ?></td>
				<th><strong><?php echo $lang->kevinsoft->replaceType; ?></strong></th>
				<td><?php echo $lang->kevinsoft->replaceTypeList[$VersionItem->replaceType]; ?></td>
			</tr>
			<tr>
				<th><strong><?php echo $lang->kevinsoft->addedBy; ?></strong></th>
				<td><?php echo $VersionItem->addedBy; ?></td>
				<th><strong><?php echo $lang->kevinsoft->addedDate; ?></strong></th>
				<td><?php echo $VersionItem->addedDate; ?></td>
			</tr>
			<tr>
				<th><strong><?php echo $lang->kevinsoft->lastEditedBy; ?></strong></th>
				<td><?php echo $VersionItem->lastEditedBy; ?></td>
				<th><strong><?php echo $lang->kevinsoft->lastEditedDate; ?></strong></th>
				<td><?php echo $VersionItem->lastEditedDate; ?></td>
			</tr>
		</table>
	</fieldset>

	<?php
	if ($VersionItem->type === '1') {
		echo html::a($this->createLink('kevinsoft', 'groupversioncreate', "group={$VersionItem->id}", '', true), "<i class='icon-plus'></i>" . $lang->kevinsoft->groupversioncreate, '', "class='btn' data-toggle='modal' data-type='iframe'");
	} else {
		$files		 = $VersionItem->files;
		$fieldset	 = 'true';
		include '/fileprint.html.php';
	}
	?>
	<div class='actions left'><?php if (!$VersionItem->deleted) echo $actionLinks; ?></div>
	<?php include '../../common/view/action.html.php'; ?>
</div>

<?php include '../../common/view/footer.html.php'; ?>