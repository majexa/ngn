<?php

class CssCore {

  static function wrapSelectors($css, $wrapSelector) {
    // ревнивый *+
    return preg_replace_callback('/(\s*+)(.+)(\s*+\{[^\}]*\})/Ums', function($m) use ($wrapSelector) {
      return $m[1].$wrapSelector.' '.$m[2].$m[3];
    }, $css);
  }

  static function getProloadJs($css) {
    if (!preg_match_all('/url\(([^\)]+)\)/', $css, $m)) return '';
    $js = "";
    foreach ($m[1] as $url) $js .= "new Image().src = '$url';\n";
    return "\n(function() {\n".$js."}).delay(1000);\n";
  }

  static function brightness($hex, $percent, $returnRgb = false) {
    $hash = '';
    if (stristr($hex, '#')) {
      $hex = str_replace('#', '', $hex);
      $hash = '#';
    }
    $rgb = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    for ($i = 0; $i < 3; $i++) {
      if ($percent > 0) {
        // Lighter
        $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1 - $percent));
      }
      else {
        // Darker
        $positivePercent = $percent - ($percent * 2);
        $rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1 - $positivePercent));
      }
      if ($rgb[$i] > 255) $rgb[$i] = 255;
    }
    if ($returnRgb) return $rgb;
    $hex = '';
    for ($i = 0; $i < 3; $i++) {
      $hexDigit = dechex($rgb[$i]);
      if (strlen($hexDigit) == 1) $hexDigit = "0".$hexDigit;
      $hex .= $hexDigit;
    }
    return $hash.$hex;
  }

  static function btnColors($baseColor, $selector = null, $addSelector = '') {
    if ($selector) $selector = $selector.' ';
    $lightColor = CssCore::brightness($baseColor, 0.7);
    $darkColor = CssCore::brightness($baseColor, -0.9);
    $lightColorOver = CssCore::brightness($baseColor, 0.3);
    $darkColorOver = $baseColor;
    $lightColorPushed = CssCore::brightness($baseColor, -0.9);
    $darkColorPushed = CssCore::brightness($baseColor, -0.85);
    $borderColor = CssCore::brightness($baseColor, -0.7);
    $shadowColors = implode(', ', CssCore::brightness($baseColor, -0.5, true));
    return "
{$selector}a.btn$addSelector {
border-color: $borderColor;
background: -webkit-linear-gradient(top, $lightColor, $darkColor);
}
{$selector}a.btn$addSelector:hover:not(.nonActive):not(.pushed), b.btn$addSelector {
background: -webkit-linear-gradient(top, $lightColorOver, $darkColorOver);
}
{$selector}a.btn$addSelector:active, {$selector}a.btn$addSelector.pushed {
background: -webkit-linear-gradient(top, $lightColorPushed, $darkColorPushed);
box-shadow: inset 0 1px 2px rgba($shadowColors, 0.5);
}
";
  }

}
