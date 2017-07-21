<?php
if (!current_user_can('manage_options')) {
  wp_die(__('You do not have sufficient permissions to access this page.'));
}
$config = include(dirname(__FILE__) . '/../config.php');
$success = false;

if (true === isset($_REQUEST['here_maps_submit'])) {
  $new_app_id = trim(addslashes(htmlspecialchars($_REQUEST['here_maps_app_id'])));
  $new_app_code = trim(addslashes(htmlspecialchars($_REQUEST['here_maps_app_code'])));

  if(true === empty($new_app_id)) {
    $new_app_id = $config['app_id'];
  }

  if(true === empty($new_app_code)) {
    $new_app_code = $config['app_code'];
  }

  update_option('here_maps_app_id', $new_app_id);
  update_option('here_maps_app_code', $new_app_code);
  $success = true;
}

$app_id = get_option('here_maps_app_id');
$app_code = get_option('here_maps_app_code');

if($app_id === $config['app_id']) {
  $app_id = null;
}

if($app_code === $config['app_code']) {
  $app_code = null;
}
?>
<style type="text/css">
  .label_style {
    display:inline-block;
    font-weight:bold;
    width:150px;
  }
  .form_style {
    width:450px;
  }
  .form_style input {
    width:239px;
  }
  .form_style div {
    margin-bottom:5px;
  }
  .center_style {
    text-align:center;
  }
</style>
<div class='wrap'>
  <h2><?=__('here-maps-admin-page-auth-title', 'here-maps');?></h2>

  <?php if (true === isset($_REQUEST['here_maps_submit'])) : ?>
    <div class="<?= (true === $success) ? 'updated' : 'error'; ?>">
      <p>
        <?= (true === $success) ? __('Saved') : __('Something went wrong...'); ?>
      </p>
    </div>
  <?php endif; ?>

  <div id='col-container'>
    <div id='col-right' style="clear:left;float:left;">
      <div class='col-wrap'>
        <p>
          <?=__('here-maps-admin-page-app-info', 'here-maps');?>
        </p>

        <p>
          <?=__('here-maps-admin-page-restore', 'here-maps');?>
        </p>

        <form method='post' action='' class="form_style">
          <div>
            <label for="here_maps_app_id" class="label_style"><?=__('here-maps-admin-page-app-id', 'here-maps');?></label>
            <input type="text" name="here_maps_app_id" id="here_maps_app_id" value="<?=$app_id;?>">
          </div>

          <div>
            <label for="here_maps_app_code" class="label_style"><?=__('here-maps-admin-page-app-code', 'here-maps');?></label>
            <input type="text" name="here_maps_app_code" id="here_maps_app_code" value="<?=$app_code;?>">
          </div>

          <div class="center_style">
            <button type='submit' class='button' name='here_maps_submit'><?=__('here-maps-admin-page-save-params', 'here-maps');?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
