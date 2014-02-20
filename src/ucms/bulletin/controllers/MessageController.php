<?php

namespace ucms\bulletin\controllers;

class MessageController extends Controller {
  
  public function actionRead() {
  	$locale = $this->request->getParam('locale', $this->locale);
  	$submit_id = $this->request->getParam('submit_id', 0);
  	
  	$future = $this->module->getPlugin('authorizer')->isRole('bulletin.admin');
  	$message = $this->bulletinMgr->getMessage($locale, $submit_id, $future);
  	
  	if ($message === null) {
  		throw new \ultimo\mvc\exceptions\DispatchException("Message with submit_id '{$submit_id}' and locale '{$locale}' does not exist.", 404);
  	}
  	
//  	$this->view->comments = array();
//  	
//  	$viewRenderer = $this->application->getPlugin('viewRenderer');
//  	$viewRenderer->setDisabled(true);
//  	
//  	$commentController = $this->module->getController('Messagecomment');
//  	$commentController->call('index');
//  	$viewRenderer->setDisabled(false);
  	$comments = $this->getPlugin('helper')
                     ->getHelper('Commentor')
                     ->getComments('MessageComment', $submit_id, $locale);
  	

  	$this->view->comments = $comments;
  	$this->view->message = $message;
  }
    
  public function actionIndex() {
  	$locale = $this->request->getParam('locale', $this->locale);
  	$this->view->messages = $this->bulletinMgr->getMessages($locale, array(
  	  'maxDate' => date('Y-m-d H:i:s'),
  	  'count' => 10
  	));
  }
  
  public function actionCreate() {
  	$submit_id = $this->request->getParam('submit_id');
  	$this->view->submit_id = $submit_id;
  	
  	$availableLocales = $this->bulletinMgr->getSubmitAvailableLocales($submit_id, $this->locales);
  	if ($availableLocales === null) {
  		throw new \ultimo\mvc\exceptions\DispatchException("Subject with submit_id '{$submit_id}' does not exist.", 404);
  	}
    sort($availableLocales);
    if (empty($availableLocales)) {
    	$this->view->form = null;
    	return;
    }
    
    
  	$form = $this->module->getPlugin('formBroker')->createForm(
      'message\CreateForm',
  	 $this->request->getParam('form', array()),
      array(
        'availableLocales' => $availableLocales
      )
    );
    
    if ($this->request->isPost()){
    	if ($form->validate()) {
	    	$message = $this->bulletinMgr->create('Message');
	    	$message->submit_id = $submit_id;
	    	$message->locale = $form['locale'];
	    	$message->title = $form['title'];
	      $message->teaser = $form['teaser'];
	      $message->message = $form['message'];
	      $message->save();
	      
	      return $this->getPlugin('redirector')->redirect(array('action' => 'read', 'submit_id' => $message->submit_id, 'locale' => $message->locale));
    	}
    }
    
    $this->view->form = $form;
  }
  
  public function actionUpdate() {
  	$submit_id = $this->request->getParam('submit_id');
  	$locale = $this->request->getParam('locale');

    $availableLocales = array_merge($this->bulletinMgr->getSubmitAvailableLocales($submit_id, $this->locales), array($locale));
    sort($availableLocales);
    $form = $this->module->getPlugin('formBroker')->createForm(
      'message\UpdateForm',
      $this->request->getParam('form', array()),
      array(
        'availableLocales' => $availableLocales
      )
    );

    if ($this->request->isPost()) {
    	if ($form->validate()) {
	      $message = $this->bulletinMgr->get('Message', array('submit_id' => $submit_id, 'locale' => $locale), true);
	      $message->locale = $form['locale'];
	      $message->title = $form['title'];
	      $message->teaser = $form['teaser'];
	      $message->message = $form['message'];
	      $message->save();
      
        return $this->getPlugin('redirector')->redirect(array('action' => 'read', 'submit_id' => $message->submit_id, 'locale' => $message->locale));
    	}
    } else {
	    $message = $this->bulletinMgr->get('Message', array('submit_id' => $submit_id, 'locale' => $locale));
	    
	    if ($message === null) {
	      throw new \ultimo\mvc\exceptions\DispatchException("Message with submit_id '{$submit_id}' and locale '{$locale}' does not exist.", 404);
	    }
	    
	    $form->fromArray($message->toArray());
    }
    
    $this->view->submit_id = $submit_id;
    $this->view->locale = $locale;
    $this->view->form = $form;
  }
  
  public function actionDelete() {
  	$submit_id = $this->request->getParam('submit_id');
    $locale = $this->request->getParam('locale');
    $this->bulletinMgr->deleteMessage($submit_id, $locale);
    return $this->getPlugin('redirector')->redirect(array('action' => 'index', 'locale' => $locale));
  }
}