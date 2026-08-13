Changes in version 3.0
----------------------

 * **PHP >= 8.2 is now required.**

 * **Symfony 5.4 or 6.x is now required** (``symfony/framework-bundle: ^5.4|^6.0``).

 * ``symfony/console`` and ``symfony/yaml`` are now explicit dependencies of the bundle.
   They were previously relied upon as transitive dependencies of ``symfony/framework-bundle``,
   which does not actually require them. Nothing to do on your side — Composer will pull them in.

 * **BC break** — the default value of ``file_path`` changed from
   ``%kernel.project_dir%/app/config/parameters.yml`` to
   ``%kernel.project_dir%/config/parameters.yml``, since the ``app/`` directory no longer exists in
   Symfony 4 and above.

   If you were relying on the old default, declare the path explicitly in
   ``config/packages/kachkaev_assets_version.yaml``:

   ```yml
   kachkaev_assets_version:
       file_path: '%kernel.project_dir%/app/config/parameters.yml'
   ```

 * The root node of the bundle configuration was renamed from ``assets_version`` to
   ``kachkaev_assets_version`` so that it matches the extension alias. This only affects
   ``bin/console config:dump-reference`` output — the configuration key you write in your
   application was, and still is, ``kachkaev_assets_version``.

Changes in version 2.0
----------------------

 * Console command namespace was changed from ``assets_version`` to ``assets-version``
   to comply with Symfony standards.
