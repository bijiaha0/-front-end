<?php
/**
 * The view file of view method of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     todo
 * @version     $Id: view.html.php 4955 2013-07-02 01:47:21Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div class='container mw-700px'>
	<div id='titlebar'>
		<div class='heading'> <span class='prefix'><?php echo html::icon($lang->icons['todo']);?> <strong><?php echo $todo->id;?></strong></span>
			<span class='prefix'><?php echo html::icon($lang->icons['todo']); ?> <strong><?php echo $lang->kevinhours->statusList[$todo->status]; ?></strong></span>
		</div>
		<div class='actions'>
			<?php
			if (common::hasPriv('kevinhours', 'index')) {
				echo html::a($this->createLink('kevinhours', 'index', "type=&account={$todo->account}", "", false), $lang->kevinhours->index, "_top");
			}
			?>
		</div>
	</div>
	<div class='main' style='margin-top: 10px'>
		<div class='panel mg-0'>
			<div class='panel-heading'>
				<div class='panel-actions pull-right' title='<?php echo $lang->todo->beginAndEnd; ?>' style='line-height: 35px'>
					<?php echo $lang->kevinhours->beginAndEnd; ?>:
					<?php
					if (isset($times[$todo->begin]))
						echo $times[$todo->begin];
					if (isset($times[$todo->end]))
						echo ' ~ ' . $times[$todo->end];
					?>
				</div>
				<div class='panel-actions'><?php echo $todo->name; ?></div>
			</div>
			<div class='panel-body'>
				<div class='article-content'><?php echo $todo->desc; ?></div>
				<hr class='small'>
				<table class='table table-data table-borderless'> 
					<tr>
						<th class='w-80px'><?php echo $lang->todo->account; ?></th>
						<td><?php echo $todo->account; ?></td>
						<th class='w-80px'><?php echo $lang->kevinhours->date; ?></th>
						<td><?php echo $todo->date == '20300101' ? $lang->kevinhours->periods['future'] : date(DT_DATE1, strtotime($todo->date)); ?></td>
					</tr>
					<tr>
						<th><?php echo $lang->todo->type; ?></th>
						<td><?php echo $lang->todo->typeList[$todo->type]; ?></td>
						<th><?php echo $lang->todo->pri; ?></th>
						<td><?php echo $lang->todo->priList[$todo->pri]; ?></td>
					</tr>
					<tr>
						<th><?php echo $lang->kevinhours->hourstype; ?></th>
						<td><?php echo $lang->kevinhours->hoursTypeList[$todo->hourstype]; ?></td>
						<th><?php echo $lang->kevinhours->projectId; ?></th>
						<td><?php echo $todo->project; ?></td>
					</tr>
					<tr>
						<th><?php echo $lang->kevinhours->projectName; ?></th>
						<td><?php echo $this->loadModel('kevincom')->getProjectNameByProject($todo->project); ?></td>
						<th><?php echo $lang->kevinhours->hours; ?></th>
						<td><?php echo $this->kevinhours->showHours($todo->minutes) . ' ' . $lang->kevinhours->hourunits; ?></td>
					</tr>
				</table>
			</div>
			<div class='panel-footer text-center'>
				<?php
				if ($this->session->todoList) {
					$browseLink = $this->session->todoList;
				} elseif ($todo->account == $app->user->account) {
					$browseLink = $this->createLink('my', 'todo');
				} else {
					$browseLink = $this->createLink('user', 'todo', "account=$todo->account");
				}

				if ($todo->account == $app->user->account) {
					common::printIcon('todo', 'finish', "id=$todo->id", $todo, 'button', '', 'hiddenwin', 'showinonlybody btn-success');
					common::printIcon('todo', 'edit', "todoID=$todo->id");
					common::printIcon('todo', 'delete', "todoID=$todo->id", '', 'button', '', 'hiddenwin');
				}
				common::printRPN($browseLink);
				?>
			</div>
		</div>
	</div>
	<div class='main'>
		<?php
		$actionTheme = 'table';
		include '../../common/view/action.html.php';
	?>
	</div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
