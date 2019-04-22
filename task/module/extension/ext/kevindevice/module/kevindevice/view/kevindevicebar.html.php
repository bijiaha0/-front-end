<?php if (!isset($subMenu)) $subMenu = ''; ?>
<div id='featurebar'>
	<ul class='nav'>
		<li id='devlist'><?php echo html::a($this->createLink('kevindevice', 'devlist'), $lang->kevindevice->devlist); ?></li>	
		<li id='grouplist'><?php echo html::a($this->createLink('kevindevice', 'grouplist'), $lang->kevindevice->grouplist); ?></li>	
		<li id='statistic'><?php echo html::a($this->createLink('kevindevice', 'statistic'), $lang->kevindevice->statistic); ?></li>	
		<script>$('#<?php echo $subMenu; ?>').addClass('active')</script>
	</ul>
	<div class='actions'>
		<?php
		if ($subMenu == "devlist") common::printIcon('kevindevice', 'devcreate', '', '', 'button', 'plus');
		else common::printIcon('kevindevice', 'groupcreate', '', '', 'button', 'plus', '', 'iframe', true, "data-width='550'");
		?>
	</div>
	<?php if (isset($showStyle)) : ?>
		<div id = "ShowStyleDiv" class='pull-right'>
			<form id='ShowStyleForm' method="post" class='form-condensed'>
				<ul class='nav'>
					<li><?php echo $lang->kevindevice->showStyle; ?></li>
					<li><?php echo html::select("showStyle", $lang->kevindevice->showStyleList, $showStyle, "class='' onchange= \"SubmitFormByID('ShowStyleForm')\" "); ?>	</li>
				</ul>
			</form>
		</div>
	<?php endif; ?>
</div>