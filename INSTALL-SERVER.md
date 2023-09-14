# shibd and Apache Server Setup

Sample installation, tested on Ubuntu 22.04

Always refer to the [official documentation](https://shibboleth.atlassian.net/wiki/spaces/SP3/pages/2065335062/Apache).

## Install packages

1. `apt install shibboleth-sp-common shibboleth-sp-utils libapache2-mod-shib`
2. Adapt your config files in /etc/shibboleth (especially `attriute-map.xml`, `attribute-policy.xml` and `shibboleth2.xml`)
3. `sudo shib-keygen -f -u _shibd -h example.com -y 10 -e https://example.com/shibboleth -o /etc/shibboleth/`
4. `systemctl restart apache2 shibd`
5. Supply the https://example.com/Shibboleth.sso/Metadata file to your IDP (this contains a notice on top, which can be simply removed)

## Make the sso routes available 

Sample Apache Configuration

```
RewriteEngine on
RewriteOptions InheritBefore
RewriteRule ^Shibboleth.sso - [L]
RewriteRule ^shibboleth-sp - [L]

AllowOverride FileInfo

AuthType shibboleth
ShibRequestSetting requireSession 1

Require expr %{REQUEST_URI} !~ m#/ssoauth.*#
Require shib-session
```

Test with: https://example.com/Shibboleth.sso/Login
