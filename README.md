# Multiselect lists

## Technologies
* react (jsx)
* Symfony 6
* MySQL
* Bootstrap
* SCSS
* PHP 8.1

## Instructions

### Important to have
* Symfony 6 and PHP 8.1

### 1 Configure .env file in crud-api root directory. Change credentials:
DATABASE_URL="mysql://root:@127.0.0.1:3306/list_data?serverVersion=5.7"
 
* root - user name
* after "root:" should be password if it is set. In my case not.
* 127.0.0.1 - host
* 3306 - port
* list_data - db name

### 2 crud-api root directory

Important to have xampp/wamp
In control panel start MySQL module

To install dependencies:
composer i

To run backend
symfony server:start

### front-end root directory
To install dependencies:
npm i

To run front-end
npm run dev

## Database structure
post - table

fields:
* id: number
* title: string
* title_loc: string (title location)