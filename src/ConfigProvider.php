<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\LogViewer;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => '',
                    'source' => __DIR__ . '/../publish/logViewer.php',
                    'destination' => BASE_PATH . '/config/autoload/logViewer.php',
                ],
                [
                    'id' => 'view',
                    'description' => '',
                    'source' => __DIR__ . '/../publish/log.html',
                    'destination' => BASE_PATH . '/storage/view/log.html',
                ],
            ],
        ];
    }
}
