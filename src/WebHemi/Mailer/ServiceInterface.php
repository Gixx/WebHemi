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

namespace WebHemi\Mailer;

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * MailAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration);

    /**
     * Sets the sender.
     *
     * @param string $name
     * @param string $email
     * @return ServiceInterface
     */
    public function setFrom(string $name, string $email) : ServiceInterface;

    /**
     * Sets a recipient.
     *
     * @param string $name
     * @param string $email
     * @return ServiceInterface
     */
    public function addTo(string $name, string $email) : ServiceInterface;

    /**
     * Sets a Carbon Copy recipient.
     *
     * @param string $name
     * @param string $email
     * @return ServiceInterface
     */
    public function addCc(string $name, string $email) : ServiceInterface;

    /**
     * Sets a Blind Carbon Copy recipient.
     *
     * @param string $name
     * @param string $email
     * @return ServiceInterface
     */
    public function addBcc(string $name, string $email) : ServiceInterface;

    /**
     * Sets the subject.
     *
     * @param string $subject
     * @return ServiceInterface
     */
    public function setSubject(string $subject) : ServiceInterface;

    /**
     * Sets the body.
     *
     * @param string $body
     * @return ServiceInterface
     */
    public function setBody(string $body) : ServiceInterface;

    /**
     * Adds an attachment to the mail.
     *
     * @param string $name
     * @param string $path
     * @return ServiceInterface
     */
    public function addAttachment(string $name, string $path) : ServiceInterface;

    /**
     * Sends the email.
     *
     * @return bool
     */
    public function send() : bool;
}
