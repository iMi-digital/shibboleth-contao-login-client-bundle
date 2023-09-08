# shibd and Apache Server Setup

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
