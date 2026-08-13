KachkaevAssetsVersionBundle
===========================

[![License](https://poser.pugx.org/grizzlylab/assets-version-bundle/license)](https://packagist.org/packages/grizzlylab/assets-version-bundle)

Updating the assets version manually at each deploy is a real pain. This Symfony bundle automates the
process and thus makes your life a bit happier.

The bundle can read and write the ``assets_version`` parameter in any ``*.yml`` file from the Symfony
console. The original file formatting is carefully preserved, so you won’t lose your comments or empty
lines between the groups of parameters, if there are any.

**Requirements:** PHP >= 8.2, Symfony 5.4 or 6.x.

Imagine the configuration of your project looks the following way:

``config/packages/framework.yaml``
```yml
framework:
    # ...
    assets:
        version: "%assets_version%"
```
``config/parameters.yml``
```yml
parameters:
    # ...
    assets_version: v042
    # ...
```

You simply call ``bin/console assets-version:increment``, ``v042`` changes to ``v043`` and all your
assets get a new URL: ``my_cosy_homepage.css?v042`` → ``my_cosy_homepage.css?v043``. More features are
described below.

It is important to clear the ``prod`` cache after updating the assets version for a change to take
effect (just as with any other application parameter).

Versioning your project’s assets is a common good practice. More on the ``assets.version`` setting can
be found in the Symfony docs:
https://symfony.com/doc/current/reference/configuration/framework.html#version


Installation
------------

Run ```composer require grizzlylab/assets-version-bundle```

Unless you are using Symfony Flex, register the bundle in ``config/bundles.php``

```php
return [
    // ...
    Kachkaev\AssetsVersionBundle\KachkaevAssetsVersionBundle::class => ['all' => true],
];
```


Configuration
-------------

Here is the default configuration for the bundle:

```yml
kachkaev_assets_version:

    # path to the file that contains the assets version parameter
    file_path:             '%kernel.project_dir%/config/parameters.yml'

    # name of the parameter to work with
    parameter_name:        assets_version

    # name of the class that reads and writes the assets version parameter
    manager:               Kachkaev\AssetsVersionBundle\AssetsVersionManager
```

You don’t need to create ``config/packages/kachkaev_assets_version.yaml`` for these values to apply.

### Option 1 (simple): Assets versioning is done on the server

If you don’t want the changes of ```assets_version``` to show up in your git repository, all you have
to do is the following:

1. Create ```config/parameters.yml```:

 ```yml
 parameters:
     # ...
     assets_version: v000
 ```

2. Make sure the file is imported. In ``config/services.yaml``:

 ```yml
 imports:
     - { resource: ../parameters.yml }
 ```

3. Enable ```%assets_version%``` in ```config/packages/framework.yaml``` (see the top of this file)

4. Commit and push your local changes

5. All done! Now each time you want to update the version of the assets, call these commands on the
   server:
 ```sh
bin/console assets-version:increment --env=prod
bin/console cache:clear --env=prod
```

### Option 2 (recommended): Assets versioning is under source control

If your app runs on multiple production servers, you will benefit from keeping the assets version in
the project’s git repo. Since ```config/parameters.yml``` is often listed in ```.gitignore```,
```assets_version``` should be stored somewhere else.

1. Create ```config/assets_version.yml``` and import it from ```config/services.yaml```

 ``config/assets_version.yml``
 ```yml
 parameters:
     assets_version: v000
 ```
 ``config/services.yaml``
 ```yml
 imports:
     - { resource: assets_version.yml }
     # ...
 ```
 Do __not__ add ```config/assets_version.yml``` to ```.gitignore```!

2. Enable ```%assets_version%``` in ```config/packages/framework.yaml``` (see the top of this file)

3. Create ```config/packages/kachkaev_assets_version.yaml```:
 ```yml
 kachkaev_assets_version:
     file_path:  "%kernel.project_dir%/config/assets_version.yml"
  ```

4. That’s it, you are ready to commit what you have! Now each time you want to update the assets,
   follow this routine:

 _on the local machine_
 ```sh
 bin/console assets-version:increment
 git commit
 ```

 _on the production server(s)_
 ```sh
 git pull
 bin/console cache:clear --env=prod
 ```


Console commands
----------------

The bundle adds two commands to the Symfony console: ``assets-version:increment`` and
``assets-version:set``.
Usage examples:

```sh
# Increments assets version by 1 (e.g. was v1, became v2; was 0042, became 0043 - leading letters and zeros are kept)
bin/console assets-version:increment

# Increments assets version by 10 (e.g. was v1, became v11; was 0042, became 0052)
bin/console assets-version:increment 10

# Sets version to "1970-01-01_0000"
bin/console assets-version:set 1970-01-01_0000

# Sets version to "abcDEF-something_else" (no numeric part, so assets-version:increment will stop working)
bin/console assets-version:set abcDEF-something_else

# Decrements assets version by 10 (e.g. was 0052, became 0042; was lorem.ipsum.0.15, became lorem.ipsum.0.5)
# Note two dashes before the argument that prevent symfony from parsing -10 as an option name
bin/console assets-version:increment -- -10

# Decrementing version by a number bigger than current version results in 0 (e.g. was v0010, became v0000)
bin/console assets-version:increment -- -1000
```

The value for assets version must consist only of letters, numbers and the following characters:
``.-_``. Incrementing only works when the current parameter value is an integer or has a numeric
ending.

Please don’t forget to clear the cache by calling ``bin/console cache:clear --env=prod`` for changes
to take effect in the production environment.
