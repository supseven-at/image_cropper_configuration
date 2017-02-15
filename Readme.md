# TYPO3 Extension `image_cropper_configuration`

This extension makes it possible to define the cropping configuration per `CType` if using for a content element or per table + FAL field name.

This extension is highly alpha ;)

## Example
   
Example showing 2 cropping configurations for the CType *exampleA* and *exampleB*.
    
    defaultCropArea {
        x = 0.0
        y = 0.0
        width = 1.0
        height = 1.0
    }
    
    cropVariants {
        tt_content.exampleA {
            desktop {
                title = Desktop (1110 x 1322)
                allowedAspectRatios {
                    default {
                        title = 0,84:1
                        value = 1110/1322
                    }
                }
                selectedRatio = default
                cropArea < defaultCropArea
    
            }
            mobile {
                title = Mobile (1474 x 970)
                allowedAspectRatios {
                    default {
                        title = 1,52:1
                        value = 1474/970
                    }
                }
                selectedRatio = default
                cropArea < defaultCropArea
            }
        }
        tt_content.exampleB {
            desktop {
                title = Desktop (2620 x 1310)
                allowedAspectRatios {
                    2:1 {
                        title = 2:1
                        value = 2.0
                    }
                }
                selectedRatio = 2:1
                cropArea < defaultCropArea
    
            }
            mobile {
                title = Mobile (1536 x 1024)
                allowedAspectRatios {
                    138:1 {
                        title = 1.38:1
                        value = 1536/1024
                    }
                }
                selectedRatio = 138:1
                cropArea < defaultCropArea
            }
        }
    }
    
