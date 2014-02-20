<?php

namespace ucms\bulletin\controllers;

class ArchiveController extends Controller {
  public function actionYearindex() {
    $locale = $this->request->getParam('locale', $this->locale);
    
    $currentYear = date('Y');
    $year = (int) $this->request->getParam('year', $currentYear);
    
    // don't show archive for future years
    if ($year > $currentYear) {
      throw new \ultimo\mvc\exceptions\DispatchException("Archive year index for {$year} does not exist.", 404);
    }
    
    $this->view->messageCounts = $this->bulletinMgr->getMessageCountPerMonth($year, $locale);
    $this->view->locale = $locale;
    $this->view->year = $year;
  }
  
  public function actionMonthindex() {
    // maxDate = min(maxDate, now)!
    $locale = $this->request->getParam('locale', $this->locale);
    $year = (int) $this->request->getParam('year', date('Y'));
    $month = (int) $this->request->getParam('month', date('n'));
    
    $now = new \DateTime();
    $date = new \DateTime();
    $date->setDate($year, $month, 1);
    $date->setTime(0, 0, 0);
    
    // don't show archive for future months
    if ($date->diff($now)->format('%r%a') < 0) {
      throw new \ultimo\mvc\exceptions\DispatchException("Archive month index for {$year}-{$month} does not exist.", 404);
    }
  
    $minDate = $date->format('Y-m-d H:i:s');
    
    // the max date is the beginning of the next month, or now, if the current
    // month is selected
    if ($now->format('Y-n') == $date->format('Y-n')) {
      $maxDate = $now->format('Y-m-d H:i:s');
    } else {
      $date->setDate($year, $month+1, 1);
      $maxDate = $date->format('Y-m-d H:i:s');
    }

    $this->view->messages = $this->bulletinMgr->getMessages($locale, array(
      'maxDate' => $maxDate,
      'minDate' => $minDate,
      'order' => 'ASC'
    ));
    
    $this->view->locale = $locale;
    $this->view->year = $year;
    $this->view->month = $month;
  }
  
  public function actionDayindex() {
    $locale = $this->request->getParam('locale', $this->locale);
    $year = (int) $this->request->getParam('year', date('Y'));
    $month = (int) $this->request->getParam('month', date('n'));
    $day = (int) $this->request->getParam('day', date('j'));
    
    $now = new \DateTime();
    $date = new \DateTime();
    $date->setDate($year, $month, $day);
    $date->setTime(0, 0, 0);
    
    // don't show archive for future days
    if ($date->diff($now)->format('%r%a') < 0) {
      throw new \ultimo\mvc\exceptions\DispatchException("Archive day index for {$year}-{$month}-{$day} does not exist.", 404);
    }
  
    $minDate = $date->format('Y-m-d H:i:s');
    
    // the max date is the beginning of the next month, or now, if the current
    // day is selected
    if ($now->format('Y-n-j') == $date->format('Y-n-j')) {
      $maxDate = $now->format('Y-m-d H:i:s');
    } else {
      $date->setDate($year, $month, $day+1);
      $maxDate = $date->format('Y-m-d H:i:s');
    }

    $this->view->messages = $this->bulletinMgr->getMessages($locale, array(
      'maxDate' => $maxDate,
      'minDate' => $minDate,
      'order' => 'ASC'
    ));
    
    $this->view->locale = $locale;
    $this->view->year = $year;
    $this->view->month = $month;
    $this->view->day = $day;
  }
}