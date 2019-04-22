<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include 'titlebar.html.php'; ?>
<div class='container mw-700px'>
	<div id='titlebar'>
		<div class='heading'>
		<strong class='pull-left'><?php echo $lang->kevincalendar->batchcreate;?>&nbsp;</strong>
		</div>
	</div>
	<form class='form-condensed' method='post' target='hiddenwin'>
		<table class='table table-form' width='700px'>
			<thead>
				<tr>
				<th class='w-30px'><?php echo $lang->idAB;?></th> 
				<th class='w-120px'><?php echo $lang->kevincalendar->date;?></th>
				<th class='w-80px'><?php echo $lang->kevincalendar->status;?></th>
				<th class='w-p30 red'><?php echo $lang->kevincalendar->desc;?></th>
				</tr>
			</thead>
			<?php for($i = 0; $i < $config->kevincalendar->batchcreate; $i++):?>
				<tr class='text-center'>
					<td><?php echo $i+1;?></td>
					<td>
						<div class='input-group'>
					<?php echo html::input("dates[$i]", '', "class='form-control form-date'");?>
						</div>
					</td>
					<td><?php echo html::select("statuses[$i]", $lang->kevincalendar->statusList, '', "onchange='loadList(this.value, " . ($i + 1) . ")' class='form-control'");?></td>
					<td><?php echo html::input("descs[$i]", '', "class='form-control'");?></td>
				</tr>  
			<?php endfor;?>
			<tfoot>
				<tr><td colspan='4'><?php echo html::submitButton() . html::backButton();?></td></tr>
			</tfoot>
		</table>
	</form>
</div>
<?php include '../../common/view/footer.html.php';?>