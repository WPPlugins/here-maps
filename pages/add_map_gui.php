<?php
if (!current_user_can('edit_pages') && !current_user_can('edit_posts')) {
  wp_die(__("You are not allowed to be here"));
}

wp_enqueue_media();
wp_enqueue_script('media-upload');

$src = plugins_url(HereMapsCore::$pluginName . '/');
?><!doctype html>
<html class="admin-gui">
<head>
  <meta charset="UTF-8">
  <script>
    (function(w){ // H is namespace for HERE Maps
      if ("undefined" === typeof w.H) { w.H={}; }
      w.H.Config = {
        path: '<?=plugin_dir_url(__DIR__);?>',
        userLang: '<?=str_replace('_', '-', here_maps_detect_language());?>',
        auth: {
          id: '<?=get_option('here_maps_app_id');?>',
          code: '<?=get_option('here_maps_app_code');?>'
        },
        mapPin: {
          id: 0,
          url: null
        },
        lang: {
          'here-maps-markers-new-marker-tooltip': '<?=__('here-maps-markers-new-marker-tooltip', 'here-maps');?>',
          'here-maps-markers-new-marker-title': '<?=__('here-maps-markers-new-marker-title', 'here-maps');?>',
          'here-maps-markers-label-changepin': '<?=__('here-maps-admin-gui-label-changepin', 'here-maps');?>',
          'here-maps-markers-label-restorepin': '<?=__('here-maps-admin-gui-label-restorepin', 'here-maps');?>',
          'here-maps-markers-label-seeonhere': '<?=__('here-maps-front-template-see', 'here-maps');?>',
          'here-maps-markers-label-getdirection': '<?=__('here-maps-front-template-get', 'here-maps');?>',
          'here-maps-markers-label-noresult': '<?=__('here-maps-search-result-nothing', 'here-maps');?>',
          'here-maps-zoom-in': '<?=__('here-maps-zoom-in', 'here-maps');?>',
          'here-maps-zoom-out': '<?=__('here-maps-zoom-out', 'here-maps');?>'
        }
      };
    })(window);
  </script>
  <?php wp_head(); ?>
  <link rel="stylesheet" href="<?php echo here_maps_join_file('stylesheets/style.min.css'); ?>">
</head>
<body id="wrapper" class="theme-dark">
<div class="here-maps-admin-gui-title clearfix">
  <div class="pull-left here-maps-admin-title">
    <b><?=__('here-maps-admin-title', 'here-maps');?></b>
  </div>
  <div class="pull-right finish-btn">
    <a class="btn btn-primary pull-right btn-xs" id="insertAction"><?=__('here-maps-admin-gui-add', 'here-maps');?></a>
    <a class="btn btn-default pull-right btn-xs" id="cancelAction"><?=__('here-maps-admin-gui-cancel', 'here-maps');?></a>
  </div>
</div>

