<?php

$lang->kevinhours->common				 = 'Kevin工时';
//添加代码
$lang->kevinhours->hoursTypeColor['nor'] = '#cfcfcf';
$lang->kevinhours->hoursTypeColor['hol'] = '#00ffff';
$lang->kevinhours->hoursTypeColor['ann'] = '#00ffff';
$lang->kevinhours->hoursTypeColor['rep'] = '#00ffff';
$lang->kevinhours->hoursTypeColor['ove'] = 'yellow';

$lang->kevinhours->hoursTypeChar['nor']	 = 'L';
$lang->kevinhours->hoursTypeChar['hol']	 = 'D';
$lang->kevinhours->hoursTypeChar['ann']	 = 'N';
$lang->kevinhours->hoursTypeChar['rep']	 = 'R';
$lang->kevinhours->hoursTypeChar['ove']	 = 'J';

$lang->kevinhours->hoursTypeList['nor']	 = '正常';
$lang->kevinhours->hoursTypeList['ove']	 = '加班';
$lang->kevinhours->hoursTypeList['hol']	 = '请假';
$lang->kevinhours->hoursTypeList['ann']	 = '年假';
$lang->kevinhours->hoursTypeList['rep']	 = '调休';

$lang->kevinhours->hoursTypeNor	 = 'nor';
$lang->kevinhours->hoursTypeOve	 = 'ove';
$lang->kevinhours->hoursTypeHol	 = 'hol';
$lang->kevinhours->hoursTypeAnn	 = 'ann';
$lang->kevinhours->hoursTypeRep	 = 'rep';
$lang->kevinhours->hoursTypeLaw	 = 'law';

$lang->kevinhours->exportHoursTypeList['all']	 = '全部';
$lang->kevinhours->exportHoursTypeList['nor']	 = '正常';
$lang->kevinhours->exportHoursTypeList['ove']	 = '加班';
$lang->kevinhours->exportHoursTypeList['hol']	 = '请假';
$lang->kevinhours->exportHoursTypeList['ann']	 = '年假';
$lang->kevinhours->exportHoursTypeList['rep']	 = '调休';

$lang->kevinhours->employeeTypeList['all']		 = '全部';
$lang->kevinhours->employeeTypeList['formal']	 = '正式';
$lang->kevinhours->employeeTypeList['informal']	 = '外援';

$lang->kevinhours->statusList['wait']	 = '未开始';
$lang->kevinhours->statusList['doing']	 = '进行中';
$lang->kevinhours->statusList['done']	 = '已完成';

$lang->kevinhours->deptCountList['']	 = '';
$lang->kevinhours->deptCountList['0']	 = '当前部门';
$lang->kevinhours->deptCountList['1']	 = '含有一级子部门';
$lang->kevinhours->deptCountList['2']	 = '含有二级子部门';
$lang->kevinhours->deptCountList['3']	 = '含有三级子部门';
$lang->kevinhours->deptCountList['4']	 = '含有四级子部门';
$lang->kevinhours->deptCountList['5']	 = '含有五级子部门';
$lang->kevinhours->deptCountList['x']	 = '含有全部子部门';

//等待翻译：
//error for hours work
$lang->error->ErrorKevinTimeOverly	 = '时间和已有的工时重叠，请修改！';
$lang->error->ErrorKevinMustBeSelf	 = '必须是本人才可以操作';
$lang->error->ErrorTodoHoursSameTime = "起止时间不能相同";
$lang->error->ErrorKevinTimeOut		 = "已超出添加时间,如有问题,请联系考勤管理员!";
$lang->error->ErrorKevinAfterToday	 = "在今天之后不能设为完成！";

//error for clock
$lang->error->KevinClockactPriv		 = "没有打卡权限";
$lang->error->KevinRecordExist		 = "记录已经存在";
$lang->error->KevinClockInlaterOut	 = "警告：上班时间要早于下班时间";
$lang->error->KevinClockInOnly1		 = "上班打卡只能打一次！";
$lang->error->KevinClockInFirst		 = "必须先上班打卡！";
$lang->error->KevinClockInMinutes	 = "1分钟内打过卡了！";
$lang->error->KevinClockInFirst		 = "必须先上班打卡！";
$lang->error->KevinClockActionType	 = "action should be in or out.";

//不规范的提示：
$lang->kevinhours->accessDenied			 = "没有权限";
$lang->kevinhours->ErrorCheckAll		 = "没有查看其他人待办的权限！";
$lang->kevinhours->KevinDeptinfoNotExist = "部门信息不存在！";

//菜单等
$lang->kevinhours->my			 = '个人';
$lang->kevinhours->todo			 = '待办列表';
$lang->kevinhours->calendar		 = '日历';
$lang->kevinhours->batchEdit	 = '批量编辑';
$lang->kevinhours->batchCreate	 = '批量添加';
$lang->kevinhours->project		 = '项目';
$lang->kevinhours->product		 = '产品';
$lang->kevinhours->users		 = '用户';
$lang->kevinhours->clock		 = '打卡主页';
$lang->kevinhours->clockact		 = '打卡操作';

