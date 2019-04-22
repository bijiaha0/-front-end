<?php
/**
 * The browse view file of product dept of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: browse.html.php 5096 2013-07-11 07:02:43Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
include '../../common/view/header.html.php';
js::set('deptID', $deptID);
js::set('confirmDelete', $lang->user->confirmDelete);
$lockedSeconds	 = $this->config->user->lockMinutes * 60;
$nowTime		 = time();
$longLockTime	 = '2030-01-01 00:00:00';

$this->app->loadLang("kevinlogin");
?>
<div id='querybox' class='show'><?php echo $searchForm ?></div>
<div class='side'>
  <a class='side-handle' data-id='companyTree'><i class='icon-caret-left'></i></a>
  <div class='side-body'>
	<div class='panel panel-sm'>
	  <div class='panel-heading nobr'><?php echo html::icon($lang->icons['company']); ?> <strong><?php echo $lang->dept->common; ?></strong></div>
	  <div class='panel-body'>
		  <?php echo $deptTree; ?>
		<div class='text-right'><?php common::printLink('dept', 'browse', '', $lang->dept->manage); ?></div>
	  </div>
	</div>
  </div>
</div>
<div class='main'>
  <form action='<?php echo $this->createLink('user', 'batchEdit', "deptID=$deptID") ?>' method='post' id='userListForm'>
	<table class='table table-condensed table-hover table-striped tablesorter table-fixed' id='userList'>
	  <thead>
		<tr class='colhead'>
			<?php $vars				 = "param=$param&type=$type&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; ?>
		  <th class='w-id'><?php common::printorderlink('id', $orderBy, $vars, $lang->idAB); ?></th>
		  <th><?php common::printorderlink('realname', $orderBy, $vars, $lang->user->realname); ?></th>
		  <th><?php echo $lang->kevinhours->calendar; ?></th>	
		  <th><?php common::printOrderLink('account', $orderBy, $vars, $lang->user->account); ?></th>
		  <th><?php common::printOrderLink('code', $orderBy, $vars, $lang->user->code); ?></th>
		  <th><?php common::printOrderLink('dept', $orderBy, $vars, $lang->kevinhours->dept); ?></th>
		  <th><?php common::printOrderLink('deptdispatch', $orderBy, $vars, $lang->user->deptdispatch); ?></th>
		  <th><?php common::printOrderLink('email', $orderBy, $vars, $lang->user->email); ?></th>
		  <th><?php common::printOrderLink('gender', $orderBy, $vars, $lang->user->gender); ?></th>
		  <th><?php common::printOrderLink('last', $orderBy, $vars, $lang->user->last); ?></th>
		  <th><?php common::printOrderLink('visits', $orderBy, $vars, $lang->user->visits); ?></th>
		  <th class='w-80px'><?php echo $lang->actions; ?></th>
		</tr>
	  </thead>
	  <tbody>

		<?php
		$canBatchEdit		 = common::hasPriv('user', 'batchEdit');
		$canManageContacts	 = common::hasPriv('user', 'manageContacts');
		?>
		<?php foreach ($users as $user):
			$isLock = (strtotime(date('Y-m-d H:i:s')) - strtotime($user->locked)) < $this->config->user->lockMinutes * 60;
			if ($isLock)  $lockbackground = "style='background:yellow'";
			else $lockbackground= "";		?>
			<tr class='text-center'>
			  <td <?php echo $lockbackground;?> >
				  <?php
				  if ($canBatchEdit or $canManageContacts)
					  echo "<input type='checkbox' name='users[]' value='$user->account'> ";
				  printf('%03d', $user->id);
				  ?>
			  </td>
			  <td <?php echo $lockbackground;?>><?php if (!common::printLink('user', 'profile', "account=$user->account", $user->realname)) echo $user->realname; ?></td>
			  <td><?php
				  $hoursURL	 = $this->createLink('kevinhours', 'index', "typt=thisMonth&account=" . $user->account);
				  echo html::a($hoursURL, "工时日历");
				  ?></td>	
			  <td><?php echo $user->account; ?></td>
			  <td><?php if ($user->code) echo $user->code; ?></td>
			  <td><?php
				  if (isset($depts[$user->dept]))
					  echo $depts[$user->dept];
				  else
					  echo '&nbsp;';
				  ?></td>
			  <td><?php
				  if (isset($depts[$user->deptdispatch]))
					  echo $depts[$user->deptdispatch];
				  else
					  echo '&nbsp;';
				  ?></td>	
			  <td><?php echo html::mailto($user->email); ?></td>
			  <td><?php if (isset($lang->user->genderList[$user->gender])) echo $lang->user->genderList[$user->gender]; ?></td>
			  <td><?php if ($user->last) echo date('Y-m-d', $user->last); ?></td>
			  <td><?php echo $user->visits; ?></td>
			  <td class='text-left' <?php echo $lockbackground;?>>
				  <?php
				  common::printIcon('user', 'edit', "userID=$user->id&from=company", '', 'list');
				  $isNotAdmin	 = strpos($this->app->company->admins, ",{$user->account},") === false;

				  if ($isNotAdmin) {
					  $titlelockedNone = $lang->kevinlogin->lockedNone . $lang->kevinlogin->userLock . "?";
					  $titlelockedTemp = $lang->kevinlogin->lockedTemp . $lang->kevinlogin->unlock . "?";
					  $titlelockedLong = $lang->kevinlogin->lockedLong . $lang->kevinlogin->unlock . "?";
					  if ((int) $user->locked == 0) {
						  echo html::a($this->createLink('kevinlogin', 'userLock', "userID=$user->account"), "<i class='icon-unlock-alt text-muted'></i>", 'hiddenwin', "class='btn-icon' title={$titlelockedNone}", false);
					  } else if ($user->locked < $longLockTime) {
						  echo html::a($this->createLink('kevinlogin', 'unlock', "userID=$user->account"), "<i class='icon-lock'></i>", 'hiddenwin', "class='btn-icon' title={$titlelockedTemp}", false);
					  } else {
						  echo html::a($this->createLink('kevinlogin', 'unlock', "userID=$user->account"), "<i class='icon-lock text-muted'></i>", 'hiddenwin', "class='btn-icon' title={$titlelockedLong}", false);
					  }
				  }
				  ?>
			  </td>
			</tr>
		<?php endforeach; ?>
	  </tbody>
	  <tfoot>
		<tr>
		  <td colspan='12'>
			<div class='table-actions clearfix'>
			  <?php
			  if ($canBatchEdit or $canManageContacts)
				  echo "<div class='btn-group'>" . html::selectButton() . '</div>';
			  if ($canBatchEdit)
				  echo html::submitButton($lang->edit, 'onclick=batchEdit()', 'btn-default');
			  if ($canManageContacts)
				  echo html::submitButton($lang->user->contacts->manage, 'onclick=manageContacts()');
			  ?>
			</div>
			<?php echo $pager->show(); ?>
		  </td>
		</tr>
	  </tfoot>
	</table>
  </form>
</div>
<script lanugage='javascript'>
	$('#dept<?php echo $deptID; ?>').addClass('active');
	function batchEdit()
	{
		$('#userListForm').attr('action', createLink('kevinhours', 'userbatchedit', 'dept=' + deptID));
	}
	function manageContacts()
	{
		$('#userListForm').attr('action', createLink('kevinhours', 'manageContacts'));
	}
</script>
<?php include '../../common/view/footer.html.php'; ?>
