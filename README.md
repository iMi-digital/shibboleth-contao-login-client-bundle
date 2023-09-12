# Shibboleth Login for Contao

Some notes about installing the Apache module are in [INSTALL-SERVER.md](INSTALL-SERVER.md)

## Prerequisites

* shibd / mod_shib is installed
* The Shibboleth login handler is available in `/Shibboleth.sso/Login` (this is the default in mod_shib)
* Verify that the login works in general by opening `/Shibboleth.sso/Login`.
* The module currently [expects the following fields](https://github.com/iMi-digital/shibboleth-contao-login-client-bundle/issues/2) to be set in the `env` by the shibd module:
  ```
  REDIRECT_unscoped-affiliation
  REDIRECT_uid
  REDIRECT_sn
  REDIRECT_mail
  REDIRECT_cn
  ```
  
## Installation

1. Install the package via composer
2. Add the frontend module to your page for the frontend login

## Configuration

Add to `config/config.yaml`:

```yaml
shibboleth_auth_client:
    shibboleth:
        auto_create_frontend_user: false
        allowed_backend_groups:
            - admin
```

## Development

### Testing

1. `CREATE DATABASE contaoshibboleth`
2. `composer install` (in the module directory)
3. `vendor/bin/phpunit`

## Acknowledgements

Thanks to [@markocupic](https://github.com/markocupic) who wrote the [SAC Login Bundle](https://github.com/markocupic/swiss-alpine-club-contao-login-client-bundle)
which this module is heavily based on.





