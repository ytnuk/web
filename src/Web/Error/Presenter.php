<?php
namespace Ytnuk\Web\Error;

use Exception;
use Nette;
use Tracy;
use Ytnuk;

final
class Presenter
	extends Ytnuk\Web\Application\Presenter
{

	/**
	 * @var Nette\Application\Application
	 */
	private $application;

	/**
	 * @var Tracy\ILogger
	 */
	private $logger;

	/**
	 * @var int
	 */
	private $code = Nette\Http\IResponse::S404_NOT_FOUND;

	public function __construct(
		Nette\Application\Application $application,
		Tracy\ILogger $logger = NULL
	) {
		parent::__construct();
		$this->application = $application;
		$this->logger = $logger;
	}

	/**
	 * @inheritDoc
	 */
	public function loadState(array $params)
	{
		if ( ! $request = $params['request'] ?? $this->application->getRouter()->match($this->getHttpRequest())) {
			$request = $this->application->getRouter()->match(new Nette\Http\Request(new Nette\Http\UrlScript($this->getHttpRequest()->getUrl()->getBaseUrl())));
		}
		if ($request) {
			$this->application->onRequest(
				$this->application,
				$request
			);
			$params += $request->getParameters();
		}
		parent::loadState($params);
	}

	public function sendPayload()
	{
		$this->getHttpResponse()->setCode(Nette\Http\IResponse::S200_OK);
		$payload = $this->getPayload();
		$payload->redirect = $this->getHttpRequest()->getUrl()->getRelativeUrl();
		parent::sendPayload();
	}

	public function actionDefault(
		Exception $exception
	) {
		$code = $exception->getCode();
		if ( ! $exception instanceof Nette\Application\BadRequestException) {
			$code = Nette\Http\IResponse::S500_INTERNAL_SERVER_ERROR;
			if ($this->logger) {
				$this->logger->log(
					$exception,
					Tracy\ILogger::EXCEPTION
				);
			}
		}
		$view = $this->getView();
		$this->setView($this->code = $code);
		if ( ! count($this->formatTemplateFiles())) {
			$this->setView($view);
		}
	}

	public function renderDefault(Exception $exception)
	{
		$this[Ytnuk\Web\Control::NAME][Ytnuk\Menu\Control::NAME][] = 'web.error.presenter.title.' . $this->view;
		$template = $this->getTemplate();
		if ($template instanceof Nette\Bridges\ApplicationLatte\Template) {
			$template->add(
				'exception',
				$exception
			)->add(
				'code',
				$this->code
			);
		}
	}
}
