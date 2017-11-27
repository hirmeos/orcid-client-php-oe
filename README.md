# orcid-client-php-oe
Open Edition Orcid Member API PHP Client

You'll need working Orcid Member Api Credrentials in order to connect to the API

make sure they are defined in OrcidConfiguration.php as "CLIENT_ID" and "CLIENT_SECRET"

The endpoints defined in this file are those of the Orcid Api sandbox environnment.

Run composer install / update .

To test search Api query : php index.php query="your query" ( token="your search session access token" ) . It should output an xml .
