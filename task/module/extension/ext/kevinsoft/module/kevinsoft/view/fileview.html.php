<?php
/**
 * @package     kevinsoft
 */
?>
<?php include '../../kevincom/view/header.html.php';?>

<?php echo css::internal($keTableCSS);?>
<div id='titlebar'>
  <div class='heading'>
    <span class='prefix' title='view'><?php echo html::icon($lang->icons['view']);?> <strong><?php echo $file->id;?></strong></span>
    <strong><?php echo $file->title;?></strong>
    <?php if($file->deleted):?>
    <span class='label label-danger'><?php echo "已删除";?></span>
    <?php endif; ?>
  </div>
  <div class='actions'>
    <?php
    $browseLink = $this->createLink('kevinsoft','groupversionlist',"");
    $params     = "fileID=$file->id";
    if(!$file->deleted)
    {
        ob_start();
        echo "<div class='btn-group'>";
        common::printIcon('kevinsoft', 'fileedit', $params, $file, 'list','pencil','','',false);
        common::printIcon('kevinsoft', 'filedelete', $params, '', 'list', 'remove', 'hiddenwin');
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
  <div class='col-main'>
    <div class='main'>
      <div class='file-content'>
		<?php echo $this->fetch('file', 'printFiles', array('files' => $file->files, 'fieldset' => 'true'));?>
      </div>
      <div class='actions'><?php if(!$file->deleted) echo $actionLinks;?></div>
    </div>
  </div>
  <div class='col-side'>
    <div class='main main-side'>
      <fieldset>
        <legend><?php echo $lang->kevinsoft->basicInfo;?></legend>
        <table class='table table-data table-condensed table-borderless'>
         <tr>
            <th><?php echo $lang->kevinsoft->filesoft;?></th>
            <td><?php echo $file->soft;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinsoft->fileversion;?></th>
            <td><?php echo $file->version;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinsoft->filepathname;?></th>
            <td><?php echo $file->pathname;?></td>
          </tr>
		   <tr>
            <th><?php echo $lang->kevinsoft->filesoft;?></th>
            <td><?php echo $file->soft;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinsoft->fileversion;?></th>
            <td><?php echo $file->version;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinsoft->filepathname;?></th>
            <td><?php echo $file->pathname;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinsoft->addedBy;?></th>
            <td><?php echo $users[$file->addedBy];?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinsoft->addedDate;?></th>
            <td><?php echo $file->addedDate;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinsoft->lastEditedBy;?></th>
            <td><?php echo $file->editedBy;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinsoft->lastEditedDate;?></th>
            <td><?php echo $file->LastEditedDate;?></td>
          </tr>
        </table>
      </fieldset>
      <?php include '../../common/view/action.html.php';?>
    </div>
  </div>
</div>
<?php include '../../common/view/syntaxhighlighter.html.php';?>
<?php include '../../common/view/footer.html.php';?>