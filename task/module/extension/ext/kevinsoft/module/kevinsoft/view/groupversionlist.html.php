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
<?php include '../../common/view/treeview.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<div class='main'>

	<table class='table setTable table-hover table-striped tablesorter table-fixed' id='KevinValueList' style="table-layout:fixed;" style ="width:400px">
		<thead>
			<tr class='text-center' height=35px>
				<th class='text-center'><?php echo $lang->kevinsoft->groupversionID; ?></th>
				<th class='text-center'><?php echo $lang->kevinsoft->subversionID; ?></th>
				<th class='text-center {sorter:false}'><?php echo $lang->actions; ?></th>
			</tr>
		</thead>

		<?php
		foreach ($GroupversionArray as $item):
			$viewLink	 = $this->createLink('kevinsoft', 'showfiles', "versionID=$item->version");
			$canView	 = common::hasPriv('kevinsoft', 'showfiles');
			?>
			<tr >	
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->groupversion; ?></td>
				<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->version; ?></td>
				<td class='text-center'>
					<?php
					common::printIcon('kevinsoft', 'groupversiondelete', "group=$item->groupversion&version=$item->version", '', 'list', 'remove', 'hiddenwin');
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		<tfoot>
			<tr>
				<td colspan=3 align='right'><?php $pager->show(); ?></td>
			</tr>
		</tfoot>
	</table>
</div>
<?php include '../../common/view/footer.html.php'; ?>
