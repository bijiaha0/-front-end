<?php
/**
 * The browse side view file of kevinchart module
 *
 * @copyright   Kevin
 * @author      Kevin<3301647@qq.com>
 * @date		 2015-10-4
 * @package     kevinchart
 */
?>

<div class='side'>
	<a class='side-handle' data-id='companyTree'><i class='icon-caret-left'></i></a>
	<div class='side-body'>
		<div class='panel panel-sm'>
			<div class='panel-heading nobr'><?php echo html::icon($lang->icons['company']); ?> <strong>
					<?php common::printLink('kevinchart', $methodName, "", $lang->kevinchart->$methodName); ?></strong>
			</div>
			<div class='panel-body'>
				<ul class="tree treeview">
		<li  class='nobr'>Item 1</li>
		<li  class='nobr'>Item 2</li>
				</ul>
			</div>
		</div>
	</div>
</div>