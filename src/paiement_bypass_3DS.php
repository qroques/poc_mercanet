<?php
    require_once('Mercanet.php');
	
    // Initialisation de la classe Mercanet avec passage en parametre de la cle secrete
    $paymentRequest = new Mercanet('S9i8qClCnb2CZU3y3Vn0toIOgz3z_aBi79akR30vM9o');

    // Indiquer quelle page de paiement appeler : TEST ou PRODUCTION 
    $paymentRequest->setUrl(Mercanet::TEST);
    $amount = 1000;
	
    // Renseigner les parametres obligatoires pour l'appel de la page de paiement 
    $paymentRequest->setMerchantId('211000021310001');
    $paymentRequest->setKeyVersion('1');
    $paymentRequest->setTransactionReference("test" . rand(100000,999999));
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
    
    // Si le montant de la transaction est superieur a 50€ alors appel du 3DS sinon pas de 3-D Secure
    if ($amount < 5000)
    {
	$paymentRequest->setFraudDataBypass3DS(Mercanet::BYPASS3DS_ALL);	
    }
	
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
