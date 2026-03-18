<?php
require_once 'config.inc.php';
require_once 'includes/main/WebUI.php';

$db = current(PearDatabase::getInstance());

$templateId = '9'; // Assume atom_auto_pay_reminder_3 has id 9 or something. Let me just get the template directly.

$sql = "SELECT id, components FROM vtiger_whatsapp_templates WHERE template_name = 'atom_auto_pay_reminder_3'";
$res = current(PearDatabase::getInstance())->pquery($sql, array());
$templateData = current(PearDatabase::getInstance())->raw_query_result_rowdata($res, 0);

$componentsStr = html_entity_decode($templateData['components'], ENT_QUOTES, 'UTF-8');
$components = json_decode($componentsStr, true);

$groupedVariables = array();

if (is_array($components)) {
    foreach ($components as $component) {
        if (isset($component['type']) && in_array($component['type'], array('BODY', 'HEADER', 'BUTTONS'))) {
            $type = $component['type'];
            $componentStr = json_encode($component);

            // Extract all {{variable}} patterns
            if (preg_match_all('/\{\{([a-zA-Z0-9_]+)\}\}/', $componentStr, $matches)) {
                $uniqueVars = array_unique($matches[1]);

                $exampleValues = array();
                $namedExamples = array();

                if ($type == 'BODY') {
                    if (isset($component['example']['body_text'][0])) {
                        $exampleValues = $component['example']['body_text'][0];
                    } elseif (isset($component['example']['body_text_named_params'])) {
                        foreach ($component['example']['body_text_named_params'] as $np) {
                            $namedExamples[$np['param_name']] = $np['example'];
                        }
                    }
                } elseif ($type == 'HEADER') {
                    if (isset($component['example']['header_text'][0])) {
                        $exampleValues = $component['example']['header_text'];
                    } elseif (isset($component['example']['header_text_named_params'])) {
                        foreach ($component['example']['header_text_named_params'] as $np) {
                            $namedExamples[$np['param_name']] = $np['example'];
                        }
                    }
                }

                $varIndex = 0;
                foreach ($uniqueVars as $match) {
                    $varName = '{{' . $match . '}}';

                    $exampleData = '';
                    if (isset($namedExamples[$match])) {
                        $exampleData = $namedExamples[$match];
                    } elseif (isset($exampleValues[$varIndex])) {
                        $exampleData = $exampleValues[$varIndex];
                    }

                    $contextInfo = '';
                    if ($type == 'BUTTONS') {
                        $bIndex = 1;
                        if (isset($component['buttons'])) {
                            foreach ($component['buttons'] as $btn) {
                                if (isset($btn['url']) && strpos($btn['url'], $varName) !== false) {
                                    $exampleData = isset($btn['text']) ? $btn['text'] : 'Button ' . $bIndex;
                                    $contextInfo = "Button " . $bIndex . " URL Parameter";
                                }
                                $bIndex++;
                            }
                        }
                    }

                    $groupedVariables[$type][] = array(
                        'name' => $varName,
                        'type' => $type,
                        'key' => $match,
                        'example' => $exampleData,
                        'context' => $contextInfo
                    );
                    $varIndex++;
                }
            }
        }
    }
}

file_put_contents('temp_vars_grouped.txt', json_encode($groupedVariables, JSON_PRETTY_PRINT));
echo "Saved";
