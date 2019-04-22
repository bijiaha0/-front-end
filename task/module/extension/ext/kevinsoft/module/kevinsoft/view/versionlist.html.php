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
<?php include '../../common/view/treeview.html.php';?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<?php
$sessionString = $config->requestType == 'PATH_INFO' ? '?' : '&';
$sessionString .= session_name() . '=' . session_id();
?>
<div class='main'>
<table class='setTable table-hover table-striped tablesorter table-fixed' id='KevinValueList' style="table-layout:fixed;" style ="width:100px">
	<thead>
		<tr class='text-center' height=35px>
			<th class='text-center'><?php echo $lang->kevinsoft->id;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->versionsoft;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->name;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->versionversion;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->versiondownloads;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->versionvalid;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->versionmd5;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->replaceType;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->addedBy;?></th>
			<th class='text-center w-150px'><?php echo $lang->kevinsoft->addedDate;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->deleted;?></th>
			<th class='text-center {sorter:false}'><?php echo $lang->actions;?></th>
		</tr>
	</thead>
		<?php foreach ($VersionArray as $item):
			if($item->deleted) $style = "background:red";
			else $style = "";
			$viewLink = $this->createLink('kevinsoft', 'versionview', "versionID=$item->id");
			$canView  = common::hasPriv('kevinsoft', 'versionview');
		?>
	<tr>
		<td class='text-center' style="<?php echo $style;?>"><?php if($canView) echo html::a($viewLink, sprintf('%03d', $item->id)); else printf('%03d', $item->id);?></td>
		<td class='text-center'><?php
		echo html::a($this->createLink('kevinsoft', 'softview', "id=$item->soft") . $sessionString,  $item->softname, '', "");?></td>
		<td class='text-center'><?php echo $item->name;?></td>
		<td class='text-center'><?php echo $item->version;?></td>
		<td class='text-center'><?php echo $item->downloads;?></td>
		<td class='text-center'><?php echo  $lang->kevinsoft->versionvalidList[$item->valid];?></td>
		<td class='text-center'><?php echo $item->md5;?></td>
		<td class='text-center'><?php echo $lang->kevinsoft->replaceTypeList[$item->replaceType];?></td>
		<td class='text-center'><?php echo $item->addedBy;?></td>
		<td class='text-center'><?php echo $item->addedDate;?></td>
		<td class='text-center'><?php echo $lang->kevinsoft->deletedList[$item->deleted];?></td>
		<td class='text-center'>
		<?php
			common::printIcon('kevinsoft', 'versiondelete', "id={$item->id}", '', 'list', 'remove', 'hiddenwin'); //弹出窗口，取消后可以消失
			common::printIcon('kevinsoft', 'versionedit',   "id={$item->id}", '', 'list', 'pencil', '', 'iframe', true);//模态窗口
		?>
		</td>
		</tr>
<?php endforeach;?>
<tfoot>
  <tr>
	<td colspan=11 align='right'>
		<?php $pager->show();?>
	</td>
  </tr>
</tfoot>
	</table>
</div>
<?php include '../../common/view/footer.html.php';?>
