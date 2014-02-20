<?php

namespace ucms\bulletin\managers;

class BulletinManager extends \ultimo\orm\Manager {
  protected function init() {
    $this->registerModelNames(array('Message', 'Submit', 'MessageComment', 'User', 'SubmitImage'));
  }
  
  public function getSubmits($locale=null) {
  	$query = $this->selectAssoc('Submit')
  	              ->order('datetime', 'DESC');
  	
  	$params = array();
  	if ($locale !== null) {
  	  $params[':locale'] = $locale;
  	  $query->with('@messages', '@messages.locale = :locale')
  	        ->where('@messages.locale IS NOT NULL');
  	}
  	
  	return $query->fetch($params);
  }
  
  public function getSubmit($id) {
  	return $this->selectAssoc('Submit')
  	            ->with('@messages')
  	            ->where('@id = :id')
  	            ->order('@messages.locale')
  	            ->fetchFirst(array(
  	              ':id' => $id
  	            ));
  }
  
  public function deleteSubmit($id) {
  	return $this->selectAssoc('Submit')
                ->with('@messages')
                ->with('@messages.comments')
                ->where('@id = :id')
                ->delete(array(
                  ':id' => $id
                ));
  }
  
  public function deleteMessage($submit_id, $locale) {
    return $this->selectAssoc('Message')
                ->with('@comments')
                ->where('@locale = :locale')
                ->where('@submit_id = :submit_id')
                ->delete(array(
                  ':submit_id' => $submit_id,
                  ':locale' => $locale
                ));
  }
  
  public function getMessages($locale, array $options = array()) {
    $options = array_merge(array(
      'offset' => 0,
      'count' => self::MAX_ROWCOUNT,
      'order' => 'DESC',
      'minDate' => null,
      'maxDate' => null
    ), $options);
    
    extract($options);
    
    $localeObj = new \ultimo\util\locale\Locale($locale);
    $language = $localeObj->getLanguage();
    
  	$params = array(':locale' => $locale, ':language' => $language);
  	
  	$query = $this->selectAssoc('Message')
	  	            ->with('@submit')
                  ->with('@submit.images')
	  	            ->where('@locale = :locale OR @locale = :language')
	  	            ->limit($offset, $count)
	  	            ->order('@submit.datetime', $order);
	  	            
	  if ($maxDate !== null) {
	    $params[':maxDate'] = $maxDate;
	    $query->where('@submit.datetime < :maxDate');
	  }
	  
	  if ($minDate !== null) {
      $params[':minDate'] = $minDate;
      $query->where('@submit.datetime >= :minDate');
    }
	  
	  // filter double entries, because message are present in both locale and
	  // language
	  $messages = array();
	  foreach ($query->fetch($params) as $message) {
	    $submitId = $message['submit_id'];
	    if (!isset($messages[$submitId]) || $message['locale'] == $locale) {
	      $messages[$submitId] = $message;
	    }
	  }
	  
	  return $messages;
  }
  
  public function getMessageCount($submit_id) {
    return $this->selectAssoc('Message')
                ->where('@submit_id = :submit_id')
                ->count(array(':submit_id' => $submit_id));
  }
  
  public function getMessage($locale, $submit_id, $future=false) {
  	$params = array(':locale' => $locale, ':submit_id' => $submit_id);
  	
  	$query = $this->selectAssoc('Message')
  	              ->calcFoundRows('@comment_count')
                  ->with('@submit')
                  ->with('@submit.images')
                  ->where('@locale = :locale')
                  ->where('@submit_id = :submit_id');
    
    if (!$future) {
      $params[':now'] = date("Y-m-d H:i:s");
      $query->where('@submit.datetime < :now');
    }
    
    return $query->fetchFirst($params);
  }
  
  public function getSubmitLocales($submit_id) {
  	$subject = $this->selectAssoc('Submit')
  	                ->with('@messages')
  	                ->where('@id = :submit_id')
  	                ->fetchFirst(array(
			                ':submit_id' => $submit_id
			              ));
		if ($subject === null) {
			return null;
		}
		
	  $locales = array();
		foreach ($subject['messages'] as $message) {
			$locales[] = $message['locale'];
		}
		return $locales;
  }
  
  public function getSubmitAvailableLocales($submit_id, array $locales) {
  	$submitLocales = $this->getSubmitLocales($submit_id);
  	if ($submitLocales === null) {
  		return null;
  	}
  	
  	return array_diff($locales, $submitLocales);
  }
  
  public function getMessageCountPerMonth($year, $locale) {
    $params = array(
      ':locale' => $locale,
      ':minDateTime' => "{$year}-1-1 0:00:00",
      ':maxDateTime' => "{$year}-12-31 23:59:59"
    );
    
    $submits = $this->selectAssoc('Submit')
                    ->with('@messages', '', false)
                    ->alias('MONTH(@datetime)', '@month')
                    ->alias('COUNT(@messages.locale)', '@message_count')
                    ->where('@messages.locale = :locale')
                    ->where('@datetime >= :minDateTime')
                    ->where('@datetime <= :maxDateTime')
                    ->groupBy('@month')
                    ->order('@month')
                    ->fetch($params);
    
    $messageCounts = array();
    for ($i=1; $i<=12; $i++) {
      $messageCounts[$i] = 0;
    }
    
    foreach ($submits as $submit) {
      $messageCounts[$submit['month']] = $submit['message_count'];
    }
    return $messageCounts;
    
  }
}