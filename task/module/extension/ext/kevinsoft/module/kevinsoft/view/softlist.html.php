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
<?php
$this->moduleName	 = "kevinsoft";
$this->methodName	 = "config";
?>
<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/treeview.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<div id='featurebar'>
	<div class='actions'>
		<?php
		echo html::a($this->createLink('kevinsoft', 'modulecreate', "", '', true)
				, "<i class='icon-plus'></i>" . $lang->kevinsoft->modulecreate, '', "data-toggle='modal' data-type='iframe' data-icon='check'");
		?>
	</div>
</div>


<div class='side' id='treebox'>
	<a class='side-handle' data-id='kevinsoftTree'><i class='icon-caret-left'></i></a>
	<div class='side-body'>
		<div class='panel panel-sm' style="height: 500px;">
			<?php
			if (common::hasPriv('kevinsoft', 'softFilter')):
				$Filter_Name	 = (array_key_exists("name", $keywordsArray)) ? $keywordsArray["name"] : "";
				$Filter_Deleted	 = (array_key_exists("deleted", $keywordsArray)) ? $keywordsArray["deleted"] : 0;
				?>      
				<div class='panel-heading nobr'><?php echo html::icon($lang->icons['product']); ?> <button id='softFilter' class='btn' type='button' value='2' onclick='onButtonFilter()'><?php echo $lang->kevinsoft->softFilter; ?></button></div>
	<?php $url = helper::createLink('kevinsoft', 'softFilter'); ?>
				<form id='searchform' method="post" <?php echo 'action=' . $url; ?> class='form-condensed'>
					<table class='table table-form'>
						<tr>
							<th class='text-left nobr'><?php echo $lang->kevinsoft->softname; ?></th>
							<td class='w-auto'><?php echo html::input('name', $Filter_Name, 'class=form-control'); ?></td>
						</tr>
					<?php endif; ?>
<?php if (common::hasPriv('kevinsoft', 'softSeeDeleted')): ?>      
						<tr>
							<th class='text-left nobr'><?php echo $lang->kevinsoft->deleted; ?></th>
							<td class='w-auto'><?php echo html::select('deleted', $lang->kevinsoft->deletedArray, $Filter_Deleted, 'class=form-control'); ?></td>
						</tr>
<?php endif; ?>
					<tr><td class='text-right' colspan="4"><?php echo html::submitButton('搜索'); ?></td></tr>
				</table>
			</form>
		</div>

	</div>
</div>
<div class='main'>
	<table class='table setTable table-hover table-striped tablesorter table-fixed' id='KevinValueList' style="table-layout:fixed;" style ="width:100px">
		<thead>
			<tr class='text-center' height=35px>
				<th class='text-center'><?php echo $lang->kevinsoft->id; ?></th>
				<th class='text-center'><?php echo $lang->kevinsoft->softIID; ?></th>
				<th class='text-center'><?php echo $lang->kevinsoft->softname; ?></th>
				<th class='text-center'><?php echo $lang->kevinsoft->softvalid; ?></th>
				<th class='text-center'><?php echo $lang->kevinsoft->softtype; ?></th>
				<th class='text-center'><?php echo $lang->kevinsoft->addedBy; ?></th>
				<th class='text-center'><?php echo $lang->kevinsoft->addedDate; ?></th>
				<th class='text-center'><?php echo $lang->kevinsoft->deleted; ?></th>
				<th class='text-center {sorter:false}'><?php echo $lang->actions; ?></th>
			</tr>
		</thead>
		<?php
		foreach ($SoftArray as $item):
			if ($item->deleted) $style		 = "background:red";
			else $style		 = "";
			$viewLink	 = $this->createLink('kevinsoft', 'softview', "softID=$item->id");
			$canView	 = common::hasPriv('kevinsoft', 'softview');
			?>
			<tr>		
				<td class='text-center' style="<?php echo $style; ?>"><?php if ($canView) echo html::a($viewLink, sprintf('%03d', $item->id));
			else printf('%03d', $item->id); ?></td>
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->IID; ?></td>
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->name; ?></td>
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $lang->kevinsoft->softvalidList[$item->valid]; ?></td>
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $lang->kevinsoft->softtypeList[$item->type]; ?></td>
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->addedBy; ?></td>
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->addedDate; ?></td>
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $lang->kevinsoft->deletedList[$item->deleted]; ?></td>
				<td class='text-center'>
					<?php
					common::printIcon('kevinsoft', 'softdelete', "id={$item->id}", '', 'list', 'remove', 'hiddenwin');
					common::printIcon('kevinsoft', 'softedit', "id=" . $item->id, '', 'list', 'pencil', '', 'iframe', true);
					?>
				</td>
			</tr>
					<?php endforeach; ?>
		<tfoot>
			<tr>
				<td colspan=9 align='right'>
<?php $pager->show(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<?php include '../../common/view/footer.html.php'; ?>
