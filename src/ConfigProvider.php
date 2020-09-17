<?php

declare(strict_types=1);
/**
 * This file is part of Sett.
 *
 * @link     https://www.Sett.io
 * @document https://doc.Sett.io
 * @contact  group@Sett.io
 * @license  https://github.com/Sett/Sett/blob/master/LICENSE
 */
namespace Sett\LogViewer;

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
                ]
            ],
        ];
    }
}
