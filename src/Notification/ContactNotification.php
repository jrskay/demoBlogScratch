<?php 

namespace App\Notification;

use App\Entity\Contact;
use App\Entity\User;
use Twig\Environment;

class ContactNotification
{
	
	/**
 	* @var \Swift_Mailer 
	*/
	private $mailer;

	/**
 	* @var Environment
	*/
	private $renderer;

	function __construct(\Swift_Mailer $mailer, Environment $renderer)
	{
		$this->mailer = $mailer;
		$this->renderer = $renderer;
		// hors d'un cntroller il est possible de faire des injections de dépendance SEULEMENT dans un constructeur
	}

	public function notify(Contact $contact)
	{
		$message = (new \Swift_Message('Nouveau message de ' . $contact->getEmail()))
				->setFrom($contact->getEmail())
				->setTo('samuel.evgform@gmail.com')
				->setReplyTo($contact->getEmail())
				->setBody($this->renderer->render('emails/contact.html.twig', [
					'contact' =>$contact
				]),
					'text/html'); // contenu du mail, corps du message déclaréda,s le template emails/contact.html.twig				
		$this->mailer->send($message);
	}

	public function registerNotify(User $user)
	{
		$message = (new \Swift_Message('Nouveau message de ' . $user->getEmail()))
				->setFrom($user->getEmail())
				->setTo('samuel.evgform@gmail.com')
				->setReplyTo($user->getEmail())
				->setBody($this->renderer->render('emails/register.html.twig', [
					'user' =>$user
				]),
					'text/html'); // contenu du mail, corps du message déclaréda,s le template emails/contact.html.twig				
		$this->mailer->send($message);
	}




}