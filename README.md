# Open Edition Orcid Member API PHP Client

You'll need working Orcid Member Api Credrentials in order to connect to the API which you can obtain at https://orcid.org/


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
        },
        "autoload": {
                "psr-4": {
                    "OpenEdition\\OrcidClient\\": "src/"
                }
        }
      }
    }
  },
    "require": {
        "php": ">=5.6.29",
        "guzzlehttp/psr7": "1.4.2",
        "psr/http-message": "1.0",
        "openedition/orcid-client":"1.0"
    },
```
To apply your credentials do the following :
```sh
new OrcidConfiguration($yourClientId, $yourClientSecret)
```
Note: The endpoints defined in this configuration file are those of the Orcid Api sandbox environnment.
To change these : 
```sh
(new OrcidConfiguration() )->setConfiguration( $yourConfiguration ) ;
```
## Testing
To test a "search"  query : php index.php query="your query" ( token="your search session access token" ) . It should output an xml .