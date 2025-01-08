<?php

namespace Wenguii;

use GuzzleHttp\Client;
use Exception;

class WenguiiClient
{
    protected $baseUrl;
    protected $client;
    protected $cdprt;
    protected $usr;
    protected $pwd;
    protected $endUrl;

    public function __construct()
    {
        $this->baseUrl = env('WENGUII_BASE_URL', 'https://wenguii.net/');
        $this->cdprt = env('WENGUII_CDPRT');
        $this->usr = env('WENGUII_USR');
        $this->pwd = env('WENGUII_PWD');
        $this->endUrl = $this->cdprt . '/' . $this->usr . '/' . $this->pwd;

        $this->verifyCredentials();

        // Créer le client Guzzle
        $this->client = new Client();
    }
    /**
     * Effectuer un paiement
     */
    public function payment(string $expediteurPhone, float $montant): array
    {
        $url = $this->baseUrl . 'PAIEMENTP/' . $expediteurPhone . '/' . $montant . '/' . $this->endUrl;
        $response = $this->client->get($url);
        $data = json_decode($response->getBody(), true);
        if (!isset($data['ETAT'])) {
            throw new \Exception("La clé 'ETAT' est manquante dans la réponse de l'API.");
        }
        $code = $data['ETAT'];
        $errorCodes = [
            201 => "Échec d'envoi",
            202 => "Mot de passe erroné",
            203 => "Utilisateur inactif",
            204 => "Code partenaire erroné",
            205 => "Utilisateur non trouvé",
            500 => "Partenaire désactivé",
            501 => "Partenaire inexistant",
            200 => "La transaction a été initialisée",
        ];
        if (isset($errorCodes[$code])) {
            $data['MESSAGE'] = $errorCodes[$code];
        }
        return $data;
    }

    /**
     * Effectuer un retrait
     */
    public  function withdrawal(string $beneficiairePhone, float $montant): array
    {
        $url = $this->baseUrl . 'DEPOTP/' . $beneficiairePhone . '/' . $montant . '/' . $this->endUrl;
        $response = $this->client->get($url);
        $data = json_decode($response->getBody(), true);
        if (!isset($data['ETAT'])) {
            throw new \Exception("La clé 'ETAT' est manquante dans la réponse de l'API.");
        }
        $code = $data['ETAT'];
        $errorCodes = [
            300 => "Le retrait à été initié",
            301 => "Échec d'envoi",
            302 => "Mot de passe erroné",
            303 => "Utilisateur inactif",
            304 => "Code partenaire erroné",
            305 => "Plafond utilisateur insuffisant",
            306 => "Utilisateur non trouvé",
            307 => "Solde partenaire insuffisant",
            500 => "Partenaire désactivé",
            501 => "Partenaire inexistant",
        ];
        if (isset($errorCodes[$code])) {
            $data['MESSAGE'] = $errorCodes[$code];
        }
        return $data;
    }

    /**
     * Vérifier l'état d'une transaction
     */
    public  function checkStatus(string $transactionId): array
    {
        $url = $this->baseUrl . 'ETATO/' . $transactionId . '/' . $this->endUrl;
        $response = $this->client->get($url);
        $data = json_decode($response->getBody(), true);
        if (!isset($data['ETAT'])) {
            throw new \Exception("La clé 'ETAT' est manquante dans la réponse de l'API.");
        }
        $code = $data['ETAT'];
        $errorCodes = [
            400 => 'La transaction à été trouvée',
            401 => "Transaction en cours de paiement",
            402 => "Transaction en attente de validation",
            403 => "Transaction annulée",
            404 => "Transaction inexistante",
            202 => "Mot de passe erroné",
            203 => "Utilisateur inactif",
            204 => "Code partenaire erroné",
            205 => "Utilisateur non trouvé",
            500 => "Partenaire inactif",
            501 => "Partenaire inexistant",
        ];
        if (isset($errorCodes[$code])) {
            $data['MESSAGE'] = $errorCodes[$code];
        }
        return $data;
    }


    private function verifyCredentials()
    {
        if (empty($this->cdprt)) {
            throw new \Exception("Le champ 'cdprt' est manquant.");
        }
        if (empty($this->usr)) {
            throw new \Exception("Le champ 'usr' est manquant.");
        }
        if (empty($this->pwd)) {
            throw new \Exception("Le champ 'pwd' est manquant.");
        }
    }

    /**
     * Consulter le solde
     */
    public  function getBalance(): array
    {
        $url = $this->baseUrl . 'SOLDE/' . $this->endUrl;
        $response = $this->client->get($url);
        $data = json_decode($response->getBody(), true);
        if (!isset($data['ETAT'])) {
            throw new \Exception("La clé 'ETAT' est manquante dans la réponse de l'API.");
        }
        $code = $data['ETAT'];
        $errorCodes = [
            200 => "Le solde a été récupéré avec succès",
            202 => "Mot de passe erroné",
            203 => "Utilisateur inactif",
            204 => "Code partenaire erroné",
            205 => "Utilisateur non trouvé",
            500 => "Partenaire inactif",
            501 => "Partenaire inexistant",
        ];
        if (isset($errorCodes[$code])) {
            $data['MESSAGE'] = $errorCodes[$code];
        }
        return $data;
    }
}

class WenguiiException extends Exception {}