$lang->kevinhours->recently = '最近:';
$lang->kevinhours->suggest	= '请使用火狐浏览器来运行本插件.  Please Use Firefox to browse this plugin';

//个人项目工时周期
$lang->kevinhours->periods['thisSeason'] = '本季';
$lang->kevinhours->periods['thisYear']	 = '今年';
$lang->kevinhours->periods['lastYear']	 = '去年';
$lang->kevinhours->periods['all']		 = '所有';
$lang->kevinhours->endList[7]			 = '一星期';
$lang->kevinhours->endList[14]			 = '两星期';
$lang->kevinhours->endList[31]			 = '一个月';
$lang->kevinhours->endList[62]			 = '两个月';
$lang->kevinhours->endList[93]			 = '三个月';
$lang->kevinhours->endList[186]			 = '半年';
$lang->kevinhours->endList[365]			 = '一年';


//函数入口
$lang->kevinhours->index			 = '日历';
$lang->kevinhours->service			 = '服务工时单';
$lang->kevinhours->updateCashStat	 = '更新付费统计';
$lang->kevinhours->printservicelist	 = '打印工时单';
$lang->kevinhours->count			 = '统计';
$lang->kevinhours->printover		 = "打印加班";
$lang->kevinhours->dispatched		 = "外协打印";
$lang->kevinhours->printovertable	 = "正式加班记录";
$lang->kevinhours->searchproject	 = "搜索项目代号";
$lang->kevinhours->project			 = '项目工时';
$lang->kevinhours->product			 = '产品工时';
$lang->kevinhours->create			 = '创建';
$lang->kevinhours->update			 = '更新';
$lang->kevinhours->batchcreate		 = "批量添加";
$lang->kevinhours->edit				 = '编辑';
$lang->kevinhours->batchedit		 = "批量编辑";
$lang->kevinhours->finish			 = '完成';
$lang->kevinhours->batchfinish		 = '批量完成';
$lang->kevinhours->delete			 = '删除';
$lang->kevinhours->view				 = "详细";
$lang->kevinhours->modifyOtherHours	 = "修改过期考勤";
$lang->kevinhours->ratepay			 = "加班费计算";
$lang->kevinhours->browse			 = "用户";
$lang->kevinhours->userbatchedit	 = "批量编辑用户";
$lang->kevinhours->manageContacts	 = "维护联系人";
$lang->kevinhours->over				 = "个人加班统计";
$lang->kevinhours->overdept			 = "科室加班统计";
$lang->kevinhours->parentdept		 = '返回上级科室';
$lang->kevinhours->deptchild		 = '子科室';
$lang->kevinhours->fillauthlist['1'] = '填写制表人';

$lang->kevinhours->clockin	 = '上班打卡';
$lang->kevinhours->clockout	 = '下班打卡';
$lang->kevinhours->in		 = '上班';
$lang->kevinhours->out		 = '下班';

//权限翻译
$lang->kevinhours->checkAll			 = "查看所有考勤";
$lang->kevinhours->browseDeptHours	 = '查看科室考勤';



//界面翻译
$lang->kevinhours->certainYear	 = "年份";
$lang->kevinhours->certainMonth	 = "月份";
$lang->kevinhours->MonthZh		 = "月";
$lang->kevinhours->certainSeason = "季度";

$lang->kevinhours->month['']	 = '';
$lang->kevinhours->month['01']	 = '1月';
$lang->kevinhours->month['02']	 = '2月';
$lang->kevinhours->month['03']	 = '3月';
$lang->kevinhours->month['04']	 = '4月';
$lang->kevinhours->month['05']	 = '5月';
$lang->kevinhours->month['06']	 = '6月';
$lang->kevinhours->month['07']	 = '7月';
$lang->kevinhours->month['08']	 = '8月';
$lang->kevinhours->month['09']	 = '9月';
$lang->kevinhours->month['10']	 = '10月';
$lang->kevinhours->month['11']	 = '11月';
$lang->kevinhours->month['12']	 = '12月';

$lang->kevinhours->season['']	 = '';
$lang->kevinhours->season['1']	 = '第一季度';
$lang->kevinhours->season['2']	 = '第二季度';
$lang->kevinhours->season['3']	 = '第三季度';
$lang->kevinhours->season['4']	 = '第四季度';

$lang->kevinhours->serviceList	 = '服务工时单';
$lang->kevinhours->clientName	 = '客户名称';
$lang->kevinhours->projectName	 = '项目名称';
$lang->kevinhours->cashCodeName	 = '项目付费号';
$lang->kevinhours->partment		 = '部门';
$lang->kevinhours->accountName	 = '姓名';
$lang->kevinhours->actualHours	 = '实际';
$lang->kevinhours->amountToHours = '折算';
$lang->kevinhours->summation	 = '合计';
$lang->kevinhours->totalHours	 = '总工时';

