<?php
/**
 * The view file
 *
 * @copyright   Kevin
 * @charge: free
 * @license: ZPL (http://zpl.pub/v1)
 * @author      Kevin <3301647@qq.com>
 * @package     kevinsoft
 * @link        http://www.zentao.net
 */
?>
<?php include '../../kevincom/view/header.html.php';?>
<div id='titlebar'>
  <div class='heading'>
    <span class='prefix' title='SOFT'><?php echo html::icon($lang->icons['view']);?> <strong><?php echo $Soft->id;?></strong></span>
    <strong><?php echo $Soft->name;?></strong>
    <?php if($Soft->deleted):?>
    <span class='label label-danger'><?php echo "已删除";?></span>
    <?php endif; ?>
  </div>
  <div class='actions'>
    <?php
    $browseLink = $this->createLink('kevinsoft','softlist',"");
    $params     = "softID=$Soft->id";
    if(!$Soft->deleted)
    {
        ob_start();
        echo "<div class='btn-group'>";
        common::printIcon('kevinsoft', 'softedit', $params, $Soft, 'button','pencil','','',false);
        common::printIcon('kevinsoft', 'delete', $params, '', 'button', '', 'hiddenwin');
        echo '</div>';
        echo "<div class='btn-group'>";
        common::printRPN($browseLink, $preAndNext);
        echo '</div>';
        $actionLinks = ob_get_contents();
        ob_end_clean();
        echo $actionLinks;
    }
    else
    {
        common::printRPN($browseLink);
    }
    ?>
  </div>
</div>
<div class='row-table'>
  <div class='col-side'>
    <div class='main main-side'>
      <fieldset>
        <legend><?php echo $lang->kevinsoft->basicInfo;?></legend>
        <table class='table table-data table-condensed table-borderless'>
         <tr>
			 <th class='w-80px'><strong><?php echo $lang->kevinsoft->softIID;?></strong></th>
            <td><?php echo $Soft->IID;?></td>
            <th><strong><?php echo $lang->kevinsoft->softname;?></strong></th>
            <td><?php echo $Soft->name;?></td>
          </tr>
          <tr>
            <th><strong><?php echo $lang->kevinsoft->softvalid;?></strong></th>
            <td><?php echo $lang->kevinsoft->softvalidList[$Soft->valid];?></td>
            <th><strong><?php echo $lang->kevinsoft->softtype;?></strong></th>
            <td><?php echo $lang->kevinsoft->softtypeList[$Soft->type];?></td>
          </tr>
          <tr>
            <th><strong><?php echo $lang->kevinsoft->lastEditedBy;?></strong></th>
            <td><?php echo $Soft->lastEditedBy;?></td>
            <th><strong><?php echo $lang->kevinsoft->lastEditedDate;?></strong></th>
            <td><?php echo $Soft->lastEditedDate;?></td>
          </tr>
        </table>
      </fieldset>
      <?php include '../../common/view/action.html.php';?>
		<div class='actions left'><?php if(!$file->deleted) echo $actionLinks;?></div>
    </div>
	  
  </div>
</div>
<?php include '../../common/view/syntaxhighlighter.html.php';?>
<?php include '../../common/view/footer.html.php';?>