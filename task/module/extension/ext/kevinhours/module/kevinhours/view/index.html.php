<?php
/**
 * kevin calendar
 * @copyright   kevin
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/treeview.html.php'; ?>
<script language='Javascript'>var account = '<?php echo $this->kevinhours->account; ?>'</script>
<?php
include 'titlebar.html.php';
echo $overtreetitle;
if ($CalendarTableString == "") {
	$warningString = '<h1>Get calendar array error!</h1>';
	echo $warningString;
} else {
	?>
	<div class="main" style="margin-left:<?php if(common::hasPriv('kevinhours', 'browseDeptHours'))echo $config->kevinhours->sideWidth+20;?>px;">
	  <table width = 100%>
		<tr style="vertical-align: top;"><td><?php echo $oveHoursTableString . $CalendarTableString; ?></td></tr>
	  </table>	
	  <?php include 'todolockwarning.html.php'; ?>
	</div>
	<?php
}
include '../../common/view/footer.html.php';
?>
