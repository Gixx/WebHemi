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

namespace WebHemi\Renderer\Helper;

use WebHemi\Adapter\Renderer\RendererHelperInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;
use WebHemi\Renderer\GetSelectedThemeResourcePathTrait;

/**
 * Class DefinedHelper
 */
class DefinedHelper implements RendererHelperInterface
{
    /** @var string */
    private $templateViewPath;
    /** @var string */
    private $defaultTemplateViewPath;

    use GetSelectedThemeResourcePathTrait;

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 'defined';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return 'defined(string templateFileName) : bool';
    }

    /**
     * Should return a description text.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDescription() : string
    {
        return 'Checks if the given filepath exists in the template\'s path. Use @WebHemi for the default theme and '
            . '@Theme for the actual (custom) theme.';
    }

    /**
     * DefinedHelper constructor.
     *
     * @param ConfigInterface $configuration
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(ConfigInterface $configuration, EnvironmentManager $environmentManager)
    {
        $documentRoot = $environmentManager->getDocumentRoot();
        $selectedTheme = $environmentManager->getSelectedTheme();
        $selectedThemeResourcePath = $this->getSelectedThemeResourcePath(
            $selectedTheme,
            $configuration,
            $environmentManager
        );

        $this->defaultTemplateViewPath = $documentRoot.EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH.'/view';
        $this->templateViewPath = $documentRoot.$selectedThemeResourcePath.'/view';
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return bool
     */
    public function __invoke() : bool
    {
        $fileName = func_get_args()[0];
        $pattern = [
            '@WebHemi',
            '@Theme',
        ];
        $replacement = [
            $this->defaultTemplateViewPath,
            $this->templateViewPath,
        ];

        $fileName = str_replace($pattern, $replacement, $fileName);
        return file_exists($fileName);
    }
}
