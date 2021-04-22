# Chantiers demo

1) Les biblios utilisés
   ```
   "cocur/slugify": "^4.0"
   ```
   Pour la création des slugs
   
2) Modifier les configs du fichier .env pour se connecter à la base des données

3) Créer la base et les tables en exécutant ces deux commandes
   ```
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
   
4) Pour lancer le démo :
   ```
   php bin/console serve:run
   ```
