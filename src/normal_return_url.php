<?php
    require_once('Mercanet.php');
	
	// Initialisation de la classe Mercanet avec passage en parametre de la cle secrete
    $paymentResponse = new Mercanet('S9i8qClCnb2CZU3y3Vn0toIOgz3z_aBi79akR30vM9o');
	
	$paymentResponse->setResponse($_POST);

	if($paymentResponse->isValid() && $paymentResponse->isSuccessful()) 
	{
        // Traitement pour les paiements valides
		echo "paiement reussi " . print_r($_POST, true);
		// ...
    }
    else 
	{
        // Traitement pour les paiements en echec
		echo "paiement en echec " . print_r($_POST, true);
		// ...
    }
?>
