<?php
/**
 * The view file
 *
 * @copyright   Kevin
 * @charge: free
 * @license: ZPL (http://zpl.pub/v1)
 * @author      Kevin <3301647@qq.com>
 * @package     kevinsoft
 * @link        http://www.zentao.net
 */
?>
<?php
$this->moduleName = "kevinsoft";
$this->methodName = "config";
?>
<?php include '../../kevincom/view/header.html.php';?>
<?php include '../../common/view/treeview.html.php';?>
<?php include '../../common/view/tablesorter.html.php'; ?>

<div id='featurebar'>
	<?php if($subMenu == "modulelist"&&isset($method)&&$method=="modulelist") {
	  if (('0' != $type || '0'!= $module||0!= $deleted||'0'!=$grouptype)&&('' != $type || ''!= $module||0!= $deleted||''!=$grouptype)) { ?>
        <form class='form-condensed' method='post' target='hiddenwin' id='deleteKeyForm'>
            <?php
            echo html::hidden('postType', 'delete');
            echo html::hidden('deleteKey', '') . html::hidden('deleteValue', '');
            ?>
        </form>
        <div id="" title="" class="chosen-container chosen-container-multi chosen-container-active">
            <ul class="chosen-choices" style="height: 30px">
                <?php
                if ('' != $module&&'0'!==$module) {
                    ?>
                    <li class="search-choice"><span><?php echo $selectedModule; ?></span><a onclick=<?php echo "\"onRemoveSearchChoice('module', '$selectedModule');\""; ?> class = "search-choice-close"></a></li>
                <?php } if ('' != $grouptype&&'0'!==$grouptype) {
                    ?>
                    <li class="search-choice"><span><?php echo $lang->kevinsoft->grouptypeMenu[$selectedGrouptype]; ?></span><a onclick=<?php echo "\"onRemoveSearchChoice('grouptype', '$selectedGrouptype');\""; ?> class = "search-choice-close"></a></li>
                <?php }if ('' != $type&&'0'!==$type) {
                    ?>
                    <li class="search-choice"><span><?php echo $lang->kevinsoft->modulePrivList[$selectedType]; ?></span><a onclick=<?php echo "\"onRemoveSearchChoice('type', '$selectedType');\""; ?> class = "search-choice-close"></a></li>
                    <?php
                }
				if ('' != $deleted&&0!=$deleted) { ?>
                    <li class="search-choice"><span><?php echo $lang->kevinsoft->deletedList[$selectedDeleted]; ?></span><a onclick=<?php echo "\"onRemoveSearchChoice('deleted', '$selectedDeleted');\""; ?> class = "search-choice-close"></a></li>
                <?php } ?>
                <li class="search-field"><input value=" " class="" autocomplete="off" style="width:15px;" type="text"></li>
            </ul>
        </div>
	<?php }}
	?>
	<div class='actions'>
 <?php
	if(!isset($subMenu))$subMenu = '';
	else if($subMenu == "softlist") {
		echo html::a($this->createLink('kevinsoft', 'softcreate', "", '', true)
		, "<i class='icon-plus'></i>". $lang->kevinsoft->softcreate, '', "data-toggle='modal' data-type='iframe' data-icon='check'");
	}elseif($subMenu == "modulelist") {
		echo html::a($this->createLink('kevinsoft', 'modulecreate', "", '', true)
		, "<i class='icon-plus'></i>". $lang->kevinsoft->modulecreate, '', "data-toggle='modal' data-type='iframe' data-icon='check'");
	
	}?>
</div>
</div>


<div class='side' id='treebox' >
  <a class='side-handle' data-id='kevinsoftTree'><i class='icon-caret-left'></i></a>
  <div class='side-body'>
	<div class='panel panel-sm' style="height: 400px;">
	<?php if(common::hasPriv('kevinsoft', 'moduleFilter')):
//		$Filter_Name = (array_key_exists("module", $keywordsArray))?$keywordsArray['module']:"";
//		$Filter_Deleted =  (array_key_exists("deleted", $keywordsArray))?$keywordsArray['deleted']:0;
		
		?>      
 <div class='panel-heading nobr'><?php echo html::icon($lang->icons['product']); ?> <?php echo $lang->kevinsoft->moduleFilter; ?></div>
    <?php $url = helper::createLink('kevinsoft', 'moduleFilter'); ?>
                <form style="height: 400px;width:198px;" id='searchform' method="post" <?php echo 'action=' . $url; ?> class='form-condensed'>
                    <table class='table table-form table-condensed'>
                        <tr>
                            <th class='text-left nobr'><?php echo $lang->kevinsoft->module; ?></th>
                            <td><?php echo html::select('module',$moduleMenu, $module, "class='form-control chosen' "); ?></td>
                        </tr>
						 <tr>
                            <th class='text-left nobr'><?php echo $lang->kevinsoft->grouptype; ?></th>
                            <td><?php echo html::select('grouptype',$lang->kevinsoft->grouptypeMenu, $grouptype, "class='form-control chosen' "); ?></td>
                        </tr>
						<tr>
							<th class='text-left nobr'><?php echo $lang->kevinsoft->type; ?></th>
							<td><?php echo html::radio('type', (array)$lang->kevinsoft->modulePrivList, $selectedType);?></td>		
						</tr>
