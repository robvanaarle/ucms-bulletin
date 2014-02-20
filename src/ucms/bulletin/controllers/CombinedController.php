<?php

namespace ucms\bulletin\controllers;

class CombinedController extends Controller {
  
  public function actionCreate() {
    $form = $this->module->getPlugin('formBroker')->createForm(
      'combined\CreateForm', $this->request->getParam('form', array('datetime' => date('Y-m-d H:i:s')))
    );
    
    if ($this->request->isPost()){
      if ($form->validate()) {
        $submit = $this->bulletinMgr->create('Submit');
        $submit->label = $form['title'];
        $submit->datetime = $form['datetime'];
        $submit->save();
        
        $message = $this->bulletinMgr->create('Message');
        $message->title = $form['title'];
        $message->teaser = $form['teaser'];
        $message->message = $form['message'];
        $message->locale = $this->locale;
        $message->submit_id = $submit->id;
        $message->save();
        
        return $this->getPlugin('redirector')->redirect(array('action' => 'read', 'controller' => 'message', 'submit_id' => $submit->id));
      }
    }
    
    $this->view->form = $form;
  }
  
  public function actionUpdate() {
    $submit_id = $this->request->getParam('submit_id');
    $locale = $this->locale;

    $form = $this->module->getPlugin('formBroker')->createForm(
      'combined\UpdateForm',
      $this->request->getParam('form', array())
    );

    if ($this->request->isPost()) {
      if ($form->validate()) {
        $submit = $this->bulletinMgr->get('Submit', $submit_id, true);
        $submit->label = $form['title'];
        $submit->datetime = $form['datetime'];
        $submit->save();
        
        $message = $this->bulletinMgr->get('Message', array('submit_id' => $submit_id, 'locale' => $locale), true);
        $message->title = $form['title'];
        $message->teaser = $form['teaser'];
        $message->message = $form['message'];
        $message->save();
      
        return $this->getPlugin('redirector')->redirect(array('action' => 'read', 'controller' => 'message', 'submit_id' => $message->submit_id));
      }
    } else {
      $message = $this->bulletinMgr->getMessage($locale, $submit_id, true);
      //$submit = $this->bulletinMgr->get('Submit', array('id' => $submit_id));
      
      if ($message === null) {
        throw new \ultimo\mvc\exceptions\DispatchException("Message with submit_id '{$submit_id}' and locale '{$locale}' does not exist.", 404);
      }
      
      $form->fromArray(array_merge($message, $message['submit']));
    }
    
    $this->view->images = $this->module->getPlugin('helper')
                         ->getHelper('Visualiser')
                         ->getImages('SubmitImage', $submit_id);
    $this->view->imageForm = $this->module->getPlugin('formBroker')->createForm(
      'image\CreateForm'
    );
    
    $this->view->submit_id = $submit_id;
    $this->view->form = $form;
    
    $this->imageForm = $this->module->getPlugin('formBroker')->createForm(
      'image\CreateForm',
      $this->request->getParam('image', array())
    );
  }
  
  public function actionDelete() {
    $submit_id = $this->request->getParam('submit_id');
    $this->bulletinMgr->deleteMessage($submit_id, $this->locale);
    
    if ($this->bulletinMgr->getMessageCount($submit_id) == 0) {
      $this->bulletinMgr->deleteSubmit($submit_id);
    }
    
    return $this->getPlugin('redirector')->redirect(array('action' => 'index', 'controller' => 'submit'));
  }

}