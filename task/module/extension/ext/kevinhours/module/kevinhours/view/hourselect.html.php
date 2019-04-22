<div class='main'>
  <form class='form-condensed' method='post' target='hiddenwin' id='dataform'>
    <table class='table table-form'>
	  <tr><td><table>
        <th><?php echo $lang->kevinhours->certainYear;?></th>
        <td><?php echo html::select('year', $yearList, $year, 'class=form-control');?></td>
		<th><?php echo $lang->kevinhours->certainMonth;?></th>
        <td><?php echo html::select('month', $lang->kevinhours->month, $month, 'onchange=setSeasonInputNull() class=form-control');?></td>
		<th><?php echo $lang->kevinhours->certainSeason;?></th>
        <td><?php echo html::select('season', $lang->kevinhours->season, $season, 'onchange=setMonthInputNull() class=form-control');?></td>
		<th><?php echo $lang->kevinhours->dept;?></th>
        <td class="w-200px"><?php echo html::select('dept', $deptArray, $deptID,  "class='form-control chosen'");?></td><td></td></table></td>
      </tr>
      <tr>
		<td><table>
		<th><?php echo $lang->kevinhours->classdept;?></th>
        <td class="w-200px"><?php echo html::select('classdept', $deptArray, $classdept,  "class='form-control chosen'");?></td>
        <td colspan='1' >
          <?php echo html::submitButton("提交") . html::backButton();?>
			<?php
			if(common::hasPriv('kevinhours', 'printservicelist'))
			{
				echo "<input class='btn' type='button' value={$this->lang->kevinhours->printservicelist} onclick=printservicelist();>";
			}
			if(common::hasPriv('kevinhours', 'updateCashStat'))
			{
				echo "<input class='btn' type='button' value={$this->lang->kevinhours->updateCashStat} onclick=gotoUpdateCashStat();>";
			}
			?></table>
		</td>
      </tr>
    </table>
  </form>
</div>
<?php $this->loadModel('kevincom');?>
<div class='container mw-1400px'>
  <center>
 <body link="blue" vlink="purple">
  <br>
  <table width="80%" border="1" cellpadding="0" cellspacing="0">
   <tr height="30">
		<th class='text-center'><?php echo $this->lang->kevinhours->classType;?></th>
		<th class='text-center'><?php echo $this->lang->kevinhours->accountName;?></th>
		<th class='text-center'><?php echo $this->lang->kevinhours->projectName;?></th>
		<th class='text-center'><?php echo $this->lang->kevinhours->cashCodeName;?></th>
		<th class='text-center'><?php echo $this->lang->kevinhours->certainYear;?></th>
		<th class='text-center'><?php echo $this->lang->kevinhours->certainMonth;?></th>
		<th class='text-center'><?php echo $this->lang->kevinhours->actualHours;?></th>
		<th class='text-center'><?php echo $this->lang->kevinhours->amountToHours;?></th>
		<th class='text-center'><?php echo $this->lang->kevinhours->summation;?></th>
   </tr>
	<?php
		$Hours = 0;
		$AdditionHours = 0;
		$totalHours = 0;
		foreach($detailCashCodeArray as $detailCashCode)
		{
			echo "<tr height='20'>";
			echo "<td>{$detailCashCode->classDept}</td>";
			echo "<td>{$detailCashCode->realname}</td>";
			echo "<td>{$detailCashCode->name}</td>";
			echo "<td>{$detailCashCode->cashCode}</td>";
			echo "<td>{$detailCashCode->year}</td>";
			echo "<td>{$detailCashCode->month}</td>";
			echo "<td>{$detailCashCode->hours}</td>";
			echo "<td>{$detailCashCode->amountto}</td>";
			echo "<td>{$detailCashCode->total}</td>";
			echo '</tr>';
			$Hours += $detailCashCode->hours;
			$AdditionHours += $detailCashCode->amountto;
			$totalHours += $detailCashCode->total;
		}
		echo "<tr height='20'>";
		echo "<td colspan=6>&nbsp;</td>";
		echo "<td>$Hours</td>";
		echo "<td>$AdditionHours</td>";
		echo "<td>$totalHours</td>";
		echo '</tr>';
	?>
  </table>
  </body>
 </div>