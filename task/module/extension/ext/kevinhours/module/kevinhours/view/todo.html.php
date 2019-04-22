<?php
/**
 * The todo view file of dashboard module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dashboard
 * @version     $Id: todo.html.php 4735 2013-05-03 08:30:02Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php js::set('confirmDelete', $lang->kevinhours->confirmDelete) ?>
<div id='featurebar'>
  <ul class='nav'>
	<li><i class="icon-user"><?php echo $this->kevinhours->employeesAll[$this->kevinhours->account]->realname; ?> </i><i class="icon-angle-right"></i></li>
	<?php include './commontitlebar.html.php'; ?>
  </ul>  
  <div class='actions'>
	  <?php
	  if (common::hasPriv('kevinhours', 'batchCreate')) {
		  echo html::a(helper::createLink('kevinhours', 'batchCreate', "date=" . str_replace('-', '', $date))
			  , "<i class='icon-plus-sign'></i> " . $lang->kevinhours->batchCreate, '', "class='btn'");
	  }

	  if (0 && common::hasPriv('kevinhours', 'create')) {
		  echo html::a(helper::createLink('kevinhours', 'create', "date=" . str_replace('-', '', $date))
			  , "<i class='icon-plus'></i> " . $lang->kevinhours->create, '', "class='btn'");
	  }
	  ?>
  </div>
</div>
<?php echo $overtreetitle;?>
<div class="main" style="margin-left:<?php if(common::hasPriv('kevinhours', 'browseDeptHours'))echo $config->kevinhours->sideWidth+20;?>px;">
<table class='table table-condensed table-hover table-striped tablesorter table-fixed' id='todoList'>
	<?php $vars = "type=$type&account=$account&deptID=&status=$status&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID"; ?>
  <thead>
	<tr class='text-center'>
	  <th class='w-id'>    <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB); ?></th>
	  <th class='w-date'>  <?php common::printOrderLink('date', $orderBy, $vars, $lang->kevinhours->date); ?></th>
	  <th>                 <?php common::printOrderLink('name', $orderBy, $vars, $lang->kevinhours->name); ?></th>
	  <th class='w-100px'> <?php common::printOrderLink('hourstype', $orderBy, $vars, $lang->kevinhours->hourstype); ?></th>
	  <th class='w-150px'> <?php common::printOrderLink('project', $orderBy, $vars, $lang->kevinhours->projectId); ?></th>
	  <th class='w-80px'> <?php common::printOrderLink('type', $orderBy, $vars, $lang->todo->type); ?></th>
	  <th class='w-80px'> <?php common::printOrderLink('minutes', $orderBy, $vars, $lang->kevinhours->workhours); ?></th>
	  <th class='w-hour'>  <?php common::printOrderLink('begin', $orderBy, $vars, $lang->kevinhours->beginAB); ?></th>
	  <th class='w-hour'>  <?php common::printOrderLink('end', $orderBy, $vars, $lang->kevinhours->endAB); ?></th>
	  <th class='w-status'><?php common::printOrderLink('status', $orderBy, $vars, $lang->kevinhours->status); ?></th>
	  <th class='w-80px {sorter:false}'><?php echo $lang->actions; ?></th>
	</tr>
  </thead>
  <tbody>
	  <?php foreach ($todos as $todo): ?>
		<tr class='text-center'>
		  <td class='text-left'>
			  <?php if (common::hasPriv('todo', 'batchEdit') or ( common::hasPriv('todo', 'import2Today') and $importFuture)): ?>
				<input type='checkbox' name='todoIDList[<?php echo $todo->id; ?>]' value='<?php echo $todo->id; ?>' />
			<?php endif; ?>
			<?php echo $todo->id; ?>
		  </td>
		  <td><?php echo $todo->date == '2030-01-01' ? $lang->todo->periods['future'] : $todo->date; ?></td>
		  <td class='text-left'><?php echo html::a($this->createLink('kevinhours', 'view', "id=$todo->id&from=my", '', true), $todo->name, '', "data-toggle='modal' data-type='iframe' data-title='" . $lang->kevinhours->view . "' data-icon='check'"); ?></td>
		  <td style="background-color:<?php echo $lang->kevinhours->hoursTypeColor[$todo->hourstype]; ?>"><?php echo $lang->kevinhours->hoursTypeList[$todo->hourstype]; ?></td>
		  <td><?php echo $todo->project . '/' . $todo->projectname; ?></td>
		  <td><?php echo $lang->todo->typeList[$todo->type]; ?></td>
		  <td><?php echo $this->kevinhours->showWorkHours($todo->minutes); ?></td>
		  <td><?php echo $todo->begin; ?></td>
		  <td><?php echo $todo->end; ?></td>
		  <td class='<?php echo $todo->status; ?>'><?php echo $lang->kevinhours->statusList[$todo->status]; ?></td>
		  <td class='text-right'>
			  <?php
			  if ('done' == $todo->status)
				  echo "<button type='button' class='disabled btn-icon'><i class='icon-todo-finish disabled icon-ok-sign' title=''></i></button>";
			  else
				  common::printIcon('kevinhours', 'finish', "id=$todo->id", $todo, 'list', 'ok-sign', 'hiddenwin');

			  common::printIcon('kevinhours', 'edit', "id=$todo->id", '', 'list', 'pencil', '', 'iframe', true);

			  if (common::hasPriv('todo', 'delete')) {
				  $deleteURL = $this->createLink('todo', 'delete', "todoID=$todo->id&confirm=yes");
				  echo html::a("javascript:ajaxDelete(\"$deleteURL\",\"todoList\",confirmDelete)", '<i class="icon-remove"></i>', '', "class='btn-icon' title='{$lang->kevinhours->delete}'");
			  }
			  ?>
		  </td>
		</tr>
	<?php endforeach; ?>
  </tbody>
  <?php if (count($todos)): ?>
	  <?php
	  $column = 12;
	  if ($config->kevinhours->ListSimpleModel)
		  $column -= 2;
	  ?>
	  <tfoot>
		<tr>
		  <td colspan=<?php echo $column; ?> align='left'>
			  <?php $pager->show(); ?>
			<div class='table-actions clearfix'>
			  <?php
			  if (common::hasPriv('kevinhours', 'batchedit') or ( common::hasPriv('kevinhours', 'import2Today') and $importFuture)) {
				  echo "<div class='btn-group'>" . html::selectButton() . '</div>';
			  }
			  echo "<div class='btn-group'>";
			  if (common::hasPriv('kevinhours', 'batchedit')) {
				  $actionLink = $this->createLink('kevinhours', 'batchEdit', "from=myTodo&type=$type&account=$account&status=$status");
				  echo html::commonButton($lang->edit, "onclick=\"setFormAction('$actionLink')\"");
			  }
			  if (common::hasPriv('kevinhours', 'batchfinish')) {
				  $actionLink = $this->createLink('todo', 'batchfinish');
				  echo html::commonButton($lang->kevinhours->finish, "onclick=\"setFormAction('$actionLink','hiddenwin')\"");
			  }
			  ?>
			</div>
		  </td>
		</tr>
	  </tfoot>
  <?php endif; ?>
</table>
<?php include 'todolockwarning.html.php';?>
</div>
<?php
js::set('listName', 'todoList');
include '../../common/view/footer.html.php';
?>
<script language='Javascript'>
	var currentAccount = '<?php echo $account; ?>';
	var currentdept = '<?php echo $this->kevinhours->accountdept; ?>';
	var nextMonth = '<?php echo $nextMonth; ?>';
	var lastMonth = '<?php echo $lastMonth; ?>';
	var thisMonth = '<?php echo $thisMonth; ?>';
	var methodName = 'todo';
</script>
