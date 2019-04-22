<!DOCTYPE html>
<html lang='zh-cn'>
	<head>
		<meta charset='utf-8'>
		<meta http-equiv='X-UA-Compatible' content='IE=edge'>
		<title><?php echo '班组加班表－打印'; ?></title>
	</head>
	<body>
		<div class='container mw-1400px'>
			<form method='post' id='todoform'>
				<center>
					<?php
					$dateArray = array();
					$lastDate = '';
					foreach ($todos as $todo): if ($lastDate != $todo->date) {
							$lastDate = $todo->date;
							$dateArray[] = $lastDate;
						} endforeach;
					$length = count($dateArray);
					$num = 0;
					for ($i = 0; $i < $length; $i++) {
						$id = 0;
						?>
						<body link="blue" vlink="purple">
							<table width="800" border="0" cellpadding="0" cellspacing="0">
								<col width="54" />
								<col width="72" span="2"/>
								<col width="135"/>
								<col width="96"/>
								<col width="95"/>
								<col width="72"/>
								<tr>
									<td colspan="10" align="center"><font size='5'>班组加班申请审批表</font></td>
								</tr>
								<tr>
									<td colspan="8"><font size='3'>部门:<?php echo $deptParentName . '-' . $deptName; ?></font></td>
									<td colspan="2" align="right"><font size='3'>申请日期:&nbsp;<?php echo $dateArray[$i]; ?></font></td>
								</tr>
							</table>
							<table width="800" border="1" cellpadding="0" cellspacing="0">
								<col width="54" />
								<col width="72" span="2"/>
								<col width="180"/>
								<col width="60"/>
								<col width="60"/>
								<col width="60"/>
								<col width="60"/>
								<col width="60"/>
								<col width="60"/>
								<tr height="19">
									<td align="center" height="38" width="54" rowspan="2"><font size='3'>序号</font></td>
									<td align="center" width="72" rowspan="2"><font size='3'>姓名</font></td>
									<td align="center" width="72" rowspan="2"><font size='3'>员工编号</font></td>
									<td align="center" width="398" colspan="4"><font size='3'>申请</font></td>
									<td align="center" width="216" colspan="3"><font size='3'>实际</font></td>
								</tr>
								<tr height="19">
									<td align="center"><font size='3'>理由</font></td>
									<td align="center"><font size='1'>开始时间</font></td>
									<td align="center"><font size='1'>结束时间</font></td>
									<td align="center"><font size='1'>小时</font></td>
									<td align="center"><font size='1'>开始时间</font></td>
									<td align="center"><font size='1'>结束时间</font></td>
									<td align="center"><font size='1'>小时</font></td>
								</tr>
								<?php
								$this->loadModel('kevincom');
								foreach ($todos as $todo):if ($todo->date == $dateArray[$i]) {
										$id+=1;
										?>
										<tr height="19">
											<td align="center"><?php echo $id; ?></td>
											<td align="center"><?php echo $todo->realname; ?></td>
											<td align="center"><?php
												$code = $todo->code;
												if ('' == $code)
													echo "&nbsp";
												else
													echo $code;
												?></td>
											<td><?php
												$tempName = mb_substr($todo->name, 0, 10, 'utf-8'); //截取字符串前几位 
												echo $tempName;
												?></td>
											<td align="center">16:30</td>
											<td align="center">18:30</td>
											<td align="center">2</td>
											<td align="center"><?php
												if ('' != $todo->begin)
													echo $todo->begin;
												else
													echo "&nbsp";
												?></td>
											<td align="center"><?php
												if ('' != $todo->end)
													echo $todo->end;
												else
													echo "&nbsp";
												?></td>
											<td align="center"><?php echo $this->kevinhours->showWorkHours($todo->minutes); ?></td>
										</tr>
										<?php
									}
								endforeach;

								$num+=1;
								for ($id; $id < 16; $id++) {
									echo "<tr><td>&nbsp </td><td>&nbsp </td><td>&nbsp </td><td>&nbsp </td><td>&nbsp </td><td>&nbsp </td><td>&nbsp </td><td>&nbsp </td><td>&nbsp </td><td>&nbsp </td></tr>";
								}
								?>
								<tr height="19">
									<td align="left" height="38" colspan="10">
										本人愿服从企业安排，自愿加班。<br/>加班人员签名（必填）：</td>
								</tr>
							</table>
							<table width="800" border="0" cellpadding="0" cellspacing="0">
								<tr   height="19">
									<td height="38" colspan="10">
										<table border =0 width = 100% height = 100%><tr ><td  align="left" width = 60%  style="line-height:10px">
													<table><tr><td valign="top" >备注:</td><td><font size="1">
																1、此加班申请单须经制表人（班长）与加班人员协商确认后填写。<br>
																2、加班申请经所属科长→部长→人力资源部部长审核批准后有效。<br>
																3、实际加班小时由制表人（班长）填写、统计员核实后计算加班小时。<br>
																4、人力资源部依据考勤系统定期核查。<br>
																5、本表一式两份，部门及人力资源部各执一份。<font></td></table>
												</td><td>
													<table border =1 cellpadding="0" cellspacing="0" width = 100% height = 80%>
														<tr height = 23><td align="center" width="60">制表人</td><td align="center">科长</td><td align="center">部长</td><td align="center">HR部长</td></tr>
														<tr height = 40><td><?php echo $fillauth;?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
													</table>
												</td></tr></table>
									</td>
								</tr>
							</table>
							<?php if ($num % 2 != 0) echo "<br><br><br>"; ?>
						</body>
						<?php if ($num % 2 == 0) { ?>
							<div style="page-break-after: always;"></div>
							<?php
						}
					}
					?>
			</form>
		</div>
	</body>
</html>
