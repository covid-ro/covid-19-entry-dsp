## [1.1.3] - 2020-05-23

### Modify
- CHANGELOG.md
- translations files
- home.blade.php
- layout.blade.php
- CSS project file
- HomeController
- UserController
- Declaration model

## [1.1.2] - 2020-05-23

### Modify
- CHANGELOG.md
- translations files
- home.blade.php
- HomeController
- partial refactored related to [#76][4] Updates for DSP User

## [1.1.1] - 2020-05-22

### Modify
- CHANGELOG.md and README.md
- translations files
- declaration.blade.php
- Declaration model
- HomeController
- partial refactored related to [#78][1] Updates for DSP Admin
- all refactored related to [#82][2] QR code - new data
- all refactored related to [#80][3] Add link in menu for all types of users to the form where to complete the declaration

## [1.1.0] - 2020-05-21

### Modify
- CHANGELOG.md
- translations files
- declaration.blade.php
- Declaration model
- HomeController

## [1.0.4] - 2020-05-21

### Modify
- CHANGELOG.md
- translations files
- declaration.blade.php
- Declaration model

## [1.0.3] - 2020-05-20

### Modify
- CHANGELOG.md
- translations files
- declaration.blade.php
- Declaration model

## [1.0.2] - 2020-05-20

### Modify
- CHANGELOG.md
- pdf js files
- pdf preview/download actions

## [1.0.1] - 2020-05-19

### Modify
- CHANGELOG.md
- translations files
- declaration.blade.php
- Declaration model
- project css file

## [1.0.0] - 2020-05-19

### Modify
- CHANGELOG.md
- fix automation
- fix declaration.blade.php

## [0.9.8.1] - 2020-05-19

### Modify
- CHANGELOG.md
- refactor automation to point webserver to src and src/storage

## [0.9.8] - 2020-05-19

### Added
- ansible automation

### Modify
- CHANGELOG.md
- refactor the whole structure of the project

## [0.9.7] - 2020-05-02

### Added
- build the search specific declaration
- register declaration with DSP user

### Modify
- CHANGELOG.md
- translations files
- refactor the controller and model to show user based list
- refactored list of declarations to be shown related to user
- fix critical jsPDF bug

## [0.9.6] - 2020-04-29

### Added
- new post route for ajax refresh declarations list
- new icons for status

### Modify
- CHANGELOG.md
- translations files
- refactor Declaration Model in order to reflect last API updates
- refactor HomeController
- refactor home and declaration blade templates

## [0.9.5] - 2020-04-22

### Added
- build reset users password mechanisms

### Modify
- CHANGELOG.md, README.md
- add small modifications on top nav menu

## [0.9.4] - 2020-04-21

### Added
- build seeders and migrate for DSP users
- build two new commands related to DSP users

### Modify
- CHANGELOG.md, README.md
- add small modifications on top nav menu

## [0.9.3] - 2020-04-21

### Added
- update style related to user admin requirements
- add migrations and seeder for DSP admin user
- build the command for reset admin password

### Modify
- CHANGELOG.md, .env.example, README.md
- top nav bar layout, related to admin user
- LoginController and login view

## [0.9.2] - 2020-04-20

### Added
- english translations for PDF

### Modify
- CHANGELOG.md

## [0.9.1] - 2020-04-15

### Added
- print PDF scripts

### Modify
- view related to Declaration view
- CHANGELOG.md

## [0.9.0] - 2020-04-15

### Added
- new translations related to Declaration view
- QR code service

### Modify
- model, controller and view related to Declaration view
- translations, routes and configurations
- CHANGELOG.md

## [0.8.1] - 2020-04-13

### Added
- new JS scripts for PDF integrations

### Modify
- move JS for PDF from resources to public namespace
- CHANGELOG.md

## [0.8.0] - 2020-04-13

### Added
- icons, css and js related to Datatables
- cache as macro
- Countries library in Composer

### Modify
- refactor HomeController and Declaration model to able list declarations and show one declaration
- CHANGELOG.md

## [0.7.0] - 2020-04-11

### Added
- Checkpoint model
- Declaration model
- ApiTrait
- declaration.blade -> view declaration
- Datatables library in Composer

### Modify
- refactor HomeController to able list declarations and show one declaration
- routing web
- CHANGELOG.md

## [0.6.1] - 2020-04-10

### Modify
- refactor Login to able login user by username instead email
- CHANGELOG.md

## [0.6.0] - 2020-04-10

### Added
- add soft delete
- add username and order
- make email not required
- make username unique and required

### Modify
- update User by managing "Border Checkpoint"
- CHANGELOG.md

## [0.5.0] - 2020-04-09

### Modify
- remove routes related to registration, verify email and reset password
- update app layout to reflect new requirements
- README.md and and CHANGELOG.md

## [0.4.0] - 2020-04-01

### Added
- add romanian translations files
- override mails and notification

### Modify
- update all files related to translations
- README.md and and CHANGELOG.md

## [0.3.0] - 2020-03-29

### Added
- install npm, redis, laravel/ui and improved run.sh
- deploy all files for Bootstrap Auth Module
- add Border Checkpoints
- mail verification

### Modify
- README.md and and CHANGELOG.md

## [0.2.0] - 2020-03-29

### Added
- deploy all files from Docker containerization
- deploy files for PDF generation

### Modify
- README.md and and CHANGELOG.md

## [0.1.0] - 2020-03-26

### Added
- deploy with Composer a bare vanilla Laravel

### Modified
- update README.md and CHANGELOG.md

## [0.0.1] - 2020-03-26

### Added
- README.md, LICENSE and CHANGELOG.md
- initial commit


[1]: https://github.com/citizennext/covid-19-entry-dsp/issues/78
[2]: https://github.com/citizennext/covid-19-entry-dsp/issues/82
[3]: https://github.com/citizennext/covid-19-entry-dsp/issues/80
[4]: https://github.com/citizennext/covid-19-entry-dsp/issues/76