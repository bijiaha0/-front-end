<?php
/**
 * The batch create view of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     todo
 * @version     $Id: create.html.php 2741 2012-04-07 07:24:21Z areyou123456 $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/m.header.html.php';?>
</div>
<form class='form-condensed' method='post' target='hiddenwin' id='dataform'>
    <table class='table table-form'>
      <tr>
        <th class='w-80px'><?php echo $lang->todo->date;?></th>
        <td class='w-p25-f'>
          <div class='input-group'>
            <?php
				echo html::select('date', $datesArray, $todo->date, '');
				echo html::hidden('type', 'custom');
			?>
          </div>
        </td><td></td>
      </tr>
		<tr>
        <th><?php echo $lang->kevinhours->hourstype;?></th>
		<td><?php echo html::select('hourstype', $lang->kevinhours->hoursTypeList, $todo->hourstype, '');?></td>
      </tr>
	  <tr>
        <th><?php echo $lang->kevinhours->projectName;?></th>
		<td><?php echo html::select('project', $projectsArray, $todo->project, '');?></td>
	  </tr>
      <tr>
        <th><?php echo $lang->todo->name;?></th>
        <td colspan='2'>
          <?php
          echo html::input('name', $todo->name);
          ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $lang->todo->beginAndEnd;?></th>
        <td>
            <?php echo html::select('begin', $times, $todo->begin, '');?>
        </td>
		<td>
            <?php echo html::select('end', $times, $todo->end, '');?>
        </td>
      </tr>  
	  <tr>
        <th><?php echo $lang->todo->status;?></th>
        <td><?php echo html::select('status', $lang->todo->statusList, $todo->status, '');?></td>
      </tr>
      <tr>
        <td></td>
        <td>
          <?php echo html::submitButton();?>
        </td>
		<td>
          <?php echo html::backButton();?>
        </td>
      </tr>
    </table>
  </form>
<?php include '../../common/view/m.footer.html.php';?>
