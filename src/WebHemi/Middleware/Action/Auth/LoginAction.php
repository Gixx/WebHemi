<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Middleware\Action\Auth;

use Exception;
use InvalidArgumentException;
use WebHemi\Auth\CredentialInterface;
use WebHemi\Auth\Result\Result;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\CSRF\ServiceInterface as CSRFInterface;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Form\Element\Html\HtmlElement;
use WebHemi\Form\ElementInterface;
use WebHemi\Form\PresetInterface;
use WebHemi\Form\ServiceAdapter\Base\ServiceAdapter as HtmlForm;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class LoginAction.
 */
class LoginAction extends AbstractMiddlewareAction
{
    /**
     * @var AuthInterface
     */
    private $authAdapter;
    /**
     * @var CredentialInterface
     */
    private $authCredential;
    /**
     * @var EnvironmentInterface
     */
    private $environmentManager;
    /**
     * @var PresetInterface
     */
    private $loginFormPreset;
    /**
     * @var CSRFInterface
     */
    private $csrfAdapter;

    /**
     * LoginAction constructor.
     *
     * @param AuthInterface        $authAdapter
     * @param CredentialInterface  $authCredential
     * @param EnvironmentInterface $environmentManager
     * @param PresetInterface      $loginFormPreset
     * @param CSRFInterface        $csrfAdapter
     */
    public function __construct(
        AuthInterface $authAdapter,
        CredentialInterface $authCredential,
        EnvironmentInterface $environmentManager,
        PresetInterface $loginFormPreset,
        CSRFInterface $csrfAdapter
    ) {
        $this->authAdapter = $authAdapter;
        $this->authCredential = $authCredential;
        $this->environmentManager = $environmentManager;
        $this->loginFormPreset = $loginFormPreset;
        $this->csrfAdapter = $csrfAdapter;
    }

    /**
     * Gets template map name or template file path.
     * This middleware does not have any output.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'admin-login';
    }

    /**
     * Gets template data.
     *
     * @throws Exception
     * @return array
     */
    public function getTemplateData() : array
    {
        /**
         * @var HtmlForm $form
         */
        $form = $this->loginFormPreset->getPreset();

        if ($this->request->getMethod() == 'POST') {
            /** @var array $postData */
            $postData = $this->request->getParsedBody();

            if (!is_array($postData)) {
                throw new InvalidArgumentException('Post data must be an array!');
            }

            $form->loadData($postData);

            if ($form->validate()) {
                $this->authCredential->setCredential('username', $postData['login']['identification'] ?? '')
                    ->setCredential('password', $postData['login']['password'] ?? '');

                /**
                 * @var Result $result
                 */
                $result = $this->authAdapter->authenticate($this->authCredential);

                if (!$result->isValid()) {
                    $passwordElement = $form->getElement('password');
                    $passwordElement->setValues(['']);
                    $passwordElement->setError(AuthInterface::class, $result->getMessage());

                    //$form = $this->getLoginForm($result->getMessage());

                    // load back faild data.
                    $form->loadData($postData);
                } else {
                    /**
                     * @var null|UserEntity $userEntity
                     */
                    $userEntity = $this->authAdapter->getIdentity();

                    if ($userEntity instanceof UserEntity) {
                        $this->authAdapter->setIdentity($userEntity);
                    }

                    // Don't redirect upon Ajax request
                    if (!$this->request->isXmlHttpRequest()) {
                        $url = 'http'.($this->environmentManager->isSecuredApplication() ? 's' : '').'://'.
                            $this->environmentManager->getApplicationDomain().
                            $this->environmentManager->getSelectedApplicationUri();

                        $this->response = $this->response
                            ->withStatus(302, 'Found')
                            ->withHeader('Location', $url);
                    }
                }
            }
        }

        $csrfElement = $form->getElement(CSRFInterface::SESSION_KEY);
        $csrfElement->setValues([$this->csrfAdapter->generate()]);

        return [
            'loginForm' => $form
        ];
    }
}
