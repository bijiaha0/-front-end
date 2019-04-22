<?php
/**
 * The browse mychart list file of kevinchart module
 *
 * @copyright   Kevin
 * @author      Kevin<3301647@qq.com>
 * @date		 2015-10-4
 * @package     kevinchart
 */
?>
<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include './indexheader.html.php'; ?>
<?php include './indexside.html.php'; ?>

<div class='main'> 
  <form action='' method='post' id='listForm'>
    <table class='table table-condensed table-hover table-striped tablesorter ' id='itemlist'>
      <thead>
        <tr class='colhead'>
            <?php $vars = "&period=$period&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"; ?>
          <th class='w-id nobr'><?php common::printorderlink('id', $orderBy, $vars, $lang->idAB); ?></th>
          <th class='w-auto nobr'><?php common::printOrderLink('start', $orderBy, $vars, $lang->kevinchart->start); ?></th>
          <th class='w-80px nobr'><?php common::printOrderLink('total', $orderBy, $vars, $lang->kevinchart->total); ?></th>
          <th class='w-80px nobr'><?php common::printOrderLink('monitor', $orderBy, $vars, $lang->kevinchart->monitor); ?></th>
		  <th class='w-50px {sorter:false}  nobr'><?php echo $lang->actions; ?></th>
        </tr>
      </thead>
      <tbody>

        <?php foreach ($myItems as $myItem): ?>
            <tr class='text-center'>
              <td><?php echo $myItem->id; ?></td>
              <td><?php echo Date("Y-m-d H:i",$myItem->start); ?></td>
              <td><?php echo $myItem->total; ?></td>
              <td><?php echo $myItem->monitor; ?></td>
			     <td class='text-center'>
            <?php
            common::printIcon('kevinchart', 'view', "id=$myItem->id", '', '', 'search', '', 'iframe', 'yes');
            ?>
          </td>
            </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
			 <td colspan='6'><?php echo $pager->show(); ?></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script lanugage='javascript'>
	$('#<?php echo $methodName; ?>').addClass('active');

	var methodName = '<?php echo $methodName; ?>';
	var targetID = '<?php echo isset($id) ? $id : 0; ?>';
<?php if (isset($lastMonth)): ?>
		var lastMonth = '<?php echo $lastMonth; ?>';
		var nextMonth = '<?php echo $nextMonth; ?>';
<?php endif; ?>
</script>