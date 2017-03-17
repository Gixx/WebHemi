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

namespace WebHemi\Middleware\Action\Auth;

use Exception;
use WebHemi\Auth\CredentialInterface;
use WebHemi\Auth\Result\Result;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Data\Entity\User\UserEntity;
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
    /** @var AuthInterface */
    private $authAdapter;
    /** @var CredentialInterface */
    private $authCredential;
    /** @var EnvironmentInterface */
    private $environmentManager;
    /** @var PresetInterface */
    private $loginFormPreset;

    /**
     * LoginAction constructor.
     *
     * @param AuthInterface        $authAdapter
     * @param CredentialInterface  $authCredential
     * @param EnvironmentInterface $environmentManager
     * @param PresetInterface      $loginFormPreset
     */
    public function __construct(
        AuthInterface $authAdapter,
        CredentialInterface $authCredential,
        EnvironmentInterface $environmentManager,
        PresetInterface $loginFormPreset
    ) {
        $this->authAdapter = $authAdapter;
        $this->authCredential = $authCredential;
        $this->environmentManager = $environmentManager;
        $this->loginFormPreset = $loginFormPreset;
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
        $form = $this->getLoginForm();

        if ($this->request->getMethod() == 'POST') {
            $postData = $this->request->getParsedBody();
            $this->authCredential->setCredential('username', $postData['login']['identification'] ?? '')
                ->setCredential('password', $postData['login']['password'] ?? '');

            /** @var Result $result */
            $result = $this->authAdapter->authenticate($this->authCredential);

            if (!$result->isValid()) {
                $form = $this->getLoginForm($result->getMessage());

                // load back faild data.
                unset($postData['login']['password']);
                $form->loadData($postData);
            } else {
                /** @var null|UserEntity $userEntity */
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

        return [
            'loginForm' => $form,
        ];
    }

    /**
     * Gets the login form.
     *
     * @param string $customError
     * @return HtmlForm
     */
    private function getLoginForm(string $customError = '') : HtmlForm
    {
        /** @var HtmlForm $form */
        $form = $this->loginFormPreset->getPreset();

        if (!empty($customError)) {
            /** @var ElementInterface[] $elements */
            $elements = $form->getElements();

            /** @var ElementInterface $element */
            foreach ($elements as $element) {
                if ($element->getType() == HtmlElement::HTML_ELEMENT_INPUT_PASSWORD) {
                    $element->setError(AuthInterface::class, $customError);
                    // Since the name attribute didn't change, adding to the form will overwrite the old one.
                    $form->addElement($element);
                    break;
                }
            }
        }

        return $form;
    }
}
