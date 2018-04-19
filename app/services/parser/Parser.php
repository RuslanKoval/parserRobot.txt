<?php

namespace app\services\parser;

use app\helpers\UrlHelper;

class Parser
{
	// default encoding
	const DEFAULT_ENCODING = 'UTF-8';

	// directives
	const DIRECTIVE_NOINDEX = 'noindex';
	const DIRECTIVE_ALLOW = 'allow';
	const DIRECTIVE_DISALLOW = 'disallow';
	const DIRECTIVE_HOST = 'host';
	const DIRECTIVE_SITEMAP = 'sitemap';
	const DIRECTIVE_USERAGENT = 'user-agent';
	const DIRECTIVE_CRAWL_DELAY = 'crawl-delay';
	const DIRECTIVE_CLEAN_PARAM = 'clean-param';

	//default user-agent
	const USER_AGENT_ALL = '*';

	const MAX_FILE_SIZE = '32768';

	private $content = '';

	private $rules = [];

	private $currentDirective;

	private $userAgentsBuffer = [];

	private $host = false;

	private $sitemap = false;

	private $url;

	private $fileSize;

	private $statusCode;


    /**
     * Parser constructor.
     * @param $url
     * @param string $encoding
     */
    public function __construct($url, $encoding = self::DEFAULT_ENCODING)
	{
        $headers = UrlHelper::getHeaders($url);
        $this->statusCode = UrlHelper::getStatusCode($headers);
        $this->url = $url;


        if($headers && preg_match("|200|", $headers[0])) {
            $this->content = file($url);

            $this->prepareRules();
        }

	}


    /**
     * @param null $userAgent
     * @return array|mixed
     */
    public function getRules($userAgent = NULL)
	{
		if (is_null($userAgent)) {
			//return all rules
			return $this->rules;
		}
		else {
			$userAgent = mb_strtolower($userAgent);
			if (isset($this->rules[$userAgent])) {
				return $this->rules[$userAgent];
			}
			else {
				return [];
			}
		}
	}

	/**
	 * Get sitemaps links.
	 * Sitemap always relates to all user-agents and return in rules with user-agent "*"
	 *
	 * @return array
	 */
	public function getSitemaps()
	{
		$rules = $this->getRules(self::USER_AGENT_ALL);
		if (!empty($rules[self::DIRECTIVE_SITEMAP])) {
			return $rules[self::DIRECTIVE_SITEMAP];
		}

		return [];
	}


    /**
     * @return array|bool|string
     */
    public function getContent()
	{
		return $this->content;
	}

	/**
	 * Return array of supported directives
	 *
	 * @return array
	 */
	private function getAllowedDirectives()
	{
		return [
			self::DIRECTIVE_NOINDEX,
			self::DIRECTIVE_ALLOW,
			self::DIRECTIVE_DISALLOW,
			self::DIRECTIVE_HOST,
			self::DIRECTIVE_SITEMAP,
			self::DIRECTIVE_USERAGENT,
			self::DIRECTIVE_CRAWL_DELAY,
			self::DIRECTIVE_CLEAN_PARAM,
		];
	}

	/**
	 * Parse rules
	 *
	 * @return void
	 */
	private function prepareRules()
	{

	    if(!$this->content)
	        return false;

		foreach ($this->content as $row) {
			$row = preg_replace('/#.*/', '', $row);
			$parts = explode(':', $row, 2);
			if (count($parts) < 2) {
				continue;
			}

			$directive = trim(strtolower($parts[0]));
			$value = trim($parts[1]);

			if (!in_array($directive, $this->getAllowedDirectives()) || !$value) {
				continue;
			}

			$this->handleDirective($directive, $value);
		}

		$this->removeDuplicates();
	}

	/**
	 * Remove duplicates rules
	 * @return void
	 */
	private function removeDuplicates()
	{
		foreach ($this->rules as $userAgent => $rules) {
			foreach ($this->rules[$userAgent] as $directive => $value) {
				if (is_array($this->rules[$userAgent][$directive])) {
					$this->rules[$userAgent][$directive] = array_values(array_unique($this->rules[$userAgent][$directive]));
				}
			}
		}
	}

