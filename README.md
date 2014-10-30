Well, I have this DAO to handle my products in my catalog. It uses sqlite to 
store my products. A product is nothing more than an id, an EAN and a name.
To avoid store products duplicated I check the EAN to be unique in my db.

I found that something is not working well in my code, and I
decided to fix it by the method of TDD. Unfortunately I am a very busy guy, so
I do not have the time to fix. I would like to ask you to patch it! All I have
is a ToDo list for you ;)

You CAN change the fingerprint of the methods, but please keep the names fo I
can easily update my app!

You MUST NOT change my database schema!

You MUST NOT touch my production database!

You MUST keep the functionality described in code documentation.

TODOs:
- Refactor the class to be able to test the public methods!
- Write the tests using separate sqlite database!
- Find the bug in the class (with your tests) and fix it!

Would be nice to have:
- Check the class characteristic and introduce an exception if it would be a better practice
- check if the class have code duplication, and refactor it

Oh, I forgot, my schema is:
CREATE TABLE product (
    id INTEGER PRIMARY KEY,
    ean varchar(64) default '',
    name text default ''
);
