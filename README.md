# Laravel Shopify Sample

This project allows you to:
1. register shopify stores via a Private App api_key/password pair
2. view those stores' products 
3. edit product titles and descriptions. 

Written assuming sqlite database, see .env.example (/PATH/TO/DB/)

# Setup Instructions 

1. Clone repository 
2. ```composer install```
3. Copy .env.example to .env
4. Fill in DB_DATABASE (.env) with a path to the sqlite file 
5. ```php artisan key:generate```
6. ```php artisan migrate```
7. ```php artisan serve``` 

# How to use 
> You will need a private app with READ/WRITE on products for any shopify store. 
> If you do not have a suitable development store, I can provide one. 

1. Register an account through the frontend. 
2. From the dashboard, select "Add Store" 
3. Fill in Private App details. Shopify Store URL should be in format "{store}.myshopify.com" 
4. Select Register Store - if successful you will be redirected to the dashboard and will now see the store there. 
    - Most common errors would be incorrect shopify store url OR store already added for a different user. 
5. Select Products for the newly added store, you should now see a list of products. 
6. You can click "Shopify" for a tab to be opened direciting to the shopify admin page for the product (assuming you are logged in on the Shopify) 
7. You can click "Details" to see title, desc and variants. 
    - On the details page Title and Description are editable, clicking save will change the information in Shopify. 

# API Authentication

After you have registered a user through the frontend, you will see an API Token on the dashboard. 
This can be used to access the RESTAPI. 

Token can be passed as a ```Bearer``` token. 

# API Endpoints

```/api/user GET``` Load current user

```/api/stores GET``` Load currently registered shopify stores

```/api/store POST``` Register a new shopify store
