# API Upload d'image (Symfony, Docker, Bucket S3)

## DESCRIPTION
Cette API, développée avec Symfony, permet de gérer l'upload d'images sur un bucket Amazon S3 (bundle [aws/aws-sdk-php](https://packagist.org/packages/aws/aws-sdk-php)) et de les lister dans un dashboard. L'API valide les images avant l'upload pour assurer un contrôle strict des fichiers.


## FONCTIONNALITÉS
- **Upload d'Images** : Permet d'uploader plusieurs images à la fois, avec un maximum configurable.
- **Validation des Images** :
  - Vérifie le type MIME des fichiers et leurs extensions (supporte les formats jpg, jpeg, png et webp).
  - Vérifie la taille des fichiers pour s'assurer qu'ils ne dépassent pas une limite spécifiée (10 MB).
- **Gestion des Erreurs** :
  - Retourne des messages d'erreur détaillés si des images échouent à la validation ou à l'upload.
  - Fournit des retours sur les images qui ont été uploadées avec succès et celles qui ont échoué.
- **Rapport de Statistiques** :
  - Fournit un rapport JSON indiquant combien d'images ont été uploadées avec succès et les noms des fichiers correspondants.
  - Indique également les erreurs d'upload pour les images non traitées malgré une validation correcte.
- **Dashboard de Listing des Images** :
  - Affiche les images stockées dans le bucket Amazon S3 via une interface utilisateur.
  - Intègre la pagination native de S3 pour faciliter la navigation dans de grands ensembles d'images. Utilise les données de session pour permettre à l'utilisateur de revenir aux pages précédentes tout en conservant l'état de la pagination.
  - Permet de visualiser les détails des images telles que le nom, la taille, la date d'upload, et la miniature.
  - Permet de supprimer une image




## INSTALLATION



### Étape 1 : Cloner le dépôt
```
git clone git@github.com:fredericpoulain/Test_upload.git .
```
### Étape 2 : Construire l'image Docker
> [!WARNING]
> Si Docker Compose V1 est utilisé, préférez la syntaxe `docker-compose`.
```bash
docker compose up --build -d
```

### Étape 3 : Lancer les conteneurs
```bash
docker compose up -d
```

### Étape 4 : Entrer dans le conteneur
```bash
docker exec -ti www_test_adictiz_fp bash
cd project
```
### Étape 5 : Installer les dépendances
```bash
composer install
```

### Étape 6 : configurer .env.local
```
DATABASE_URL="mysql://root:@database_test_adictiz_fp:3306/db_test_adictiz_fp?serverVersion=9.0.1&charset=utf8mb4"

AWS_ACCESS_KEY_ID=AKIAxxxxxxxxxxxxxxxx
AWS_SECRET_ACCESS_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
AWS_DEFAULT_REGION=xxxxxxx
AWS_BUCKET_NAME=xxxxxxxxxxxx
AWS_VERSION=latest
```


### Étape 7 : Créer les tables
```bash
symfony console doctrine:schema:update --force
```

### Étape 8 : Exécuter la "fixture" pour créer l'utilisateur admin
```bash
symfony console d:f:l --no-interaction
```
*****************
> [!WARNING]
> ### Si restriction d'accès aux fichiers depuis le container : 
> `chmod -R 775 /var/www/project` et `chown -R www-data:www-data /var/www/project`


## UTILISATION

### Ajouter les images: 
> [!NOTE]
> Avec un logiciel comme [Postman](https://www.postman.com/)
#### Endpoints Upload -> POST `http://127.0.0.1:8000/api/upload`
- **Paramètres** : Un ensemble d'images sous le champ images (images[]).
- **Réponses** :
  - **200 OK** : Lorsque toutes les images sont uploadées avec succès.
  - **400 Bad Request** : En cas d'erreurs de validation ou de problèmes lors de l'upload.

### Se connecter au dashboard
`127.0.0.1:8000/login`

> [!IMPORTANT]
> L'exécution de la fixture `AdminFixtures` est nécessaire.

- login : `admin@dashboardUpload.com`
- password : `admin`
### Connexion base de données MySQL
`127.0.0.1:8080`

> [!NOTE]
> ### Remarque
> La pagination via S3 est moins flexible que celle utilisant une base de données. Avec S3, la pagination utilise des tokens et ne permet pas d'accéder directement à des pages spécifiques. En revanche, une base de données permet une pagination plus précise avec des outils comme KnpPaginator, offrant tri, accès direct à une page, et gestion optimisée des données.
****************

## PHPUNIT

### Étape 1
**Copier** le fichier `.env.local` sous le nom de `.env.test.local`

### Étape 2
Créer la base de données de test

```bash
symfony console d:d:c --env=test
```
### Étape 3
Créer les tables

```bash
symfony console d:m:m --env=test
```

### Étape 4
#### Exécuter les tests

**AdminFixtures** en premier pour valider `AdminLoginTest.php` : 
```bash
php bin/phpunit tests/Integration/AdminFixturesTest.php
```

```bash
php bin/phpunit tests/Unit/UserTest.php
php bin/phpunit tests/Integration/ImageServiceTest.php
php bin/phpunit tests/Functionnal/AdminLoginTest.php
```
