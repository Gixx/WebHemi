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

namespace WebHemi\Application;

use Exception;
use WebHemi\Environment\ServiceInterface as EnvironmentManager;
use WebHemi\Session\ServiceInterface as SessionManager;

/**
 * Class Progress
 */
class Progress
{
    /** @var EnvironmentManager */
    private $environmentManager;
    /** @var string */
    private $sessionId;
    /** @var string */
    private $callerName;
    /** @var string */
    private $progressId;
    /** @var int */
    private $totalSteps;
    /** @var int */
    private $currentStep;

    /**
     * Progress constructor.
     *
     * @param EnvironmentManager $environmentManager
     * @param SessionManager $sessionManager
     */
    public function __construct(EnvironmentManager $environmentManager, SessionManager $sessionManager)
    {
        $this->environmentManager = $environmentManager;
        $this->sessionId = $sessionManager->getSessionId();
    }

    /**
     * Starts the progress.
     *
     * @param int    $totalSteps
     * @param string $callerName
     */
    public function start(int $totalSteps, string $callerName = null) : void
    {
        $this->callerName = $callerName ?? $this->getCallerName();
        $this->totalSteps = $totalSteps;
        $this->currentStep = 0;
        $this->progressId = md5($this->sessionId).'_'.$this->callerName;

        $this->next();
    }

    /**
     * Gets the Class name where the progress was called from.
     *
     * @return string
     */
    private function getCallerName() : string
    {
        if (!isset($this->callerName)) {
            try {
                throw new Exception('Get Trace');
            } catch (Exception $exception) {
                $trace = $exception->getTrace()[1]['file'] ?? rand(0, 10000);
                $trace = explode('/', $trace);
                $this->callerName = str_replace('.php', '', array_pop($trace));
            }
        }

        return $this->callerName;
    }

    /**
     * Increments the progress and writes to file.
     */
    public function next() : void
    {
        $handler = fopen(
            $this->environmentManager->getApplicationRoot().'/data/progress/'.$this->progressId.'.json',
            'w'
        );

        if ($handler) {
            $data = [
                'total' => $this->totalSteps,
                'current' => $this->currentStep
            ];
            fwrite($handler, json_encode($data));
            fclose($handler);

            $this->currentStep++;
        }
    }

    /**
     * Returns the progress identifier.
     *
     * @return string
     */
    public function getProgressId() : string
    {
        return $this->progressId;
    }
}
