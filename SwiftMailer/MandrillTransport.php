<?php

namespace Accord\MandrillSwiftMailerBundle\SwiftMailer;

use Mandrill;

use \Swift_Events_EventDispatcher;
use \Swift_Events_EventListener;
use \Swift_Events_SendEvent;
use \Swift_Mime_Message;
use \Swift_Transport;
use \Swift_Attachment;

class MandrillTransport implements Swift_Transport
{
    /**
     * @type Swift_Events_EventDispatcher
     */
    protected $dispatcher;

    /** @var string|null */
    protected $apiKey;
    
    /**
     * @param Swift_Events_EventDispatcher $dispatcher
     */
    public function __construct(Swift_Events_EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->apiKey = null;
    }
       
    /**
     * Not used
     */
    public function isStarted()
    {
        return false;
    }
    
    /**
     * Not used
     */
    public function start()
    {
    }

    /**
     * Not used
     */
    public function stop()
    {
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return Mandrill
     * @throws \Swift_TransportException
     */
    protected function createMandrill()
    {
        if($this->apiKey === null) throw new \Swift_TransportException('Cannot create instance of \Mandrill while API key is NULL');
        return new Mandrill($this->apiKey);
    }

    /**
     * @param Swift_Mime_Message $message
     * @param null $failedRecipients
     * @return int Number of messages sent
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        if ($event = $this->dispatcher->createSendEvent($this, $message)) {
            $this->dispatcher->dispatchEvent($event, 'beforeSendPerformed');
            if ($event->bubbleCancelled()) {
                return 0;
            }
        }

        $sendCount = 0;

        $mandrillMessage = $this->getMandrillMessage($message);

        $mandrill = $this->createMandrill();

        try {
            $result = $mandrill->messages->send($mandrillMessage);

            foreach ($result as $item) {
                if ($item['status'] == 'sent') {
                    $sendCount++;
                } else {
                    $failedRecipients[] = $item['email'];
                }
            }

        } catch (\Exception $e) {
        }

        if ($event) {
            if ($sendCount > 0) {
                $event->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
            } else {
                $event->setResult(Swift_Events_SendEvent::RESULT_FAILED);
            }
            $this->dispatcher->dispatchEvent($event, 'sendPerformed');
        }

        return $sendCount;
    }
  
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->dispatcher->bindEventListener($plugin);
    }
    
    /**
     * So far sends only basic html email and attachments
     * 
     * https://mandrillapp.com/api/docs/messages.php.html#method-send
     * 
     * @param Swift_Mime_Message $message
     * @return array Mandrill Send Message
     */
    public function getMandrillMessage(Swift_Mime_Message $message)
    {
        $fromAddresses = $message->getFrom();
        $fromEmails = array_keys($fromAddresses);
        
        $toAddresses = $message->getTo();
        $ccAddresses = $message->getCc() ? $message->getCc() : [];
        $bccAddresses = $message->getBcc() ? $message->getBcc() : [];
        $replyToAddresses = $message->getReplyTo() ? $message->getReplyTo() : [];
        
        $to = array();
        $attachments = array();
        $headers = array();

        foreach ($toAddresses as $toEmail => $toName) {
            $to[] = array(
                'email' => $toEmail,
                'name'  => $toName,
                'type'  => 'to'
            );
        }

        foreach ($replyToAddresses as $replyToEmail => $replyToName) {
            if($replyToName){
                $headers['Reply-To'] = sprintf('%s <%s>', $replyToEmail, $replyToName);
            }
            else{
                $headers['Reply-To'] = $replyToEmail;
            }
        }

        foreach ($ccAddresses as $ccEmail => $ccName) {
            $to[] = array(
                'email' => $ccEmail,
                'name'  => $ccName,
                'type'  => 'cc'
            );
        }

        foreach ($bccAddresses as $bccEmail => $bccName) {
            $to[] = array(
                'email' => $bccEmail,
                'name'  => $bccName,
                'type'  => 'bcc'
            );
        }

        foreach ($message->getChildren() as $child) {
            if ($child instanceof Swift_Attachment) {
                $attachments[] = array(
                    'type'    => $child->getContentType(),
                    'name'    => $child->getFilename(),
                    'content' => base64_encode($child->getBody())
                );
            }
        }

        $mandrillMessage = array(
            'html'       => $message->getBody(),
            'subject'    => $message->getSubject(),
            'from_email' => $fromEmails[0],
            'from_name'  => $fromAddresses[$fromEmails[0]],
            'to'         => $to,
            'headers'    => $headers
        );
        
        if (count($attachments) > 0) {
            $mandrillMessage['attachments'] = $attachments;
        }
		
        return $mandrillMessage;        
    }

    /**
     * @param Swift_Mime_Message $message
     * @param string $mime_type
     * @return null|\Swift_Mime_MimeEntity
     */
    protected function getMIMEPart(Swift_Mime_Message $message, $mime_type)
    {
        $htmlPart = null;
        foreach ($message->getChildren() as $part) {
            if (strpos($part->getContentType(), 'text/html') === 0) {
                $htmlPart = $part;
            }
        }
        return $htmlPart;
    }
}
