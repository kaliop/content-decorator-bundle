# Kaliop Content Decorator Bundle

Kaliop Content Decorator Bundle is an Ibexa extension inspired by
[eZObjectWrapperBundle](https://github.com/kaliop/ezobjectwrapper). It lets you work with Ibexa `Content` objects as
typed models plus dedicated repositories.

Instead of using raw content objects everywhere, you map content types to custom classes extending
`Kaliop\Contracts\ContentDecorator\Model\ContentDecorator`.

## Compatibility

| Bundle line | Ibexa | Symfony | PHP    |
|-------------|-------|---------|--------|
| 1.x         | 4.6   | 5.4 LTS | >= 8.1 |

## Installation

```bash
composer require kaliop/content-decorator-bundle
```

If Symfony Flex does not enable the bundle automatically, register it in `config/bundles.php`:

```php
return [
    Kaliop\Bundle\ContentDecorator\KaliopContentDecoratorBundle::class => ['all' => true],
];
```

## Quick Start

1. Configure mappings:

```yaml
# config/packages/kaliop_content_decorator.yaml
kaliop_content_decorator:
    default_class: App\Model\GenericContent
    default_repository_class: Kaliop\ContentDecorator\Repository\ContentRepository
    mappings:
        App:
            namespace: 'App\Model'
            dir: '%kernel.project_dir%/src/Model'
```

2. Create a decorator class:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use Kaliop\ContentDecorator\Attribute\Decorator;
use Kaliop\Contracts\ContentDecorator\Model\ContentDecorator;

#[Decorator(repositoryClass: App\Repository\ArticleRepository::class, contentTypes: ['article'])]
final class Article extends ContentDecorator
{
}
```

3. Create a repository:

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use Kaliop\ContentDecorator\Repository\AbstractContentRepository;

final class ArticleRepository extends AbstractContentRepository
{
    public function findByAuthorId(int $authorId): array
    {
        // Implement query logic
        return [];
    }
}
```

4. Use `ContentDecoratorManager` in your service/controller:

```php
$repository = $contentDecoratorManager->getRepository(App\Model\Article::class);
$articles = $repository->findByAuthorId(42);
```

## Configuration

```yaml
kaliop_content_decorator:
    default_class: App\Model\GenericContent
    default_repository_class: Kaliop\ContentDecorator\Repository\ContentRepository
    mappings:
        App:
            namespace: 'App\Model'
            dir: '%kernel.project_dir%/src/Model'
    content_types:
        article: App\Model\Article
```

## Injectors

Decorators are not Symfony services. Use injectors to add services during decoration. Implement
`Kaliop\Contracts\ContentDecorator\Injector\InjectorInterface` and tag as `kaliop.content_decorator.injector`
(autoconfiguration handles this automatically).

Default injectors:

- `ConfigResolverInjector`
- `IbexaRepositoryInjector`
- `ImageVariationInjector`
- `LoggerInjector`
- `ManagerInjector`
- `TranslatorInjector`
- `UrlGeneratorInjector`

## Performance and Method-Level Cache

- Decorated instances are cached in memory per request.
- Proxy classes can cache selected method results.
- Mark methods with `#[Cacheable]`.
- Method parameters must be serializable.

```php
use Kaliop\ContentDecorator\Attribute\Cacheable;

#[Cacheable]
public function getExpensiveComputation(): array
{
    return [];
}
```

## Contribute

The tool comes with quite a few built-in fixers, but everyone is more than
welcome to [contribute](CONTRIBUTING.md) more of them.

## License

This library is released under the MIT license. See the included
[LICENSE](LICENSE) file for more information.
