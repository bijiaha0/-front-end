<?php
//cashCode
$this->app->loadLang("kevinhours");
$codeStr = html::input('cashCode', $project->cashCode, "class='form-control'");
$tr = <<<EOT
<div class="input-group">
	<table class="table table-form"><tr>
	<th>{$lang->kevinhours->cashCode}</th>
	<td>$codeStr</td>
	</tr><table>
</div>
EOT;
?>
<script>
	var appendHTML = <?php echo json_encode($tr); ?>;
	$('#name').parent().next().prepend(appendHTML);
</script>
