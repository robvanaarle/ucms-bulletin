<?php

namespace ucms\bulletin\controllers;


class MessagecommentController extends \ucms\commentor\controllers\CommentController {
	protected function getCommente($commente_id, $locale) {
    $manager = $this->module->getPlugin('uorm')->getManager('Bulletin');
    return $manager->get('Message', array('submit_id' => $commente_id, 'locale' => $locale));
	}
}