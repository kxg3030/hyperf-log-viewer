<?php

namespace Sett\LogViewer\Controller;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\View\RenderInterface;
use Sett\LogViewer\Concrete\LogFile;



class LogViewController
{
    /**
     * @var LogFile
     */
    private $logFile;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var RenderInterface
     */
    private $render;

    private $config;

    /**
     * LogViewController constructor.
     * @param LogFile $logFile
     * @param RenderInterface $render
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(RenderInterface $render, RequestInterface $request, ResponseInterface $response, ConfigInterface $config) {
        $this->request = $request;
        $this->render  = $render;
        $this->config  = $config;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index() {
        $logFile                     = new LogFile();
        $logFile->paginate->size     = $this->config->get("logViewer.size", 10);
        $logFile->currentContentPage = $this->request->query("content_page", 1);
        $logFile->currentFilePage    = $this->request->query("file_page", 1);
        $logFile->keyword            = $this->request->query("keyword", "");
        $logFile->level              = $this->request->query("level", "");
        $this->logFile               = $logFile;
        $logList                     = $this->logFile->getLogListForPage();
        $logFile->currentFileName    = $this->request->query("file", $logList->first());
        $logDetail                   = $this->logFile->getDetailForPage()->toArray();
        return $this->render->render("log", [
            "logList"        => $this->markActive($logList->toArray()),
            "logPage"        => $this->logFile->logPage(),
            "logTotal"       => $this->logFile->getLogListTotal(),
            "logDetail"      => $logDetail,
            "logDetailPage"  => $this->logFile->contentPage(),
            "logDetailTotal" => $this->logFile->getDetailTotal(),
            "level"          => [
                [
                    "name"   => "info",
                    "active" => $this->logFile->level == "info"
                ],
                [
                    "name"   => "error",
                    "active" => $this->logFile->level == "error"
                ],
                [
                    "name"   => "warning",
                    "active" => $this->logFile->level == "warning"
                ]
            ],
        ]);
    }

    private function markActive(array $logList) {
        $logListMark = [];
        if ($logList) {
            foreach ($logList as $item) {
                $logListMark[] = [
                    "name"   => $item,
                    "active" => $item == $this->logFile->currentFileName
                ];
            }
        }
        return $logListMark;
    }
}