<?php

namespace ucms\bulletin\models;

class Message extends \ultimo\orm\Model {
  public $submit_id;
  public $locale = '';
  public $title = '';
  public $teaser = '';
  public $message = '';
  
  static protected $fields = array('submit_id', 'locale', 'title', 'teaser', 'message');
  static protected $primaryKey = array('submit_id', 'locale');
  static protected $relations = array(
    'submit' => array('Submit', array('submit_id' => 'id'), self::MANY_TO_ONE),
    'comments' => array('MessageComment', array('submit_id' => 'commente_id', 'locale' => 'locale'), self::ONE_TO_MANY)
  );

}