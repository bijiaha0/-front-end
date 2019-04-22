<?php
//Cashcode set
$this->app->loadLang("kevinhours");
$tr = <<<EOT
<fieldset>
<legend>{$lang->kevinhours->project}</legend>
<table class="table table-data table-condensed table-borderless">
  <tbody><tr>
<th class="w-80px text-right strong">{$lang->kevinhours->cashCode}</th>
<td>{$project->cashCode}</td> </tr>
</tbody></table>
</fieldset>
EOT;
?>
<script>
	var appendHTML = <?php echo json_encode($tr); ?>;
	var div1 = $("div.main-side");
	div1.append(appendHTML);
</script>
