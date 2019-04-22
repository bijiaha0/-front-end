<?php
/**
 * The view file  to kevinhours
 */
?>
<?php include '../../../common/view/header.html.php';?>
<script language=javascript> ;
	var link = createLink('kevinhours', 'view', 'id=' + <?php echo $todo->id; ?>);
	window.top.location.href=link;   
</script>
