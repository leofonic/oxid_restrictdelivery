<?php
class restrictdelivery_oxdelivery extends restrictdelivery_oxdelivery_parent
{
    public function isForBasket( $oBasket, $oBasketItem = null )
    {
        // amount for conditional check
        $blHasArticles = $this->hasArticles();
        $blHasCategories = $this->hasCategories();
        $blUse = true;
        $iAmount = 0;
        $blForBasket = false;
        
        //Load only Single Item if supplied
        if (!$oBasketItem){
            $aBasketItemsToCheck = $oBasket->getContents();
        } else {
            $aBasketItemsToCheck = array($oBasketItem);
        }

        // category & article check
        if ($blHasCategories || $blHasArticles) {
            $blUse = false;

            $aDeliveryArticles = $this->getArticles();
            $aDeliveryCategories = $this->getCategories();

            foreach ($aBasketItemsToCheck as $oContent) {

                //V FS#1954 - load delivery for variants from parent article
                $oArticle = $oContent->getArticle(false);
                $sProductId = $oArticle->getProductId();
                $sParentId = $oArticle->getParentId();

                if ($blHasArticles && (in_array($sProductId, $aDeliveryArticles) || ($sParentId && in_array($sParentId, $aDeliveryArticles)))) {
                    $blUse = true;
                    $iArtAmount = $this->getDeliveryAmount($oContent);
                    if ($this->getCalculationRule() != self::CALCULATION_RULE_ONCE_PER_CART) {
                        if ($this->_isForArticle($oContent, $iArtAmount)) {
                            $blForBasket = true;
                        }
                    }
                    if (!$blForBasket) {
                        $iAmount += $iArtAmount;
                    }

                } elseif ($blHasCategories) {

                    if (isset(self::$_aProductList[$sProductId])) {
                        $oProduct = self::$_aProductList[$sProductId];
                    } else {
                        $oProduct = oxNew('oxArticle');
                        $oProduct->setSkipAssign(true);

                        if (!$oProduct->load($sProductId)) {
                            continue;
                        }

                        $oProduct->setId($sProductId);
                        self::$_aProductList[$sProductId] = $oProduct;
                    }

                    foreach ($aDeliveryCategories as $sCatId) {

                        if ($oProduct->inCategory($sCatId)) {
                            $blUse = true;
                            $iArtAmount = $this->getDeliveryAmount($oContent);
                            if ($this->getCalculationRule() != self::CALCULATION_RULE_ONCE_PER_CART) {
                                if ($this->_isForArticle($oContent, $iArtAmount)) {
                                    $blForBasket = true;
                                }
                            }
                            if (!$blForBasket) {
                                $iAmount += $iArtAmount;
                            }
                            //HR#5650 product might be in multiple rule categories, counting it once is enough
                            break;
                        }
                    }

                }
            }
        } else {
            // regular amounts check
            foreach ($aBasketItemsToCheck as $oContent) {
                $iArtAmount = $this->getDeliveryAmount($oContent);
                if ($this->getCalculationRule() != self::CALCULATION_RULE_ONCE_PER_CART) {
                    if ($this->_isForArticle($oContent, $iArtAmount)) {
                        $blForBasket = true;
                    }
                }
                if (!$blForBasket) {
                    $iAmount += $iArtAmount;
                }
            }
        }

        /* if ( $this->getConditionType() == self::CONDITION_TYPE_PRICE ) {
             $iAmount = $oBasket->_getDiscountedProductsSum();
         }*/

        //#M1130: Single article in Basket, checked as free shipping, is not buyable (step 3 no payments found)
        if (!$blForBasket && $blUse && ($this->_checkDeliveryAmount($iAmount) || $this->_blFreeShipping)) {
            $blForBasket = true;
        }

        return $blForBasket;
    }
}
