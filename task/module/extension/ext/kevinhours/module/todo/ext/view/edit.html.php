<?php include '../../../common/view/header.html.php';?>
<script language=javascript> ;
	var link = createLink('kevinhours', 'edit', 'id=' + <?php echo $todo->id; ?>);
	window.top.location.href=link;   
</script>
