<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class WhatsappIntergration_Record_Model extends Vtiger_Record_Model {
	
	public static function getHeaderPlaceHolders($apiData){
		$headerPlaceholders = [];
		if (!empty($apiData['components']) && is_array($apiData['components'])) {

			foreach ($apiData['components'] as $comp) {

				if (!isset($comp['type']) || strtoupper($comp['type']) !== 'HEADER' || strtoupper($comp['format'] ?? '') !== 'TEXT' || empty($comp['example']) ) {
					continue;
				}

				// -------------------------
				// POSITIONAL HEADER {{1}}
				// -------------------------
				if ( ($apiData['parameter_format'] ?? '') === 'POSITIONAL' && isset($comp['example']['header_text']) && is_array($comp['example']['header_text']) ) {
					$idx = 1;
					foreach ($comp['example']['header_text'] as $ph) {
						$headerPlaceholders[] = [
							'index'       => $idx,
							'placeholder' => $ph
						];
						$idx++;
					}
				}

				// -------------------------
				// NAMED HEADER {{status}}
				// -------------------------
				if ( ($apiData['parameter_format'] ?? '') === 'NAMED' && isset($comp['example']['header_text_named_params']) && is_array($comp['example']['header_text_named_params']) ) {
					foreach ($comp['example']['header_text_named_params'] as $ph) {
						if (!is_array($ph)) {
							continue;
						}
						$headerPlaceholders[] = [
							'index'       => $ph['param_name'],
							'placeholder' => $ph['example']
						];
					}
				}
			}
		}
		return $headerPlaceholders;
	}
	public static function getBodyPlaceHolders($apiData){	
		$bodyPlaceholders   = [];
		if (!empty($apiData['components']) && is_array($apiData['components'])) {
                        foreach ($apiData['components'] as $comp) {
                                if (isset($comp['type']) && strtoupper($comp['type']) === 'HEADER' && isset($comp['example']['header_text'])) {
                                        $idx = 1;
                                        foreach ($comp['example']['header_text'] as $ph) {
                                                $headerPlaceholders[] = ['index' => $idx, 'placeholder' => $ph];
                                                $idx++;
                                        }
                                }

                                if (!isset($comp['type']) || strtoupper($comp['type']) !== 'BODY' || empty($comp['example'])) {
                                        continue;
                                }
                                // -------------------------
                                // POSITIONAL ({{1}}, {{2}})
                                // -------------------------
                                if (($apiData['parameter_format'] ?? '') === 'POSITIONAL' && isset($comp['example']['body_text'][0]) && is_array($comp['example']['body_text'][0])) {
                                        $idx = 1;
                                        foreach ($comp['example']['body_text'][0] as $ph) {
                                                $bodyPlaceholders[] = [
                                                        'index'       => $idx,
                                                        'placeholder' => $ph
                                                ];
                                                $idx++;
                                        }
                                }
                                // -------------------------
                                // NAMED ({{name}}, {{order_id}})
                                // -------------------------
                                if (($apiData['parameter_format'] ?? '') === 'NAMED' && isset($comp['example']['body_text_named_params']) && is_array($comp['example']['body_text_named_params'])) {
                                        foreach ($comp['example']['body_text_named_params'] as $ph) {
                                                if (!is_array($ph)) {
                                                        continue;
                                                }
                                                $bodyPlaceholders[] = [
                                                        'index'       => $ph['param_name'],
                                                        'placeholder' => $ph['example']
                                                ];
                                        }
                                }
                        }
                }
		return $bodyPlaceholders;
	}
	public static function getButtonPlaceHolders($apiData){
		$buttonPlaceholders = [];
		if (!empty($apiData['components']) && is_array($apiData['components'])) {
			foreach ($apiData['components'] as $comp) {
				if (strtoupper($comp['type']) !== 'BUTTONS' || empty($comp['buttons'])) {
					continue;
				}
				foreach ($comp['buttons'] as $btnIndex => $btn) {
					// Only URL buttons
					if (($btn['type'] ?? '') !== 'URL' || empty($btn['url'])) {
						continue;
					}
					// ✅ example must exist → dynamic button
					if (empty($btn['example']) || empty($btn['example'][0])) {
						continue; // static button
					}
					$url = urldecode($btn['url']);
					// ✅ Extract numeric placeholders only {{1}}, {{2}}
					preg_match_all('/\{\{(\d+)\}\}/', $url, $numMatches);

					// Use FIRST numeric placeholder index
					$buttonPlaceholders[] = [
						'index'       => $btnIndex,
						'param'       => $numMatches[1][0], // 1, 2, etc
						'text'        => $btn['text'],
						'url'         => $btn['url'],
						'example'     => $btn['example'][0],
					];
				}
			}
		}
		return $buttonPlaceholders;	
	}


}



?>
