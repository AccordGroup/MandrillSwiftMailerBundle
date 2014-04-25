<?php

namespace Accord\MandrillSwiftMailerBundle\SwiftMailer;

use Mandrill;

use \Swift_Events_EventDispatcher;
use \Swift_Events_EventListener;
use \Swift_Events_SendEvent;
use \Swift_Mime_HeaderSet;
use \Swift_Mime_Message;
use \Swift_Transport;

class MandrillTransport implements Swift_Transport {
    
    protected $started  = false;

    protected $dispatcher;
	
	private $mandrill;

	/**
	 * @param Swift_Events_EventDispatcher $dispatcher
	 * @param type $apiKey
	 */
    public function __construct(Swift_Events_EventDispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }
       
    public function isStarted()
    {
        return $this->started;
    }
    
    public function start()
    {
		$this->started = true;

    }

    public function stop()
    {
        $this->started = false;
    }

	public function setApiKey($apiKey){
		$this->mandrill = new Mandrill($apiKey);
	}
	
    /**
     * @param Swift_Mime_Message $message
     * @return int Number of messages sent
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = NULL)
    {        
        	
		if ($event = $this->dispatcher->createSendEvent($this, $message)) {
            $this->dispatcher->dispatchEvent($event, 'beforeSendPerformed');
            if ($event->bubbleCancelled()) {
                return 0;
            }
        }
		
        $send_count = 0;

		$mandrillMessageData = $this->getMandrillMessage($message);
		
        try {
            $result = $this->mandrill->messages->send($mandrillMessageData);
			
			foreach($result as $item){
				if($item['status'] == 'sent'){
					$send_count++;
				}else{
					$failedRecipients[] = $item['email'];
				}
			}
			
        }
        catch (Exception $e) {
			
        }

        if ($event) {
            if ($send_count > 0) {
                $event->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
            }
            else {
                $event->setResult(Swift_Events_SendEvent::RESULT_FAILED);
            }

            $this->dispatcher->dispatchEvent($event, 'sendPerformed');
        }

        return $send_count;
    }
  
    public function registerPlugin(Swift_Events_EventListener $plugin) {
        $this->dispatcher->bindEventListener($plugin);
    } 

	/**
	 * So far sends only basic html email
	 * TODO attachments, images etc
	 * 
	 * https://mandrillapp.com/api/docs/messages.php.html#method-send
	 * 
	 * @param Swift_Mime_Message $message
	 * @return array Mandrill Send Message
	 */
    protected function getMandrillMessage(Swift_Mime_Message $message)
    {
		
		$fromAddresses = $message->getFrom();
		$formEmails = array_keys($fromAddresses);
		$toAddresses = $message->getTo();		
		$to = array();
		
		foreach($toAddresses as $toEmail => $toName){
			$to[] = array(
				'email' => $toEmail,
				'name' => $toName,
				'type' => 'to'
			);
		}
		
		$mandrillMessage = array(
			'html' => $message->getBody(),
			'subject' => $message->getSubject(),
			'from_email' => $formEmails[0],
			'from_name' => $fromAddresses[$formEmails[0]],
			'to' => $to
		);
		
        return $mandrillMessage;        
    }

    /**
     * @param Swift_Mime_Message $message
     * @param string $mime_type
     * @return Swift_Mime_MimePart
     */
    protected function getMIMEPart(Swift_Mime_Message $message, $mime_type) {
        $html_part = NULL;
        foreach ($message->getChildren() as $part) {
            if (strpos($part->getContentType(), 'text/html') === 0)
                $html_part = $part;
        }
        return $html_part;
    }
}