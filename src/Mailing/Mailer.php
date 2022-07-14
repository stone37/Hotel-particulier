<?php

namespace App\Mailing;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Twig\Environment;

class Mailer
{
    private Environment $twig;
    private MailerInterface $mailer;
    private $dkimKey;

    public function __construct(
        Environment $twig,
        MailerInterface $mailer,
        ?string $dkimKey = null
    ) {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->dkimKey = $dkimKey;
    }

    /**
     * @param string $template
     * @param array $data
     * @return Email
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function createEmail(string $template, array $data = []): Email
    {
        $this->twig->addGlobal('format', 'html');
        $html = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.html.twig']));
        $this->twig->addGlobal('format', 'text');
        $text = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.text.twig']));

        return (new Email())
            ->from('noreply@oblackmarket.com')
            ->html($html)
            ->text($text);
    }

    /**
     * @param Email $email

    public function send(Email $email): void
    {
        $this->enqueue->enqueue(self::class, 'sendNow', [$email]);
    }*/

    /**
     * @param Email $email
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendNow(Email $email): void
    {
        if ($this->dkimKey) {
            $dkimSigner = new DkimSigner("file://{$this->dkimKey}", 'hostel.com', 'default');
            // On signe un message en attendant le fix https://github.com/symfony/symfony/issues/40131
            $message = new Message($email->getPreparedHeaders(), $email->getBody());
            $email = $dkimSigner->sign($message, []);
        }

        $this->mailer->send($email);
    }
}
