<?php
/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'writable' => [
 *             // list of directories that should be set writable
 *         ],
 *     ],
 * ];
 * ```
 */
return [
    'Development' => [
        'path' => 'dev',
        'writable' => [
            'stockDepartment/runtime',
            'stockDepartment/web/assets',
            'stockDepartment/web/uploads',
            'stockDepartment/web/log',
            'clientDepartment/runtime',
            'clientDepartment/web/assets',
            'bossDepartment/runtime',
            'bossDepartment/web/assets',
            'pointDepartment/runtime',
            'pointDepartment/web/assets',
            'personalDepartment/runtime',
            'personalDepartment/web/assets',
            'frontendDepartment/runtime',
            'frontendDepartment/web/assets',
        ],
        'executable' => [
            'yii',
        ],
    ],
    'Production' => [
        'path' => 'prod',
        'writable' => [
            'stockDepartment/runtime',
            'stockDepartment/web/assets',
            'stockDepartment/web/uploads',
            'stockDepartment/web/log',
            'clientDepartment/runtime',
            'clientDepartment/web/assets',
            'bossDepartment/runtime',
            'bossDepartment/web/assets',
            'pointDepartment/runtime',
            'pointDepartment/web/assets',
            'personalDepartment/runtime',
            'personalDepartment/web/assets',
            'frontendDepartment/runtime',
            'frontendDepartment/web/assets',
        ],
        'executable' => [
            'yii',
        ],
    ],
    'Local' => [
        'path' => 'local',
        'writable' => [
            'stockDepartment/runtime',
            'stockDepartment/web/assets',
            'stockDepartment/web/uploads',
            'stockDepartment/web/log',
            'clientDepartment/runtime',
            'clientDepartment/web/assets',
            'bossDepartment/runtime',
            'bossDepartment/web/assets',
            'pointDepartment/runtime',
            'pointDepartment/web/assets',
            'personalDepartment/runtime',
            'personalDepartment/web/assets',
            'frontendDepartment/runtime',
            'frontendDepartment/web/assets',
        ],
        'executable' => [
            'yii',
        ],
    ],
];
