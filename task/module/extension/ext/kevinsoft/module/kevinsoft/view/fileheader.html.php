<div id='featurebar'>	
<?php if( '' != $vendor){?>
<form class='form-condensed' method='post' target='hiddenwin' id='deleteKeyForm'>
<?php echo html::hidden('postType', 'delete');echo html::hidden('deleteKey', '').html::hidden('deleteValue', '');?>
</form>
	筛选：
<div id="account_chosen" title="" class="chosen-container chosen-container-multi chosen-container-active">
	<ul class="chosen-choices" style="height: 30px">
	<?php 
	if('' != $vendor ){?>
	<li class="search-choice"><span><?php echo $selectedVendor;?></span><a onclick=<?php echo "\"onRemoveSearchChoice('vendor', '$selectedVendor');\"";?> class="search-choice-close"></a></li>
	<?php  }
		if(common::hasPriv('kevinsoft', 'softSeeDeleted')):  
		if(0 != $deleted ){?>
		<li class="search-choice"><span><?php echo '删除软件';?></span><a onclick=<?php echo "\"onRemoveSearchChoice('deleted', '$selectedDeleted');\"";?> class="search-choice-close"></a></li>
		<?php }
		endif;
		?>
	<li class="search-field"><input value=" " class="" autocomplete="off" style="width:15px;" type="text"></li>
	</ul>
</div>
<?php }?>
<div class='actions'>
	<div class='btn-group'>
		<div class='btn-group'>
        <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' id='exportAction'>
            <i class='icon-upload-alt'></i> <?php echo $lang->import ?>
            <span class='caret'></span>
        </button>
        <ul class='dropdown-menu' id='exportActionMenu'>
        <?php 
        $misc = common::hasPriv('kevinsoft', 'import') ? "class='export iframe' data-width='700'" : "class=disabled";
        $link = common::hasPriv('kevinsoft', 'import') ?  $this->createLink('kevinsoft', 'import', "") : '#';
        echo "<li>" . html::a($link, $lang->kevinsoft->import, '', $misc) . "</li>";
        ?>
        </ul>
      </div>
    </div>
	<?php echo html::a(helper::createLink('kevinsoft', 'filecreate'), "<i class='icon-plus'></i> " . $lang->kevinsoft->filecreate, '', "class='btn'");?>
</div>
</div>
