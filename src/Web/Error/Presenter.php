<?php
namespace Ytnuk\Web\Error;

use Nette;
use stdClass;
use Symfony;
use Throwable;
use Tracy;
use Ytnuk;

final class Presenter
	extends Ytnuk\Web\Application\Presenter
{

	/**
	 * @var Nette\Application\IPresenter
	 */
	private static $lastPresenter;

	/**
	 * @var Nette\Application\Application
	 */
	private $application;

	/**
	 * @var Tracy\ILogger
	 */
	private $logger;

	/**
	 * @var Nette\Localization\ITranslator
	 */
	private $translator;

	/**
	 * @var int
	 */
	private $code;

	public function __construct(
		Nette\Application\Application $application,
		Tracy\ILogger $logger = NULL,
		Nette\Localization\ITranslator $translator = NULL
	) {
		parent::__construct();
		$this->application = $application;
		$this->logger = $logger;
		$this->translator = $translator;
	}

	public static function onError(Nette\Application\Application $application)
	{
		self::$lastPresenter = $application->getPresenter();
	}

	public function checkRequirements($element)
	{
		try {
			parent::checkRequirements($element);
		} catch (Nette\Application\BadRequestException $ex) {
		};
	}

	/**
	 * @inheritDoc
	 */
	public function loadState(array $params)
	{
		if ( ! $request = $params['request'] ?? $this->application->getRouter()->match($httpRequest = $this->getHttpRequest())) {
			$request = $this->application->getRouter()->match(new Nette\Http\Request(new Nette\Http\UrlScript($httpRequest->getUrl()->getBaseUrl())));
		}
		if ($request) {
			$this->application->onRequest(
				$this->application,
				$request
			);
			$params += $request->getParameters();
		}
		try {
			parent::loadState($params);
		} catch (Nette\Application\BadRequestException $exception) {
			if ( ! $this->web) {
				$this->web = new Ytnuk\Web\Entity;
				$this->web->menu = new Ytnuk\Menu\Entity;
			}
		}
	}

	public function sendPayload()
	{
		$this->getHttpResponse()->setCode(Nette\Http\IResponse::S200_OK);
		$payload = $this->getPayload();
		$payload->redirect = $this->getHttpRequest()->getUrl()->getRelativeUrl();
		$lastPresenter = self::$lastPresenter;
		if ($lastPresenter instanceof Nette\Application\UI\Presenter) {
			try {
				Nette\Bridges\ApplicationLatte\UIRuntime::renderSnippets(
					$lastPresenter,
					new stdClass,
					[]
				);
			} catch (Throwable $e) {
			}
			$lastPayload = $lastPresenter->getPayload();
			if ($lastPayload && isset($lastPayload->snippets) && $snippetId = $this->getSnippetId()) {
				$snippets = array_filter(
					(array) $lastPayload->snippets,
					function (string $snippet) use
					(
						$snippetId
					) {
						return ! Nette\Utils\Strings::startsWith(
							$snippet,
							$this->getSnippetId()
						);
					},
					ARRAY_FILTER_USE_KEY
				);
				array_walk(
					$snippets,
					function (
						$snippet,
						$id
					) use
					(
						$payload
					) {
						$payload->snippets[$id] = $snippet;
					}
				);
			}
		}
		parent::sendPayload();
	}

	public function actionDefault(Throwable $exception)
	{
		if ($exception instanceof Nette\Application\BadRequestException) {
			$code = $exception->getCode();
			if ($this->logger) {
				$this->logger->log($exception->getMessage());
			}
		} else {
			$code = Nette\Http\IResponse::S500_INTERNAL_SERVER_ERROR;
			if ($this->logger) {
				$this->logger->log(
					$exception,
					Tracy\ILogger::EXCEPTION
				);
			}
		}
		if (ob_get_level() && ob_get_length()) {
			$this->setLayout(FALSE);
		}
		$view = $this->getView();
		$this->setView(
			$this->code = $this->translator instanceof Symfony\Component\Translation\TranslatorBagInterface && $this->translator->getCatalogue()->has(
				implode(
					'.',
					[
						'error.message',
						$code,
						'title',
					]
				),
				'web'
			) && $this->translator->getCatalogue()->has(
				implode(
					'.',
					[
						'error.message',
						$code,
						'description',
					]
				),
				'web'
			) ? $code : 0
		);
		if ( ! count($this->formatTemplateFiles())) {
			$this->setView($view);
		}
	}

	public function renderDefault(Throwable $exception)
	{
		$this['web']['menu'][] = $title = implode(
			'.',
			[
				'web.error.message',
				$this->code,
				'title',
			]
		);
		$template = $this->getTemplate();
		if ($template instanceof Nette\Bridges\ApplicationLatte\Template) {
			$template->add(
				'exception',
				$exception
			)->add(
				'code',
				$this->code
			)->add(
				'title',
				$title
			)->add(
				'description',
				implode(
					'.',
					[
						'web.error.message',
						$this->code,
						'description',
					]
				)
			);
		}
	}
}