<div id="mapContainer" class="here-maps-admin-gui here-maps-admin-gui-height">
  <ul class="option-box list-unstyled list-icons js-list-icons">
    <li class="active form-inline js-contour-mode-deactivate">
      <span class="map-tab js-icon glyphicon glyphicon-map-marker" title="<?=__('here-maps-admin-gui-title-pin-icon', 'here-maps');?>"></span>
      <div class="row map-option">
        <div class="form-group">
          <input type="text" class="form-control input-xs search-input" id="here-search-input" placeholder="<?=__('here-maps-admin-gui-input-placeholder', 'here-maps');?>">
          <span class="js-clear-input clear-input hidden" title="<?=__('here-maps-admin-gui-title-x-icon', 'here-maps');?>"></span>
          <button type="button" class="btn btn-success btn-xs btn-search" id="here-search-button"><span class="glyphicon glyphicon-search"></span></button>
        </div>
        <ul id="here-search-results" class="list-unstyled hidden"></ul>
      </div>
    </li>
    <li class="js-contour-mode-activate">
      <span class="map-tab js-icon" title="<?=__('here-maps-admin-gui-title-setting-icon', 'here-maps');?>"><img class="selection-mark" src="<?= $src; ?>/dist/images/mark.svg"></span>
      <div class="map-option optional-btns">
        <h2 class="opt-btn"><?=__('here-maps-admin-gui-block-title-contour', 'here-maps');?></h2>
        <p class="contour-desc">
          <?=__('here-maps-admin-gui-label-contour', 'here-maps');?>
        </p>

        <div class="button-wrap clearfix">
        <span class="js-remove-last-point hidden btn btn-primary btn-xs pull-left space">
          <span class="glyphicon glyphicon-map-marker"></span>
          <?=__('here-maps-admin-gui-label-delete-last-point', 'here-maps');?>
        </span>
        <span class="js-remove-contour hidden btn btn-danger btn-xs pull-right space">
          <span class="glyphicon glyphicon-trash"></span>
          <?=__('here-maps-admin-gui-label-delete-contour', 'here-maps');?>
        </span>
        </div>

        <h2 class="opt-btn"><?=__('here-maps-admin-gui-block-title-contour-design', 'here-maps');?></h2>

        <p class="contour-desc">
          <?=__('here-maps-admin-gui-label-colour', 'here-maps');?>
        </p>
        <div class="clearfix">
        <span data-color="#8f8e94" class="js-set-color active-color color-palette gray"></span>
        <span data-color="#f9676d" class="js-set-color color-palette light-red"></span>
        <span data-color="#ed462f" class="js-set-color color-palette red"></span>
        <span data-color="#f39531" class="js-set-color color-palette orange"></span>
        <span data-color="#fbce33" class="js-set-color color-palette yellow"></span>
        <span data-color="#fbf833" class="js-set-color color-palette light-yellow"></span>
        <span data-color="#b8dd5f" class="js-set-color color-palette lime"></span>
        <span data-color="#6add5f" class="js-set-color color-palette green"></span>
        <span data-color="#54c8fa" class="js-set-color color-palette light-blue"></span>
        <span data-color="#3faae0" class="js-set-color color-palette blue"></span>
        <span data-color="#0075fb" class="js-set-color color-palette dark-blue"></span>
        <span data-color="#5851da" class="js-set-color color-palette purple"></span>
