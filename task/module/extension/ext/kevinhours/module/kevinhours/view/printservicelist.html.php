<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php
$monthArray	 = $DispachServiceClass->monthArray;
$Counter	 = 0;
foreach ($DispachServiceClass->ClassDeptArray as $dispatchDept => $currentDept) {
	if (empty($currentDept->accountArray) || empty($currentDept->cashCodeArray) || $currentDept->total == 0) {
		continue;
	}
	$Counter +=1;
	?>
	<div class='container mw-800px'>
	  <center>
		<body link="blue" vlink="purple">
		  <table width="800" border="0" cellpadding="0" cellspacing="0">
			<col width="54" />
			<col width="72"/>
			<col width="135"/>
			<col width="96"/>
			<col width="95"/>
			<col width="72"/>
			<tr height='40'>
			  <th colspan="5" class='text-left'><font size='4'><?php echo $this->loadModel('company')->getById($this->app->company->id)->name . $lang->kevinhours->serviceList; ?></font></th>
			</tr>
			<tr height="20">
			  <td colspan="5" class='text-left'><font size='3'><?php echo $this->lang->kevinhours->clientName; ?>:&nbsp;&nbsp;&nbsp;<?php echo $DeptName; ?></font></td>
			</tr>
			<tr height="20">
			  <td colspan="5" class='text-left'><font size='3'><?php echo $this->lang->kevinhours->partment; ?>:&nbsp;&nbsp;&nbsp;<?php echo $currentDept->name; ?></font></td>
			</tr>
		  </table>
		  <br><br>
		  <table width="800" border="1" cellpadding="0" cellspacing="0">
			<tr height="30">
			  <th rowspan='2' class='text-center'><?php echo $this->lang->kevinhours->projectName; ?></th>
			  <th rowspan='2' class='text-center'><?php echo $this->lang->kevinhours->cashCodeName; ?></th>
			  <th colspan=<?php echo count($monthArray); ?> class='text-center'><?php echo $this->lang->kevinhours->certainMonth; ?></th>
			  <th rowspan='2' class='text-center'><?php echo $this->lang->kevinhours->totalHours; ?></th>
			</tr>
			<tr height="19">
	<?php
	foreach ($monthArray as $currentMonth) {
		echo "<td align='right'><strong>$currentMonth{$this->lang->kevinhours->MonthZh}</strong></td>";
	}
	?>
			</tr>
				<?php
				$rowNum			 = 0;
				$defaultRowNum	 = 16;
				$grandTotal		 = 0.0;
				foreach ($currentDept->cashCodeArray as $cashCodeObj) {
					$rowNum += 1;
					$cashCode	 = $cashCodeObj->cashCode;
					$total		 = 0;
					echo '<tr height=19>';
					echo "<td align='center'><strong>{$cashCodeObj->name}</strong></td>";
					echo "<td align='center'><strong>$cashCode</strong></td>";
					foreach ($monthArray as $currentMonth) {
						$monthTotal	 = $cashCodeObj->hoursArray[$currentMonth];
						$monthTotal	 = number_format($monthTotal, 1);
						$total += $monthTotal;
						echo "<td align='right'><strong>$monthTotal</strong></td>";
					}
					$grandTotal += $total;
					$total = number_format($total, 1);
					echo "<td align='right'><strong>$total</strong></td>";
					echo '<tr>';
				}

				for (; $rowNum < $defaultRowNum; $rowNum++) {
					echo '<tr height=19>';
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					foreach ($monthArray as $currentMonth) {
						echo "<td align='center'>&nbsp;</td>";
					}
					echo "<td>&nbsp;</td>";
					echo '<tr>';
				}
				//打印总计
				echo '<tr height=19>';
				echo "<td align='center'><strong>{$this->lang->kevinhours->summation}</strong></td>";
				echo "<td>&nbsp;</td>";
				foreach ($monthArray as $currentMonth) {
					echo "<td>&nbsp;</td>";
				}
				$grandTotal = number_format($grandTotal, 1);
				echo "<td align='right'><strong>$grandTotal</strong></td>";
				echo '<tr>';
				?>
		  </table>
		</body>
	</div>
	<div style="page-break-after: always;"></div>
	<br><br>
	<?php
}
if ($Counter == 0) {
	echo " <center><h1>No record find!</h1>";
}
?>
<?php include '../../common/view/footer.html.php'; ?>
