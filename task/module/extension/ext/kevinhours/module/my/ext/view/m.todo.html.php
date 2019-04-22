<?php
/**
 * The todo view file of dashboard module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     my
 * @version     $Id: todo.html.php 4735 2013-05-03 08:30:02Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/m.header.html.php';?>
  <div data-role='navbar' id='subMenu'>
    <ul>
      <?php foreach($config->mobile->todoBar as $period):?>
      <?php $active = $type == $period ? 'ui-btn-active' : ''?>
      <li><?php echo html::a($this->createLink('my', 'todo', "type=$period"), $lang->todo->periods[$period], '', "class='$active' data-theme='d'")?></li>
      <?php endforeach;?>
    </ul>
  </div>
</div>
<?php $this->session->set('todoType', $type);?>
<?php foreach($todos as $todo):?>
<?php if(!$todo->private or ($todo->private and $todo->account == $app->user->account)):?>
<div data-role="collapsible-set">
  <div data-role="collapsible" data-collapsed="true" class='collapsible'>
    <h1 onClick="showDetail('kevinhours', <?php echo $todo->id;?>)">
		<?php 
			//$year=((int)substr($todo->date,0,4));//取得年份
			//$month=((int)substr($todo->date,5,2));//取得月份
			$day=((int)substr($todo->date,8,2));//取得几号
			echo $day.'日';
			$this->loadModel('kevinhours');
			if('nor' != $todo->hourstype) echo ",".$lang->kevinhours->hoursTypeList[$todo->hourstype];
			if('done' != $todo->status) echo ",".$lang->kevinhours->statusList[$todo->status];
			echo ",".mb_substr($todo->name, 0, 8, 'utf-8');//截取字符串前8位
			if(strlen($todo->name) > 8) echo '...';
		?>
	</h1> 
    <div><?php echo $todo->desc;?></div>
    <div id='item<?php echo $todo->id;?>'><?php echo $todo->desc;?></div>
	<?php if($todo->account == $app->user->account && $this->loadModel('kevinhours')->timeVerificated($todo->date)):?>
    <div data-role='content' class='text-center'>
      <?php
		if(common::hasPriv('kevinhours', 'finish'))
		{
			if('done' != $todo->status)
			{
				common::printIcon('kevinhours', 'finish', "id=$todo->id", $todo, 'button', '', 'hiddenwin');
			}
		}
		if(($type != 'today') && common::hasPriv('todo', 'import2Today'))
		{
			common::printIcon('todo', 'import2Today',   "todoID=$todo->id");
		}
		if(common::hasPriv('todo', 'delete'))
		{
			common::printIcon('todo', 'delete', "todoID=$todo->id", '', 'button', '', 'hiddenwin');
		}
		if(common::hasPriv('kevinhours', 'edit'))
		{
			echo html::a($this->createLink('kevinhours', 'edit', "todoID=$todo->id"), $lang->todo->edit, '', "data-icon='plus' data-theme='e'");
		}
      ?>
    </div>
	<?php endif?>
    <?php if(end($todos) == $todo) echo "<hr />";?>
  </div>
</div>
<?php endif;?>
<?php endforeach;?>
<?php echo $pager->show('left', 'mobile')?>
<div data-role='footer' data-position='fixed'>
  <div data-role='navbar' data-iconpos="left">
    <ul>
      <li><?php echo html::a($this->createLink('kevinhours', 'create'), $lang->todo->create, '', "data-icon='plus' data-theme='e'");?></li>
    </ul>
  </div>
</div>
<?php include '../../../common/view/m.footer.html.php';?>