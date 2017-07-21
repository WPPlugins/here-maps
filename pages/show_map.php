<?php
$places = array();
$vertices = array();

$params = isset($_GET['place']) ? $_GET['place'] : array();
$contour = isset($_GET['contour']) ? $_GET['contour'] : array();

foreach ($params as $item) {
  $matches = explode('|', $item);
  $icon = (isset($matches[4])) ? wp_get_attachment_url($matches[4]) : null;
  $title = json_encode(htmlspecialchars($matches[3]));
  $desc = json_encode(htmlspecialchars($matches[2]));

  $amps = array('&amp;#038;', '&amp;amp;');

  $places[] = array(
    'x' => (float) $matches[0],
    'y' => (float) $matches[1],
    'title' => str_replace($amps, '&', $title),
    'description' => str_replace($amps, '&', $desc),
    'icon' => $icon
  );
}

foreach ($contour as $item) {
  $matches = explode(',', $item);

  $vertices[] = array(
    'x' => (float) $matches[0],
    'y' => (float) $matches[1],
  );
}

$center = (isset($_GET['center'])) ? $_GET['center'] : null;

if(false === is_null($center)) {
  $center = explode('|', $_GET['center']);
}
?><!DOCTYPE html>
<html class="here-maps-root">
<head>
  <meta charset="UTF-8">
  <title><?=__('here-maps-front-title', 'here-maps');?></title>
  <meta name="viewport" content="initial-scale=1.0, width=device-width">
  <link rel="stylesheet" href="<?php echo here_maps_join_file('stylesheets/style.min.css'); ?>">
  <?php if(1 === count($places)) : ?>
    <?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
    <meta property="og:type" content="place">
    <meta property="og:url" content="<?php echo htmlspecialchars($actual_link, ENT_QUOTES, 'UTF-8');?>">
    <meta property="place:location:latitude"  content="<?=$places[0]['y'];?>">
    <meta property="place:location:longitude" content="<?=$places[0]['x'];?>">
  <?php endif; ?>
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
        },
        getParams: {
          zoom: <?=json_encode(isset($_GET['zoom']) ? $_GET['zoom'] : "");?>,
          center: <?=json_encode(isset($_GET['center']) ? $_GET['center'] : "");?>,
          template: <?=json_encode(isset($_GET['template']) ? $_GET['template'] : "");?>,
          hidden: <?=json_encode(isset($_GET['hidden']) ? $_GET['hidden'] : "");?>,
          map_mode: <?=json_encode(isset($_GET['map_mode']) ? $_GET['map_mode'] : "");?>,
          placeid: <?=json_encode(isset($_GET['placeid']) ? $_GET['placeid'] : "");?>,
          title: <?=json_encode(isset($_GET['title']) ? $_GET['title'] : "");?>,
          contour_opacity: <?=json_encode(isset($_GET['contour_opacity']) ? $_GET['contour_opacity'] : "");?>,
          contour_color: <?=json_encode(isset($_GET['contour_color']) ? $_GET['contour_color'] : "");?>
        }
      };
      w.H.places=[];w.H.contour=[];
      <?php foreach($places as $item) : ?>
        w.H.places.push({ lat: <?=$item['x'];?>, lng: <?=$item['y'];?>, description: <?=$item['description'];?>, title: <?=$item['title'];?>, icon: '<?=$item['icon'];?>'});
      <?php endforeach; ?>
      <?php foreach($vertices as $item) : ?>
        w.H.contour.push({ lat: <?=$item['x'];?>, lng: <?=$item['y'];?> });
      <?php endforeach; ?>
    })(window);
  </script>
</head>
<?php
  $hidden = isset($_GET['hidden']) ? htmlspecialchars($_GET['hidden']) : null;
  $template = isset($_GET['template']) ? htmlspecialchars($_GET['template']) : 'empty';
  $theme = isset($_GET['theme']) ? htmlspecialchars($_GET['theme']) : null;

  if (null === $theme) {
    $box_class = null;
  } else {
    $box_class = ('dark' === $theme) ? ' theme-dark' : ' theme-light';
  }

?><body class="container-fluid<?=$box_class;?>">
<div id="mapContainer">
<?php
  if ('fixed' === $template) {
    include(dirname(__FILE__) . '/include/template-box.html');
  }

  if (isset($_GET['hidden']) && false == preg_match('/map_mode/i', $_GET['hidden'])) {
    include(dirname(__FILE__) . '/include/map-select.html');
  }
?>
</div>

<script src="<?php echo here_maps_join_file('javascripts/api.min.js') ?>"></script>
<script src="<?php echo here_maps_join_file('javascripts/front.min.js') ?>"></script>
</body>
</html>