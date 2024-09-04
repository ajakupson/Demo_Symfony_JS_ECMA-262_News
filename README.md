# Demo of Symfony application with administrator and public interfaces

## UI:
![app](app1.png)
![app](app2.png)
![app](app3.png)
![app](app4.png)
![app](app5.png)

Easiest way to run:
- 1. With docker installed from project's root folder run command:
 ```
 docker compose up
 ```
It will create container and images for Symfony app and apply migrations
- 2. Navigate to http://localhost:8080/admin for administration
     (username: admin, password: admin) 
- 3. Navigate to http://localhost:8080/news to see the news

## Project structure:
 ```
|   .env
|   .env.test
|   .gitignore
|   composer.json
|   composer.lock
|   docker-compose.yaml
|   Dockerfile
|   importmap.php
|   phpunit.xml.dist
|   README.md
|   symfony.lock
|
+---.idea
|   |   .gitignore
|   |   codeception.xml
|   |   dataSources.xml
|   |   modules.xml
|   |   php.xml
|   |   phpspec.xml
|   |   phpunit.xml
|   |   sqldialects.xml
|   |   SymfonyNewsDemo.iml
|   |   vcs.xml
|   |
|   \---commandlinetools
|       |   Symfony_9_4_24__7_31_PM.xml
|       |
|       \---schemas
|               frameworkDescriptionVersion1.1.4.xsd
|
+---assets
|   +---fonts
|   |       fontawesome-webfont.eot
|   |       fontawesome-webfont.svg
|   |       fontawesome-webfont.ttf
|   |       fontawesome-webfont.woff
|   |       fontawesome-webfont.woff2
|   |       FontAwesome.otf
|   |
|   +---js
|   |   +---AdminBundle
|   |   |       news-category.js
|   |   |       news-list.js
|   |   |       news.js
|   |   |
|   |   +---CoreBundle
|   |   |       ajax.js
|   |   |       modal.js
|   |   |       notification.js
|   |   |
|   |   \---PublicBundle
|   |           news.js
|   |
|   \---styles
|       +---AdminBundle
|       |       design.css
|       |       font-awesome.css
|       |       modal.css
|       |       notification.css
|       |
|       +---CoreBundle
|       |       helpers.css
|       |
|       \---PublicBundle
|               design.css
|
+---bin
|       console
|       phpunit
|
+---config
|   |   bundles.php
|   |   preload.php
|   |   routes.yaml
|   |   services.yaml
|   |
|   +---packages
|   |       asset_mapper.yaml
|   |       cache.yaml
|   |       debug.yaml
|   |       doctrine.yaml
|   |       doctrine_migrations.yaml
|   |       framework.yaml
|   |       mailer.yaml
|   |       messenger.yaml
|   |       monolog.yaml
|   |       notifier.yaml
|   |       routing.yaml
|   |       security.yaml
|   |       translation.yaml
|   |       twig.yaml
|   |       validator.yaml
|   |       web_profiler.yaml
|   |
|   \---routes
|           admin.yaml
|           framework.yaml
|           public.yaml
|           security.yaml
|           web_profiler.yaml
|
+---docker
|   |   entrypoint.sh
|   |
|   +---mysql
|   |       init.sql
|   |
|   +---nginx
|   |       nginx.conf
|   |
|   +---php
|   |       php.ini
|   |
|   \---php-fpm
|           www.conf
|
+---migrations
|       .gitignore
|       Version20240904160156.php
|
+---public
|   |   index.php
|   |   phpinfo.php
|   |
|   \---images
|       \---CoreBundle
|               symfony-logo.svg
|
+---src
|   |   Kernel.php
|   |
|   +---AdminBundle
|   |   |   AdminBundle.php
|   |   |
|   |   +---Command
|   |   |       SendWeeklyNewsCommand.php
|   |   |
|   |   \---Controller
|   |           AuthController.php
|   |           NewsCategoriesController.php
|   |           NewsController.php
|   |
|   +---CoreBundle
|   |   |   CoreBundle.php
|   |   |
|   |   +---Constants
|   |   |       FileConstants.php
|   |   |
|   |   +---DTO
|   |   |       NewsCategoryDTO.php
|   |   |
|   |   +---Entity
|   |   |       News.php
|   |   |       NewsCategory.php
|   |   |       NewsComment.php
|   |   |
|   |   +---Repository
|   |   |       NewsCategoryRepository.php
|   |   |       NewsCommentsRepository.php
|   |   |       NewsRepository.php
|   |   |
|   |   +---Resources
|   |   |   \---config
|   |   |       \---doctrine
|   |   |               News.orm.xml
|   |   |               NewsCategory.orm.xml
|   |   |               NewsComment.orm.xml
|   |   |
|   |   \---Service
|   |           NewsCategoriesService.php
|   |           NewsCommentsService.php
|   |           NewsService.php
|   |           PictureUploadService.php
|   |
|   \---PublicBundle
|       |   PublicBundle.php
|       |
|       \---Controller
|               NewsController.php
|
+---templates
|   \---bundles
|       +---AdminBundle
|       |       email.weekly.news.html.twig
|       |       layout.html.twig
|       |       login.html.twig
|       |       news.categories.html.twig
|       |       news.html.twig
|       |       news.list.html.twig
|       |
|       \---PublicBundle
|               layout.html.twig
|               news.html.twig
|               news.list.html.twig
|
+---tests
|       bootstrap.php
|
\---translations
        .gitignore
 ```                    
