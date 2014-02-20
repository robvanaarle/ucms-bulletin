<?php

namespace ucms\bulletin\facades\bulletin;

class Messages extends \ultimo\mvc\Facade {
  
  /**
   * @var ucms\bulletin\managers\BulletinManager
   */
  protected $bulletinMgr;
  
  protected $config;
  
  protected $locales;
  protected $localeCount;
  protected $locale;
  
  protected function init() {
    $this->config = $this->module->getPlugin('config')->getConfig('general');
    $this->module->getPlugin('config')->getViewConfig('comment');
    
    $this->bulletinMgr = $this->module->getPlugin('uorm')->getManager('Bulletin');
    $this->locales = $this->module->getPlugin('translator')->getAvailableLocales();
    $this->localeCount = count($this->locales);
    
    switch (count($this->locales)) {
      case 0:
        $this->locale = 'xx';
        break;
      case 1:
        $this->locale = $this->locales[0];
        break;
      default:
        $this->locale = $this->module->getPlugin('translator')->getLocale();
        break;
    }
    
  }
  
  public function getLatest($count, $locale=null) {
    if ($locale === null) {
      $locale = $this->locale;
    }
    
    return $this->bulletinMgr->getMessages($locale, array(
  	  'maxDate' => date('Y-m-d H:i:s'),
  	  'count' => $count
  	));
  }
}