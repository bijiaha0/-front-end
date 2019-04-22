<?php
/**
 * kevin calendar
 * @copyright   kevin
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<div class="main">
  <div id='featurebar'>
	<ul class='nav'>
	  <li><i class="icon-user"><?php echo $this->app->user->realname ?> </i><i class="icon-angle-right"></i></li>
	</ul>  
	<?php
	echo $lang->kevinhours->warningClockUpdate;
	echo $this->kevinhours->formattime($this->kevinhours->clockInor->itemTarget->time) . '? ';
	echo html::a(helper::createLink('kevinhours', 'clockact', "action=$action&ok=1", '', true)
		, "<i class='icon-ok-sign'></i> " . $lang->kevinhours->update, '', " data-toggle='modal' data-type='iframe' class='btn'");
	?>
  </div>

  <table class="table table-condensed table-hover table-striped tablesorter table-fixed" id="clockitem"> 
	<tr align = 'center'>
	  <th>id</th>
	  <th>account</th>
	  <th>action</th>
	  <th>date</th>
	  <th>time</th>
	</tr>
	<tr>
	  <td><?php echo $item->id; ?></td>
	  <td><?php echo $this->app->user->realname; ?></td>
	  <td><?php echo $lang->kevinhours->$action; ?></td>
	  <td><?php echo $item->date; ?></td>
	  <td><?php echo $this->kevinhours->formattime($item->time); ?></td>
	</tr>
  </table>

</div>
<?php
include '../../common/view/footer.html.php';
?>