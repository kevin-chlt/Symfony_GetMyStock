# GetMyStock || French Stock Management App
Simple PHP project with the framework symfony and a relational DB.
I've used some basic bundle as Doctrine, Security, Twig, Mailer... (see in composer.json)
-----------------------------------------------------------------------------------------------------------
## App Functionalities

### => Subscribe to the website <=

At the subscription the app will set your role at USER, send you an email with a key
(your uuid userID and an account_key generated with uniqid function), so you cannot access to the
site before you confirm your mail. (Error 403).  
When your email is confirmed, the app will give you a ROLE_MEMBER.  
Sure, the username and email address must be unique, and the app gonna check that.

## => Then, you are authenticated and member <=  
You can access to the page of the table who contains all of your stock with the price excl tax or incl tax.  
You can click on a product name and see the detail of the product in a new page  
If you have the ROLE_MANAGER or ROLE_ADMIN, you have access to the create product form who gonna appear in a Bootstrap offcanvas, 
an edition page of the product chosen.

-------------------------------------------------------------------------------------------------------------

## Installation 

1) Pull the repository.
2) Make a "composer install --no-dev --optimize-autoloader" if you are in prod environment. 
3) Configure your .env files with your DB config and your SMTP.
4) Make the migration in your DB with the doctrine command "doctrine:migration:migrate"
5) You need to create an admin, go to the route "/subscribe", complete the form, and you'll be added to the table "user" inside your DB, now
you can set your role as ["ROLE_ADMIN"].

For the moment, I didn't code the interface for set the users roles, so you must set them inside your user table.