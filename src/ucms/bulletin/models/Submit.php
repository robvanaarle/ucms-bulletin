<?php

namespace ucms\bulletin\models;

class Submit extends \ultimo\orm\Model {
  public $id;
  public $label = '';
  public $datetime = '';
  
  static protected $fields = array('id', 'label', 'datetime');
  static protected $primaryKey = array('id');
  static protected $autoIncrementField = 'id';
  static protected $relations = array(
    'messages' => array('Message', array('id' => 'submit_id'), self::ONE_TO_MANY),
    'images' => array('SubmitImage', array('id' => 'visualised_id'), self::ONE_TO_MANY)
  );

}