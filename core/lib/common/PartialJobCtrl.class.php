<?php

trait PartialJobCtrl {

  protected function getPjLastStepKey(PartialJob $oPJ) {
    return $oPJ->getId().'LastStep';
  }

  protected function getPjLastStep(PartialJob $oPJ) {
    return Settings::get($this->getPjLastStepKey($oPJ));
  }

  protected function actionJsonPJ(PartialJob $oPJ) {
    $settingsKey = $this->getPjLastStepKey($oPJ);
    $step = $this->req->rq('step');
    $this->json['step'] = $step;
    if (!$step and ($_step = Settings::get($settingsKey))) {
      // Если 0-й шаг, начинаем с последнего сохраненного шага
      $step = $_step + 1;
    }
    $this->json = $oPJ->stepData($step);
    try {
      $oPJ->makeStep($step);
    } catch (Exception $e) {
      if ($e->getCode() == 1040) {
        // Шаг больше максимально возможного.
        // Значит по какой-то причине предыдущий шаг не был успешно завершен
        // Завершаем
        $oPJ->complete();
        return;
      }
      // 'continueErrorCodes' - коды ошибок, для которых включена ф-я "продолжить"
      // Если эти коды существуют
      // Проверяем выброшеное исключение на наличие в них
      elseif (!empty($this->req->r['continueErrorCodes']) and in_array($e->getCode(), $this->req->r['continueErrorCodes'])
      ) {
        // И, если оно там есть, переходим к следующему шагу
        Settings::set($settingsKey, $step);
      }
      // И выбрасываем ошибку, она нам ещё понадобиться в формировании ответного json-массива
      throw $e;
    }
    Settings::set($settingsKey, $step);
  }

  protected function cleanupPJStep(PartialJob $oPJ) {
    Settings::delete($this->getPjLastStepKey($oPJ));
  }

}