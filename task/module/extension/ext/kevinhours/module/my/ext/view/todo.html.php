<?php
/**
 * The todo view by Kevin
 */
?>
<?php include '../../../common/view/header.html.php';?>
 <?php
$codeStr = html::a(helper::createLink('kevinhours', 'index'), '<h1>待办功能被项目工时考勤替代，(3秒自动跳转)--Kevin</h1>', '', "class='btn btn-large btn-block btn-primary'"); 
?>
<table>
	<tr>
		<td height='100px' width='400'><?php echo $codeStr ?></td>
	</tr>
</table>
<script language=javascript> ;

setTimeout("delayURL()", 3000);   
function delayURL(url) {  
	var link = createLink('kevinhours', 'index');
	window.top.location.href=link;   
} 
</script>
<?php include '../../../common/view/footer.html.php';?>
