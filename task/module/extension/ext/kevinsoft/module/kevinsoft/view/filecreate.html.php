<?php include '../../kevincom/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>

<div class='container mw-1400px'>
<div id='titlebar'>
	<div class='heading'>
	<small class='text-muted'><?php echo html::icon($lang->icons['create']). ''.$lang->kevinsoft->filecreate;?></small> 
	</div>
</div>
<form class='form-condensed' method='post' enctype='multipart/form-data' target='hiddenwin' id='dataform'>
	<table class='table table-form table-condensed'> 
		<tr>
			<!--关联模块-->
			<th><?php echo $lang->kevinsoft->filesoft;?></th>
			<td><?php echo html::input('soft','', "class='form-control'");?></td><td></td>
		</tr> 
		<tr>
			<!--版本-->
			<th><?php echo $lang->kevinsoft->fileversion;?></th>
			<td><?php echo html::input('version', '',  "class='form-control'");?></td><td></td>
		</tr> 
		<tr>
			<!--文件有效性-->
			<th><?php echo $lang->kevinsoft->filevalid;?></th>
			<td><?php echo html::select('valid', $lang->kevinsoft->filevalidList, '',  "class='form-control'");?></td>
		</tr> 
		<tr>
			<!--文件加密-->
			<th><?php echo $lang->kevinsoft->filemd5;?></th>
			<td><?php echo html::input('md5', '',  "class='form-control'");?></td>
		</tr> 
		<tr>
			<!--文件类型-->
			<th><?php echo $lang->kevinsoft->filetype;?></th>
			<td><?php echo html::select('type', $lang->kevinsoft->filetypeList, '', 'class=form-control');?></td>
		</tr>
		<tr>
			<th><?php echo $lang->kevinsoft->comment;?></th>
			<td colspan='2'><?php echo html::textarea('comment','', "class='form-control' rows=3");?></td>
		</tr> 
		<tr id='fileBox' class=''>
			<th><?php echo $lang->kevinsoft->files;?></th>
			<td colspan='2'><?php echo $this->fetch('file', 'buildform');?></td>
		</tr>
		<tr>
			<td></td>
			<td colspan='2'>
			  <?php echo html::submitButton() . html::backButton();?>
			</td>
	  </tr>	
	</table>
</form>
</div>
<?php include '../../common/view/footer.html.php';?>

