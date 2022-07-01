<?php
    require_once('Mercanet.php');
	
    // Initialisation de la classe Mercanet avec passage en parametre de la cle secrete
    $paymentRequest = new Mercanet('S9i8qClCnb2CZU3y3Vn0toIOgz3z_aBi79akR30vM9o');

    // Indiquer quelle page de paiement appeler : TEST ou PRODUCTION 
    $paymentRequest->setUrl(Mercanet::TEST);

    $transactionReference = "echeance1" . rand(100000,999999);
    $amount = 2999;
    
    // Renseigner les parametres obligatoires pour l'appel de la page de paiement 
    $paymentRequest->setMerchantId('211000021310001');
    $paymentRequest->setKeyVersion('1');
    $paymentRequest->setTransactionReference($transactionReference);
    $paymentRequest->setAmount($amount);
    $paymentRequest->setCurrency('EUR');
    if (empty($_SERVER["HTTPS"]))
    {
      $http = "http://";
    }
    else
    {
      $http = "https://";
    }
    $urlReturn = $http . $_SERVER["HTTP_HOST"] . dirname($_SERVER["REQUEST_URI"]) . "/normal_return_url.php" ;
    $paymentRequest->setNormalReturnUrl($urlReturn);     

    // Renseigner les parametres facultatifs pour l'appel de la page de paiement 
    $paymentRequest->setLanguage('fr');
    // ...
    
    // Calcul des informations liées au paiement en n-fois
    $number = 3;
    $delai_entre_echeances = 30;
    $datesList = date("Ymd");
    $transactionReferencesList = $transactionReference;
    $amt = $amount / $number;
    $totalAmount = (int)$amt;
    $amountsList = (int)$amt;
    for ($echeance = 1; $echeance < $number; $echeance++)
    {
	// Pour la derniere echeance, il faut calculer le montant restant a payer par rapport a la somme des precedentes echeances
	if ($echeance == ($number-1))
	{
	    $lastAmount = (int)($amount - $totalAmount);
	    $amountsList .= "," . $lastAmount;	 
	}
	else
	{
	    $totalAmount = $totalAmount + (int)$amt;
	    $amountsList .= "," . (int)$amt;	    
	}
	$transactionReferencesList .= ',' . "echeance". $echeance . rand(100000,999999);
	$ecart = $echeance*$delai_entre_echeances;
	$datesList .= "," . date("Ymd",strtotime("+".$ecart." days"));
    }
    
    // Renseigner les informations liées au paiement en n-fois
    $paymentRequest->setInstalmentDataNumber($number);
    $paymentRequest->setInstalmentDatesList($datesList);
    $paymentRequest->setInstalmentDataTransactionReferencesList($transactionReferencesList);
    $paymentRequest->setInstalmentDataAmountsList($amountsList);
    $paymentRequest->setPaymentPattern(Mercanet::INSTALMENT);
	
    // Verification de la validite des parametres renseignes
    $paymentRequest->validate();
	
    // Appel de la page de paiement Mercanet avec le connecteur POST en passant en parametres : Data, InterfaceVersion, Seal
    echo "<html><body><form name=\"redirectForm\" method=\"POST\" action=\"" . $paymentRequest->getUrl() . "\">" .
		 "<input type=\"hidden\" name=\"Data\" value=\"". $paymentRequest->toParameterString() . "\">" .
		 "<input type=\"hidden\" name=\"InterfaceVersion\" value=\"". Mercanet::INTERFACE_VERSION . "\">" .
		 "<input type=\"hidden\" name=\"Seal\" value=\"" . $paymentRequest->getShaSign() . "\">" . 
		 "<noscript><input type=\"submit\" name=\"Go\" value=\"Click to continue\"/></noscript> </form>" .
		 "<script type=\"text/javascript\"> document.redirectForm.submit(); </script>" .
		 "</body></html>";
?>
