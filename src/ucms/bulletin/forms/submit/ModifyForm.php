<?php

namespace ucms\bulletin\forms\submit;

class ModifyForm extends \ultimo\form\Form {
  
  protected function init() {
    $this->appendValidator('label', 'StringLength', array(1, 255));
    
    $this->appendValidator('datetime', 'NotEmpty');
    $this->appendValidator('datetime', 'Date', array('Y-m-d H:i:s'));
  }
}