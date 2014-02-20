<?php

namespace ucms\bulletin\controllers;


class SubmitimageController extends \ucms\visualiser\controllers\ImageController {
	protected function getVisualised($visualised_id) {
    $manager = $this->module->getPlugin('uorm')->getManager('Bulletin');
    return $manager->get('Submit', $visualised_id);
	}
}