<?php

namespace Unicorn\Service;


use Wpcb\FunctionalityPlugin\Manager;
use Wpcb\Repository\SnippetRepository;
use Wpcb\Snippet\SnippetMeta;

class SnippetToResponseMapper
{
    public function mapSnippetToResponse($snippet)
    {

        if(!is_array($snippet['runType'])) {
            $snippet['runType'] = [
                'label' => ucwords($snippet['runType']),
                'value' => $snippet['runType']

            ];
        }

        if(!is_array($snippet['codeType'])) {
            $snippet['codeType'] = [
                'label' => strtoupper($snippet['codeType']),
                'value' => $snippet['codeType']

            ];
        }

		$snippet['savedToCloud'] = !!$snippet['savedToCloud'];
		$snippet['error'] = !!$snippet['error'];

        return $snippet;



        $fp = new Manager(\get_option('wpcb_functionality_plugin_name'));
        $snippetMeta = new SnippetMeta(\get_post_meta($snippet->ID));

        $where_to_run_values = [
            "everywhere" => [
                'label' => "Everywhere",
                'value' => "everywhere"
            ],
            "frontend" => [
                'label' => "Frontend",
                'value' => "frontend"
            ],
            "admin" => [
                'label' => "Admin Area",
                'value' => "admin"
            ],
            "custom" => [
                'label' => "Custom",
                'value' => "custom"
            ]
        ];

        $hookPriority = !empty($snippetMeta->get('wpcb_hook_priority')) ? intval($snippetMeta->get('wpcb_hook_priority')) : 10;

        $runType = $snippetMeta->get('wpcb_run_type');
        $where_to_run = $snippetMeta->get('wpcb_where_to_run');

        if (!$runType) {
            $runType = "always";
        }

        if ($runType == "once") {
            $runType = [
                'value' => "once",
                'label' => "Manual (On Demand)"
            ];
        } else if ($runType == 'always') {
            $runType = [
                'value' => "always",
                'label' => "Always (On Page Load)"
            ];
        } else if ($runType == 'never') {
            $runType = [
                'value' => "never",
                'label' => "Do not run"
            ];
        } else if ($runType == 'external') {
            $runType = [
                'value' => "external",
                'label' => "Using external secure URL"
            ];
        }


        if (!$where_to_run) {
            $where_to_run = "everywhere";
        }

        $codeType = $snippetMeta->get('wpcb_code_type');

        if (!$codeType) {
            $codeType = 'php';
        }

        $code = $snippetMeta->get('wpcb_code');
        $original_code = $snippetMeta->get('wpcb_original_code');

        $location = $snippetMeta->get('wpcb_location');

        if (!$location || $location === 'header') {
            $location_value = [
                'value' => 'header',
                'label' => 'Header'
            ];
        } else {
            $location_value = [
                'value' => 'footer',
                'label' => 'Footer'
            ];
        }

        $hook = $snippetMeta->get('wpcb_hook');

        if (!$hook ||
            ($codeType !== 'php' && isset($hook['value']) && isset($hook['label']) &&
                ($hook['value'] == 'root'
                    || $hook['value'] == 'custom_root'
                ))) {
            $hook = $this->mapSettingsToHook($codeType, $hookPriority, $location_value, $where_to_run);
        } else {
            if (isset($hook['value']) && isset($hook['label'])) {
                if($hook['label'] === 'Root (Default)') {
                    $hook['label'] = 'Root';
                    $hook['value'] = 'custom_root';
                }
                $hook = [
                    [
                        'hook' => $hook,
                        'priority' => $hookPriority
                    ]
                ];
            } else {
                if ($codeType !== "php" && isset($hook['value']) && $hook['value'] === "root") {
                    if ($location_value['value'] === 'header') {
                        $hook = [
                            [
                                'hook' => ['label' => 'Frontend Header (default)', 'value' => 'custom_frontend_header'],
                                'priority' => $hookPriority
                            ]
                        ];
                    } else {
                        $hook = [[
                            'hook' => ['label' => 'Frontend Footer', 'value' => 'custom_frontend_footer'],
                            'priority' => $hookPriority
                        ]];
                    }
                }

                if ($codeType === 'php') {
                    if (isset($hook['value']) && $hook['value'] === 'root') {
                        $hook = [['hook' => [
                            ['label' => 'Root', 'value' => 'custom_root'],
                            'priority' => $hookPriority
                        ]]];
                    }
                }
            }
        }


        if ($codeType === "php") {
            $codeType = [
                'value' => 'php',
                'label' => "PHP"
            ];
        }

        if ($codeType === 'css') {
            $codeType = [
                'value' => 'css',
                'label' => "CSS"
            ];

            $code = $original_code;

        }

        if ($codeType === 'scssp') {
            $codeType = [
                'value' => 'scssp',
                'label' => "SCSS Partial"
            ];

            $code = $original_code;
        }

        if ($codeType === 'scss') {
            $codeType = [
                'value' => 'scss',
                'label' => "SCSS"
            ];

            $code = $original_code;
        }

        if ($codeType === 'less') {
            $codeType = [
                'value' => 'less',
                'label' => "LESS"
            ];

            $code = $original_code;
        }

        if ($codeType === 'js') {
            $codeType = [
                'value' => 'js',
                'label' => "JavaScript"
            ];

            $code = $original_code;

            $codeData['tagOptions'] = $snippetMeta->get('wpcb_tag_options');

        }

        if ($codeType === 'html') {
            $codeType = [
                'value' => 'html',
                'label' => "HTML"
            ];
        }

        if ($codeType === 'txt') {
            $codeType = [
                'value' => 'txt',
                'label' => "Plain Text"
            ];
        }

        if ($codeType === 'ex_css') {
            $codeType = [
                'value' => 'ex_css',
                'label' => "CSS (External File)"
            ];

            $codeData = json_decode($code, true);
        }

        if ($codeType === 'ex_js') {
            $codeType = [
                'value' => 'ex_js',
                'label' => "JavaScript (External File)"
            ];
            $codeData = json_decode($code, true);
        }

        $should_run = $snippetMeta->get('wpcb_should_run');

        if ($should_run === "not_run") {
            $should_run = [
                'value' => 'not_run',
                'label' => "Don't run on pages"
            ];
        } else {
            $should_run = [
                "value" => "run",
                "label" => "Run on pages"
            ];
        }

        // Map old "Where to run" to conditions
        $conditions = $snippetMeta->get('wpcb_conditions');

        if ($where_to_run === 'frontend') {

            if($codeType['value'] === 'php') {
                $conditions = array(
                    0 =>
                        array(
                            'id' => '1',
                            'type' =>
                                array(
                                    'value' => 'OR',
                                    'label' => 'OR',
                                ),
                            'conditions' =>
                                array(
                                    0 =>
                                        array(
                                            'conditionTitle' => 'Location',
                                            'conditionVerbs' =>
                                                array(
                                                    0 => 'Is Everywhere',
                                                    1 => 'Is Frontend',
                                                    2 => 'Is Admin',
                                                    3 => 'Is Login',
                                                ),
                                            'conditionVerb' =>
                                                array(
                                                    'label' => 'Is Frontend',
                                                    'value' => 1,
                                                ),
                                            'conditionVerbIndex' => 0,
                                            'component' => 'null',
                                            'andor' => 'AND',
                                            'extraData' => false,
                                            'extraData2' => false,
                                        ),
                                ),
                        ),
                );
            }
        } else if ($where_to_run === 'admin') {
            if($codeType['value'] === 'php') {
                $conditions = array(
                    0 =>
                        array(
                            'id' => '1',
                            'type' =>
                                array(
                                    'value' => 'OR',
                                    'label' => 'OR',
                                ),
                            'conditions' =>
                                array(
                                    0 =>
                                        array(
                                            'conditionTitle' => 'Location',
                                            'conditionVerbs' =>
                                                array(
                                                    0 => 'Is Everywhere',
                                                    1 => 'Is Frontend',
                                                    2 => 'Is Admin',
                                                    3 => 'Is Login',
                                                ),
                                            'conditionVerb' =>
                                                array(
                                                    'label' => 'Is Admin',
                                                    'value' => 2,
                                                ),
                                            'conditionVerbIndex' => 0,
                                            'component' => 'null',
                                            'andor' => 'AND',
                                            'extraData' => false,
                                            'extraData2' => false,
                                        ),
                                ),
                        ),
                );
            }
        }



        $where_to_run_value = $where_to_run_values[$where_to_run];


        $should_run_page_value = [];

        $should_run_page = $snippetMeta->get('wpcb_should_run_page');

        if (is_array($should_run_page) && !isset($should_run_page['value'])) {

            foreach ($should_run_page as $page) {
                if ($page === "-1") {
                    $should_run_page_value[] = [
                        "value" => "-1",
                        "label" => "Home Page"
                    ];

                } else {
                    $page_obj = get_post($page);

                    if ($page_obj) {
                        $should_run_page_value[] = [
                            "value" => $page_obj->ID,
                            "label" => $page_obj->post_title
                        ];
                    }
                }
            }

        } else {

            if ($should_run_page === "-1") {
                $should_run_page_value = [
                    "value" => "-1",
                    "label" => "Home Page"
                ];

            } else {
                $page = get_post($should_run_page);

                if ($page) {
                    $should_run_page_value = [
                        "value" => $page->ID,
                        "label" => $page->post_title
                    ];
                } else {
                    $should_run_page_value = [];
                }
            }
        }

        $quickActions = !!$snippetMeta->get('wpcb_add_to_quick_actions');

        $renderType = $snippetMeta->get('wpcb_render_type');

        if ($renderType === 'external') {
            $renderTypeValue = [
                'label' => 'External',
                'value' => 'external'
            ];
        } else {
            $renderTypeValue = [
                'label' => 'Inline',
                'value' => 'inline'
            ];
        }

        return array(
            'id' => $snippet->ID,
            'title' => $snippet->post_title,
            'code' => $code,
            'runType' => $runType,
            'enabled' => !!$snippetMeta->get('wpcb_enabled'),
            'whereToRun' => $where_to_run_value,
            'savedToCloud' => !!$snippetMeta->get('wpcb_saved_to_cloud'),
            'description' => $snippet->post_content,
            'remoteId' => $snippetMeta->get('wpcb_remote_id'),
            'error' => $snippetMeta->get('wpcb_error'),
            'errorMessage' => $snippetMeta->get('wpcb_error_message'),
            'errorTrace' => $snippetMeta->get('wpcb_error_trace'),
            'errorLine' => $snippetMeta->get('wpcb_error_line') ? $snippetMeta->get('wpcb_error_line') : 'N/A',
            'codeType' => $codeType,
            'tags' => $codeType['value'],
            'devMode' => !!$snippetMeta->get('wpcb_dev_mode_enabled'),
            'shouldRun' => $should_run,
            'shouldRunPage' => $should_run_page_value,
            'conditions' => $conditions,
            'priority' => $snippet->menu_order,
            'location' => $location_value,
            'addToQuickActions' => $quickActions,
            'renderType' => $renderTypeValue,
            'minify' => !!$snippetMeta->get('wpcb_minify'),
            'tagOptions' => isset($codeData) && is_array($codeData) && isset($codeData['tagOptions']) ? $codeData['tagOptions'] : [],
            'externalUrl' => isset($codeData) && is_array($codeData) && isset($codeData['externalUrl']) ? $codeData['externalUrl'] : [],
            'order' => !empty($snippetMeta->get('wpcb_order')) ? intval($snippetMeta->get('wpcb_order')) : 0,
            'hook' => $hook,
            'hookPriority' => $hookPriority,
            'shortcode' => !empty($snippetMeta->get('wpcb_shortcode')) ? $snippetMeta->get('wpcb_shortcode') : '',
            'customAction' => !empty($snippetMeta->get('wpcb_custom_action')) ? $snippetMeta->get('wpcb_custom_action') : '',
            'filePath' => $fp->getSnippetPath($snippet),
            'secureUrl' => get_site_url() . '/wp-load.php?wpcb_token=' . $snippetMeta->get('wpcb_secret')
        );
    }

