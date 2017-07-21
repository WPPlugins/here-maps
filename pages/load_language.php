<?php

/**
 * Simple protection for sites which language files does not exists.
 *
 * If site language is not supported then plugin using english package.
 */

$locale = get_locale();
$file = sprintf('%s/languages/here-maps-%s.mo', dirname(__DIR__), $locale);

if (false === file_exists($file)) {
  $file = sprintf('%s/languages/here-maps-en_US.mo', dirname(__DIR__));

  load_textdomain('here-maps', $file);
}
