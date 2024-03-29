---
title: Usage with factories
weight: 1
---

A small helper for making translations has been added for use in factories:

This is what a few possible usages look like:

```php
/** @var $this \Illuminate\Database\Eloquent\Factories\Factory */

$this->translations('en', 'english')
// output: ['en' => 'english']

$this->translations(['en', 'nl'], 'english')
// output: ['en' => 'english', 'nl' => 'english']

$this->translations(['en', 'nl'], ['english', 'dutch'])
// output: ['en' => 'english', 'nl' => 'dutch']
```

The helper can also be used outside of factories using the following syntax:

```php
\Illuminate\Database\Eloquent\Factories\Factory::translations('en', 'english');
// output: ['en' => 'english']
```

## In a Factory 

```php
class UserFactory extends \Illuminate\Database\Eloquent\Factories\Factory {
    public function definition(): array
    {
        return [
            'bio' => $this->translations('en', 'english'),
        ];
    }
}
```
