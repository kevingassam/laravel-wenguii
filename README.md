<img src="images/logo.png" alt="Logo" width="80" height="80">

## Laravel WenGuii

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kevingassam/laravel-wenguii.svg)](https://packagist.org/packages/kevingassam/laravel-wenguii)
[![License](https://img.shields.io/packagist/l/kevingassam/laravel-wenguii.svg)](https://github.com/kevingassam/laravel-wenguii/blob/main/LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/kevingassam/laravel-wenguii.svg)](https://packagist.org/packages/kevingassam/laravel-wenguii)
[![GitHub issues](https://img.shields.io/github/issues/kevingassam/laravel-wenguii.svg)](https://github.com/kevingassam/laravel-wenguii/issues)
[![GitHub stars](https://img.shields.io/github/stars/kevingassam/laravel-wenguii.svg?style=social)](https://github.com/kevingassam/laravel-wenguii/stargazers)



Une bibliothèque Laravel pour intégrer facilement l'API de paiement WenGuii dans vos applications.

## Installation

```bash
composer require wenguii/laravel-wenguii:1.0.0
```

## Configuration

Publiez le fichier de configuration :

```bash
php artisan vendor:publish --provider="Wenguii\WenguiiServiceProvider" --tag="config"
```


Ajoutez vos credentials dans votre fichier `.env` :

```env
WENGUII_BASE_URL=https://wenguii.net/
WENGUII_CDPRT=votre-code-partenaire
WENGUII_USR=votre-utilisateur
WENGUII_PWD=votre-mot-de-passe
```

## Utilisation

### Effectuer un paiement

```php
use Wenguii\WenguiiClient;

public function payment(WenguiiClient $wenguii)
{
    try {
        $result = $wenguii->payment(
            expediteurPhone: '1234567890',
            montant: 1000.00
        );
        return $result;
    } catch (WenguiiException $e) {
        // Gérer l'erreur
    }
}
```

### Effectuer un retrait

```php
$result = $wenguii->withdrawal(
    beneficiairePhone: '1234567890',
    montant: 1000.00
);
```

### Vérifier l'état d'une transaction

```php
$status = $wenguii->checkStatus('transaction-id');
```

### Consulter le solde

```php
$balance = $wenguii->getBalance();
```

## Gestion des erreurs

La bibliothèque lance une `WenguiiException` en cas d'erreur. Vous pouvez capturer cette exception pour gérer les erreurs de manière appropriée.

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à soumettre une Pull Request.

## Licence

The MIT License (MIT). Voir [License File](LICENSE.md) pour plus de détails.