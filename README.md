# CUSTOM MINI-SHOP: SYMFONY 4
A Symfony-4 based Micro-Shop Demo. This Micro-App demonstrates certain aspects of Symfony-4. 
The App  exposes only the Front-End implementation of a Custom Web-Shop with SQLite3 DB as the Data-Source.
Javascript was also used here for interaction and REST-API Interactions. Another demonstration of how easy it is
to craft an API using Symfony 4.


## INSTALLATION + USAGE HINTS
Simply run `composer install` on the cloned package.
Once Composer finishes installing all the necessary Dependencies, run `php bin/console server:start`
That's all! Afterwards, you may want to navigate to `http:localhost:8000`



### SOME NOTES
This Symfony-4 Micro-App is not documented. Anyways; All Methods/Functions have long but Descriptive names that allows you to determine 
instantly what each of them does in the Program. 


A Few Screenshots were also added to the top Directory with the name: **-SCREENSHOTS-**.
There are no Admin Interfaces to this App. This means, you can't manage the Shop like you would 
in an Ideal Case. Similarly, the Checkout is also not functional since this is only a Mock anyways.
SQLite3 Database is used here as the Data-Source for Portability + Quick & Easy Prototyping.