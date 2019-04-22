<?php
if(!isset($deptchildmenu)) $deptchildmenu='';
if(!isset($deptChilds))	$deptChilds=false;
if(!isset($deptinfo)) $deptinfo=false;
if(!isset($deptname)) $deptname='';
if(!isset($method)) $method='over';
if($this->kevinhours->user->checkAll)
	$deptArray=$this->kevincom->getDeptOptionMenu();
elseif($this->kevinhours->user->browseDeptHours){
	$deptArray=$deptchildmenu;
}else
	$deptArray='';
$sidewidth=200;//侧边栏像素宽度
//$employeesActive= $this->kevinhours->employeesActive;
//if($employeesActive) {
	?>
	<?php if (common::hasPriv('kevinhours', 'browseDeptHours')) { ?>
<div class='side' style='width:<?php echo $sidewidth;?>px;'>
		<div class='side-body'>
			<div class='panel panel-sm'>
				<div class='panel-heading'>
					<strong>
						 <?php  echo "<ul class='nav'><li id='NextLinkOver'></li></ul>";
					 	if (!empty($deptArray)) {
							echo html::select('dept', $deptArray, $deptID, "class='form-control chosen' onchange='gotoOtherDept(this.value);'").'</br>';
						} ?></strong></div>
			<div class='panel-body table table-condensed  table-fixed' style = 'min-height:400px'>		
	<div class="nobr"><button class="btn pull-left <?php if($method=='over') echo 'active';?>" id="accountreverse" type="button" onclick="onButtonMember()"><?php echo $lang->kevinhours->account;?></button> <button class="btn  pull-left <?php if($method=='overdept') echo 'active';?>" id="deptchildreverse" type="button" onclick="onButtonDeptChild()"><?php echo $lang->kevinhours->dept;?></button>

	<?php if($deptinfo){
	if($deptinfo->parent){ 
		if($method=='over') 
			echo "&nbsp;&nbsp;&nbsp;<a class='btn pull-center' id='gotoparent' href='/zentao/kevinhours-over-".$type.'--'.$deptinfo->parent."'><i class='icon-level-up'></i></a>";
		elseif($method=='overdept')
			echo "&nbsp;&nbsp;&nbsp;<a class='btn pull-center' id='gotoparent' href='/zentao/kevinhours-overdept-".$type.'-'.$deptinfo->parent."'><i class='icon-level-up'></i></a>";
	}
}?></div>
	<a class='side-handle' data-id='companyTree' onclick='onsplice();'><i id='slipicon' class='icon-caret-left'></i></a>

	<table id='deptchildlist' class='nav <?php if($method=='over') echo 'hidden';?>'>
	<?php if ($deptChilds) {
		if(isset($deptname)){$lastdept=explode('/',$deptname);
		echo '<th>'.html::a($this->createLink('kevinhours', 'overdept', 'type='.$type. '&dept='. $this->session->currentdeptID), "<i class='icon-building'></i> " . $lastdept[count($lastdept)-1], '','').'</th>';}
		foreach ($deptChilds as $deptChild) {			
				echo "<tr id=$deptChild->id><td>".html::a(helper::createLink('kevinhours', 'over', "type=$type&account=&deptID=$deptChild->id"),"<i class='icon-user'>&nbsp;</i>", '', "") . html::a(helper::createLink('kevinhours', 'overdept', "type=$type&deptID=$deptChild->id"),"<i class='icon-building'>&nbsp;</i>", '', "").$deptChild->name. '</td></tr>';
			}
		}else{
			if(isset($deptname)){$lastdept=explode('/',$deptname);
			echo '<th>'.html::a($this->createLink('kevinhours', 'overdept', 'type='.$type. '&dept='. $this->session->currentdeptID), "<i class='icon-building'></i> " . $lastdept[count($lastdept)-1], '','').'</th>';echo '<tr><td>'.$lang->kevinhours->nothing.'</td></tr>';}
		} ?>
		</table>		
 <table class='table table-condensed table-fixed table-hover table-striped <?php if($method=='overdept') echo 'hidden';?>' id='deptmember'>
			<?php	$employeesAll	 = $this->kevinhours->employeesAll;
						
						$j=0;
						echo "<tr onclick='hideindeptmember(0);'><th><strong class='pull-left'>&nbsp;".$this->lang->kevinhours->indeptmember."</strong> <strong class='pull-right'><i class='icon-angle-right'></i></strong></th></tr>";
						foreach ($employeesAll as $key => $name) {					
							if(isset($name->isexternal)&&$name->isexternal==0){
								if(isset($name->locked)&&$name->locked==0){
								if ($key) {
												$tempKey = str_replace(".", "-", $key);
												echo "<tr id=$tempKey class='indeptmember'><td>" . html::a(helper::createLink('kevinhours', 'over', "type=" . $type . "&account=" . $key . "&deptID={$this->session->currentdeptID}")
														, "<i class='icon-user'>&nbsp;</i>" . $name->realname, '', "");
												if($key==$this->session->kevin_user_account) echo html::icon($lang->icons['story']);
												echo '</td></tr>';
												$j++;
											}
								}
							}
						}
						if($j==0)echo "<tr class='indeptmember'><td>".$this->lang->kevinhours->nothing.'</td></tr>';
						$j=0;
						echo "<tr class='text-right indeptmember' id ='InactiveUsers' onclick='hideInactive(0);'><td><i class='icon-angle-left'><strong><small>&nbsp{$this->lang->kevinhours->inactive}</small></strong></td></tr>";
						foreach ($employeesAll as $key => $name) {					
							if(isset($name->isexternal)&&$name->isexternal==0){
								if(isset($name->locked)&&$name->locked==1){
								if ($key) {
												$tempKey = str_replace(".", "-", $key);
												echo "<tr id=$tempKey class='hideInactive'><td class='indeptmember'>" . html::a(helper::createLink('kevinhours', 'over', "type=" . $type . "&account=" . $key . "&deptID={$this->session->currentdeptID}")
														, "<i class='icon-user'>&nbsp;</i>" . $name->realname, '', "");
												if($key==$this->session->kevin_user_account) echo html::icon($lang->icons['story']);
												echo '</td></tr>';
												$j++;
											}
								}			
							}
						}
						if($j==0)echo "<tr class='hideInactive'><td class='indeptmember'>".$this->lang->kevinhours->nothing.'</td></tr>';
						$j=0;
						echo "<tr onclick='hideindeptmember(1);'><th><strong class='pull-left'>&nbsp;".$this->lang->kevinhours->externalmember."</strong> <strong class='pull-right'><i class='icon-angle-right'></i></strong></th></tr>";
						foreach ($employeesAll as $key => $name) {					
							if(isset($name->isexternal)&&$name->isexternal==1){
								if(isset($name->locked)&&$name->locked==0){
								if ($key) {
												$tempKey = str_replace(".", "-", $key);
												echo "<tr id=$tempKey class='externalmember'><td>" .html::a(helper::createLink('kevinhours', 'over', "type=" . $type . "&account=" . $key . "&deptID={$this->session->currentdeptID}")
														, "<i class='icon-user'>&nbsp;</i>" . $name->realname, '', "");
												if($key==$this->session->kevin_user_account) echo html::icon($lang->icons['story']);
												echo '</td></tr>';
												$j++;
											}
								}
							}
						}
						if($j==0)echo "<tr class='externalmember'><td>".$this->lang->kevinhours->nothing.'</td></tr>';
						$j=0;
						echo "<tr class='text-right externalmember' id ='InactiveUsers' onclick='hideInactive(1);'><td><i class='icon-angle-left'><strong><small>&nbsp{$this->lang->kevinhours->inactive}</small></strong></td></tr>";
						foreach ($employeesAll as $key => $name) {					
							if(isset($name->isexternal)&&$name->isexternal==1){
								if(isset($name->locked)&&$name->locked==1){
								if ($key) {
												$tempKey = str_replace(".", "-", $key);
												echo "<tr id=$tempKey class='hidextInactive'><td class='externalmember'>" . html::a(helper::createLink('kevinhours', 'over', "type=" . $type . "&account=" . $key . "&deptID={$this->session->currentdeptID}")
														, "<i class='icon-user'>&nbsp;</i>" . $name->realname, '', "");
												if($key==$this->session->kevin_user_account) echo html::icon($lang->icons['story']);
												echo '</td></tr>';
												$j++;
											}
								}			
							}
						}
						if($j==0)echo "<tr class='hidextInactive'><td class='externalmember'>".$this->lang->kevinhours->nothing.'</td></tr>';
					}else echo '<tr><td>'.$lang->kevinhours->nothing.'</td></tr>';
					?>
				</table>
			</div>
			</div>
		</div>
	</div>
