**Installing**

1. Create a database (You can choose the name)
2. Exec data.sql file to create tables
3. Unzip bbtv.zip to your root folder
4. Edit “/.env” File Setting your database information
5. The “Public” folder is the application root

**If you want to use your own USDA Key, just edit “.env” file changing “USDA_KEY”**

**Using**

1. Create User:

    URL: api/user/add
    Parameters:
        Email
    Return:
        Key

__Store “key” to use as authentication__

2. Create a Recipe:

    URL: api/recipe/add
	Parameters:
		key (Returned on Step 1)
		name
	Return:
		Id (It is the Recipe ID)

3. Add Ingredients (As much as you want)

    URL: api/ingredient/add
    Parameters:
		key (Returned on Step 1)
		recipe_id (Returned on Step 2)
        ndbno
        quantity
        unit
	Return:
		Id (It is the Ingredient ID)

4. Check your Recipe Report

    URL: api/recipe/get
    Parameters:
		key (Returned on Step 1)
		id (Recipe ID - Returned on Step 2)
	Return:
		Recipe Report

5. Other Options (Have a look to documentation to see all parameters)

    User:
        List - api/user/list

    Recipe:
		List - api/recipe/list
		Edit - api/recipe/edit
		Delete - api/recipe/delete

	Ingredient:
		List - api/ingredient/list
		Edit - api/ingredient/edit
		Delete - api/ingredient/delete