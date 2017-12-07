<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Mailer\ServiceAdapter\TxMailer;

use Tx\Mailer;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Mailer\ServiceInterface;

/**
 * Class ServiceAdapter
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var Mailer */
    private $mailer;

    /**
     * ServiceAdapter constructor.
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $configuration = $configuration->getData('email');

        $secure = isset($configuration['secure']) ? $configuration['secure'] : null;
        $auth = isset($configuration['auth']) ? $configuration['auth'] : null;

        $this->mailer = new \Tx\Mailer(null);
        $this->mailer->setServer($configuration['host'], $configuration['port'], $secure);

        if ($auth == 'login') {
            $this->mailer->setAuth($configuration['username'], $configuration['password']);
        } elseif ($auth == 'oatuh') {
            $this->mailer->setOAuth($configuration['oauthToken']);
        }

        $this->mailer->setFrom($configuration['from']['name'], $configuration['from']['email']);
    }

    /**
     * Sets the sender.
     *
     * @param string $name
     * @param string $email
     * @return ServiceInterface
     */
    public function setFrom(string $name, string $email) : ServiceInterface
    {
        $this->mailer->setFrom($name, $email);

        return $this;
    }

    /**
     * Sets a recipient.
     *
     * @param string $name
     * @param string $email
     * @return ServiceInterface
     */
    public function addTo(string $name, string $email) : ServiceInterface
    {
        $this->mailer->addTo($name, $email);

        return $this;
    }

    /**
     * Sets a Carbon Copy recipient.
     *
     * @param string $name
     * @param string $email
     * @return ServiceInterface
     */
    public function addCc(string $name, string $email) : ServiceInterface
    {
        $this->mailer->addCc($name, $email);

        return $this;
    }

    /**
     * Sets a Blind Carbon Copy recipient.
     *
     * @param string $name
     * @param string $email
     * @return ServiceInterface
     */
    public function addBcc(string $name, string $email) : ServiceInterface
    {
        $this->mailer->addBcc($name, $email);

        return $this;
    }

    /**
     * Sets the subject.
     *
     * @param string $subject
     * @return ServiceInterface
     */
    public function setSubject(string $subject) : ServiceInterface
    {
        $this->mailer->setSubject($subject);

        return $this;
    }

    /**
     * Sets the body.
     *
     * @param string $body
     * @return ServiceInterface
     */
    public function setBody(string $body) : ServiceInterface
    {
        $this->mailer->setBody($body);

        return $this;
    }

    /**
     * Adds an attachment to the mail.
     *
     * @param string $name
     * @param string $path
     * @return ServiceInterface
     */
    public function addAttachment(string $name, string $path) : ServiceInterface
    {
        $this->mailer->addAttachment($name, $path);
        return $this;
    }

    /**
     * Sends the email.
     *
     * @return bool
     */
    public function send() : bool
    {
        return $this->mailer->send();
    }
}