<?php // }
?>
<script type="text/javascript">	
$('.hideInactive').addClass('hidden');
$('.hidextInactive').addClass('hidden');
function onButtonMember()
{
	if($("#deptmember").hasClass("hidden"))
	{
			$("#deptchildlist").addClass("hidden");
			$("#deptmember").removeClass("hidden");
			$("#deptchildreverse").removeClass('active');
			$("#accountreverse").addClass('active');
			var link='/zentao/kevinhours-over-'+'<?php echo $type;?>'+'-'+'<?php echo $user->account;?>'+'-'+'<?php if(isset($deptinfo->parent)) echo $deptinfo->parent;?>';
			$('#gotoparent').attr('href',link);
	}
	else $("#deptchildlist").addClass("hidden");
}
function onButtonDeptChild()
{
	if ($("#deptchildlist").hasClass("hidden"))
	{
			$("#deptchildlist").removeClass("hidden");
			$("#deptmember").addClass("hidden");
			$("#deptchildreverse").addClass('active');
			$("#accountreverse").removeClass('active');
			var link='/zentao/kevinhours-overdept-'+'<?php echo $type;?>'+'-'+'<?php if(isset($deptinfo->parent)) echo $deptinfo->parent;?>';
			$('#gotoparent').attr('href',link);
	}
	else $("#deptmember").addClass("hidden");
}
function onsplice(){
	if($("#slipicon").hasClass('icon-caret-right')){
		var width='<?php echo $sidewidth.'px';?>';
		var mainmargin='<?php echo ($sidewidth+20).'px';?>';
		$(".side").css('width',width);
		$(".main").css('margin-left',mainmargin);
		$(".main").resize();
	}else{
		$(".side").css('width','');
		$(".main").css('margin-left','');
		$(".main").resize();
//		$(".side").addClass('flag');
	}
}
function hideInactive(type){
if(type=='0')
	$('.hideInactive').toggleClass('hidden');

else
	$('.hidextInactive').toggleClass('hidden');
}
function hideindeptmember(type){
	if(type=='0')
		$('.indeptmember').toggleClass('hidden');
	else
		$('.externalmember').toggleClass('hidden');
	
}
</script>