	/**
	 * Handle directive with value
	 * Assign value to directive
	 *
	 * @param string $directive
	 * @param string $value
	 * @return void
	 */
	private function handleDirective($directive, $value)
	{
		switch ($directive) {
			case self::DIRECTIVE_USERAGENT:
				if ($this->currentDirective != self::DIRECTIVE_USERAGENT) {
					$this->userAgentsBuffer = [];
				}

				$userAgent = strtolower($value);
				$this->userAgentsBuffer[] = $userAgent;
				$this->currentDirective = $directive;

				if (!isset($this->rules[$userAgent])) {
					$this->rules[$userAgent] = [];
				}

				break;

			case self::DIRECTIVE_DISALLOW:
				$this->currentDirective = $directive;

				foreach ($this->userAgentsBuffer as $userAgent) {
					$this->rules[$userAgent][self::DIRECTIVE_DISALLOW][] = $value;
				}

				break;
			case self::DIRECTIVE_CRAWL_DELAY:
				$this->currentDirective = $directive;

				foreach ($this->userAgentsBuffer as $userAgent) {
					$this->rules[$userAgent][self::DIRECTIVE_CRAWL_DELAY] = (double)$value;
				}

				break;

			case self::DIRECTIVE_SITEMAP:
				$this->currentDirective = $directive;

				$this->rules[self::USER_AGENT_ALL][self::DIRECTIVE_SITEMAP][] = $value;

				break;

			case self::DIRECTIVE_HOST:
				$this->currentDirective = $directive;

				if (empty($this->rules[self::USER_AGENT_ALL][self::DIRECTIVE_HOST])) {
				    $this->host[] = $value;
					$this->rules[self::USER_AGENT_ALL][self::DIRECTIVE_HOST] = $value;
				}

				break;

			default:
				$this->currentDirective = $directive;

				foreach ($this->userAgentsBuffer as $userAgent) {
					$this->rules[$userAgent][$this->currentDirective][] = $value;
				}

				break;
		}
	}

    /**
     * @return string
     */
    public function fileRecommendation()
    {
        if ($this->getContent()) {
            return 'Доработки не требуются';
        }

        return 'Программист: Создать файл robots.txt и разместить его на сайте.';
    }

    /**
     * @return string
     */
    public function fileError()
    {
        if($this->getContent()) {
            return 'Файл robots.txt присутствует';
        }

        return 'Файл robots.txt отсутствует';
    }

    /**
     * @return string
     */
    public function hostRecommendation()
    {
        if ($this->getHostDirective()) {
            return 'Доработки не требуются';
        }

        return 'Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.';
    }

    /**
     * @return string
     */
    public function hostError()
    {
        if($this->getHostDirective()) {
            return 'Директива Host указана';
        }

        return 'В файле robots.txt не указана директива Host';
    }

    /**
     * @return string
     */
    public function hostCountRecommendation()
    {

        if($this->getHostDirective() && count($this->getHostDirective()) == 1) {
            return 'Доработки не требуются';
        }

        return 'Программист: Директива Host должна быть указана в файле толоко 1 раз. Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую основному зеркалу сайта';
    }

    /**
     * @return string
     */
    public function hostCountError()
    {
        if($this->getHostDirective() && count($this->getHostDirective()) == 1) {
            return 'В файле прописана 1 директива Host';
        }

        return 'В файле прописано несколько директив Host';
    }


    /**
     * @return string
     */
    public function sizeError()
    {

        if($this->checkFileSize()) {
            return "Размер файла robots.txt составляет {$this->getFileSize()} байта, что находится в пределах допустимой нормы";
        }

        return "Размера файла robots.txt составляет {$this->getFileSize()} байта, что превышает допустимую норму";
    }

    /**
     * @return string
     */
    public function sizeRecommendation()
    {

        if($this->checkFileSize()) {
            return 'Доработки не требуются';
        }

        return 'Программист: Максимально допустимый размер файла robots.txt составляем 32 кб. Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб';
    }


    /**
     * @return string
     */
    public function siteMapError()
    {

        if($this->getSitemaps()) {
            return "Директива Sitemap указана";
        }

        return "В файле robots.txt не указана директива Sitemap";
    }

    /**
     * @return string
     */
    public function siteMapRecommendation()
    {

        if($this->getSitemaps()) {
            return 'Доработки не требуются';
        }

        return 'Программист: Добавить в файл robots.txt директиву Sitemap';
    }


    /**
     * @return string
     */
    public function statusCodeError()
    {

        if($this->getStatusCode() == '200') {
            return "Файл robots.txt отдаёт код ответа сервера 200";
        }

        return "При обращении к файлу robots.txt сервер возвращает код ответа {$this->getStatusCode()}";
    }

    /**
     * @return string
     */
    public function statusCodeRecommendation()
    {

        if($this->getStatusCode() == '200') {
            return 'Доработки не требуются';
        }

        return "Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращает код ответа 200";
    }


    /**
     * @return bool
     */
    public function getHostDirective()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getFileSize()
    {

        if($this->content && !$this->fileSize )
        {

            $file = fopen($this->url,"r");
            $inf = stream_get_meta_data($file);
            fclose($file);

            $this->fileSize = $inf['unread_bytes'];
        }

        return $this->fileSize;
    }

    /**
     * @return bool
     */
    public function checkFileSize()
    {
        $result = false;

        if($this->getFileSize() && $this->getFileSize() < self::MAX_FILE_SIZE)
        {
            $result = true;
        }

        return $result;
    }

    /**
     * @return bool|string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

}
