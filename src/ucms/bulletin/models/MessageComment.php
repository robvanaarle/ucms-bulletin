<?php

namespace ucms\bulletin\models;

class MessageComment extends \ucms\commentor\models\Comment {
	static protected $relations = array(
    'commentor' => array('User', array('commentor_id' => 'id'), self::MANY_TO_ONE),
	  'message' => array('Message', array('commente_id' => 'submit_id', 'locale' => 'locale'), self::MANY_TO_ONE)
  );
}