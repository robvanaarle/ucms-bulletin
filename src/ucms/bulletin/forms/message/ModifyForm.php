<?php

namespace ucms\bulletin\forms\message;

class ModifyForm extends \ultimo\form\Form {
  
  protected function init() {
    $this->appendValidator('locale', 'InArray', array($this->getConfig('availableLocales')));
    $this->appendValidator('title', 'StringLength', array(1, 255));
    $this->appendValidator('teaser', 'StringLength', array(1, 255));
    $this->appendValidator('message', 'NotEmpty');
  }
}