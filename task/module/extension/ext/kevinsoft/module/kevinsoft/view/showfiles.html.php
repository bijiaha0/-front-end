<?php
/**
 * @package     kevinsoft
 */
?>
<?php include '../../kevincom/view/header.html.php';?>

<?php echo css::internal($keTableCSS);?>
<?php include 'groupversionshow.html.php';?>
<table class='table setTable table-hover table-striped tablesorter table-fixed' id='showfileList' style="table-layout:fixed;" style ="width:100px">
	<thead>
		<tr class='text-center' height=35px>
			<th class='text-center'><?php echo $lang->kevinsoft->id;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->filesoft;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->fileversion;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->filesize;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->filedownloads;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->filevalid;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->filemd5;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->filetype;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->addedBy;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->addedDate;?></th>
			<th class='text-center'><?php echo $lang->kevinsoft->deleted;?></th>
			<th class='text-center {sorter:false}'><?php echo $lang->actions;?></th>
		</tr>
	</thead>
<?php foreach ($files as $item):
				if($item->deleted) $tdstyle = "background:red";
				else $tdstyle = "";
				$viewLink = $this->createLink('kevinsoft', 'fileview', "fileID=$item->id");
				$canView  = common::hasPriv('kevinsoft', 'fileview');
			?>
			<tr>
				<td class='text-center' style="<?php echo $tdstyle;?>"><?php if($canView) echo html::a($viewLink, sprintf('%03d', $item->id)); else printf('%03d', $item->id);?></td>
				<td class='text-center'><?php echo $item->soft;?></td>
				<td class='text-center'><?php echo $item->version;?></td>
				<td class='text-center'><?php echo $item->size;?></td>
				<td class='text-center'><?php echo $item->downloads;?></td>
				<td class='text-center'><?php echo $lang->kevinsoft->filevalidList[$item->valid];?></td>
				<td class='text-center'><?php echo $item->md5;?></td>
				<td class='text-center'><?php echo $lang->kevinsoft->filetypeList[$item->type];?></td>
				<td class='text-center'><?php isset($users[$item->addedBy]) ? print($users[$item->addedBy]) : print($item->addedBy);?></td>
				<td class='text-center'><?php echo date("m-d H:i", strtotime($item->addedDate));?></td>
				<td class='text-center'><?php echo $lang->kevinsoft->deletedList[$item->deleted];?></td>
				<td class='text-center'>
					<?php
						common::printIcon('kevinsoft', 'filedelete', "id={$item->id}", '', 'list', 'remove', 'hiddenwin');
						common::printIcon('kevinsoft', 'fileedit',   "id=". $item->id, $item, 'list', 'pencil', '', '', false);//iframe
					?>
				</td>
			</tr>
			<?php endforeach;?>
</table>			
<?php include '../../common/view/syntaxhighlighter.html.php';?>
<?php include '../../common/view/footer.html.php';?>