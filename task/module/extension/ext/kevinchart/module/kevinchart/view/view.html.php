<?php
/**
 * The view file of kevinchart module
 *
 * @copyright   Kevin
 * @author      Kevin<3301647@qq.com>
 * @date		 2015-10-4
 * @package     kevinchart
 */
?>
<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id='titlebar'>
  <div class='heading'>
    <span class='prefix'><strong><?php echo $myItem->id . " ". $lang->kevinchart->myItem ?></strong></span>
  </div>
  <div class='actions'>
      <?php
      $actionLinks = '';
		ob_start();
		echo "<div class='btn-group'>";
		echo '</div>';
		$actionLinks = ob_get_contents();
		ob_clean();
		echo $actionLinks;

      ?>
  </div>
</div>
<div class='row-table'>
  <div class='col-main'>
    <div class='main'>
      <fieldset>
        <legend><?php echo $lang->kevinchart->legendBasic; ?></legend>
        <div class='article-content'>
        <table class='table table-data table-condensed table-borderless'> 
                    <tr>
            <th><?php echo $lang->kevinchart->total; ?></th>
            <td><?php echo $myItem->total; ?></td>
          </tr>
          <tr>
            <th><?php echo $lang->kevinchart->monitor; ?></th>
            <td><?php echo $myItem->monitor; ?></td>
          </tr>   
        </table>
		</div>
      </fieldset>
      <div class='actions'> <?php echo $actionLinks; ?></div>
    </div>
  </div>
  <div class='col-side'>
    <div class='main main-side'>
      <fieldset>
        <legend><?php echo $lang->kevinchart->legendBasic; ?></legend>
        <table class='table table-data table-condensed table-borderless'> 
          <tr>
            <th class='w-80px'><?php echo $lang->kevinchart->id; ?></th>
            <td><?php echo $myItem->id; ?></td>
          </tr>  
          <tr>
            <th><?php echo $lang->kevinchart->start; ?></th>
            <td><?php echo Date("Y-m-d H:i",$myItem->start); ?></td>
          </tr>  

        </table>
      </fieldset>
    </div>
  </div>
</div>
<?php include '../../common/view/syntaxhighlighter.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>
