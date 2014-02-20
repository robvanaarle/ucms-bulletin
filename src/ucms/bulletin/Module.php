<?php

namespace ucms\bulletin;

class Module extends \ultimo\mvc\Module implements \ultimo\security\mvc\AuthorizedModule {
  protected function init() {
    $this->setAbstract(true);
    $this->addPartial($this->application->getModule('\ucms\commentor'));
    $this->addPartial($this->application->getModule('\ucms\visualiser'));
  }
  
  public function getAcl() {
    $acl = new \ultimo\security\Acl();
    $acl->addRole('bulletin.guest');
    $acl->addRole('bulletin.member', array('bulletin.guest'));
    $acl->addRole('bulletin.admin', array('bulletin.member'));
    
    $acl->allow('bulletin.guest', array('message.index', 'message.read', 'archive.yearindex', 'archive.monthindex', 'archive.dayindex'));
    $acl->allow('bulletin.member', 'messagecomment.create');
    $acl->allow('bulletin.member', array('messagecomment.update', 'messagecomment.delete'), array($this, 'isMessageCommentModifyAllowed'));
    $acl->allow('bulletin.admin');
    return $acl;
  }
  
  public function isMessageCommentModifyAllowed($role, $privilege, $params) {
    
    
  	if (is_array($params) && isset($params['commentor_id']) && isset($params['user_id'])) {
  		return $params['commentor_id'] == $params['user_id'];
  	}
  	// Seems illogical, but the dispatcher does not provide params, so the
  	// update and delete actions have to call this again with params. In order
  	// to reach these action, we have to allow it.
  	return true;
  }
}