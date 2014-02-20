<?php

namespace ucms\bulletin\forms\combined;

class ModifyForm extends \ultimo\form\Form {
  
  protected function init() {
    $this->appendValidator('datetime', 'NotEmpty');
    $this->appendValidator('datetime', 'Date', array('Y-m-d H:i:s'));
    
    $this->appendValidator('title', 'StringLength', array(1, 255));
    $this->appendValidator('teaser', 'StringLength', array(1, 255));
    $this->appendValidator('message', 'NotEmpty');
  }
}