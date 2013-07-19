<?php
class restrictdelivery_oxdelivery extends restrictdelivery_oxdelivery_parent
{
    public function isForBasket( $oBasket, $oBasketItem = null )
    {
        // amount for conditional check
        $blHasArticles   = $this->hasArticles();
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
        if ( $blHasCategories || $blHasArticles ) {
            $blUse = false;

            $aDeliveryArticles   = $this->getArticles();
            $aDeliveryCategories = $this->getCategories();
            
            

            foreach ( $aBasketItemsToCheck as $oContent ) {

                //V FS#1954 - load delivery for variants from parent article
                $oArticle   = $oContent->getArticle(false);
                $sProductId = $oArticle->getProductId();
                $sParentId  = $oArticle->getProductParentId();

                if ( $blHasArticles && (in_array( $sProductId, $aDeliveryArticles ) || ( $sParentId && in_array( $sParentId, $aDeliveryArticles ) ) ) ) {
                    $blUse = true;
                    $iArtAmount = $this->getDeliveryAmount( $oContent );
                    if ( $this->oxdelivery__oxfixed->value > 0 ) {
                        if ( $this->_isForArticle( $oContent, $iArtAmount ) ) {
                            $blForBasket = true;
                        }
                    }
                    if (!$blForBasket) {
                        $iAmount += $iArtAmount;
                    }

                } elseif ( $blHasCategories ) {

                    if ( isset( self::$_aProductList[$sProductId] ) ) {
                        $oProduct = self::$_aProductList[$sProductId];
                    } else {
                        $oProduct = oxNew( 'oxarticle' );
                        $oProduct->setSkipAssign( true );

                        if ( !$oProduct->load( $sProductId ) ) {
                            continue;
                        }

                        $oProduct->setId($sProductId);
                        self::$_aProductList[$sProductId] = $oProduct;
                    }

                    foreach ( $aDeliveryCategories as $sCatId ) {

                        if ( $oProduct->inCategory( $sCatId ) ) {
                            $blUse = true;

                            $iArtAmount = $this->getDeliveryAmount( $oContent );
                            if ( $this->oxdelivery__oxfixed->value > 0 ) {
                                if ( $this->_isForArticle( $oContent, $iArtAmount ) ) {
                                    $blForBasket = true;
                                }
                            }

                            break;
                        }
                    }
                    if (!$blForBasket) {
                        $iAmount += $iArtAmount;
                    }
                }
            }

        } else {
            // regular amounts check
            foreach ( $aBasketItemsToCheck as $oContent ) {
                $iArtAmount = $this->getDeliveryAmount( $oContent );
                if ( $this->oxdelivery__oxfixed->value > 0 ) {
                    if ( $this->_isForArticle( $oContent, $iArtAmount ) ) {
                        $blForBasket = true;
                    }
                }
                if (!$blForBasket) {
                    $iAmount += $iArtAmount;
                }
            }
        }

        //#M1130: Single article in Basket, checked as free shipping, is not buyable (step 3 no payments found)
        if ( !$blForBasket && $blUse && ( $this->_checkDeliveryAmount($iAmount) || $this->_blFreeShipping ) ) {
            $blForBasket = true;
        }
        return $blForBasket;
    }
}
