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

    public function __construct()
    {
        $this->baseUrl = env('WENGUII_BASE_URL', 'https://wenguii.net/');  
        $this->cdprt = env('WENGUII_CDPRT');
        $this->usr = env('WENGUII_USR');
        $this->pwd = env('WENGUII_PWD');

         // Vérifier si les informations d'identification sont présentes
         if (empty($this->cdprt) || empty($this->usr) || empty($this->pwd)) {
            throw new \Exception("Les informations d'identification sont manquantes dans le fichier .env.");
        }

        // Créer le client Guzzle
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
        ]);
    }
    /**
     * Effectuer un paiement
     */
    public  function payment(string $expediteurPhone, float $montant): array
    {
        $response = $this->makeRequest('PAIEMENTP', [
            'EXPO' => $expediteurPhone,
            'MONTO' => $montant,
        ]);

        if ($response['ETAT'] !== 200) {
            throw new WenguiiException($this->getPaymentErrorMessage($response['ETAT']));
        }

        return $response;
    }

    /**
     * Effectuer un retrait
     */
    public  function withdrawal(string $beneficiairePhone, float $montant): array
    {
        $response = $this->makeRequest('DEPOTP', [
            'BENO' => $beneficiairePhone,
            'MONTO' => $montant,
        ]);

        if ($response['ETAT'] !== 300) {
            throw new WenguiiException($this->getWithdrawalErrorMessage($response['ETAT']));
        }

        return $response;
    }

    /**
     * Vérifier l'état d'une transaction
     */
    public  function checkStatus(string $transactionId): array
    {
        $response = $this->makeRequest('ETATO', [
            'IDO' => $transactionId,
        ]);

        return $response;
    }


    private function verifyCredentials()
    {
        if (empty($this->cdprt) || empty($this->usr) || empty($this->pwd)) {
            throw new \Exception("Les informations d'identification sont manquantes.");
        }
    }

    /**
     * Consulter le solde
     */
    public  function getBalance(): array
    {
        $response = $this->makeRequest('SOLDE', []);

        if ($response['ETAT'] !== 200) {
            throw new WenguiiException($this->getBalanceErrorMessage($response['ETAT']));
        }

        return $response;
    }

    protected function makeRequest(string $endpoint, array $params): array
    {
        $this->verifyCredentials();

        $params = array_merge($params, [
            'CDPRT' => $this->cdprt,
            'USR' => $this->usr,
            'PWD' => $this->pwd,
        ]);

        try {
            $path = "/{$endpoint}/" . implode('/', array_values($params));
            $response = $this->client->get($path);
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new WenguiiException("Erreur lors de la requête API: " . $e->getMessage());
        }
    }

    protected function getPaymentErrorMessage(int $code): string
    {
        return match ($code) {
            201 => "Échec d'envoi",
            202 => "Mot de passe erroné",
            203 => "Utilisateur inactif",
            204 => "Code partenaire erroné",
            205 => "Utilisateur non trouvé",
            500 => "Partenaire désactivé",
            501 => "Partenaire inexistant",
            default => "Erreur inconnue",
        };
    }

    protected function getWithdrawalErrorMessage(int $code): string
    {
        return match ($code) {
            301 => "Échec d'envoi",
            302 => "Mot de passe erroné",
            303 => "Utilisateur inactif",
            304 => "Code partenaire erroné",
            305 => "Plafond utilisateur insuffisant",
            306 => "Utilisateur non trouvé",
            307 => "Solde partenaire insuffisant",
            500 => "Partenaire désactivé",
            501 => "Partenaire inexistant",
            default => "Erreur inconnue",
        };
    }

    protected function getBalanceErrorMessage(int $code): string
    {
        return match ($code) {
            202 => "Mot de passe erroné",
            203 => "Utilisateur inactif",
            204 => "Code partenaire erroné",
            205 => "Utilisateur non trouvé",
            500 => "Partenaire inactif",
            501 => "Partenaire inexistant",
            default => "Erreur inconnue",
        };
    }
}

class WenguiiException extends Exception {}