$lang->kevinhours->countPeriods['today']		 = '今日';
$lang->kevinhours->countPeriods['yesterday']	 = '昨日';
$lang->kevinhours->countPeriods['thisWeek']		 = '本周';
$lang->kevinhours->countPeriods['lastWeek']		 = '上周';
$lang->kevinhours->countPeriods['thisMonth']	 = '本月';
$lang->kevinhours->countPeriods['lastmonth']	 = '上月';
$lang->kevinhours->countPeriods['thisSeason']	 = '本季';
$lang->kevinhours->countPeriods['thisYear']		 = '本年';
$lang->kevinhours->countPeriods['all']			 = '所有';
$lang->kevinhours->countPeriods['unknown']		 = '待定';

//考勤统计翻译
$lang->kevinhours->hours		 = '工作时间';
$lang->kevinhours->minutes		 = '工作时间';
$lang->kevinhours->minuteunits	 = '分钟';
$lang->kevinhours->timeunits	 = '时间(小时)';
$lang->kevinhours->hourunits	 = '小时';

$lang->kevinhours->dept			 = '科室';
$lang->kevinhours->classdept	 = '分类父科室';
$lang->kevinhours->classType	 = '分类';
$lang->kevinhours->code			 = '员工编号';
$lang->kevinhours->realname		 = '姓名';
$lang->kevinhours->username		 = '姓名';
$lang->kevinhours->employeeCode	 = '工号';
$lang->kevinhours->hourstype	 = '工时类型';
$lang->kevinhours->containchild  = '子部门';

$lang->kevinhours->projectId		 = '项目ID';
$lang->kevinhours->projectCode		 = '项目代号';
$lang->kevinhours->noprojectname	 = '项目代号未填写';
$lang->kevinhours->workcontent		 = '工作内容';
$lang->kevinhours->cashCode			 = '付费号';
$lang->kevinhours->workcontenttitle	 = '工作内容(细分到具体项目)';
$lang->kevinhours->comment			 = '修改备注';
$lang->kevinhours->isIncludeAnn[1]	 = '包含年假';
$lang->kevinhours->isShowDetail		 = '显示详细';
$lang->kevinhours->deptAccount		 = '科室成员';
$lang->kevinhours->finishThisMonth	 = '完成本月';
$lang->kevinhours->account			 = '成员';
$lang->kevinhours->nothing			 = '（无）';
$lang->kevinhours->indeptmember		 = '内部人员';
$lang->kevinhours->externalmember	 = '外部人员';
$lang->kevinhours->inactive			 = '未激活';

//todo列表
$lang->kevinhours->date				 = '日期';
$lang->kevinhours->begin			 = '开始时间';
$lang->kevinhours->beginAB			 = '开始';
$lang->kevinhours->end				 = '结束时间';
$lang->kevinhours->endAB			 = '结束';
$lang->kevinhours->beginAndEnd		 = '起止时间';
$lang->kevinhours->type				 = '类型';
$lang->kevinhours->pri				 = '优先级';
$lang->kevinhours->name				 = '名称';
$lang->kevinhours->status			 = '状态';
$lang->kevinhours->desc				 = '描述';
$lang->kevinhours->workhours		 = '工时';
$lang->kevinhours->lblDisableDate	 = '暂时不设定时间';

//手机显示
$lang->kevinhours->mobileContent	 = '内容';
$lang->kevinhours->mobileBeginAndEnd = '起止';
$lang->kevinhours->mobileTime		 = '时间';
$lang->kevinhours->mobileDate		 = '日期';
$lang->kevinhours->domainFullAccount = '域用戶';
$lang->kevinhours->mobileProject	 = '项目';
$lang->kevinhours->mobilehourstype	 = '类型';

//添加翻译
$lang->kevinhours->warning				 = "<li>%lastMonth%月考勤锁定时间 %lastMonthLock% %time%。</li><li>%thisMonth%月考勤锁定时间 %thisMonthLock% %time%。</li>";
$lang->kevinhours->beyondNotes			 = "<li>同时超过%4天,不能修改考勤。</li>";
$lang->kevinhours->tips					 = '<li>部分功能与IE不兼容,建议使用火狐浏览器。<a href="/zentao/doc-view-1.html?onlybody=yes"'
		. ' target="" data-toggle="modal" data-type="iframe" data-title="下载" data-icon="check">下载</a></li>';
$lang->kevinhours->confirmDelete		 = "您确定要删除这条待办吗？";
$lang->kevinhours->finishThisMonthTip	 = "本月考勤全部设为完成状态";
$lang->kevinhours->warningClockUpdate	 = '已经有了打卡记录，是否要更新时间到';
