# Usage
## Create
```php
$newUser = [
	'firstname' => 'johnny',
	'lastname' => 'bravo',
	'username' => 'johnnyb'
	'password' => 'somerandomstring'
];

try
{
	$crud = new Crud(
		new Database('databaseName', 'username', 'password'),
		'users'
	);

	$id = $crud->create($newUser);

	var_dump($id); // ID of the record just created in the database
}
catch(Exception $e)
{
	echo $e; // Output errors, if any
}
```

## Read
```php
$columns = ['id', 'firstname', 'username'];

try
{
	$crud = new Crud(
		new Database('databaseName', 'username', 'password'),
		'users'
	);

	$result1 = $crud->read(); // Uses ['*'] as the default argument if nothing is passed

	var_dump($result1); // Array of all records in the table, showing all columns

	$result2 = $crud->read($columns);

	var_dump($result2); // Array of all records in the table, showing their id, first name and username
}
catch(Exception $e)
{
	echo $e; // Output errors, if any
}
```

### Using WHERE
The `$where` parameter can take a string (regular SQL, for complex restrictions) or a multi-dimensional array as shown below

```php
$columns = ['id', 'firstname', 'lastname', 'username'];

$where = [
	['WHERE', 'firstname', '=', 'johnny'],
	['OR', 'id', '>=', 10]
];

try
{
	$crud = new Crud(
		new Database('databaseName', 'username', 'password'),
		'users'
	);

	$result = $crud->read($columns, $where);

	var_dump($result); // Returns all records where the firstname is johnny or where the id is 10 or higher
}
catch(Exception $e)
{
	echo $e; // Output errors, if any
}
```

## Update
```php
$changedUserData = [
	'username' => 'johnnyboya'
	'password' => 'somenewrandomstring'
];

$where = [
	['WHERE', 'id', '=', 1]
];

try
{
	$crud = new Crud(
		new Database('databaseName', 'username', 'password'),
		'users'
	);

	$rows = $crud->update($changedUserData, $where);

	var_dump($rows); // Shows how many rows were updated
}
catch(Exception $e)
{
	echo $e; // Output errors, if any
}
```

## Delete
```php
$columns = ['id', 'firstname', 'lastname', 'username'];

// Same syntax as when using read() or update()
$where = [
	['WHERE', 'firstname', '=', 'johnny'],
	['OR', 'id', '>=', 10]
];

try
{
	$crud = new Crud(
		new Database('databaseName', 'username', 'password'),
		'users'
	);

	$result = $crud->delete($where); // Deletes all records where the firstname is johnny or where the id is 10 or higher

	var_dump($result); // Returns true if successful

	$crud->delete(); // Deletes all data in the table
}
catch(Exception $e)
{
	echo $e; // Output errors, if any
}
```
