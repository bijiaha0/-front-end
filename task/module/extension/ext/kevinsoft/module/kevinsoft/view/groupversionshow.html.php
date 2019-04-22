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

<table class='table setTable table-hover table-striped tablesorter table-fixed' id='KevinValueList' style="table-layout:fixed;" style ="width:100px">
	<thead>
		<tr class='text-center' height=35px>
			<th class='text-center'><?php echo $lang->kevinsoft->groupversion;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->subversion;?></th>
			<th class='text-centerx'><?php echo $lang->kevinsoft->deleted;?></th>
			<th class='text-center {sorter:false}'><?php echo $lang->actions;?></th>
		</tr>
	</thead>
				
		<?php foreach ($GroupversionArray as $item):
			if (($versionID == $item->version) && ($filesID == $item->file)) $trstyle = "background-color:blue";
			else $trstyle = "";
			//if($item->deleted) $style = "background:red";
			//else $style = "";
			$viewLink = $this->createLink('kevinsoft', 'showfiles', "versionID=$item->version&filesID=$item->file");
			$canView  = common::hasPriv('kevinsoft', 'showfiles');
		?>
			<tr  style=<?php echo $trstyle;?>>	
			<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->version;?></td>
			<td class='text-center' style="<?php echo $style;?>"><?php if($canView) echo html::a($viewLink, sprintf('%03d', $item->subversion)); else printf('%03d', $item->file);?></td>
			<td class='text-center'  style="word-wrap:break-word;"><?php echo  $item->deleted;?></td>
			<td class='text-center'>
			<?php
					common::printIcon('kevinsoft', 'groupversiondelete', "versionID=$item->version&fileID=$item->file", '', 'list', 'remove', 'hiddenwin');
					common::printIcon('kevinsoft', 'groupversionedit',   "versionID=$item->version&fileID=$item->file", '', 'list', 'pencil', '', 'iframe', true);
			?>
			</td>
			</tr>
		<?php endforeach;?>
<tfoot>
  <tr>
	<td colspan=4 align='right'>
		<?php $pager->show();?>
	</td>
  </tr>
</tfoot>
	</table>
