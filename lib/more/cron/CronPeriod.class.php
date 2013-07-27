<?php

class CronPeriod {
  
  /**
   * Возвращает все существующие в платформе крон-периоды
   *
   * @return array
   */
  static function getPeriods() {
    return [
      'every5min' => [
        'title' => 'раз в 5 минут',
        'min' => 5
      ],
      'every10min' => [
        'title' => 'раз в 10 минут',
        'min' => 10
      ],
      'every30min' => [
        'title' => 'раз в 30 минут',
        'min' => 30
      ],
      'every1h' => [
        'title' => 'раз в час',
        'min' => 60
      ],
      'every2h' => [
        'title' => 'раз в 2 часа',
        'min' => 120
      ],
      'every3h' => [
        'title' => 'раз в 3 часа',
        'min' => 180
      ],
      'every6h' => [
        'title' => 'раз в 6 часов',
        'min' => 360
      ],
      'every12h' => [
        'title' => 'раз в 12 часов',
        'min' => 720
      ],
      'daily' => [
        'title' => 'раз в сутки',
        'min' => 1440
      ],
      'every3d' => [
        'title' => 'раз в сутки',
        'min' => 1440
      ],
      'weekly' => [
        'title' => 'раз в неделю',
        'min' => 1440
      ],
      'every2w' => [
        'title' => 'раз в неделю',
        'min' => 1440
      ],
      'monthly' => [
        'title' => 'раз в неделю',
        'min' => 1440
      ],
    ];
  }
  
}
