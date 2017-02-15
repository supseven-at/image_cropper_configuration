<?php
declare(strict_types=1);

namespace Sup7even\ImageCropperConfiguration\Form\Element;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Generation of image manipulation FormEngine element.
 * This is typically used in FAL relations to cut images.
 */
class ImageManipulationElement extends \TYPO3\CMS\Backend\Form\Element\ImageManipulationElement
{

    /**
     * @param array $config
     * @param string $elementValue
     * @param File $file
     * @return array
     * @throws \TYPO3\CMS\Core\Imaging\ImageManipulation\InvalidConfigurationException
     */
    protected function processConfiguration(array $config, string &$elementValue, File $file)
    {
        if ($this->data['command'] !== 'new') {
            $table = $this->data['databaseRow']['tablenames'] . '.';

            if ($table === 'tt_content.') {
                $uid = $this->data['databaseRow']['uid_foreign'];
                $fullRow = BackendUtility::getRecord('tt_content', $uid, 'CType');
                $subKey = $fullRow['CType'];
            } else {
                $subKey = $this->data['databaseRow']['fieldname'];
            }
            $subKey .= '.';
            $final = [];

            if (isset($this->data['pageTsConfig']['cropVariants.']) && is_array($this->data['pageTsConfig']['cropVariants.'])) {
                $tsconfig = $this->data['pageTsConfig']['cropVariants.'];
                $relevantConfig = [];
                if (isset($tsconfig[$table])) {
                    if (isset($tsconfig[$table][$subKey])) {
                        $relevantConfig = $tsconfig[$table][$subKey];
                    }
                }

                foreach ($relevantConfig as $variantKey => $variant) {
                    $variantKey = rtrim($variantKey, '.');
                    $final[$variantKey] = [
                        'title' => $variant['title'],
                    ];

                    if (isset($variant['allowedAspectRatios.']) && is_array($variant['allowedAspectRatios.'])) {
                        foreach ($variant['allowedAspectRatios.'] as $key => $ratios) {
                            $key = rtrim($key, '.');
                            $final[$variantKey]['allowedAspectRatios'][$key]['title'] = $ratios['title'];
                            $final[$variantKey]['allowedAspectRatios'][$key]['value'] = $this->calc($ratios['value']);
                        }
                    }
                    if (isset($variant['selectedRatio'])) {
                        $final[$variantKey]['selectedRatio'] = $variant['selectedRatio'];
                    }

                    foreach (['cropArea', 'focusArea'] as $name) {
                        $name2 = $name . '.';
                        if (isset($variant[$name2])) {
                            $final[$variantKey][$name] = $this->getArea($variant[$name2]);
                        }
                    }
                    if (isset($variant['coverAreas.'])) {
                        foreach ($variant['coverAreas.'] as $cKey => $cValue) {
                            $cKey = rtrim($cKey, '.');
                            $final[$variantKey]['coverArea'][$cKey] = $this->getArea($cValue);
                        }
                    }
                }
            }

            if (!empty($final)) {
                $config['cropVariants'] = $final;
            }
        }
        return parent::processConfiguration($config, $elementValue, $file);
    }

    protected function getArea(array $area)
    {
        $new = [];
        foreach ($area as $k => $v) {
            $new[$k] = (float)MathUtility::calculateWithPriorityToAdditionAndSubtraction($v);
        }
        return $new;
    }

    protected function calc(string $string)
    {
        return MathUtility::calculateWithPriorityToAdditionAndSubtraction($string);
    }


}
