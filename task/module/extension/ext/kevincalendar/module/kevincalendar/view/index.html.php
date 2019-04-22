<?php
/**
 * kevin calendar
 * @copyright   kevin
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/treeview.html.php'; ?>
<?php include 'titlebar.html.php'; ?>

<div style="margin: 0" class="op-calendar-new">
	<?php
	$statusArray = $this->kevincalendar->getInofoByMonth($type);
	if (empty($statusArray)) {
		echo '<h1>Get calendar array error!</h1>';
	} else {
		//$month = month(statusArray.front());
		$frontday			 = 6;
		?>
		<div id = "kevincalendar" class="kevincalendar">
			<div class="op-calendar-new-left c-clearfix">
				<div class="op-calendar-new-table-box c-gap-top">
					<table width = '100%' border = 1 class="op-calendar-new-table">
						<tbody>
							<tr class="fc-first fc-last">
								<th style="width: 205px;" class="kevincalendar-Title-W">星期一</th>
								<th style="width: 205px;" class="kevincalendar-Title-W">星期二</th>
								<th style="width: 205px;" class="kevincalendar-Title-W">星期三</th>
								<th style="width: 205px;" class="kevincalendar-Title-W">星期四</th>
								<th style="width: 205px;" class="kevincalendar-Title-W">星期五</th>
								<th style="width: 206px;" class="kevincalendar-Title-W">星期六</th>
								<th style="width: 207px;" class="kevincalendar-Title-W">星期天</th>
							</tr>
							<?php
							$countOfThisMonth	 = count($statusArray);
							for ($index = 0; $index < 6; $index++) {
								echo '<tr style="vertical-align:top;">';
								for ($weekday = 0; $weekday < 7; $weekday++) {
									echo '<td><div class = "div-kevincalendar-day ';
									$showDay	 = '&nbsp;';
									$status		 = '';
									$type		 = '';
									$currentItem = current($statusArray);
									$day		 = $currentItem->date;
									$status		 = $currentItem->status;
									$type		 = $currentItem->type;
									$showDay	 = date('j', strtotime($day));
									next($statusArray); //索引下移
									if ('C' == $type) {
										if ('hol' == $status) {
											echo 'kevincalendar-day-S';
										} else if ('law' == $status) {
											echo 'kevincalendar-day-H';
										} else if ('nor' == $status) {
											echo 'kevincalendar-day-W';
										} else {
											echo 'kevincalendar-day-otherMonth';
										}
									} else {
										echo 'kevincalendar-day-otherMonth';
									}

									echo '">';
									echo $showDay;
									echo '<div class = "pull-right text-muted">';
									if ('' != $currentItem->desc) {
										echo $currentItem->desc;
										common::printIcon('kevincalendar', 'edit', "id=$currentItem->id", '', 'list', 'pencil', '', 'iframe', true);
									} else {
										echo html::a(helper::createLink('kevincalendar', 'create'
												, "date=" . str_replace('-', '', $day), '', true)
											, "<i class='icon-plus-sign'></i>", '', " data-toggle='modal' data-type='iframe' title={$lang->kevincalendar->create}");
									}
									echo '</div>';

									if (!empty($currentItem->todos)) {
										foreach ($currentItem->todos as $todo) {
											$href = $this->createLink('todo', 'view', "id=$todo->id", '', true);
											echo "<a href=$href data-toggle='modal' data-type='iframe'><div class='fc-event'><span>{$todo->name}</span></div></a>";
										}
									}
									echo '</div></td>';
								}
								echo '</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>
	<div style = "display: none;" class = "op-calendar-new-holidaytip"></div>
</div>

<?php
include '../../common/view/footer.html.php';
