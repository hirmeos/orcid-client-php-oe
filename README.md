# Open Edition Orcid Member API PHP Client

You'll need working Orcid Member Api Credrentials in order to connect to the API which you can obtain at https://orcid.org/
Make sure they are defined in src/OrcidConfiguration.php as "CLIENT_ID" and "CLIENT_SECRET" .
The endpoints defined in this configuration file are those of the Orcid Api sandbox environnment.

### Installation

Requires guzzlehttp/psr7 and psr/http-message . To install, copy-past this in your composer.json and run composer update.
```sh
"repositories": {
     "orcid-client": { 
      "type": "package",
      "package": {
        "name": "openedition/orcid-client",  
        "version": "1.0",
        "source": {
          "url": "https://github.com/hirmeos/orcid-client-php-oe", 
          "type": "git", 
          "reference": "origin/master"
        }
      }
    }
  },
    "require": {
        "openedition/orcid-client":"1.0"
    },
```
## Testing
To test a "search"  query : php index.php query="your query" ( token="your search session access token" ) . It should output an xml .