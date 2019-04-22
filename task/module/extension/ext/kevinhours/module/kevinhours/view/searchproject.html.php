<?php include '../../common/view/header.lite.html.php';?>
<div class='container mw-1400px'>
  <form class='form-condensed' method='post' target='hiddenwin' id='dataform' onsubmit="return checkKeywordsLength();">
  <center>
    <table class='table-form'> 
      <tr>
		<td class='text-center'>关键词</td>
        <td><?php echo html::input("keywords", $keywords, "class='form-control'");?></td>
        <td colspan='1' class='text-center'>
          <?php echo html::submitButton("提交") . html::backButton();?>
        </td>
      </tr>
    </table>
	<br/>
	</form>
	<center>
	<?php if(!empty($projectArray)){?>
  <table width="800" border="1" cellpadding="0" cellspacing="0">
	<tr height="20">
			<td align="center"><font size='4'>项目代号</font></td>
			<td align="center"><font size='4'>项目名称</font></td>
	</tr>
	<?php foreach($projectArray as $project=>$projectName){?>	
	<tr height="20">
			<td align="center"><font size='4'><?php echo $project;?></font></td>
			<td align="center"><font size='4'><?php echo $projectName;?></font></td>
	</tr>
	<?php }?>
  </table>
  <?php }else echo '搜索结果为0';?>
</div>
<?php include '../../common/view/footer.html.php';?>
<script language='Javascript'>
function checkKeywordsLength()
 {  
	var keywords = document.getElementById('keywords').value;
	if(keywords.length < 2)
	{
		alert("关键词长度至少为2！");
		return false;
	}
	return true;
}  
</script>