<?php
/**
 * The browse user view file of kevindevice module
 *
 * @copyright   Kevin
 * @author      kevin<3301647@qq.com>
 * @package     kevindevice
 */
?>
<?php
include '../../kevincom/view/header.html.php';
include '../../common/view/treeview.html.php';
js::set('groupID', $groupID);
js::set('confirmDelete', $config->kevindevice->confirmDelete);
?>
<?php include 'kevindevicebar.html.php';?>
<div class='side'>
	<a class='side-handle' data-id='companyTree'><i class='icon-caret-left'></i></a>
	<div class='side-body'>
		<div class='panel panel-sm'>
			<div class='panel-heading nobr'><?php echo html::icon($lang->icons['company']); ?> <strong>
					<?php common::printLink('kevindevice', 'devlist', "", $lang->kevindevice->group); ?></strong></div>
			<div class='panel-body'>
				<ul class="tree treeview">
					<?php foreach ($groups as $group):
						?>
						<li ><?php common::printLink('kevindevice', 'devlist', "groupid=$group->id", $group->name); ?>        </li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class='main' style="overflow:auto; ">

	<?php if ($targetgroup) : ?>
		<div><?php echo $lang->kevindevice->group . " > " . $targetgroup->name . " > " . $lang->kevindevice->devlist ?> : </div>
	<?php
	endif;
	if ($showStyle == '1')include './devlisttablesimple.html.php';//简单显示列，
	else if ($showStyle == '2')include './devlisttablecomputer.html.php';
	?>
</div>
<script lanugage='javascript'>$('#dept<?php echo $groupID; ?>').addClass('active');</script>
<?php include '../../common/view/footer.html.php'; ?>
