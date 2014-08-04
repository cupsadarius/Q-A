Q-A
===

Q&amp;A platform based on Flake

INSTALATION:<br />
1. Clone the Repository or Download the .zip file<br />
2. Create a virtual host on your server<br />
3. Create a database and import the *.sql file<br />
4. Edit /application/assets/Db/DbConnection.php with your mysql server user password and db<br />
5. Edit the $app->base_url from index.php and js/site.js to match your virtual host<br />
<br /><br />
FEATURES:<br /><br />
Guests can:<br />
1. View sections<br />
2. View questions and answers<br />
3. Register<br />
<br />
Members can:<br />
1. Add questions<br />
2. Edit questions<br />
3. Subscribe to a question( you recive an email when a subscribed question is updated or an answer is added)<br />
4. Answer to questions<br />
5. Edit answers<br />
6. Rate answers<br />
7. Manage subscriptions<br />
8. Edit profile<br />
<br /><br />
INFO:<br />
Design patterns used:<br />
1. Observer(for subscribed questions on update or answer sends an email to subscribed users)<br />
2. Data Mapper(for mapping the entities to the database)<br />
3. Singleton(the Db connection and the Registry)<br />
4. Abstract Factory(the Registry)<br />
5. Factory(ControllerFactory and ModelFactory)<br />
6. Strategy (Based on the login method chosed calls different methods)<br />