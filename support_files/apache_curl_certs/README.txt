**Last Update 21OCT2021 - RG
These need to go into the apache24/bin folder or the mailer function will fail.

It looks like the cacerts.pem is the same file as the curl-ca-bundle.crt with a different name. The cacerts.pem file won't be on the repo as it is ignored.