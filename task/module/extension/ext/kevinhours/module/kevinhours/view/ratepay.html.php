<?php include '../../common/view/header.lite.html.php';?>
<div class='container mw-1400px'>
  <form class='form-condensed' method='post' target='hiddenwin' id='dataform'">
  <center>
    <table class='table-form'> 
      <tr>
		<td class='text-center'>加班费基数</td>
        <td><?php echo html::input("ratepay", $ratePay, "class='form-control'");?></td>
        <td colspan='1' class='text-center'>
          <?php echo html::submitButton("提交");?>
        </td>
      </tr>
    </table>
	</form>
</div>
<?php include '../../common/view/footer.html.php';?>