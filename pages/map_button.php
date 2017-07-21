<?php

class HereMapsCore
{

  public static $pluginName = 'here-maps';

  public $path = '';

  public function __construct()
  {
    $this->path = plugins_url(static::$pluginName . '/');
  }

  /**
   * Add 'Open GUI insert map' button to Media Nav Bar.
   *
   * @param $context
   */
  public function attachHereMapsButtonToMediaButtons($context)
  {
    echo $this->getMediaButton();
    echo $this->getMediaButtonScript();
  }

  /**
   * Is possible attach button with GUI Map to Nav Bar?
   */
  public function canAttachHereMapsButtonToMediaButtons()
  {
    // Don't bother doing this stuff if the current user lacks permissions
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
      return;
    }

    add_action('media_buttons', array($this, 'attachHereMapsButtonToMediaButtons'), 100);
  }

  /**
   * Get media button with GUI.
   *
   * @return string
   */
  protected function getMediaButton()
  {
    $placesApi_media_button_image = $this->path . '/dist/images/pin_small.png';
    $here_maps_url_gui = get_site_url() . '/?plugin=here-maps&amp;action=gui&amp;TB_iframe=true';

    return '
      <a id="add_place"
        onclick="switch_tb_position();"
        href="' . $here_maps_url_gui . '"
        class="thickbox button">
        <img src="' . $placesApi_media_button_image . '" alt="" style="position: relative; top: -1px;"> Add map
      </a>
    ';
  }

  /**
   * Get script for media button.
   *
   * @return string
   */
  protected function getMediaButtonScript()
  {

    return '
      <script type="text/javascript">
        function switch_tb_position() {
          var old_tb_position = tb_position;
          var old_tb_remove = tb_remove;

          tb_remove = function () {
            tb_position = old_tb_position;
            tb_remove = old_tb_remove;
            tb_remove();
          };

          tb_position = function () {
            jQuery("#TB_title").remove();

            var tbWindow = jQuery("#TB_window"),
              adminbar_height = 0;

            if (jQuery("body.admin-bar").length) {
              adminbar_height = 28;
            }

            var width = jQuery(window).width(),
              height = jQuery(window).height(),
              W = width - 150,
              H = height - 95;

            if (tbWindow.size()) {
              tbWindow.width(W).height(H);
              jQuery("#TB_iframeContent").width(W).height(H);
              tbWindow.css({"margin-left": "-" + W/2 + "px"});

              if (typeof document.body.style.maxWidth != "undefined")
                tbWindow.css({"top": 20 + adminbar_height + "px", "margin-top": "0"});
            }
          };
        }
      </script>
    ';
  }
}

$here_maps_core = new HereMapsCore;

add_action('init', array($here_maps_core, 'canAttachHereMapsButtonToMediaButtons'));