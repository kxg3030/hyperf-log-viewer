<?php

namespace Hyperf\LogViewer\Concrete;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Collection;


class LogFile implements LogFileInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var mixed
     */
    private $pattern;

    /**
     * @var mixed
     */
    private $path;

    /**
     * @var float|int
     */
    private $maxSize = 50 * 1024 * 1024;

    /**
     * @var Paginate
     */
    public $paginate;

    /**
     * @var Collection
     */
    private $detail;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var int
     */
    public $currentContentPage = 1;

    /**
     * @var int
     */
    public $currentFilePage = 1;

    /**
     * @var string
     */
    public $currentFileName = "";

    public $keyword = "";

    public $level = "";


    /**
     * @return int
     */
    public function getLogListTotal(): int {
        return $this->collection->count();
    }


    /**
     * @return int
     */
    public function getDetailTotal(): int {
        return $this->detail->count();
    }


    public $param;

    /**
     * @var array
     */
    private $class = [
        'debug'     => 'text-primary',
        'info'      => 'text-primary',
        'notice'    => 'text-secondary',
        'warning'   => 'text-warning',
        'error'     => 'text-danger',
        'critical'  => 'text-danger',
        'alert'     => 'text-danger',
        'emergency' => 'text-danger',
        'processed' => 'text-primary',
        'failed'    => 'text-danger',
    ];

    /**
     * @var array
     */
    private $logLevel = [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug',
        'processed',
        'failed'
    ];

    /**
     * LogFile constructor.
     * @param Paginate $paginate
     */
    public function __construct() {
        $this->config   = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $this->path     = $this->config->get("logViewer.path");
        $this->pattern  = $this->config->get("logViewer.pattern");
        $this->paginate = new Paginate();
    }

    /**
     * @return string
     */
    public function logPage() {
        $link = "?file_page=%d&content_page=" . $this->currentContentPage . "&file=" . $this->currentFileName;
        return $this->getLogPage($this->collection, $link, $this->currentFilePage);
    }

    /**
     * @return string
     */
    public function contentPage() {
        $link = "?file_page=" . $this->currentFilePage . "&content_page=%d&file=" . $this->currentFileName . "&keyword=" . $this->keyword . "&level=" . $this->level;
        return $this->getLogPage($this->detail, $link, $this->currentContentPage);
    }

    /**
     * @param Collection $collection
     * @param $param
     * @param $filePage
     * @return string
     */
    private function getLogPage(Collection $collection, $link, $filePage) {

        return $this->paginate->render($collection, $link, $filePage);
    }

    /**
     * @param $currentPage
     * @param Collection $collection
     * @return Collection
     */
    public function slice($currentPage, Collection $collection) {
        return new Collection(array_slice($collection->toArray(), ($currentPage - 1) * $this->paginate->size, $this->paginate->size));
    }

    /**
     * @return $this
     */
    private function getLogFile() {
        $filePattern      = sprintf("%s%s", $this->path, $this->pattern);
        $this->collection = new Collection();
        if ($filePattern) {
            $collection = new Collection(glob($filePattern));
            if ($collection->isNotEmpty()) {
                $collection       = $collection->filter(function ($log) {
                    return filesize($log) < $this->maxSize;
                })->map(function ($log) {
                    return $this->getFileName($log);
                })->unique();
                $this->collection = $collection;
            }
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getLogListForPage() {
        return $this->getLogFile()->slice($this->currentFilePage, $this->collection);
    }

    /**
     * @param string $logFile
     * @return false|string
     */
    private function getFileName(string $logFile) {
        return substr($logFile, strrpos($logFile, "/") + 1);
    }

    /**
     * @return $this
     */
    private function getDetail() {
        $lineList = [];
        $fullPath = $this->config->get("logViewer.path") . $this->currentFileName;
        if (file_exists($fullPath)) {
            $content    = $this->readFileLine($fullPath);
            $collection = new Collection($content);
            if ($collection->isNotEmpty()) {
                $collection->each(function ($content) use (&$lineList) {
                    $content = trim(strtolower($content));
                    foreach ($this->logLevel as $level) {
                        $match = $this->pregMatch($level, $content);
                        if (empty($match[4])) {
                            continue;
                        }
                        if ($this->keyword) {
                            preg_match("/$this->keyword/i", $match[4], $keyword);
                            if (empty($keyword)) {
                                continue;
                            }
                        }
                        if ($this->level) {
                            if ($this->level !== $level) {
                                continue;
                            }
                        }
                        $lineList[] = [
                            'context' => $match[3],
                            'level'   => $level,
                            "class"   => $this->class[$level],
                            'date'    => $match[1],
                            'text'    => $match[4],
                            'in_file' => isset($current[5]) ? $match[5] : "",
                            'stack'   => preg_replace("/^\n*/", '', $content)
                        ];
                    }
                });
            }
        }
        $this->detail = new Collection($lineList);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getDetailForPage() {
        return $this->getDetail()->slice($this->currentContentPage, $this->detail);
    }

    /**
     * @param $level
     * @param $content
     * @return mixed
     */
    private function pregMatch($level, $content) {
        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i', $content, $match);
        return $match;
    }

    /**
     * @param $fullPath
     * @return array
     */
    private function readFileLine($fullPath) {
        $content = [];
        $handle  = fopen($fullPath, "r+");
        while (feof($handle) == false) {
            $line = fgets($handle);
            if ($line) {
                $content[] = $line;
            }
        }
        return $content;
    }
}