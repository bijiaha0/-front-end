<?php include '../../common/view/header.html.php'; ?>
<?php include 'titlebar.html.php'; ?>
<div class='container mw-700px'>
	<div id='featurebar'>
		<ul class='nav'>
			<?php
			foreach ($lang->kevincalendar->periods as $period => $label) {
				$vars = "date=$period";
				if ($period == 'before')
					$vars .= "&account={$app->user->account}&status=undone";
				echo "<li id='$period'>" . html::a(inlink('lists', $vars), $label) . '</li>';
			}
			?>
			<script>$('#<?php echo $type; ?>').addClass('active')</script>
		</ul>
	</div>
	<table class='table table-condensed table-hover table-striped tablesorter table-fixed' id='todoList'>
		<thead>
			<tr class='text-center'>
				<th class='w-id'>    <?php echo $lang->idAB; ?></th>
				<th class='w-date'>  <?php echo $lang->kevincalendar->date; ?></th>
				<th class='w-type'>  <?php echo $lang->kevincalendar->status; ?></th>
				<th   <?php echo $lang->kevincalendar->desc; ?></th>
				<th class='w-60px {sorter:false}'><?php echo $lang->actions; ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $id = 0;
			foreach ($kevincalendars as $kevincalendar): $id+=1;
				?>
				<tr class='text-center'>
					<td><?php echo $id; ?></td>
					<td><?php echo $kevincalendar->date; ?></td>
					<td><?php echo $lang->kevincalendar->statusList[$kevincalendar->status]; ?></td>
					<td><?php echo $kevincalendar->desc; ?></td>
					<td class='text-right'>
						<?php
						common::printIcon('kevincalendar', 'edit', "id=$kevincalendar->id", '', 'list', 'pencil', '', 'iframe', true);
						?>
					</td>
				</tr>
<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php include '../../common/view/footer.html.php'; ?>
