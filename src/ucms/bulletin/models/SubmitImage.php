<?php

namespace ucms\bulletin\models;

class SubmitImage extends \ucms\visualiser\models\Image {
	static protected $relations = array(
	  'submit' => array('Submit', array('visualised_id' => 'id'), self::MANY_TO_ONE)
  );
}