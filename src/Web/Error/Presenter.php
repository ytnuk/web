<?php
namespace Ytnuk\Web\Error;

use Exception;
use Nette;
use stdClass;
use Symfony;
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
	 * @var Nette\Localization\ITranslator
	 */
	private $translator;

	/**
	 * @var Tracy\ILogger
	 */
	private $logger;

	/**
	 * @var int
	 */
	private $code;

	public function __construct(
		Nette\Application\Application $application,
		Nette\Localization\ITranslator $translator,
		Tracy\ILogger $logger = NULL
	) {
		parent::__construct();
		$this->application = $application;
		$this->translator = $translator;
		$this->logger = $logger;
	}

	public static function onError(Nette\Application\Application $application)
	{
		self::$lastPresenter = $application->getPresenter();
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
		parent::loadState($params);
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
			} catch (Exception $e) {
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

	protected function startup()
	{
		try {
			parent::startup();
		} catch (Nette\Application\BadRequestException $exception) {
			$this->web = new Ytnuk\Web\Entity;
			$this->web->menu = new Ytnuk\Menu\Entity;
		}
	}

	public function actionDefault(
		Exception $exception
	) {
		if ($exception instanceof Nette\Application\BadRequestException) {
			$code = $exception->getCode();
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

	public function renderDefault(Exception $exception)
	{
		$this[Ytnuk\Web\Control::NAME][Ytnuk\Menu\Control::NAME][] = $title = implode(
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
