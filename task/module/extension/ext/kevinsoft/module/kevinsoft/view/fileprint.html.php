<?php
$sessionString  = $config->requestType == 'PATH_INFO' ? '?' : '&';
$sessionString .= session_name() . '=' . session_id();
?>
<style> .files-list {margin: 0;} .files-list > .list-group-item {padding: 0px; border:0px;} .files-list > .list-group-item a {color: #666} .files-list > .list-group-item:hover a {color: #333} .files-list > .list-group-item > .right-icon {opacity: 0.01; transition: all 0.3s;} .files-list > .list-group-item:hover > .right-icon {opacity: 1} .files-list .btn-icon > i {font-size:15px}</style>
<script language='Javascript'>
$(function(){
     $(".edit").modalTrigger({width:350, type:'iframe'});
})

/* Delete a file. */
function deleteFile(fileID)
{
    if(!fileID) return;
    hiddenwin.location.href =createLink('kevinsoft', 'filedelete', 'fileID=' + fileID);
}
/* Download a file, append the mouse to the link. Thus we call decide to open the file in browser no download it. */
function downloadFile(fileID)
{
    if(!fileID) return;
    var sessionString = '<?php echo $sessionString;?>';
    var url = createLink('kevinsoft', 'filedownload', 'fileID=' + fileID + '&mouse=left') + sessionString;
    window.open(url, '_blank');
    return false;
}
</script>
<?php if($fieldset == 'true'):?>
<fieldset>
  <legend><?php echo $lang->file->common;?></legend>
<?php endif;?>
  <div class='list-group files-list'>
  <?php
  $hasPrivFileDelete = common::hasPriv('kevinsoft', 'filedelete');//delete
  $hasPrivFileEdit = common::hasPriv('kevinsoft', 'fileedit');//delete
  foreach($files as $file)
  {
      if(common::hasPriv('kevinsoft', 'filedownload'))
      {
          $fileTitle = "<li class='list-group-item'><i class='icon-file-text text-muted icon'></i> &nbsp;" . $file->title .'.' . $file->extension;
          echo html::a($this->createLink('kevinsoft', 'filedownload', "id=$file->id") . $sessionString, $fileTitle, '_blank', "onclick='return downloadFile($file->id)'");
          echo "<span class='right-icon'>";
          if($hasPrivFileEdit) common::printLink('kevinsoft', 'fileedit', "id=$file->id", "<i class='icon-pencil'></i>", '', "class='edit btn-icon' title='{$lang->file->edit}'");
          if($hasPrivFileDelete) echo html::a('###', "<i class='icon-remove'></i>", '', "class='btn-icon' onclick='deleteFile($file->id)' title='$lang->delete'");
          echo '</span>';
          echo '</li>';
      }
  }
  ?>
  </div>
<?php if($fieldset == 'true') echo '</fieldset>';?>
