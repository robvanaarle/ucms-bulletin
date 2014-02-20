<?php

namespace ucms\bulletin\controllers;

class SubmitController extends Controller {
  
  public function actionIndex() {
    $locale = null;
    if ($this->localeCount <= 1) {
      $locale = $this->locale;
    }
  	$this->view->submits = $this->bulletinMgr->getSubmits($locale);
  }
  
  public function actionRead() {
  	$id = $this->request->getParam('id');
  	$submit = $this->bulletinMgr->getSubmit($id);
  	if ($submit === null) {
  		throw new \ultimo\mvc\exceptions\DispatchException("Submit with id '{$id}' does not exist.", 404);
  	}
  	$this->view->submit = $submit;
  }
  
  public function actionCreate() {
  	$form = $this->module->getPlugin('formBroker')->createForm(
      'submit\CreateForm', $this->request->getParam('form', array('datetime' => date('Y-m-d H:i:s')))
    );
    
    if ($this->request->isPost()){
      if ($form->validate()) {
        $submit = $this->bulletinMgr->create('Submit');
        $submit->label = $form['label'];
        $submit->datetime = $form['datetime'];
        $submit->save();
        
        return $this->getPlugin('redirector')->redirect(array('action' => 'read', 'id' => $submit->id));
      }
    }
    
    $this->view->form = $form;
  }
  
  public function actionUpdate() {
  	$id = $this->request->getParam('id');

    $form = $this->module->getPlugin('formBroker')->createForm(
      'submit\UpdateForm', $this->request->getParam('form', array())
    );

    if ($this->request->isPost()) {
      if ($form->validate()) {
        $submit = $this->bulletinMgr->get('Submit', $id, true);
        $submit->label = $form['label'];
        $submit->datetime = $form['datetime'];
        $submit->save();
      
        return $this->getPlugin('redirector')->redirect(array('action' => 'read', 'id' => $submit->id));
      }
    } else {
      $submit = $this->bulletinMgr->get('Submit', $id);
      
      if ($submit === null) {
        throw new \ultimo\mvc\exceptions\DispatchException("Submit with id '{id}' does not exist.", 404);
      }
      
      $form->fromArray($submit->toArray());
    }
    
    $this->view->id = $id;
    $this->view->form = $form;
  }
  
  public function actionDelete() {
  	$id = $this->request->getParam('id');
  	$this->bulletinMgr->deleteSubmit($id);
  	return $this->getPlugin('redirector')->redirect(array('action' => 'index'));
  }
}