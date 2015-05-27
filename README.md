QUIZMASTER
==========

This is a pretty basic web-app for single-shot pass/fail quizzes. The 
intended use is for a knowledge check before forcing volunteers to be 
re-trained. 

The idea is that volunteers will receive an email with a unique link which
has already been configured in the database. 

Speaking of database.... 

Database
--------
Quizmaster expects a sqlite3 database in 'database.db' with a single table:

    CREATE TABLE users(id, email, taken, pass, name, responses, config);

- id is the id that goes on the url (http://quizmaster/?id=foobar)
- email is the email address of the user
- taken determines whether or not the particular user has taken the quiz 
  (0 - not taken / 1 - taken) 
- pass is whether the user passed the quiz. 
- name is the user's real name. 
- responses has a json array of the user's responses. Not good rdb practice,
  but it's a crude hack for now. To be fixed later with another table. 
- config - tells the server which config file to use to ask the user 
  questions. 