</div>
        <p class="contour-desc">
          <?=__('here-maps-admin-gui-label-opacity', 'here-maps');?>
        </p>
        <input type="range" min="0" max="100" step="1" class="js-opacity" value="30">
      </div>
    </li>
    <li class="js-contour-mode-deactivate">
      <span class="map-tab js-icon glyphicon glyphicon-cog" title="<?=__('here-maps-admin-gui-title-setting-icon', 'here-maps');?>"></span>
      <div class="map-option optional-btns">
        <h2 class="opt-btn"><?=__('here-maps-admin-gui-block-title-optional', 'here-maps');?></h2>
        <div class="map-form map-btns">
          <div class="form-group checked">
            <div class="labInp">
              <label for="opt-full-screen">
                <span class="glyphicon glyphicon-resize-full"></span>
                <input type="checkbox" name="opt-full-screen" value="true" id="opt-full-screen" class="js-opt-icon" checked>
                <?=__('here-maps-admin-gui-label-fullscreen', 'here-maps');?>
              </label>
            </div>
          </div>
          <div class="form-group checked">
            <div class="labInp">
              <label for="opt-zoom">
                <span class="glyphicon glyphicon-plus"></span>
                <input type="checkbox" name="opt-map-zoom" value="true" id="opt-zoom" class="js-opt-icon" checked>
                <?=__('here-maps-admin-gui-label-zoom', 'here-maps');?>
              </label>
            </div>
          </div>
          <div class="form-group checked">
            <div class="labInp">
              <label for="opt-map-types">
                <div class="map-types">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </div>
                <input type="checkbox" name="opt-map-types" value="true" id="opt-map-types" class="js-opt-icon" checked>
                <?=__('here-maps-admin-gui-label-maptypes', 'here-maps');?>
              </label>
            </div>
          </div>
        </div>

        <h2><?=__('here-maps-admin-gui-block-title-size', 'here-maps');?></h2>
        <div class="map-form">
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label for="opt-map-width"><?=__('here-maps-admin-gui-label-width', 'here-maps');?></label>
                <input type="text" class="form-control input-xs" name="opt-map-width" value="100%" id="opt-map-width">
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label for="opt-map-height"><?=__('here-maps-admin-gui-label-height', 'here-maps');?></label>
                <input type="text" class="form-control input-xs" name="opt-map-height" value="400px" id="opt-map-height">
              </div>
            </div>
          </div>
        </div>

        <h2><?= __('here-maps-admin-gui-block-title-graphic', 'here-maps'); ?></h2>

        <div class="map-graphics">
          <div class="custom-pin">
            <span class="js-uploader-custom-pin custom-new btn btn-primary btn-xs">
            <span class="glyphicon glyphicon-picture"></span>
              <?= __('here-maps-admin-gui-label-changepin', 'here-maps'); ?>
            </span>

            <div class="uploader-current-marker js-uploader-current-marker"></div>
            <span class="js-uploader-custom-restore custom-restore hidden btn btn-primary btn-xs">
            <span class="glyphicon glyphicon-map-marker"></span>
              <?= __('here-maps-admin-gui-label-restorepin', 'here-maps'); ?>
              
            </span>
          </div>
        </div>

        <h2><?=__('here-maps-admin-gui-block-title-choose', 'here-maps');?></h2>
        <ul class="map-labels list-inline js-map-labels">
          <li class="checked">
            <label for="here-template-fixed">
              <img class="not-selected" src="<?= $src; ?>/dist/images/box.png" alt="">
              <img class="selected" src="<?= $src; ?>/dist/images/box-active.png" alt="">
              <input type="radio" name="here-template" value="fixed" id="here-template-fixed" checked>
              <?=__('here-maps-admin-gui-label-box', 'here-maps');?>
            </label>
          </li>
          <li>
            <label for="here-template-tooltip">
              <img class="not-selected" src="<?= $src; ?>/dist/images/tooltip.png" alt="">
              <img class="selected" src="<?= $src; ?>/dist/images/tooltip-active.png" alt="">
              <input type="radio" name="here-template" value="tooltip" id="here-template-tooltip">
              <?=__('here-maps-admin-gui-label-tooltip', 'here-maps');?>
            </label>
          </li>
          <li>
            <label for="here-template-empty">
              <img class="not-selected" src="<?= $src; ?>/dist/images/none.png" alt="">
              <img class="selected" src="<?= $src; ?>/dist/images/none-active.png" alt="">
              <input type="radio" name="here-template" value="empty" id="here-template-empty">
              <?=__('here-maps-admin-gui-label-none', 'here-maps');?>
            </label>
          </li>
        </ul>

        <h2><?= __('here-maps-admin-gui-block-title-theme', 'here-maps'); ?></h2>

        <div class="map-theme">
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label for="opt-map-theme-light">
                <img src="<?= $src; ?>/dist/images/theme_light.svg">
                <?=__('here-maps-admin-gui-label-theme-light', 'here-maps');?>
                <input type="radio" class="form-control input-xs theme-control" name="opt-map-theme" value="light" id="opt-map-theme-light">
                </label>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
              <label for="opt-map-theme-dark">
                <img src="<?= $src; ?>/dist/images/theme_dark.svg">
                <?=__('here-maps-admin-gui-label-theme-dark', 'here-maps');?>
                <input type="radio" class="form-control input-xs theme-control" name="opt-map-theme" value="dark" id="opt-map-theme-dark" checked>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </li>
  </ul>

  <div class="here-maps-how-add-marker js-here-maps-how-add-marker">
    <?=__('here-maps-how-add-marker', 'here-maps');?>
  </div>

  <?php include(dirname(__FILE__) . '/include/map-select.html'); ?>
  <div class="here-maps-show-all-pins">
    <div class="js-here-maps-show-all-pins-icon here-maps-show-all-pins-icon">
      <span class="glyphicon glyphicon-a glyphicon-map-marker"></span>
      <span class="glyphicon glyphicon-b glyphicon-map-marker"></span>
      <span class="glyphicon glyphicon-c glyphicon-map-marker"></span>
    </div>
    <div class="js-here-maps-show-all-pins-tooltip here-maps-show-all-pins-tooltip"><?=__('here-maps-admin-gui-title-show-markers', 'here-maps');?></div>
  </div>
</div>

<?php wp_footer(); ?>
<script src="<?php echo here_maps_join_file('javascripts/api.min.js'); ?>"></script>
<script src="<?php echo here_maps_join_file('javascripts/admin.min.js'); ?>"></script>
</body>
</html>
