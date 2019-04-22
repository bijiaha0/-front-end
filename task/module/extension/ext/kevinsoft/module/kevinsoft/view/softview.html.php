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
		<span class='prefix' title='SOFT'><?php echo html::icon($lang->icons['app']); ?> <strong><?php echo $Soft->id; ?></strong></span>
		<strong><?php echo $Soft->name; ?></strong>
		<?php if ($Soft->deleted): ?>
			<span class='label label-danger'><?php echo "已删除"; ?></span>
		<?php endif; ?>
	</div>
	<div class='actions'>
		<?php
		$browseLink	 = $this->createLink('kevinsoft', 'softlist', "");
		$params		 = "softID=$Soft->id";

		ob_start();
		echo "<div class='btn-group'>";
		common::printIcon('kevinsoft', 'softedit', $params, $Soft, 'button', 'pencil', '', 'iframe', true);
		if (!$Soft->deleted) common::printIcon('kevinsoft', 'softdelete', $params, '', 'button', 'remove', 'hiddenwin');

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
						<th class='w-80px'><strong><?php echo $lang->kevinsoft->softIID; ?></strong></th>
						<td><?php echo $Soft->IID; ?></td>
						<th><strong><?php echo $lang->kevinsoft->softname; ?></strong></th>
						<td><?php echo $Soft->name; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->softvalid; ?></strong></th>
						<td><?php echo $lang->kevinsoft->softvalidList[$Soft->valid]; ?></td>
						<th><strong><?php echo $lang->kevinsoft->softtype; ?></strong></th>
						<td><?php echo $lang->kevinsoft->softtypeList[$Soft->type]; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->addedBy; ?></strong></th>
						<td><?php echo $Soft->addedBy; ?></td>
						<th><strong><?php echo $lang->kevinsoft->addedDate; ?></strong></th>
						<td><?php echo $Soft->addedDate; ?></td>
					</tr>
					<tr>
						<th><strong><?php echo $lang->kevinsoft->lastEditedBy; ?></strong></th>
						<td><?php echo $Soft->lastEditedBy; ?></td>
						<th><strong><?php echo $lang->kevinsoft->lastEditedDate; ?></strong></th>
						<td><?php echo $Soft->lastEditedDate; ?></td>
					</tr>
				</table>
			</fieldset>
			<?php //endif;?>
			<?php include '../../common/view/action.html.php'; ?>
			<div class='actions left'><?php if (!$Soft->deleted) echo $actionLinks; ?>

			</div>
			<?php
			if (count($versionList)):
				echo "<br>" . $lang->kevinsoft->versionlist . "<br>";
				?>
			</div>
			<div>
				<table class='setTable table-hover table-striped tablesorter table-fixed' id='KevinValueList' style="table-layout:fixed;" style ="width:100px">
					<thead>
						<tr class='text-center' height=35px>
							<th class='text-center'><?php echo $lang->kevinsoft->id; ?></th>
							<th class='text-center'><?php echo $lang->kevinsoft->versionversion; ?></th>
							<th class='text-center'><?php echo $lang->kevinsoft->versiondownloads; ?></th>
							<th class='text-center'><?php echo $lang->kevinsoft->versionvalid; ?></th>
							<th class='text-center'><?php echo $lang->kevinsoft->replaceType; ?></th>
							<th class='text-center'><?php echo $lang->kevinsoft->lastEditedBy; ?></th>
							<th class='text-center'><?php echo $lang->kevinsoft->lastEditedDate; ?></th>
							<th class='text-center'><?php echo $lang->kevinsoft->deleted; ?></th>
							<th class='text-center {sorter:false}'><?php echo $lang->actions; ?></th>
						</tr>
					</thead>
					<?php
					foreach ($versionList as $item):
						if ($item->deleted) $style		 = "background:red";
						else $style		 = "";
						$viewLink	 = $this->createLink('kevinsoft', 'versionview', "softID=$item->soft&versionID=$item->version");
						$canView	 = common::hasPriv('kevinsoft', 'versionview');
						?>
						<tr>
							<td class='text-center' style="<?php echo $style; ?>"><?php
								if ($canView) echo html::a($viewLink, sprintf('%03d', $item->id));
								else printf('%03d', $item->id);
								?></td>
							<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->version; ?></td>
							<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->downloads; ?></td>
							<td class='text-center'  style="word-wrap:break-word;"><?php echo $lang->kevinsoft->versionvalidList[$item->valid]; ?></td>
							<td class='text-center'  style="word-wrap:break-word;"><?php echo $lang->kevinsoft->replaceTypeList[$item->type]; ?></td>
							<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->lastEditedBy; ?></td>
							<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->lastEditedDate; ?></td>
							<td class='text-center'  style="word-wrap:break-word;"><?php echo $lang->kevinsoft->deletedList[$item->deleted]; ?></td>
							<td class='text-center'>
								<?php
								common::printIcon('kevinsoft', 'versiondelete', "id={$item->id}", '', 'list', 'remove', 'hiddenwin'); //弹出窗口，取消后可以消失
								common::printIcon('kevinsoft', 'versionedit', "id={$item->id}", '', 'list', 'pencil', '', 'iframe', true); //模态窗口
								?>
							</td>
						</tr>
					<?php endforeach; ?>

				</table>
			<?php endif; ?>
		</div>
		<div>
			<?php
			echo html::a($this->createLink('kevinsoft', 'versioncreate', "softID={$Soft->id}", '', true), "<i class='icon-plus'></i>" . $lang->kevinsoft->versioncreate, '', "class='btn' data-toggle='modal' data-type='iframe'");
			?></div>
	</div>
</div>
<?php include '../../common/view/syntaxhighlighter.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>