    /**
     * @param $codeType
     * @param $hookPriority
     * @param $location_value
     * @param $where_to_run
     * @return array
     */
    private function mapSettingsToHook($codeType, $hookPriority, $location_value, $where_to_run)
    {
        $hook = [];

        if ($codeType === "php") {
            $hook = [
                [
                    'hook' => ['label' => 'Plugins Loaded (Default)', 'value' => 'plugins_loaded'],
                    'priority' => $hookPriority
                ]
            ];
        } else {
            if ($location_value['value'] === 'header' && ($where_to_run === 'everywhere' || $where_to_run === 'custom' )) {

                $hook = [
                    ['hook' => ['label' => 'Frontend Header (Default)', 'value' => 'custom_frontend_header'], 'priority' => $hookPriority],
                    ['hook' => ['label' => 'Login Header', 'value' => 'custom_login_header'], 'priority' => $hookPriority],
                    ['hook' => ['label' => 'Admin Header', 'value' => 'custom_admin_header'], 'priority' => $hookPriority],
                ];
            } else if ($location_value['value'] === 'footer' && ($where_to_run === 'everywhere' || $where_to_run === 'custom')) {
                $hook = [
                    ['hook' => ['label' => 'Frontend Footer', 'value' => 'custom_frontend_footer'], 'priority' => $hookPriority],
                    ['hook' => ['label' => 'Login Footer', 'value' => 'custom_login_footer'], 'priority' => $hookPriority],
                    ['hook' => ['label' => 'Admin Footer', 'value' => 'custom_admin_footer'], 'priority' => $hookPriority]
                ];
            }


            if ($location_value['value'] === 'header' && $where_to_run === 'admin') {

                $hook = [
                    ['hook' => ['label' => 'Admin Header', 'value' => 'custom_admin_header'], 'priority' => $hookPriority],
                ];
            } else if ($location_value['value'] === 'footer' && $where_to_run === 'admin') {
                $hook = [
                    ['hook' => ['label' => 'Admin Footer', 'value' => 'custom_admin_footer'], 'priority' => $hookPriority]
                ];
            }

            if ($location_value['value'] === 'header' && $where_to_run === 'frontend') {

                $hook = [
                    ['hook' => ['label' => 'Frontend Header (Default)', 'value' => 'custom_frontend_header'], 'priority' => $hookPriority],
                ];
            } else if ($location_value['value'] === 'footer' && $where_to_run === 'frontend') {
                $hook = [
                    ['hook' => ['label' => 'Frontend Footer', 'value' => 'custom_frontend_footer'], 'priority' => $hookPriority],
                ];
            }
        }
        return $hook;
    }

}
