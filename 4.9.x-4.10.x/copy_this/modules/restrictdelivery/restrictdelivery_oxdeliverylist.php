<?php
class restrictdelivery_oxdeliverylist extends restrictdelivery_oxdeliverylist_parent
{
    public function hasDeliveries( $oBasket, $oUser, $sDelCountry, $sDeliverySetId )
    {

        //Check for each Article whether it matches a delivery rule, not for whole basket
        $aBasketItemsToCheck = $oBasket->getContents();
        foreach ($aBasketItemsToCheck as $oBasketItem){
            $blHas = false;
            // loading delivery list to check if some of them fits
            $this->_getList( $oUser, $sDelCountry, $sDeliverySetId );
            foreach ( $this as $oDelivery ) {
                if ( $oDelivery->isForBasket( $oBasket, $oBasketItem ) ) {
                    $blHas = true;
                    break;
                }
            }
            //if no match return false
            if (!$blHas){
                break;
            }
        }

        return $blHas;
    }
}
