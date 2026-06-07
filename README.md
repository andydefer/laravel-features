# Laravel Features

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12%2F13%2F14%2F15-ff2d20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

## Installation

```bash
composer require andydefer/laravel-features
```

## Philosophie du package

Ce package est une collection de fonctionnalités réutilisables pour Laravel : adresses, likes, avis, et plus encore.

Il s'inspire de plusieurs principes du génie logiciel qui servent de **boussole**, pas de **carcan** :

| Principe | Ce qu'il encourage | Ce qu'il n'impose pas |
|----------|-------------------|----------------------|
| **Composition Over Inheritance** | Préférer l'injection de dépendances à l'héritage | L'héritage reste possible quand il est pertinent |
| **Dependency Inversion** | Dépendre des interfaces plutôt que des classes concrètes | Les DTOs et Value Objects peuvent être concrets |
| **Capability-Based Design** | Exposer des capacités spécifiques plutôt que des services fourre-tout | Un service peut avoir plusieurs méthodes cohésives |
| **Domain-Driven Design** | Organiser le code par domaine fonctionnel | La structure peut évoluer librement |

L'objectif est d'obtenir un code **testable**, **découplé** et **maintenable**, sans tomber dans l'extrémisme architectural.

---

## Ce qu'une fonctionnalité est

```php
// ✅ Une fonctionnalité = un service qui expose des capacités métier
final class AddressService
{
    // ✅ Des dépendances injectées dans le constructeur
    public function __construct(
        private readonly AddressRepository $repository,
    ) {}
    
    // ✅ Des méthodes qui reçoivent leurs données en paramètres
    public function add(Model $addressable, AddressRecord $record): Model
    {
        // Ajoute une adresse à n'importe quel modèle
        $record = AddressRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            ...$record->toArrayWithoutNulls(),
        ]);

        return $this->repository->create($record);
    }
    
    // ✅ Pas d'état interne, pas de mémoire entre les appels
    public function primary(Model $addressable): ?Model
    {
        return $this->repository->findPrimary($addressable);
    }
}
```

---

## Pourquoi cette philosophie ?

### Problème : Les traits (anti-pattern)

```php
// ❌ Un trait : impossible à tester isolément
trait HasAddresses
{
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
    
    public function primaryAddress(): ?Address
    {
        return $this->addresses()->where('address_type', 'primary')->first();
    }
}

class User extends Model
{
    use HasAddresses;  // ❌ Couplage implicite, test impossible
}
```

### Solution : Le service

```php
// ✅ Un service : testable, injectable, découplé
final class AddressService
{
    public function __construct(
        private readonly AddressRepository $repository,
    ) {}
    
    public function getPrimary(Model $addressable): ?Address
    {
        return $this->repository->findPrimary($addressable);
    }
}

// ✅ Dans votre contrôleur ou action
final class UserController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService,
    ) {}
    
    public function show(User $user): JsonResponse
    {
        $primary = $this->addressService->getPrimary($user);
        // ...
    }
}
```

---

## Exemple complet : La testabilité en action

```php
// Le service
final class AddressService
{
    public function __construct(
        private readonly AddressRepository $repository,
    ) {}
    
    public function add(User $user, array $data): Address
    {
        $record = AddressRecord::from([
            'addressable_type' => $user->getMorphClass(),
            'addressable_id' => $user->getKey(),
            ...$data,
        ]);

        return $this->repository->create($record);
    }
}

// Le test
final class AddressServiceTest extends IntegrationTestCase
{
    public function test_add_creates_address_for_user(): void
    {
        // ✅ Toutes les dépendances sont mockables
        $repository = $this->createMock(AddressRepository::class);
        $repository->expects($this->once())->method('create');
        
        $service = new AddressService($repository);
        $user = User::factory()->create();
        
        $address = $service->add($user, [
            'street' => '123 Main St',
            'city' => 'Paris',
        ]);
        
        // ✅ Aucune base de données réelle
        // ✅ Test rapide, isolé, fiable
    }
}
```

---
## Fonctionnalités disponibles

Consultez la [documentation complète des fonctionnalités](./docs/FEATURES.md) pour découvrir toutes les fonctionnalités disponibles et leurs cas d'utilisation.

---

## Roadmap

| Fonctionnalité | Statut | Version |
|----------------|--------|---------|
| Addresses | ✅ Disponible | v1.0.0 |
| Likes | 🚧 En développement | v1.1.0 |
| Ratings | 🚧 En développement | v1.1.0 |
| Comments | 📋 Planifié | v1.2.0 |
| Favorites | 📋 Planifié | v1.2.0 |

---

## Quand déroger aux principes ?

La philosophie de ce package est **pragmatique** :

| Règle | Peut-on déroger ? | Exemple |
|-------|-------------------|---------|
| Pas d'état interne | ⚠️ Exception rare | Cache interne avec TTL court |
| Pas de `final` | ✅ Oui | Classe utilitaire sans dépendances |
| Dépendre des interfaces | ✅ Oui | Value Objects, DTOs, Configs |
| Une capacité par service | ✅ Oui | Un service peut avoir plusieurs méthodes cohésives |

**Le critère ultime** : Est-ce que mon code reste **testable** ?

---

## License

MIT © [Andy Defer](https://github.com/andydefer)