<!--                        <tr>
                            <th class='text-left nobr'><?php // echo $lang->kevinsoft->type;
//				array_unshift($lang->kevinsoft->modulePrivList, ''); ?></th>
							 <td><?php // echo html::select('type', $lang->kevinsoft->modulePrivList, $type, "class='form-control chosen' "); ?></td>
                        </tr>	-->
						<tr>
							<th class='text-left nobr'><?php echo $lang->kevinsoft->deleted;?></th>
							<td><?php echo html::radio('deleted', (array)$lang->kevinsoft->deletedList, $selectedDeleted);?></td>		
						</tr>
                        <tr><td class='text-right nobr' colspan="2"><?php echo html::submitButton('搜索'); ?></td></tr>
                    </table>
                </form>

    <?php endif;?> 
	</div>
    
	</div>
  
</div>

<div class='main' style="overflow:auto;">
<table class='table setTable table-condensed table-hover  tablesorter' id='KevinValueList' style="table-layout:fixed;" >
    <thead>
    <tr class='text-center' height=35px>
        <th class='text-center w-50px'><?php echo $lang->kevinsoft->id;?></th>
        <th class='text-center w-200px'><?php echo $lang->kevinsoft->devicename;?></th>
		<th class='text-center w-60px'><?php echo $lang->kevinsoft->grouptype;?></th>
		<th class='text-center w-200px'><?php echo $lang->kevinsoft->applynum;?></th>
		<th class='text-center w-100px'><?php echo $lang->kevinsoft->softname;?></th>
		<th class='text-center w-80px'><?php echo $lang->kevinsoft->type;?></th>
		<th class='text-center w-80px'><?php echo $lang->kevinsoft->status;?></th>
		<th class='text-center w-200px'><?php echo $lang->kevinsoft->module;?></th>
		<th class='text-center w-200px'><?php echo $lang->kevinsoft->notes;?></th>
		<th class='text-center w-40px'><?php echo $lang->kevinsoft->count;?></th>
		<th class='text-center w-60px'><?php echo $lang->kevinsoft->startDate;?></th>
		<th class='text-center w-60px'><?php echo $lang->kevinsoft->endDate;?></th>
		<th class='text-center w-50px {sorter:false}'><?php echo $lang->actions;?></th>
    </tr>
    </thead>
    <?php foreach ($ModuleArray as $item):
		if($item->deleted) $style = "background:red";
		else $style = "";
		$viewLink = $this->createLink('kevinsoft', 'moduleview', "softID=$item->id");
		$canView  = common::hasPriv('kevinsoft', 'moduleview');
//		die(js::alert(date('Y-m-d')));
		if(date('Y-m-d')<=$item->endDate){
			$state='normal';
			$linestyle="";
			$datestyle="color: green;";
		}else{
			$state='exceed';
			$linestyle="background:yellow;";
			$datestyle="";
		}
	?>
	<tr class='text-center' style="<?php echo $linestyle;?>">		
		<td class='text-center' style="<?php echo $style;?>"><?php if($canView) echo html::a($viewLink, sprintf('%03d', $item->id)); else printf('%03d', $item->id);?></td>
		
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->devicename;?></td>
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $lang->kevinsoft->grouptypeMenu[$item->grouptype];?></td>
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->applynum;?></td>
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->softname;?></td>
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $lang->kevinsoft->modulePrivList[$item->type];?></td>
		<td class='text-center'  style="word-wrap:break-word;<?php echo $datestyle;?>"><?php echo $lang->kevinsoft->stateList[$state];?></td>
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->module;?></td>
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->notes;?></td>
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->count;?></td>
		<td class='text-center'  style="word-wrap:break-word;"><?php echo $item->startDate;?></td>
        <td class='text-center'  style="word-wrap:break-word;"><?php echo $item->endDate;?></td>
		<td class='text-center'>
			<?php
		common::printIcon('kevinsoft', 'moduledit',   "id=". $item->id, '', 'list', 'pencil', '', 'iframe', true);
		if($item->deleted)
		{
			echo "<button type='button' class='disabled btn-icon'><i class='icon-remove disabled icon-delete' title=''></i></button>";
		}else{
			common::printIcon('kevinsoft', 'moduledelete', "id={$item->id}", '', 'list', 'remove', 'hiddenwin');
		}	
			?>
		</td>
	</tr>
    <?php endforeach;?>
<tfoot>
  <tr>
	<td colspan=12 align='right'>
		<?php $pager->show();?>
	</td>
  </tr>
</tfoot>
	</table>
</div>
<?php include '../../common/view/footer.html.php';?>
<script language='javascript'>
function onButtonFilter()
{
    if($(".form-condensed .table").hasClass("hidden"))
    {
        $(".form-condensed .table").removeClass("hidden");
    }
    else
    {
        $(".form-condensed .table").addClass("hidden");
    }
}
function onRemoveSearchChoice(key, value)
{
    var deleteKeyObj = document.getElementById('deleteKey');//获得要删除的关键词类型
    deleteKeyObj.value = key;//设置变量
    var deleteValueObj = document.getElementById('deleteValue');//获得要删除的关键词类型
    deleteValueObj.value = value;//设置变量
    //提交表单
    document.getElementById("deleteKeyForm").submit();
}
</script>